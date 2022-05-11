<?php

namespace App\Repository;

use App\Data\SearchData;
use App\Entity\Badges;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Badges|null find($id, $lockMode = null, $lockVersion = null)
 * @method Badges|null findOneBy(array $criteria, array $orderBy = null)
 * @method Badges[]    findAll()
 * @method Badges[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BadgesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Badges::class);
    }
}
