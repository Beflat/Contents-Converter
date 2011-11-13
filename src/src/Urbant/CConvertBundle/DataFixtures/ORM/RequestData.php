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
        
//なぜか文字化けする。そもそもオリジナルのHTMLがおかしい。
//          $req2 = new ConvertRequest();
//          $req2->setRule($manager->merge($this->getReference('rule_nikkei_biz')));
//          $req2->setUrl('http://bizacademy.nikkei.co.jp/seminar/marketing/suisui_keizai/article.aspx?id=MMACl6000004112011');
//          $req2->setStatus(0);
//          $manager->persist($req2);
        
        $req3 = new ConvertRequest();
        $req3->setRule($manager->merge($this->getReference('rule_nikkei_pc')));
        $req3->setUrl('http://pc.nikkeibp.co.jp/article/knowhow/20110920/1036970/?set=rss');
        $req3->setStatus(0);
        $manager->persist($req3);
        
        $req4 = new ConvertRequest();
        $req4->setRule($manager->merge($this->getReference('rule_nikkei_bussiness')));
        $req4->setUrl('http://business.nikkeibp.co.jp/article/manage/20110824/222247/?P=1');
        $req4->setStatus(0);
        $manager->persist($req4);
        
        //         $req2 = new ConvertRequest();
//         $req2->setRule($manager->merge($this->getReference('rule_gihyo_jp')));
//         $req2->setUrl('http://gihyo.jp/lifestyle/serial/01/android-walking/0048?skip');
//         $req2->setStatus(0);
//         $manager->persist($req2);
        
        $manager->flush();
        
        $this->addReference('req_symfony_1', $req1);
        $this->addReference('req_nikkei_pc_1', $req3);
         $this->addReference('req_nikkei_bussiness_1', $req4);
    }
    
    
    public function getOrder() {
        return 50;
    }
}