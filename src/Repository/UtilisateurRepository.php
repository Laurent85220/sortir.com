<?php

namespace App\Repository;

use App\Entity\Sortie;
use App\Entity\Utilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Utilisateur|null find($id, $lockMode = null, $lockVersion = null)
 * @method Utilisateur|null findOneBy(array $criteria, array $orderBy = null)
 * @method Utilisateur[]    findAll()
 * @method Utilisateur[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UtilisateurRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Utilisateur::class);
    }
public function findParticipantsSortie(Sortie $sortie) {

    $dql = '
            SELECT u,s
            FROM App\Entity\Utilisateur u
             LEFT JOIN u.mesSorties s
             WHERE s.id like sortie_id
             ';

        return $this
            ->getEntityManager()
            ->createQuery($dql)
            ->setParameter(
                'sortie.id',
                $sortie->getId())
            ->getResult();

}
    public function findSortie(Sortie $sortie) {

        $req = $this->createQueryBuilder('ut');
        $req->innerJoin('ut.mesSorties', 's');
        $req->andWhere('s.id = :sortie')->setParameter('sortie', $sortie);

        return $req->getQuery()->getResult();

    }
    // /**
    //  * @return Utilisateur[] Returns an array of Utilisateur objects
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
    public function findOneBySomeField($value): ?Utilisateur
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
