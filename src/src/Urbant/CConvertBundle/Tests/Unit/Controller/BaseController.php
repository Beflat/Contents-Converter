<?php

namespace Urbant\CConvertBundle\Tests\Unit\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Client;

class BaseController extends WebTestCase {
    
    /**
     *
     * @var Symfony\Bundle\FrameworkBundle\Client
     */
    protected $client;
    
    /**
     *
     * @param Client $client
     */
    protected function loginAsSuperAdmin($client) {
    
        $crawler = $client->request('GET', '/login');
    
        $form = $crawler->selectButton('ログイン')->form();
        $form['_username'] = 'admin';
        $form['_password'] = 'testtest';
    
        $client->submit($form);
        $client->followRedirect();
    }
    
    /**
     *
     * @param Client $client
     */
    protected function loginAsNormalUser($client) {
    
        $crawler = $client->request('GET', '/login');
    
        $form = $crawler->selectButton('ログイン')->form();
        $form['_username'] = 'normal';
        $form['_password'] = 'testtest';
    
        $client->submit($form);
        $client->followRedirect();
    }
    
    /**
     *
     * @param Symfony\Component\BrowserKit\Client $client
     * @param string $userName
     * @param string $password
     */
    protected function login(Client $client, $userName, $password) {
    
        $crawler = $client->request('GET', '/login');
    
        $form = $crawler->selectButton('ログイン')->form();
        $form['_username'] = $userName;
        $form['_password'] = $password;
    
        $client->submit($form);
        $client->followRedirect();
    }
    
}