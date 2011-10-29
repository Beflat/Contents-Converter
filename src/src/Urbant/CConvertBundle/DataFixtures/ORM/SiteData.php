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
        
        $manager->flush();
        
        $this->addReference('site_gihyo_jp', $site1);
    }
    
    
    public function getOrder() {
        return 30;
    }
}