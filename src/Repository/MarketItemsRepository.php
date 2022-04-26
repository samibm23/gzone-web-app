<?php

namespace App\Repository;
use Doctrine\ORM\EntityRepository;

class MarketItemsRepository extends EntityRepository
{
    public function getBestStore() {
        return $this->createQueryBuilder('mi')
            ->select('IDENTITY(mi.store)')
            ->groupBy('mi.store')
            ->orderBy('count(mi.store)', 'DESC')
            ->setMaxResults(1)->getQuery()->getSingleScalarResult();
    }
}