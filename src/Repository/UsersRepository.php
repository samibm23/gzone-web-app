<?php

namespace App\Repository;

use App\Data\SearchData;
use App\Entity\Users;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UsersRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Users::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Users $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Users $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /** 
    *@return Users[]
    */
    public function findSearch(SearchData $search ) : array
    {
        $query = $this->createQueryBuilder('u')->select('u');
        
      

        if ($search->q || $search->p ) {
            $query =
                $query
                    ->where('u.full_name;e LIKE :q')
                    ->setParameter('q','%' .$search->q .'%')
                    ->andWhere('u.username LIKE :p')
                    ->setParameter('p','%' .$search->p .'%');
        }

      


        return $query->getQuery()->getResult();
    }


    public function orderByName()
{
    $em =$this->getEntityManager();
    $query=$em->createQuery('select o from App\Entity\Users o order by o.username ASC');
    return $query->getResult();
}

    // /**
    //  * @return User[] Returns an array of User objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
