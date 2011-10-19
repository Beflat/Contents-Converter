<?php

namespace Urbant\CConvertBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Urbant\CConvertBundle\Entity\Rule;

class RuleData implements FixtureInterface{
    
    public function load($manager) {
        
        $rule = new Rule();
        $rule->setName('gihyo.jp');
        $rule->setFilePath('sample1.xml');
        $manager->persist($rule);
        
        for($i=0;$i<30;$i++) {
            $rule = new Rule();
            $rule->setName('test site - ' . $i);
            $rule->setFilePath('sample_' . $i . '.xml');
            $manager->persist($rule);
        }
        
        $manager->flush();
    }
    
}