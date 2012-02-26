<?php

namespace Urbant\CConvertBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * 変換後のコンテンツ1件分を表すデータオブジェクト。
 * 
 * EntityManagerやRepositoryとの依存関係を持たせたくないため、
 * これらのオブジェクトとの連携が必要な処理はContentServiceに記述する。
 *
 * @ORM\Table(name="content")
 * @ORM\Entity(repositoryClass="Urbant\CConvertBundle\Repository\ContentRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Content
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
     * 
     * @ORM\ManyToOne(targetEntity="Rule")
     * @ORM\JoinColumn(name="rule_id", referencedColumnName="id")
     */
    private $rule;
    
    
    /**
     * @ORM\ManyToOne(targetEntity="ConvertRequest")
     * @ORM\JoinColumn(name="request_id", referencedColumnName="id")
     */
    private $request;
    
    /**
     * @var integer $status
     *
     * @ORM\Column(name="status", type="integer")
     */
    private $status;
    
    /**
     * @ORM\Column(name="title", type="string")
     */
    private $title;

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

    const STATE_INPROCESS = 0;
    const STATE_FAILED = 10;
    const STATE_UNREAD = 20;
    const STATE_DOWNLOADED = 30;
    const STATE_READ = 40;

    protected $statusList = array(
         0 => '作成中',
        10 => '作成失敗',
        20 => '未読',
        30 => 'DL済',
        40 => '既読',
    );
    
    
    public function __toString() {
        return $this->name;
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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
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
     * Set request
     *
     * @param Urbant\CConvertBundle\Entity\ConvertRequest $request
     */
    public function setRequest(\Urbant\CConvertBundle\Entity\ConvertRequest $request)
    {
        $this->request = $request;
    }

    /**
     * Get request
     *
     * @return Urbant\CConvertBundle\Entity\ConvertRequest 
     */
    public function getRequest()
    {
        return $this->request;
    }
    
    
    public function getStatusList() {
        return $this->statusList;
    }
    
    
    public function getStatusName($value, $default='') {
        return isset($this->statusList[$value]) ? $this->statusList[$value] : $default;
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
    
    
    public function getDataDirPath() {
        return sprintf('/%010d', $this->id);
    }

    
    public function getDataFileName() {
        return sprintf('cc_%06d.epub', $this->id);
    }
    
    public function getDataFilePath() {
        return sprintf('%s/%s', $this->getDataDirPath(), $this->getDataFileName());
    }
    
    /**
     * Set title
     *
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }
    
    
    public function isDone() {
        return in_array($this->status, array(self::STATE_DOWNLOADED, self::STATE_UNREAD, self::STATE_READ));
    }
}