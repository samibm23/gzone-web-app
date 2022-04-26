<?php

namespace App\Repository;

use App\Entity\Games;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Games|null find($id, $lockMode = null, $lockVersion = null)
 * @method Games|null findOneBy(array $criteria, array $orderBy = null)
 * @method Games[]    findAll()
 * @method Games[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GamesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Games::class);
    }

    // /**
    //  * @return Games[] Returns an array of Games objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Games
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findByNameDESC()
    {
        return $this->createQueryBuilder('Games')
            ->orderBy('Games.name','DESC')
            ->getQuery()
            ->getResult()
            ;
    }
    public function findByNameASC()
    {
        return $this->createQueryBuilder('Games')
            ->orderBy(' Games.name','ASC')
            ->getQuery()
            ->getResult()
            ;
    }
public function findGameByName($name){
    return $this->createQueryBuilder('game')
        ->where('game.name LIKE :name')
        ->setParameter('name', '%'.$name.'%')
        ->getQuery()
        ->getResult();
}




    public function stat()
    {
        $qb = $this->createQueryBuilder('v')
            ->select('COUNT(v.id) AS name, SUBSTRING(v.id, 1, 100000) AS id')
            ->groupBy('id');
        return $qb->getQuery()
            ->getResult();
    }
    public function findByName()
    {
        return $this->createQueryBuilder('game')
            ->orderBy('game.name','DESC')
            ->getQuery()
            ->getResult()
            ;
    }
    public function findByNameasc()
    {
        return $this->createQueryBuilder('game')
            ->orderBy(' game.name','ASC')
            ->getQuery()
            ->getResult()
            ;
    }
    */
    public function orderByName()
    {
        $em =$this->getEntityManager();
        $query=$em->createQuery('select o from App\Entity\Games o order by o.name ASC');
        dd($query->getResult());
        return $query->getResult();
    }

    /*public function search($game){
        $query = $this->createQueryBuilder('a');
            $query->where('a.active = 1');
            if($game != null ){
                $query->andWhere('MATCH_AGAINST')
            }


    } */
    public function stat()
    {
        $qb = $this->createQueryBuilder('v')
            ->select('COUNT(v.createDate) AS createDate, SUBSTRING(v.id, 1, 100000) AS id')
            ->groupBy('id');
        return $qb->getQuery()
            ->getResult();
    }
}