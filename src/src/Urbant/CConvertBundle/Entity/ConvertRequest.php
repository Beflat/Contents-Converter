<?php

namespace Urbant\CConvertBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Urbant\CConvertBundle\Entity\ConvertRequest
 *
 * @ORM\Table(name="request")
 * @ORM\Entity(repositoryClass="Urbant\CConvertBundle\Repository\ConvertRequestRepository")
 * @ORM\HasLifecycleCallbacks
 */
class ConvertRequest
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
     * @var string $site
     *
     * @ORM\ManyToOne(targetEntity="Site")
     * @ORM\JoinColumn(name="site_id", referencedColumnName="id")
     */
    private $site;

    /**
     * @var string $url
     *
     * @ORM\Column(name="url", type="string", length=1024)
     */
    private $url;

    /**
     * @ORM\Column(name="status", type="integer")
     */
    private $status;
    
    /**
     * @var date $created
     *
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @var date $updated
     *
     * @ORM\Column(name="updated", type="datetime")
     */
    private $updated;

    
    protected $statusList = array(
        0 => '未処理',
        10 => '処理中',
        20 => '処理完了',
    );


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
     * Set url
     *
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * Get url
     *
     * @return string 
     */
    public function getUrl()
    {
        return $this->url;
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
    public function prePersist() {
        $this->setCreated(new \Datetime());
        $this->setUpdated(new \Datetime());
    }
    
    /**
     * @ORM\PreUpdate
     */
    public function preUpdate() {
        $this->setUpdated(new \Datetime());
    }
    
    /**
     * Set site
     *
     * @param Urbant\CConvertBundle\Entity\Site $site
     */
    public function setSite(\Urbant\CConvertBundle\Entity\Site $site)
    {
        $this->site = $site;
    }

    /**
     * Get site
     *
     * @return Urbant\CConvertBundle\Entity\Site 
     */
    public function getSite()
    {
        return $this->site;
    }
    
    
    public function getStatusList() {
        return $this->statusList;
    }
    
    public function getStatusName($default='') {
        return isset($this->statusList[$this->status]) ? $this->statusList[$this->status] : $default;
    }

    /**
     * Set status
     *
     * @param integer $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * Get status
     *
     * @return integer 
     */
    public function getStatus()
    {
        return $this->status;
    }
}