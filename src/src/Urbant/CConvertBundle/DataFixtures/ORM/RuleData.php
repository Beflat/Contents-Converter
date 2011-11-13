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
        $rule1->setXpath("//div[contains(concat(' ',normalize-space(@class),' '), 'readingContent01')]/*");
        $rule1->setPaginateXpath('');
        $rule1->setCookie('');
        $manager->persist($rule1);

        $rule2 = new Rule();
        $rule2->setName('Symfony The Book');
        $rule2->setFilePath('symfony_book.xml');
        $rule2->setSite($manager->merge($this->getReference('site_symfony')));
        $rule2->setXpath("//div[contains(concat(' ',normalize-space(@class),' '), 'column_02')]/*");
        $rule2->setPaginateXpath('');
        $rule2->setCookie('');
        $manager->persist($rule2);

        $rule3 = new Rule();
        $rule3->setName('nikkei.co.jp');
        $rule3->setFilePath('nikkei.xml');
        $rule3->setXpath("//div[contains(concat(' ',normalize-space(@class),' '), 'readingContent01')]/");
        $rule3->setPaginateXpath('');
        $rule3->setCookie('');
        $manager->persist($rule3);

        $rule4 = new Rule();
        $rule4->setName('nikkei_biz');
        $rule4->setFilePath('nikkei_biz.xml');
        $rule4->setXpath("//div[contains(concat(' ',normalize-space(@class),' '), 'edu-article-text')]/*");
        $rule4->setPaginateXpath("//span[contains(concat(' ', normalize-space(@class), ' '), 'edu-page-no')]/a/@href");
        $rule4->setCookie('');
        $manager->persist($rule4);

        $rule5 = new Rule();
        $rule5->setName('nikkei_pc');
        $rule5->setFilePath('nikkei_pc.xml');
        $rule5->setXpath("//div[@id='co_explain']/*");
        $rule5->setPaginateXpath("//div[@id='co_explainLink']/a/@href");
        $rule5->setCookie('');
        $manager->persist($rule5);

        $rule6 = new Rule();
        $rule6->setName('nikkei_bussiness');
        $rule6->setFilePath('nikkei_pc.xml');
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

        $manager->flush();

        $this->addReference('rule_gihyo_jp', $rule1);
        $this->addReference('rule_symfony', $rule2);
        $this->addReference('rule_nikkei', $rule3);
        $this->addReference('rule_nikkei_biz', $rule4);
        $this->addReference('rule_nikkei_pc', $rule5);
        $this->addReference('rule_nikkei_bussiness', $rule6);
    }


    public function getOrder() {
        return 40;
    }
}