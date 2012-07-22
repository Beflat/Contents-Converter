<?php

namespace Urbant\CConvertBundle\DataFixtures\ORM;


use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Urbant\CConvertBundle\Entity\Rule;

class RuleData extends AbstractFixture implements OrderedFixtureInterface{

    public function load(ObjectManager $manager) {

        $rule1 = new Rule();
        $rule1->setName('super_admin_rule_1');
        $rule1->setFilePath('gihyo.xml');
        $rule1->setMatchingRule('/gihyo\.jp/');
        $rule1->setXpath("//div[contains(concat(' ',normalize-space(@class),' '), 'readingContent01')]/*");
        $rule1->setPaginateXpath('');
        $rule1->setCookie('');
        $rule1->setUserId($manager->merge($this->getReference('user_super_admin')));
        $manager->persist($rule1);

        $rule2 = new Rule();
        $rule2->setName('normal_user_rule_1');
        $rule2->setFilePath('gihyo.xml');
        $rule2->setMatchingRule('/gihyo\.jp/');
        $rule2->setXpath("//div[contains(concat(' ',normalize-space(@class),' '), 'readingContent01')]/*");
        $rule2->setPaginateXpath('');
        $rule2->setCookie('');
        $rule2->setUserId($manager->merge($this->getReference('user_normal')));
        $manager->persist($rule2);
        
        $manager->flush();

    }


    public function getOrder() {
        return 20;
    }
}