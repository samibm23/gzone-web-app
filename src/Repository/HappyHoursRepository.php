<?php


namespace App\Repository;


use Doctrine\ORM\EntityRepository;

class HappyHoursRepository extends EntityRepository
{
    public function stat() {
        $this->createQueryBuilder()
    }
}