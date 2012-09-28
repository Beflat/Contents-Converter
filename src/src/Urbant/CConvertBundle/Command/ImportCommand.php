<?php

namespace Urbant\CConvertBundle\Command;

use Urbant\CConvertBundle\Service\ImportRequestService;

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
 * リクエスト情報(URLのリスト)をインポートするコマンド
 */
class ImportCommand extends ContainerAwareCommand {
    
    protected function configure() {
        
        $this->setName('cconvert:import')
            ->setDescription('Import request urls.')
//            ->addOption('file', null, InputOption::VALUE_REQUIRED, 'ファイル名')
            ->addArgument('file', InputArgument::REQUIRED, 'ファイル名')
        ;
    }
    
    
    protected function execute(InputInterface $input, OutputInterface $output) {
        
        $output->writeln('<info>Begin import.</info>');
        
        $file = $input->getArgument('file');
        
        $importService = $this->getContainer()->get('urbant_cconvert.import_request_service');
        
        $lineCount = $importService->getLinesInFileList($fp);
        $count = 0;
        $importService->importRequestList($file, function($url, $result) use($lineCount, $count) {
            $count++;
            if($result == ImportRequestService::RESULT_IMPORT) {
                $output->writeln(sprintf('%6d / %6d: SKIPPED', $count, $lineCount));
            } else {
                $output->writeln(sprintf('%6d / %6d: %s', $count, $lineCount, $url));
            }
        });
        
    }
    
    
    
}


