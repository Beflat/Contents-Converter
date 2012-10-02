<?php

namespace Urbant\CConvertBundle\Tests\Acceptance\Command\Fixtures;

use Urbant\CConvertBundle\Entity\Rule;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;

class RuleData extends AbstractFixture {
    
    
    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager) {
        
        $rule = new Rule();
        $rule->setName('localhost');
        $rule->setMatchingRule('|localhost/a/|');
        $rule->setXPath('|//body/*|');
        $rule->setFilePath('localhost.xml');
        
        $manager->persist($rule);
        
        $manager->flush();
    }
    
}