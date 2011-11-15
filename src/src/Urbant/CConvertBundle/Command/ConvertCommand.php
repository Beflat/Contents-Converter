<?php

namespace Urbant\CConvertBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Urbant\CConvertBundle\Entity\ConvertRequest;
use Urbant\CConvertBundle\Entity\Content;
use Urbant\CConvertBundle\Scraping\Order;
use Urbant\CConvertBundle\Scraping\ScrapingEngine;
use Urbant\CConvertBundle\Scraping\LocalifyFilter;

use Urbant\CConvertBundle\Convert\Epub\EpubConvertEngine;
use Urbant\CConvertBundle\Convert\Epub\ContentTypeDetector;
use Urbant\CConvertBundle\Convert\Epub\Item;


/**
 * 変換を実行するコマンド。
 * 将来的には、変換処理は管理画面などからルールを登録する時に
 * 「試しに実行してみる」といった事をさせてみたいため、
 * 変換処理はなるべくバッチ処理に依存しないようにしたい。
 */
class ConvertCommand extends ContainerAwareCommand {
    
    protected function configure() {
        
        $this->setName('cconvert:convert')
            ->setDescription('Execute convertion from request log.')
        ;
    }
    
    
    protected function execute(InputInterface $input, OutputInterface $output) {
        
        $output->writeln('<info>TEST</info>');
        
        //リクエストの一覧を取得する
        $em = $this->getContainer()->get('doctrine')->getEntityManager();
        $requestLogRepo = $em->getRepository('UrbantCConvertBundle:ConvertRequest');
        
        $request = new ConvertRequest();
        $requests = $requestLogRepo->getRequests(array('status'=>$request::STATE_WAIT));
        $output->writeln('Total count:' . count($requests));
        //ループが長いので複数のブロックに分解する
        foreach($requests as $request) {
            
            //TODO:ここでトランザクションスタート
            
            //ルールを取得
            $rule = $request->getRule();
            $request->setStatus($request::STATE_INPROCESS);
            $output->writeln('Rule name:' . $request->getRule()->getName());
            
            //TODO:ルール設定情報のロード
            
            //コンテンツ(Model)の生成(処理中状態で)
            $content = new Content();
            $content->setRequest($request);
            $content->setRule($rule);
            $content->setTitle('');
            $content->setStatus(0);  //TODO:状態コードを定義する
            
            $em->persist($request);
            $em->persist($content);
            $em->flush();
            
            //保存先ディレクトリの決定
            //TODO:基準ディレクトリの取得方法を考える
            $outputDir = '/var/www/data/contents_convert/src/app/cache/dev/epub' . $content->getDataDirPath();
            $workDir = $outputDir . '/work';
            $resDir = $workDir . '/res';
            if(!is_dir($resDir)) {
                if(!@mkdir($resDir, 0777, true)) {
                    throw new \Exception('ディレクトリ「' . $resDir . '」の作成に失敗');
                }
                if(!chmod($workDir, 0777)) {
                    throw new \Exception('ディレクトリ「' . $workDir . '」の権限変更に失敗');
                }
                if(!chmod($outputDir, 0777)) {
                    throw new \Exception('ディレクトリ「' . $outputDir . '」の権限変更に失敗');
                }
            }
            
            
            //リソースダウンロードフィルタの初期化
            $localifyFilter = new LocalifyFilter($resDir, 'res');
            
            //スクレイピングエンジンの初期化
            $scrapingEngine = new ScrapingEngine();
            
            if($rule->getCookie() != '') {
                $scrapingEngine->setCookie($rule->getCookie());
            }
            
            //$scrapingEngine->setOutputPath($outputDir);
            //Orderを生成
            
            $urlList = array();
            $urlList[] = $request->getUrl();
            if($rule->getPaginateXpath() != '') {
                $urlList = array_merge($urlList, $scrapingEngine->getUrlListFromXPath($request->getUrl(), $rule->getPaginateXpath()));
            }
            
            foreach($urlList as $url) {
                $order = new Order();
                $order->setTargetFile($url);
                $order->setXPathString($rule->getXPath());
                $order->addFilter('on_scraping_done', $localifyFilter);
                $scrapingEngine->addOrder($order);
            }
            
            //スクレイピングの結果を取得
            $scrapingEngine->execute();
            
            //コンテンツ変換エンジンの初期化
            //TODO: より簡単に変換処理が実施できるようにする。
            //    方針としては、
            $epubEngine = new EpubConvertEngine();
            
            //出力先設定など
            $epubEngine->setOutputPath($outputDir);
            $epubEngine->setWorkDirPath($workDir);
            $contentTitle = $scrapingEngine->getTitle();
            $epubEngine->setTitle($contentTitle);
            
            //画像やCSSなどの関連リソースを追加する
            $contentTypeDetector = new ContentTypeDetector();
            $workDirFiles = array($workDir . '/*');
            while(count($workDirFiles) != 0) {
                $dir = array_shift($workDirFiles);
                $globedDir = glob($dir);
                foreach($globedDir as $dirItem) {
                    if(is_dir($dirItem)) {
                        $workDirFiles[] = $dirItem . '/*';
                        continue;
                    }
                    
                    //TODO:コンテンツタイプを取得する
                    $contentType = $contentTypeDetector->detectFromFileName($dirItem);
                    
                    //Itemを生成、コレクションに追加
                    $item = new Item();
                    $id = $item->slugify(basename($dirItem));
                    $item->setData($id, 'res/' . basename($dirItem), $contentType);
                    $epubEngine->addItem($item);
                }
            }
            
            //スクレイピング後のコンテンツを保存して登録する。
            $contentPath = $workDir . '/page.xhtml';
            $scrapedContent = $scrapingEngine->getResult();
            if(!file_put_contents($contentPath, $scrapedContent)) {
                throw new Exception('ファイル「' . $contentPath . '」の保存に失敗');
            }
            $item = new Item();
            $item->setData('page', 'page.xhtml', $contentTypeDetector->detectFromExt('xhtml'));
            $epubEngine->addItem($item);
            $epubEngine->setMainContentId('page');
            $epubEngine->setEpubFileName($content->getDataFileName());
            
            //変換の実行
            $epubEngine->execute();
            
            //コンテンツ(Model)の更新(状態、ファイル名)
            $content->setStatus(20);
            $request->setStatus($request::STATE_SUCCEEDED);
            $content->setTitle($contentTitle);
            $em->persist($content);
            $em->persist($request);
            $em->flush();
        }
        
    }
    
    
    
}


