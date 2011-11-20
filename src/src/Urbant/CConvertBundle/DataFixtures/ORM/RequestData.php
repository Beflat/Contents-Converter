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
        
        $req5 = new ConvertRequest();
        $req5->setRule($manager->merge($this->getReference('rule_nikkei_co_jp')));
        $req5->setUrl('http://www.nikkeibp.co.jp/article/column/20111104/289495/');
        $req5->setStatus(0);
        $manager->persist($req5);
        
        $req6 = new ConvertRequest();
        $req6->setRule($manager->merge($this->getReference('rule_nikkei_itpro')));
        $req6->setUrl('http://itpro.nikkeibp.co.jp/article/COLUMN/20110407/359227/?ST=develop');
        $req6->setStatus(0);
        $manager->persist($req6);
        
//処理中に無限ループ的なものに陥るため一時的にコメントアウト
//         $req7 = new ConvertRequest();
//         $req7->setRule($manager->merge($this->getReference('rule_ibm_com')));
//         $req7->setUrl('https://www.ibm.com/developerworks/jp/web/library/wa-jqmobile/');
//         $req7->setStatus(0);
//         $manager->persist($req7);
        
        $manager->flush();
        
        $this->addReference('req_symfony_1', $req1);
        $this->addReference('req_nikkei_pc_1', $req3);
        $this->addReference('req_nikkei_bussiness_1', $req4);
        $this->addReference('req_nikkei_co_jp_1', $req5);
        $this->addReference('req_nikkei_itpro_1', $req6);
        $this->addReference('req_ibm_com_1', $req7);
    }
    
    
    public function getOrder() {
        return 50;
    }
}