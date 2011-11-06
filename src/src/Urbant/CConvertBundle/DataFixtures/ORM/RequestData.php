<?php

namespace Urbant\CConvertBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Urbant\CConvertBundle\Entity\ConvertRequest;

class RequestData extends AbstractFixture implements OrderedFixtureInterface{
    
    public function load($manager) {
        
        //
        $req1 = new ConvertRequest();
        $req1->setRule($manager->merge($this->getReference('rule_symfony')));
        $req1->setUrl('http://symfony.com/doc/current/book/service_container.html');
        $req1->setStatus(0);
        $manager->persist($req1);
        
//         $req2 = new ConvertRequest();
//         $req2->setRule($manager->merge($this->getReference('rule_gihyo_jp')));
//         $req2->setUrl('http://gihyo.jp/lifestyle/serial/01/android-walking/0048?skip');
//         $req2->setStatus(0);
//         $manager->persist($req2);
        
        $manager->flush();
        
        $this->addReference('req_symfony_1', $req1);
//        $this->addReference('req_gihyo_jp_1', $req2);
    }
    
    
    public function getOrder() {
        return 50;
    }
}