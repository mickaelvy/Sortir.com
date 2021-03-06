<?php

namespace App\Repository;

use App\Entity\Lieu;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Lieu|null find($id, $lockMode = null, $lockVersion = null)
 * @method Lieu|null findOneBy(array $criteria, array $orderBy = null)
 * @method Lieu[]    findAll()
 * @method Lieu[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LieuRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Lieu::class);
    }


    public function afficherLieuDontLeNomContient($motSaisi){
        return $this->createQueryBuilder('s')
            ->andWhere('s.nom LIKE :motSaisi')
            ->setParameter('motSaisi', '%'.$motSaisi.'%')
            ->orderBy('s.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

}
