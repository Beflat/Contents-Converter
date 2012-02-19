<?php

namespace Urbant\CConvertBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * リクエストログのコレクションを扱うオブジェクト
 */
class ConvertRequestRepository extends EntityRepository
{
    
    public function getQueryBuilderForSearch($searchConditions) {
       $qb = $this->createQueryBuilder('r')
           ->select('r')
           ->addOrderBy('r.created', 'DESC');
       
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
     * @param Request $request
     */
    public function save(Request $request) {
        
        
        
    }
}