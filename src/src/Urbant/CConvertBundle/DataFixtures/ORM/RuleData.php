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
        $rule1->setMatchingRule('/gihyo\.jp/');
        $rule1->setSite($manager->merge($this->getReference('site_gihyo_jp')));
        $rule1->setXpath("//div[contains(concat(' ',normalize-space(@class),' '), 'readingContent01')]/*");
        $rule1->setPaginateXpath('');
        $rule1->setCookie('');
        $manager->persist($rule1);

        $rule2 = new Rule();
        $rule2->setName('Symfony The Book');
        $rule2->setFilePath('symfony_book.xml');
        $rule2->setMatchingRule('/symfony\.com/');
        $rule2->setSite($manager->merge($this->getReference('site_symfony')));
        $rule2->setXpath("//div[contains(concat(' ',normalize-space(@class),' '), 'column_02')]/*");
        $rule2->setPaginateXpath('');
        $rule2->setCookie('');
        $manager->persist($rule2);

        $rule3 = new Rule();
        $rule3->setName('nikkei.co.jp');
        $rule3->setFilePath('nikkei.xml');
        $rule3->setMatchingRule('/nikkei\.co\.jp/');
        $rule3->setXpath("//div[contains(concat(' ',normalize-space(@class),' '), 'readingContent01')]/");
        $rule3->setPaginateXpath('');
        $rule3->setCookie('');
        $manager->persist($rule3);

