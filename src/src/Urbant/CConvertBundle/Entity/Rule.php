<?php

namespace Urbant\CConvertBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * 変換ルール1件分を表すデータオブジェクト。
 * 
 * EntityManagerやRepositoryとの依存関係を持たせたくないため、
 * これらのオブジェクトとの連携が必要な処理はRuleServiceに記述する。

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
     * リクエストログ(のURL)と変換ルールのマッチングに使用する
     * 正規表現文字列
     * @ORM\Column(name="matching_rule", type="string", length=255)
     */
    private $matching_rule;

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
     * @ORM\ManyToOne(targetEntity="Site")
     * @ORM\JoinColumn(name="site_id", referencedColumnName="id")
     */
    private $site;
    
    
    /**
     * @ORM\Column(name="xpath", type="string", length="255")
     * 
     * TODO: 本来はフィールドとしては持たず設定情報の一分として持たせる
     */
    private $xpath;


    /**
     * @ORM\Column(name="paginate_xpath", type="string", length="255")
     * TODO: 本来はフィールドとしては持たず設定情報の一分として持たせる
     */
    private $paginate_xpath;
    
    
    /**
     * @ORM\Column(name="cookie", type="text", length="4096")
     */
    private $cookie;


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
    
    
    public function __toString() {
        return $this->name;
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

    /**
     * Set xpath
     *
     * @param string $xpath
     */
    public function setXpath($xpath)
    {
        $this->xpath = $xpath;
    }

    /**
     * Get xpath
     *
     * @return string 
     */
    public function getXpath()
    {
        return $this->xpath;
    }

    /**
     * Set paginate_xpath
     *
     * @param string $paginateXpath
     */
    public function setPaginateXpath($paginateXpath)
    {
        $this->paginate_xpath = $paginateXpath;
    }

    /**
     * Get paginate_xpath
     *
     * @return string 
     */
    public function getPaginateXpath()
    {
        return $this->paginate_xpath;
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

    /**
     * Set matching_rule
     *
     * @param string $matchingRule
     */
    public function setMatchingRule($matchingRule)
    {
        $this->matching_rule = $matchingRule;
    }

    /**
     * Get matching_rule
     *
     * @return string 
     */
    public function getMatchingRule()
    {
        return $this->matching_rule;
    }
}