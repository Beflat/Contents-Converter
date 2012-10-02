<?php

namespace Urbant\CConvertBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * リクエストログのコレクションを扱うオブジェクト
 */
class ConvertRequestRepository extends EntityRepository
{
    
    /**
     * 
     * @param unknown_type $searchConditions
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getQueryBuilderForSearch($searchConditions, $options=array()) {
       $qb = $this->createQueryBuilder('r')->select('r');
       
       if(isset($searchConditions['rule']) && $searchConditions['rule'] != '') {
           $qb->andWhere("r.rule = :rule");
           $qb->setParameter('rule', $searchConditions['rule']);
       }
       if(isset($searchConditions['created_from']) && $searchConditions['created_from'] != '') {
           $qb->andWhere("r.created >= :created_from");
           $qb->setParameter('created_from', $searchConditions['created_from']);
       }
       if(isset($searchConditions['created_to']) && $searchConditions['created_to'] != '') {
           $qb->andWhere("r.created <= :created_to");
           $qb->setParameter('created_to', $searchConditions['created_to']);
       }
       if(isset($searchConditions['status']) && $searchConditions['status'] !== '') {
           $qb->andWhere("r.status = :status");
           $qb->setParameter('status', $searchConditions['status']);
       }
       
       if(isset($options['sort_column'])) {
           $sortDirection = (isset($options['sort_dir']) ? $options['sort_dir'] : 'ASC');
           $qb->addOrderBy('r.' . $options['sort_column'], $sortDirection);
       }
       
       return $qb;
    }
    
    
    /**
     * 
     */
    public function deleteRequestForIds($ids) {
        
        if(!is_array($ids) || count($ids) == 0) {
            throw new \UnexpectedValueException('Target ID list was empty.');
        }
        
        $qb = $this->createQueryBuilder('r')
            ->delete('UrbantCConvertBundle:ConvertRequest', 'r');
        
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
    
    
    /**
     * 
     * @param \Doctrine\ORM\QueryBuilder $qb
     */
    public function getCount($qb = null) {
        if($qb === null) {
            $qb = $this->createQueryBuilder('r');
        }
        $qb->select('count(r.id)');
        return $qb->getQuery()->getSingleScalarResult();
    }
    
    /**
     * 
     * @param Request $request
     */
    public function save(Request $request) {
        
        
        
    }
}