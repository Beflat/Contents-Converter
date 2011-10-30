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
            if(!is_dir($outputDir)) {
                if(!@mkdir($outputDir, 0777, true)) {
                    throw new \Exception('ディレクトリ「' . $outputDir . '」の作成に失敗');
                }
            }
            
            //TODO:リソースダウンロードフィルタの設定
            
            //スクレイピングエンジンの初期化
            $scrapingEngine = new ScrapingEngine();
            $scrapingEngine->setOutputPath($outputDir);
            $scrapingEngine->addOrder($order);
            
            //スクレイピングの結果を取得
            $scrapingEngine->execute();
            
            $fp = @fopen($outputDir . '/' . 'result.txt', 'w');
            if(!$fp) {
                throw new \Exception('ファイル「' . $outputDir . '/' . 'result.txt' . '」の作成に失敗');
            }
            @fputs($fp, $order->getResult());
            @fclose($fp);
            
            $output->writeln('<info>Scraping has done.</info>');
            
            //コンテンツ変換エンジンの初期化
            //出力先設定
            
            //スクレイピング後のコンテンツを渡す、変換を実行
            
            //コンテンツ(Model)の更新(状態、ファイル名)
            
        }
        
    }
}


