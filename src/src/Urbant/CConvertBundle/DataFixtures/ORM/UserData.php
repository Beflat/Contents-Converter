<?php

namespace Urbant\CConvertBundle\DataFixtures\ORM;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Urbant\CConvertBundle\Entity\User;

class UserData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface{
    
    /**
     * 
     * @var ContainerInterface
     */
    private $container;
    
    public function setContainer(ContainerInterface $container = null) {
        $this->container = $container;
    }
    
    public function load(ObjectManager $manager) {
        
        
        $user1 = new User();
        $encoder = $this->container->get('security.encoder_factory')->getEncoder($user1);
        $user1->setUsername('admin');
        $user1->setEmail('test@urban-theory.net');
        $user1->setPassword($encoder->encodePassword('testtest', $user1->getSalt()));
        $user1->setEnabled(true);
        $user1->setLocked(false);
        $user1->addRole('ROLE_SUPER_ADMIN');
        $manager->persist($user1);
        
        $user2 = new User();
        $user2->setUsername('normal');
        $user2->setEmail('pr@urban-theory.net');
        $user2->setPassword($encoder->encodePassword('testtest', $user2->getSalt()));
        $user2->setEnabled(true);
        $user2->setLocked(false);
        $user2->addRole('ROLE_USER');
        $manager->persist($user2);
        
        
        $manager->flush();
        
        $this->addReference('user_super_admin', $user1);
        $this->addReference('user_normal', $user2);
    }
    
    
    public function getOrder() {
        return 10;
    }
}
