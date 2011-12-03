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
 * コンテンツディレクトリを初期化するコマンド
 */
class InitCommand extends ContainerAwareCommand {
    
    protected function configure() {
        
        $this->setName('cconvert:init')
            ->setDescription('Initialize content directory.')
        ;
    }
    
    
    protected function execute(InputInterface $input, OutputInterface $output) {
        
        $output->writeln('<info>Initialize content directory.</info>');
        
        $contentsDirPath = $this->getContainer()->getParameter('urbant_cconvert.content_dir_path');
        
        if(!is_dir($contentsDirPath)) {
            throw new \Exception($contentsDirPath . ' is not directory.');
        }
        
        $command = 'rm -rf ' . $contentsDirPath . '/* 2>&1';
        $output->writeln($command);
        
        $result = array();
        $status = 0;
        if(exec($command, $result, $status) != 0) {
            throw new \Exception('Failed to execute command: ' . $command . "\n" 
                . "Result: " . var_export($result, true) . "\n"
                . "Status: " . $status);
        }
    }
    
    
    
}


