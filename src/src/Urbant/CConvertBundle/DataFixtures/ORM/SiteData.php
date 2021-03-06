<?php

namespace Urbant\CConvertBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Urbant\CConvertBundle\Entity\Site;

class SiteData extends AbstractFixture implements OrderedFixtureInterface{
    
    public function load($manager) {
        
        $site1 = new Site();
        $site1->setName('gihyo.jp');
        $site1->setDescription('テスト1号');
        $site1->setCookie('');
        $manager->persist($site1);
        
        $site2 = new Site();
        $site2->setName('symfony');
        $site2->setDescription('Symfony');
        $site2->setCookie('');
        $manager->persist($site2);
        
        $site3 = new Site();
        $site3->setName('日経Biz');
        $site3->setDescription('Symfony');
        $site3->setCookie('');
        $manager->persist($site3);
        
        $manager->flush();
        
        $this->addReference('site_gihyo_jp', $site1);
        $this->addReference('site_symfony', $site2);
        $this->addReference('site_nikkei_biz', $site3);
    }
    
    
    public function getOrder() {
        return 30;
    }
}