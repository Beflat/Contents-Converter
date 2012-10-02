<?php

namespace Urbant\CConvertBundle\Command;

use Symfony\Component\Console\Output\ConsoleOutput;

use Symfony\Bridge\Doctrine\DataFixtures\ContainerAwareLoader;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Urbant\CConvertBundle\Entity\ConvertRequest;

require_once dirname(__FILE__) . '/../../../../../../app/AppKernel.php';



class ImportCommandTest extends \PHPUnit_Framework_TestCase {
    
    /**
     * 
     * @var \AppKernel
     */
    private static $kernel;
    
    /**
     * @var \Symfony\Component\DependencyInjection\Container
     */
    private $container;
    
    /**
     * 
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    private $entityManager;
    
    
    private $fixtureDir = '';
    
    public function setup() {
        static::$kernel = new \AppKernel('test', true);
        static::$kernel->boot();
        
        $this->fixtureDir = dirname(__FILE__) . '/fixtures';
        
        $this->container = static::$kernel->getContainer();
        
        $bundle = static::$kernel->getBundle('UrbantCConvertBundle');
        $this->entityManager = $this->container->get('doctrine')->getManager();
        
        $loader = new ContainerAwareLoader($this->container);
        $loader->loadFromDirectory($bundle->getPath() . '/DataFixtures/Acceptance/ImportCommandTest');
        
        //テストデータを全て削除。
        $purger = new ORMPurger($this->entityManager);
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_TRUNCATE);
        $executor = new ORMExecutor($this->entityManager, $purger);
        $executor->execute($loader->getFixtures());
    }
    
    public function testImport() {
        
        $input = new ArrayInput(array(
            'command' => 'cconvert:import', 
            'file' => $this->fixtureDir . '/pattern1.txt',
        ));
        $application = new Application(static::$kernel);
        $application->setAutoExit(false);
        $application->run($input, new ConsoleOutput());
        
        //DBの内容を検証する。
        $repo = $this->entityManager->getRepository('UrbantCConvertBundle:ConvertRequest');
        $this->assertEquals(4, $repo->getCount());
        
        $qb = $repo->getQueryBuilderForSearch(array('status' => ConvertRequest::STATE_WAIT));
        $this->assertEquals(1, $repo->getCount($qb));
        
        $qb = $repo->getQueryBuilderForSearch(array('status' => ConvertRequest::STATE_FAILED));
        $this->assertEquals(3, $repo->getCount($qb));
    }
    
}

