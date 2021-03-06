<?php

namespace Urbant\CConvertBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * 変換ルールのコレクションを扱うオブジェクト
 */
class RuleRepository extends EntityRepository
{
    
    public function getQueryBuilderForSearch($searchConditions) {
       $qb = $this->createQueryBuilder('r')
           ->select('r')
           ->addOrderBy('r.created', 'DESC');
        
       if(isset($searchConditions['name']) && $searchConditions['name'] != '') {
           $qb->andWhere("r.name LIKE :name_cond");
           $qb->setParameter('name_cond', '%' . $searchConditions['name'] . '%');
       }
       if(isset($searchConditions['created_from']) && $searchConditions['created_from'] != '') {
           $qb->andWhere("r.created >= :created_from");
           $qb->setParameter('created_from', $searchConditions['created_from']);
       }
       if(isset($searchConditions['created_to']) && $searchConditions['created_to'] != '') {
           $qb->andWhere("r.created <= :created_to");
           $qb->setParameter('created_to', $searchConditions['created_to']);
       }
       
        return $qb;
    }
    
    
    /**
     * 指定したURLにマッチするURLを検索して返す
     * @param string $url URL
     * @return Rule
     * 
     * リクエストログとルールは1:1対応のため、仮に複数の
     * ルールと一致しても最初にマッチしたものを優先して返す。
     */
    public function findRuleForUrl($url) {
        
        $qb = $this->createQueryBuilder('r')
            ->addOrderBy('r.id', 'DESC');
        
        $rules = $qb->getQuery()->getResult();
        if(!is_array($rules)) {
            return null;
        }
        
        foreach($rules as $rule) {
            
            $matchingRule = $rule->getMatchingRule();
            
            if(@preg_match($matchingRule, $url)) {
                return $rule;
            }
        }
        
        return null;
    }
    
    
    /**
     * 
     */
    public function deleteRuleForIds($ids) {
        
        if(!is_array($ids) || count($ids) == 0) {
            throw new \UnexpectedValueException('Target ID list was empty.');
        }
        
        $qb = $this->createQueryBuilder('r')
            ->delete('UrbantCConvertBundle:Rule', 'r');
        
        $idConditions = array();
        $idParams = array();
        foreach($ids as $index=>$id) {
            $key = 'r_id' . $index;
            $idConditions[] = ':' . $key;
            $idParams[$key] = $id;
        }
        
        $qb->where('r.id IN ( ' . implode(' , ', $idConditions) . ' )');
        $qb->setParameters($idParams);
        
        return $qb->getQuery()->execute();
    }
    
}