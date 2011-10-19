<?php

namespace Urbant\CConvertBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Urbant\CConvertBundle\Entity\Site
 *
 * @ORM\Table(name="site")
 * @ORM\Entity(repositoryClass="Urbant\CConvertBundle\Repository\SiteRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Site
{
    /**
     * @var integer $id
      *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var text $description
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @ORM\Column(name="cookie", type="text")
     * @var string $cookie
     */
    private $cookie;
    
    /**
     * @var datetime $created
     *
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @var datetime $updated
     *
     * @ORM\Column(name="updated", type="datetime")
     */
    private $updated;


    
    public function __toString() {
        return $this->name;
    }
    
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param text $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Get description
     *
     * @return text 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set created
     *
     * @param datetime $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }

    /**
     * Get created
     *
     * @return datetime 
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set updated
     *
     * @param datetime $updated
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
    }

    /**
     * Get updated
     *
     * @return datetime 
     */
    public function getUpdated()
    {
        return $this->updated;
    }
    
    
    /**
     * @ORM\PrePersist
     */
    public function onPrePersist() {
        $this->setCreated(new \DateTime());
        $this->setUpdated(new \DateTime());
    }
    
    
    /**
    * @ORM\PreUpdate
    */
    public function onPreUpdate() {
      $this->setUpdated(new \DateTime());
    }
    

    /**
     * Set cookie
     *
     * @param text $cookie
     */
    public function setCookie($cookie)
    {
        $this->cookie = $cookie;
    }

    /**
     * Get cookie
     *
     * @return text 
     */
    public function getCookie()
    {
        return $this->cookie;
    }
}