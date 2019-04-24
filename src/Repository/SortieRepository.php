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

    /**
     * Cette fonction retourne toutes les sorties dont l'état est en cours.
     * Elle est utilisée pour "peupler" la page d'accueil du site quand le visiteur n'est pas identifié
     * Elle prend 2 paramètres, pour déterminer le nombre maximum de sorties affichées,
     * et à partir de quel résultat on affiche (dans l'idée d'une pagination éventuelle)
     */
    public function listeAccueilInvite($nbFirstResult=-1, $nbMaxResult=-1)
    {
        $query = $this->createQueryBuilder('ld');
        $query->andWhere('ld.etat = 2')
            ->addOrderBy('ld.dateHeureDebut', 'DESC')
            ->getQuery();
        // prendre en compte les paramètres de la fonction si attribués
        if ($nbFirstResult>=0){
            $query->setFirstResult($nbFirstResult);
        }
        if ($nbMaxResult>=0){
            $query->setMaxResults($nbMaxResult);
        }

        return $query->getQuery()->getResult();
    }

    /**
     * Cette fonction retourne toutes les sorties de la base par ordre de date d'évènement.
     * Elle filtre cependant les sorties passées, annulées et archivées.
     * Elle est utilisée par la page d'accueil lorsqu'un utilisateur est en session.
     */
    public function listeToutesSorties() {
        return $this->createQueryBuilder('lts')
            // etats: 5: passée; 6 = annulée; 7 = archivée
//            ->andWhere('lts.etat !=5')
            ->andWhere('lts.etat !=6')
            ->andWhere('lts.etat !=7')
            ->addOrderBy('lts.dateHeureDebut', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Cette fonction est appelée lorsqu'un utilisateur lance une recherche depuis la page d'accueil.
     */
    public function rechercheParFiltres($filtres, $utilisateur)
    {

        $query = $this->createQueryBuilder('rpf');

        // filtre par centre de formation => 'site'
        if ($filtres['site']) {
            $query
                ->andWhere('rpf.centreFormation = :site')
                ->setParameter('site', $filtres['site']);
        }

        // filtre texte => 'champ_recherche'
        if ($filtres['champ_recherche']) {
            $query
                ->andWhere(
                    $query->expr()->orX(
                        $query->expr()->like('rpf.nom', ':mot'),
                        $query->expr()->like('rpf.infosSortie', ':mot')
                        )
                )
                ->setParameter('mot', '%'.$filtres['champ_recherche'].'%');
        }

        // filtre date => 'date_debut' et 'date_fin'
        if ($filtres['date_debut']) {
            $query
                ->andWhere('rpf.dateHeureDebut > :dateDebut')
                ->setParameter('dateDebut', $filtres['date_debut']);
        }
        if ($filtres['date_fin']) {
            $query
                ->andWhere('rpf.dateHeureDebut < :dateFin')
                ->setParameter('dateFin', $filtres['date_fin']);
        }

        // filtre checkbox organisateur => 'sorties_organisees'
        if ($filtres['sorties_organisees']) {
            $query
                ->andWhere('rpf.organisateur = :organisateur')
                ->setParameter('organisateur', $utilisateur->getId());
        }

        // filtre checkbox sorties inscrit => 'mes_sorties'

        // filtre checkbox sorties non-inscrit => 'sorties_en_cours'

        // filtre checkbox sorties passées => 'sorties_passees'
        if ($filtres['sorties_passees']) {
            $query
                ->andWhere('rpf.etat = 5');
        }

        return $query
            // etats: 5: passée; 6 = annulée; 7 = archivée
//            ->andWhere('lts.etat !=5')
            ->andWhere('rpf.etat !=6')
            ->andWhere('rpf.etat !=7')
            ->orderBy('rpf.dateHeureDebut', 'DESC')
            ->getQuery()
            ->getResult();
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