// HTMLが色々とおかしい、なぜか文字化けする
//         $rule4 = new Rule();
//         $rule4->setName('nikkei_biz');
//         $rule4->setFilePath('nikkei_biz.xml');
//         $rule4->setXpath("//div[contains(concat(' ',normalize-space(@class),' '), 'edu-article-text')]/*");
//         $rule4->setPaginateXpath("//span[contains(concat(' ', normalize-space(@class), ' '), 'edu-page-no')]/a/@href");
//         $rule4->setCookie('');
//         $manager->persist($rule4);

        $rule5 = new Rule();
        $rule5->setName('nikkei_pc');
        $rule5->setFilePath('nikkei_pc.xml');
        $rule5->setMatchingRule('/pc\.nikkeibp\.co\.jp/');
        $rule5->setXpath("//div[@id='co_explain']/*");
        $rule5->setPaginateXpath("//div[@id='co_explainLink']/a/@href");
        $rule5->setCookie('');
        $manager->persist($rule5);

        $rule6 = new Rule();
        $rule6->setName('nikkei_bussiness');
        $rule6->setFilePath('nikkei_pc.xml');
        $rule6->setMatchingRule('/business\.nikkeibp\.co\.jp/');
        $rule6->setXpath("//div[@id='articlebody']/*");
        $rule6->setPaginateXpath("//div[@class='pageNumber']/a[@class='']/@href");
        $rule6->setCookie('DEUserID=c0a84914-4024-1321190602-1; wabwb2011=1321244538; vlink=; '
            . 'ssoa=kVTrFBur5b5wWJqdfsSc8GOI4XHL6Fp-WlcP7QC0wJOd0NM7qhx3A-dqMh3n*xz-J8Ai7s8AX*k7sPjXZ7KSmA##; '
            . 'sso1=T00QrFLAkk9Yp3RoJlop7XlL-9Wg-5hdx2qEJdKB6uSaqilTCD86ohK0wqYFmqvIT-'
            . 'omxNMacybYE7OvvFgFMKDwoFSBIlv2uobUAQN7p0wCADPUcAOt6f0bWL8neg5Z*'
            . 'Ur9Um*Zdntn6mnZfmkJEbJDiJtHvLoWRkKBIBNpYheoulPeyqlQPnFlAZl73pa0AfMzzPT6VzwpDISBPQdPzh1Vvr0zk4lrT'
            . '8vUkF5Dee-ZDaOKekJPqXY7n8OHRxIRZwgFTDm5WiCMLaxtDKQunlm1-y8kygZYto2UMbVqImhGNGBSRYWwxgU0W13vit*'
            . '0TKLsEryzQJw0PdYGJjbTD*wlXrb9OT*fje3KELz0PuDZc6L0iFDhG2NJMidrrcPj4R0AN100hU6wcmf6YELmA0W3LwUEkKya'
            . 'jst3BKB5BbY#; ssod=July+21%2C+2011+22%3A18%3A29; '
            . 'sson=%EF%BD%88%EF%BD%89%EF%BD%92%EF%BD%8F%EF%BD%8B%EF%BD%89; '
            . 'passport=Nu0nz9P1Ll3FgGkCRWGv9Q; service=000000000000100; '
            . 'integrate=10470001300500300506001500000501500000000000500501000501000503000000000000501500000000000'
            . '5005000000000; '
            . 'NBO_MB=YW00MTbeH%2FoC0GfUF6GxJnFD2G5EhKz9%2Bcq9THo2QUc%3D; '
            . 'NBO_SS=2gsMaVfg5zq7TTZ3oV9bqDCVsMic%2Fs5qHgWIIJlPCUI%3D; '
            . 'NBO_AL=nCmE4sslLay9OvVklIZ%2FQWCqwUyfaUChKzF%2FX1Ve4Sg%3D; ALDONE=1');
        $manager->persist($rule6);

        $rule7 = new Rule();
        $rule7->setName('nikkei_co_jp');
        $rule7->setFilePath('nikkei_pc.xml');
        $rule7->setMatchingRule('/www\.nikkeibp\.co\.jp/');
        $rule7->setXpath("//div[contains(concat(' ', normalize-space(@class), ' '), 'article-entry')]/*");
        $rule7->setPaginateXpath("//div[@class='article-pagination']/p[@class='msp']/a/@href");
        $rule7->setCookie('DEUserID=c0a84914-4024-1321190602-1; vlink=; ssoa=kVTrFBur5b5wWJqdfsSc8GOI4XHL6Fp-'
        	. 'WlcP7QC0wJOd0NM7qhx3A-dqMh3n*xz-J8Ai7s8AX*k7sPjXZ7KSmA##; '
            . 'passport=Nu0nz9P1Ll3FgGkCRWGv9Q; service=000000000011100; '
            . 'integrate=104800013005003005060015000005015000000000005005010005010005030000000000005'
        	. '015000000000005005000000000; ssod=July+21%2C+2011+22%3A18%3A29; '
            . 'sson=%EF%BD%88%EF%BD%89%EF%BD%92%EF%BD%8F%EF%BD%8B%EF%BD%89; '
        	. 'sso1=QrVhOWN39eNIaA*-1syMEge6KfranE70wKwjyJZCKE*SisTi8qEb5PK0splM8c5WURsnR3M*'
        	. 'UIGko-iNX*uhrqOmrz9UoKZzrc78y7voae4d6GtzrimAZvZ*Ie3KDScBsdZqFcYDVyy6Ym8ilOfDPQtv'
        	. 'NTkfver4N6m9yh*R-0c3gyAnJtAjTMZrYvIg-1QLreFrc893SjcdXHanyfuN8EuExdQLYSZveyqlsHLMp'
        	. 'BB8cgn6d19*gyipTaSI46-7dQD8*wiQEJxoho-goJbyIbhB8Eqj0EZGoGweglTLPc4pYvKqaB4nzC-'
        	. 'LgtzhPrhI898J6mUZn39g0mXI9yRUQXSUBimUvoqJ73vlvQy-eBiR5*72pLHZeSCnrdEgxOdfZ*'
        	. 'qLW2E*jzwWdDBrkyTknpdNYX3xie-aLxXf-S9ydpU#');
        $manager->persist($rule7);
        
        $rule8 = new Rule();
        $rule8->setName('nikkei_itpro');
        $rule8->setFilePath('nikkei_pc.xml');
        $rule8->setMatchingRule('/itpro\.nikkeibp\.co\.jp/');
        $rule8->setXpath("//div[@id='kiji']/*");
        $rule8->setPaginateXpath("//div[@id='naviBottom']/div[@class='pageNumber']/a[@class='']/@href");
        $rule8->setCookie('DEUserID=c0a84914-4024-1321190602-1; vlink=; ssoa=IJKKaoDIx7ouSOUQQSm2hZ-'
        	. 'wfwm6LJtQENlmanNPVJro9FlnEcL8DA-3HpHZLZOZ3MJHmOEmCRyMNlNa*zdhaw##; '
        	. 'passport=Nu0nz9P1Ll3FgGkCRWGv9Q; service=000000000011100; '
        	. 'integrate=10480001300500300506001500000501500000000000500501000501000503'
        	. '0000000000005015000000000005005000000000; ssod=July+21%2C+2011+22%3A18%3A29; '
        	. 'sson=%EF%BD%88%EF%BD%89%EF%BD%92%EF%BD%8F%EF%BD%8B%EF%BD%89; sso1=5OKyskuxQG0GJuT3*'
        	. 'fT5nJj4uJJgY3Rn6pdWHEgr8mSJdsd7bk0lrH3PUljlhLBk-RbCmDIwCOxqAHkFnYmrkbnDPN5EBaxhsJRggp'
        	. 'y6x*DkugyzK7V-hyMzRV7GOoL8BHMtsfRPBDqcmMj0RHiKXyMAV5JRMHIfL2F*7sNHshbwlm2n1IuGVqRtFfl'
        	. 'gwWTB0pDXV4OaffCMstFoMv38H5RQL3PASx6A6xBaFRfJLx2cj9nv8XQwn2dvK8yO1FOYiQIDmSql3fY*'
        	. 'wPQzP7ATnoEtt25yRJE6vt6uAFwRUKJIpDv29NOMw7n3ojFf-HTxvCx94ICjkt5h2GcuWQ-Jfm*ngs44cN'
        	. 'X4ytJVhcnpMntnXhqu8F8cCP4MWeKyKtq6zGyIdq7KXS7DuI3nb*UdksGzV5NbMpdFwtSbaUy7rk8%23; '
        	. 'ITPRO_MB=CKnWvCnknz2I6m4MpGSL%2BXDktx%2Fq%2BWgLQjetV5BdUa8%3D; ITPRO_SS=eLOzp9Yvv'
        	. 'shE6PPyquA1Xdt0VQBjyxyCYoPI03MkGao%3D; ITPRO_AL=BnqG0KJxiYLPDXI4OoNaZP9rJb0sZinsDl'
        	. 'XRkr818cA%3D; ALDONE=1; kids=057b35057b37; IMPASEG=S0%3D10474/S1%3D10080/S2%3D10906; '
        	. '_session_id=5798b44e1fb4fb931832fa54bbd1c971; BIGipServerTEST_itpro.nikkeibp.co.jp_'
        	. 'https=189311168.52008.0000; MyITproName=%E3%81%B2%E3%82%8D');
        
        $manager->persist($rule8);
        
        $rule9 = new Rule();
        $rule9->setName('ibm.com');
        $rule9->setFilePath('nikkei_pc.xml');
        $rule9->setMatchingRule('/ibm\.com/');
        $rule9->setXpath("//div[@class='ibm-container']/*");
        $rule9->setPaginateXpath("");
        $rule9->setCookie('');
        $manager->persist($rule9);
        
        //
        
        $manager->flush();

        $this->addReference('rule_gihyo_jp', $rule1);
        $this->addReference('rule_symfony', $rule2);
        $this->addReference('rule_nikkei', $rule3);
        //$this->addReference('rule_nikkei_biz', $rule4);
        $this->addReference('rule_nikkei_pc', $rule5);
        $this->addReference('rule_nikkei_bussiness', $rule6);
        $this->addReference('rule_nikkei_co_jp', $rule7);
        $this->addReference('rule_nikkei_itpro', $rule8);
        $this->addReference('rule_ibm_com', $rule9);
    }


    public function getOrder() {
        return 40;
    }
}