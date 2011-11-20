<?php

namespace Urbant\CConvertBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * リクエストログ1件分を表すデータオブジェクト。
 * 
 * EntityManagerやRepositoryとの依存関係を持たせたくないため、
 * これらのオブジェクトとの連携が必要な処理はConvertRequestServiceに記述する。
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
     * @ORM\ManyToOne(targetEntity="Rule")
     * @ORM\JoinColumn(name="rule_id", referencedColumnName="id")
     */
    private $rule;

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
     * @ORM\Column(name="log", type="text", length="4096")
     */
    private $log;
    
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
        20 => '処理成功',
        30 => '処理失敗',
    );

    const STATE_WAIT = 0;
    const STATE_INPROCESS = 10;
    const STATE_SUCCEEDED = 20;
    const STATE_FAILED = 40;

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
        if(is_null($this->getLog())) {
            $this->setLog('');
        }
    }
    
    /**
     * @ORM\PreUpdate
     */
    public function preUpdate() {
        $this->setUpdated(new \Datetime());
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

    /**
     * Set rule
     *
     * @param Urbant\CConvertBundle\Entity\Rule $rule
     */
    public function setRule(\Urbant\CConvertBundle\Entity\Rule $rule)
    {
        $this->rule = $rule;
    }

    /**
     * Get rule
     *
     * @return Urbant\CConvertBundle\Entity\Rule 
     */
    public function getRule()
    {
        return $this->rule;
    }

    /**
     * Set log
     *
     * @param text $log
     */
    public function setLog($log)
    {
        $this->log = $log;
    }

    
    public function appendLog($log, $delimiter="----------\n") {
        if($this->log != '') {
            $this->log .= $delimiter;
        }
        
        $this->log .= $log;
    }
    
    /**
     * Get log
     *
     * @return text 
     */
    public function getLog()
    {
        return $this->log;
    }
    
    
}