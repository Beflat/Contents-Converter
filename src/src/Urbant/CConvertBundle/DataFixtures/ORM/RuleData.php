<?php

namespace Urbant\CConvertBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Urbant\CConvertBundle\Entity\Rule;

class RuleData extends AbstractFixture implements OrderedFixtureInterface{
    
    public function load($manager) {
        
        $rule1 = new Rule();
        $rule1->setName('gihyo.jp');
        $rule1->setFilePath('sample1.xml');
        $rule1->setSite($manager->merge($this->getReference('site_gihyo_jp')));
        $manager->persist($rule1);
        
        $rule2 = new Rule();
        $rule2->setName('nikkei.co.jp');
        $rule2->setFilePath('sample1.xml');
        $manager->persist($rule2);
        
        
        $manager->flush();
        
        $this->addReference('rule_gihyo_jp', $rule1);
        $this->addReference('rule_nikkei', $rule2);
    }
    
    
    public function getOrder() {
        return 40;
    }
}