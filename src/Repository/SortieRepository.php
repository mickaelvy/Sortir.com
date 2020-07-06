<?php

namespace App\Repository;

use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }



    public  function registeredSorties($currentUserId){
    return $this->createQueryBuilder('s')
        ->innerJoin('s.participants', 'r')
        ->andWhere('r.id = :id')
        ->setParameter('id', $currentUserId)
        ->getQuery()
        ->getResult();;
    }


    public function afficherSortiesDontLeNomContient(string $motSaisi){
        return $this->createQueryBuilder('s')
            ->andWhere('s.nom LIKE :motSaisi')
            ->setParameter('motSaisi', '%'.$motSaisi.'%')
            ->orderBy('s.dateHeureDebut', 'DESC')
            ->getQuery()
            ->getResult();
    }


    public function afficherSortiesEntreDeuxDates(\DateTime $dateA, \DateTime $dateB){
        return $this->createQueryBuilder('s')

            ->andWhere('s.dateHeureDebut BETWEEN :dateA and :dateB')
            ->setParameter('dateA', $dateA)
            ->setParameter('dateB', $dateB)
            ->orderBy('s.dateHeureDebut', 'DESC')
            ->getQuery()
            ->getResult();
    }



    public function afficherSortieMoinsDunMois()
    {
        $currentDateTime = new \DateTime('now');
        $currentDateTimeLe1Mois = $currentDateTime->modify('- 1 month');

        return $this->createQueryBuilder('s')
            ->andWhere('s.dateHeureDebut > :dateX')
            ->setParameter('dateX', $currentDateTimeLe1Mois)
            ->orderBy('s.dateHeureDebut', 'ASC')
            ->getQuery()
            ->getResult();
    }





    public function rechercher($currenDate,$currentUserId,$site,$searchArea,$dateA,$dateB,$ownSorties,$registeredSorties,$passedSorties/*,$unRegisteredSorties*/)
    {
        $qb = $this->createQueryBuilder('s');



        if (!empty($site)){
            $qb->andWhere('s.siteOrganisateur = :site')
                ->setParameter('site', $site)
                ->orderBy('s.dateHeureDebut', 'DESC');
        }




        if (!empty($searchArea)) {
            $qb->andWhere('s.nom LIKE :motSaisi')
                ->setParameter('motSaisi', '%'.$searchArea.'%');
        }



        if (!empty($dateA)) {
            $qb
                ->andWhere('s.dateHeureDebut > :date1')
                ->setParameter('date1', $dateA);
        }



        if (!empty($dateB)) {
            $qb
                ->andWhere('s.dateHeureDebut < :date2')
                ->setParameter('date2', $dateB);
        }



        if (!empty($ownSorties)) {
            $qb
                ->andWhere('s.organisateur = :id')
                ->setParameter('id', $currentUserId);
        }


        if (!empty($registeredSorties)) {
            $qb
                ->innerJoin('s.participants', 'r')
                ->andWhere('r.id = :id')
                ->setParameter('id', $currentUserId);
        }


        if (!empty($passedSorties)) {
            $qb
                ->andWhere('s.dateHeureDebut < :cuurentDate')
                ->setParameter('cuurentDate', $currenDate);
        }


            $query = $qb->getQuery();
            return $query->execute();
    }

}
