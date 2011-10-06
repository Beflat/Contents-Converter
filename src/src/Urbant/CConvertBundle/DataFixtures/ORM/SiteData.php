<?php

namespace Urbant\CConvertBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Urbant\CConvertBundle\Entity\Site;

class SiteData implements FixtureInterface{
    
    public function load($manager) {
        
        $site = new Site();
        $site->setName('gihyo.jp');
        $site->setDescription('テスト1号');
        
        $manager->persist($site);
        $manager->flush();
    }
    
}