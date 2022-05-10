<?php

namespace App\Repository;

use App\Data\SearchData;
use App\Entity\UserGamePreferences;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserGamePreferences|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserGamePreferences|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserGamePreferences[]    findAll()
 * @method UserGamePreferences[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserGamePreferencesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserGamePreferences::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(UserGamePreferences $entity, bool $flush = true): void
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
    public function remove(UserGamePreferences $entity, bool $flush = true): void
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
                    ->where('u.name;e LIKE :q')
                    ->setParameter('q','%' .$search->q .'%');
        }
        return $query->getQuery()->getResult();
    }


    public function orderByName()
{
    $em =$this->getEntityManager();
    $query=$em->createQuery('select o from App\Entity\UserGamePreferences o order by o.name ASC');
    return $query->getResult();
}

}
