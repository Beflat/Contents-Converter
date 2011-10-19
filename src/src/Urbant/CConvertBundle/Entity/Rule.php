<?php

namespace Urbant\CConvertBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Urbant\CConvertBundle\Entity\Rule
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Urbant\CConvertBundle\Repository\RuleRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Rule
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
     * @var string $file_path
     *
     * @ORM\Column(name="file_path", type="string", length=255)
     */
    private $file_path;

    /**
     * @var date $created
     *
     * @ORM\Column(name="created", type="date")
     */
    private $created;

    /**
     * @var date $updated
     *
     * @ORM\Column(name="updated", type="date")
     */
    private $updated;


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
     * Set file_path
     *
     * @param string $filePath
     */
    public function setFilePath($filePath)
    {
        $this->file_path = $filePath;
    }

    /**
     * Get file_path
     *
     * @return string 
     */
    public function getFilePath()
    {
        return $this->file_path;
    }

    /**
     * Set created
     *
     * @param date $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }

    /**
     * Get created
     *
     * @return date 
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set updated
     *
     * @param date $updated
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
    }

    /**
     * Get updated
     *
     * @return date 
     */
    public function getUpdated()
    {
        return $this->updated;
    }
    
    
    /**
     * @ORM\PrePersist
     */
    public function onPrePresist() {
        $this->setCreated(new \Datetime());
        $this->setUpdated(new \Datetime());
    }
    
    /**
     * @ORM\PreUpdate
     */
    public function onPreUpdate() {
        $this->setUpdated(new \Datetime());
    }
}