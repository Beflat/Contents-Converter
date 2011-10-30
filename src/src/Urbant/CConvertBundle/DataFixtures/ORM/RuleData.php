<?php

namespace Urbant\CConvertBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Urbant\CConvertBundle\Entity\Rule;

class RuleData extends AbstractFixture implements OrderedFixtureInterface{
    
    public function load($manager) {
        
        $rule1 = new Rule();
        $rule1->setName('gihyo.jp');
        $rule1->setFilePath('gihyo.xml');
        $rule1->setSite($manager->merge($this->getReference('site_gihyo_jp')));
        $rule1->setXpath("//div[contains(concat(' ',normalize-space(@class),' '), 'readingContent01')]/");
        $rule1->setPaginateXpath('');
        $manager->persist($rule1);
        
        $rule2 = new Rule();
        $rule2->setName('Symfony The Book');
        $rule2->setFilePath('symfony_book.xml');
        $rule2->setSite($manager->merge($this->getReference('site_symfony')));
        $rule2->setXpath("//div[contains(concat(' ',normalize-space(@class),' '), 'column_02')]/*");
        $rule2->setPaginateXpath('');
        $manager->persist($rule2);
        
        $rule3 = new Rule();
        $rule3->setName('nikkei.co.jp');
        $rule3->setFilePath('nikkei.xml');
        $rule3->setXpath("//div[contains(concat(' ',normalize-space(@class),' '), 'readingContent01')]/");
        $rule3->setPaginateXpath('');
        $manager->persist($rule3);
        
        $manager->flush();
        
        $this->addReference('rule_gihyo_jp', $rule1);
        $this->addReference('rule_symfony', $rule2);
        $this->addReference('rule_nikkei', $rule3);
    }
    
    
    public function getOrder() {
        return 40;
    }
}