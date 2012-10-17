<?php

namespace Urbant\CConvertBundle\Tests\Unit\Controller;


use Symfony\Bridge\Doctrine\DataFixtures\ContainerAwareLoader;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Loader;
use Urbant\CConvertBundle\DataFixtures\ORM\RuleData;


class RuleControllerTest extends BaseController
{
    
    /**
     * 
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    private $entityManager;
    
    /**
     * 
     * @var \Doctrine\Common\DataFixtures\ReferenceRepository
     */
    private $referenceRepository;
    
    public function setUp() {
        parent::setUp();
        
        //Fixtureのロード
        $bundle = self::$kernel->getBundle('UrbantCConvertBundle');
        $this->entityManager = self::$kernel->getContainer()->get('doctrine')->getManager();
        
        $loader = new ContainerAwareLoader(self::$kernel->getContainer());
        $loader->loadFromDirectory($bundle->getPath() . '/DataFixtures/ORM');
        $purger = new ORMPurger($this->entityManager);
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_TRUNCATE);
        $executor = new ORMExecutor($this->entityManager, $purger);
        $executor->execute($loader->getFixtures());
        
        $this->referenceRepository = $executor->getReferenceRepository();
    }
    
    /**
     * 一般ユーザーでログインした場合、そのユーザーに関する情報のみが表示されることの確認。
     */
    public function testNormalUserListing() {
        
        $this->loginAsNormalUser($this->client);
        $crawler = $this->client->request('GET', '/rule');
        
        $tableRows = $crawler->filterXPath('//table[@id="data-list"]/*');
        $this->assertEquals('normal_user_rule_1', $tableRows->filterXPath('.//tr[2]/td[2]/a')->text());
        
        $this->assertEquals(2, $tableRows->count());
    }
    
    
    /**
     * 特権管理者でログインした場合、そのユーザーに関する情報のみが表示されることの確認。
     */
    public function testSuperAdminUserListing() {
    
        $this->loginAsSuperAdmin($this->client);
        $crawler = $this->client->request('GET', '/rule');
    
        $tableRows = $crawler->filterXPath('//table[@id="data-list"]/*');
        $this->assertEquals('super_admin_rule_1', $tableRows->filterXPath('.//tr[3]/td[2]/a')->text());
    
        //ヘッダ行 + 実データ(2行)
        $this->assertEquals(3, $tableRows->count());
    }
    
    
    /**
     * ルールを新規登録した場合、そのユーザーに対してしか表示されないこと
     */
    public function testRuleRegistration() {
        $this->loginAsNormalUser($this->client);
        //$this->client->followRedirects(false);
        $this->client->request('GET', '/rule');
        $crawler = $this->client->request('GET', '/rule/add');
        
        $form = $crawler->selectButton('登録')->form();
        
        $this->client->submit($form, array(
            'urbant_cconvertbundle_ruletype[name]' => 'test_new_rule',
            'urbant_cconvertbundle_ruletype[xpath]' => 'xxx',
            'urbant_cconvertbundle_ruletype[matching_rule]' => 'xxx',
            'urbant_cconvertbundle_ruletype[file_path]' => 'xxx',
        ));
        
        //メッセージが表示されていること。
        $this->isSuccessFlashMessageExists($this->client);
        
        $crawler = $this->client->request('GET', '/rule');
        
        $this->assertEquals('test_new_rule', $crawler->filterXPath('//table[@id="data-list"]/tr[2]/td[2]/*')->text());
    }
    
    /**
     * ルールを更新する際、そのルールの所有者であれば更新できること
     */
    public function testRuleCanEditByItsAuthor() {
        
        $testRule = $this->entityManager->merge($this->referenceRepository->getReference('normal_user_rule_1'));
        
        $this->loginAsNormalUser($this->client);
        $this->client->request('GET', '/rule');
        $crawler = $this->client->request('GET', '/rule/edit/' . $testRule->getId());
        
        $form = $crawler->selectButton('保存')->form();
        
        $this->client->submit($form, array(
            'urbant_cconvertbundle_ruletype[name]' => 'test_edited_rule',
            'urbant_cconvertbundle_ruletype[xpath]' => 'xxx2',
            'urbant_cconvertbundle_ruletype[matching_rule]' => 'xxx2',
            'urbant_cconvertbundle_ruletype[file_path]' => 'xxx2',
        ));
        
        //メッセージが表示されていること。
        $this->isSuccessFlashMessageExists($this->client);
        
        $crawler = $this->client->request('GET', '/rule');
        
        $this->assertEquals('test_edited_rule', $crawler->filterXPath('//table[@id="data-list"]/tr[2]/td[2]/*')->text());
        
        //タイトル行も含めて2行であること。
        $tableRows = $crawler->filterXPath('//table[@id="data-list"]/*');
        $this->assertCount(2, $tableRows, 'テーブルの行数は2行であること');
    }
    
    
    /**
     * ルールを更新する際、そのルールの所有者であれば更新できること
     */
    public function testRuleCanNotEditByAnotherAuthor() {
        
        $testRule = $this->entityManager->merge($this->referenceRepository->getReference('normal_user_rule_1'));
        $anotherUserRule = $this->entityManager->merge($this->referenceRepository->getReference('super_admin_rule_1'));
        
        $this->loginAsNormalUser($this->client);
        //$this->client->followRedirects(false);
        $this->client->request('GET', '/rule');
        $crawler = $this->client->request('GET', '/rule/edit/' . $testRule->getId());
        
        $form = $crawler->selectButton('保存')->form();
        
        $form->setValues(array(
            'urbant_cconvertbundle_ruletype[name]' => 'test_edited_rule',
            'urbant_cconvertbundle_ruletype[xpath]' => 'xxx2',
            'urbant_cconvertbundle_ruletype[matching_rule]' => 'xxx2',
            'urbant_cconvertbundle_ruletype[file_path]' => 'xxx2',
        ));
        $this->client->request($form->getMethod(), '/rule/update/' . $anotherUserRule, $form->getPhpValues(), $form->getPhpFiles());
        
        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
        
        //なぜかここでログインが切れてしまうので再ログイン(404が原因？)
        $this->loginAsNormalUser($this->client);
        $crawler = $this->client->request('GET', '/rule');
        $this->assertEquals('normal_user_rule_1', $crawler->filterXPath('//table[@id="data-list"]/tr[2]/td[2]/*')->text());
        
        $updateResult = $this->entityManager->find('UrbantCConvertBundle:Rule', $testRule->getId());
        $this->assertEquals('normal_user_rule_1', $updateResult->getName(), '名前が変わっていないこと');
    }
    
    
    /**
     * ルールを削除できること。
     */
    public function testRuleDeletion() {
        $this->loginAsNormalUser($this->client);
        //$this->client->followRedirects(false);
        $crawler = $this->client->request('GET', '/rule');
        
        $form = $crawler->selectButton('実行')->form();
        $form['ids[0]']->setValue(true);
        
        $id = $form['ids[0]']->getValue();
        $form['type']->select('d');
        
        $this->client->submit($form);
        
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        
//         $entity = $this->entityManager->find('UrbantCConvertBundle:Rule', $id);
//         $this->assertNull($entity, '削除されていること');
    }
    
    
    /**
     * ルールを削除する際、自分以外のルールのIDを指定しても削除できないこと。
     */
    public function testRuleCannotDeleteIfOwnerDoesNotMatched() {
        $this->loginAsNormalUser($this->client);
        //$this->client->followRedirects(false);
        $crawler = $this->client->request('GET', '/rule');
        
        $anotherUserRule = $this->entityManager->merge($this->referenceRepository->getReference('super_admin_rule_1'));
        
        $this->client->request('POST', $this->routeGenerator->generate('UrbantCConvertBundle_rule_batch'), array(
            'ids' => array($anotherUserRule->getId()),
            'type' => 'd',
        ));
        
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        
        $entity = $this->entityManager->find('UrbantCConvertBundle:Rule', $anotherUserRule->getId());
        $this->assertNotNull($entity, '削除されていないこと');
    }
    
    
    /*
    public function testCompleteScenario()
    {
        // Create a new client to browse the application
        $client = static::createClient();

        // Create a new entry in the database
        $crawler = $client->request('GET', '/rule/');
        $this->assertTrue(200 === $client->getResponse()->getStatusCode());
        $crawler = $client->click($crawler->selectLink('Create a new entry')->link());

        // Fill in the form and submit it
        $form = $crawler->selectButton('Create')->form(array(
            'rule[field_name]'  => 'Test',
            // ... other fields to fill
        ));

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check data in the show view
        $this->assertTrue($crawler->filter('td:contains("Test")')->count() > 0);

        // Edit the entity
        $crawler = $client->click($crawler->selectLink('Edit')->link());

        $form = $crawler->selectButton('Edit')->form(array(
            'rule[field_name]'  => 'Foo',
            // ... other fields to fill
        ));

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check the element contains an attribute with value equals "Foo"
        $this->assertTrue($crawler->filter('[value="Foo"]')->count() > 0);

        // Delete the entity
        $client->submit($crawler->selectButton('Delete')->form());
        $crawler = $client->followRedirect();

        // Check the entity has been delete on the list
        $this->assertNotRegExp('/Foo/', $client->getResponse()->getContent());
    }
    */
    
    /**
     * 処理成功のFLASHメッセージが表示されているか
     */
    protected function isSuccessFlashMessageExists(Client $client) {
        $this->assertEquals(1, $client->getCrawler()->filterXPath('//div[@class="alert alert-success"]')->count());
    }
    
}