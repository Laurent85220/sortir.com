<?php

namespace App\Repository;

use App\Entity\Etat;
use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Sortie::class);
    }


    public function findSortie(Sortie $sortie) {

        $req = $this->createQueryBuilder('ut');
        $req->innerJoin('ut.mesSorties', 's');
        $req->andWhere('s.id = :sortie')->setParameter('sortie', $sortie);

        return $req->getQuery()->getResult();

    }

    public function listeAccueilInvite($nbFirstResult, $nbMaxResult)
    {
        return $this->createQueryBuilder('ld')
            ->addOrderBy('ld.dateHeureDebut', 'DESC')
            ->getQuery()
            ->setFirstResult($nbFirstResult)
            ->setMaxResults($nbMaxResult)
            ->getResult();
    }

    public function rechercheParFiltres($test)
    {
        return $this->createQueryBuilder('rpf')
            ->andWhere('rpf.centreFormation = :val')
            ->setParameter('val', $test)
            ->orderBy('rpf.dateHeureDebut', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
            ;
    }

    public function findSortiearchive() {

        $req = $this->createQueryBuilder('s');
        $req->innerJoin('s.etat','e');
        $req->where('e.id=7');

        return $req->getQuery()->getResult();

    }

    // /**
    //  * @return Sortie[] Returns an array of Sortie objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Sortie
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
