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
        
        
        $requests = $requestLogRepo->getRequests(array());
        $output->writeln('Total count:' . count($requests));
        //ループが長いので複数のブロックに分解する
        foreach($requests as $request) {
            
            //TODO:ここでトランザクションスタート
            
            //ルールを取得
            $rule = $request->getRule();
            $output->writeln('Rule name:' . $request->getRule()->getName());
            
            //TODO:ルール設定情報のロード
            //Orderを生成
            $order = new Order();
            $order->setTargetFile($request->getUrl());
            $order->setXPathString($rule->getXPath());
            
            //コンテンツ(Model)の生成(処理中状態で)
            $content = new Content();
            $content->setRequest($request);
            $content->setRule($rule);
            $content->setStatus(0);  //TODO:状態コードを定義する
            
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
            
            
            //TODO:リソースダウンロードフィルタの設定
            $localifyFilter = new LocalifyFilter($resDir, 'res');
            $order->addFilter('on_scraping_done', $localifyFilter);
            
            //スクレイピングエンジンの初期化
            $scrapingEngine = new ScrapingEngine();
            //$scrapingEngine->setOutputPath($outputDir);
            $scrapingEngine->addOrder($order);
            
            //スクレイピングの結果を取得
            $scrapingEngine->execute();
            
//             $fp = @fopen($outputDir . '/' . 'result.txt', 'w');
//             if(!$fp) {
//                 throw new \Exception('ファイル「' . $outputDir . '/' . 'result.txt' . '」の作成に失敗');
//             }
//             @fputs($fp, $order->getResult());
//             @fclose($fp);
//             $output->writeln('<info>Scraping has done.</info>');
            
            //コンテンツ変換エンジンの初期化
            //TODO: より簡単に変換処理が実施できるようにする。
            //    方針としては、
            $epubEngine = new EpubConvertEngine();
            
            //出力先設定
            $epubEngine->setOutputPath($outputDir);
            $epubEngine->setWorkDirPath($workDir);
            
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
            
            //変換の実行
            $epubEngine->execute();
            
            //コンテンツ(Model)の更新(状態、ファイル名)
            $content->setStatus(20);
            $em->persist($content);
            $em->flush();            
        }
        
    }
}


