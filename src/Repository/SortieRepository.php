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
     * Elle est utilisée par la page d'accueil lorsqu'un utilisateur est en session,
     * utiisateur qui lui est passée en paramètre.
     */
    public function listeToutesSorties($utilisateur) {
        $userId = $utilisateur->getId();
        $query= $this->createQueryBuilder('lts');
        // etats: 1: créée; 2: ouverte; 3: cloturée; 4: en cours; 5: passée; 6 = annulée; 7 = archivée
        $query
//            ->andWhere('lts.etat = 2')
//            ->andWhere('lts.etat = 3')
//            ->andWhere('lts.etat = 4')
//            ->andWhere('lts.etat = 1 and lts.organisateur=:userId')
            ->andWhere('lts.etat !=5')
            ->andWhere('lts.etat !=6')
            ->andWhere('lts.etat !=7')
//            ->orWhere('lts.organisateur=:userId')
//            ->setParameter('userId', $userId)
            ->addOrderBy('lts.dateHeureDebut', 'DESC')
            ;

        return $query
                ->getQuery()
                ->getResult();
    }

    /**
     * Cette fonction est appelée lorsqu'un utilisateur lance une recherche depuis la page d'accueil,
     * au travers du formulaire App\Form\RechercherType.
     * Elle prend en paramètres un tableau des champs du formulaire et de l'utilisateur en session.
     */
    public function rechercheParFiltres($filtres, $utilisateur)
    {

        $query = $this->createQueryBuilder('rpf');
        $query
            ->leftJoin('rpf.lieu', 'l')
            ->leftJoin('l.ville', 'v');

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
                        $query->expr()->like('rpf.infosSortie', ':mot'),
                        $query->expr()->like('l.nom', ':mot'),
                        $query->expr()->like('v.nom', ':mot')
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

        // checkboxes!
//        if ($filtres['organisees'] && $filtres['inscrit'] && $filtres['non_inscrit'] && $filtres['passees']) {
//            // c'est un cas où l'utilisateur veut voir toutes les sorties que l'on veut bien lui montrer,
//            // donc, on ne fait rien, c'est comme si l'utilisateur n'avait pas touché aux checkboxes
//        } else if($filtres['organisees'] && $filtres['inscrit'] && $filtres['non_inscrit']) {
//            // on filtre les sorties passées
//
//        } else {
            // filtre checkbox organisateur => 'organisees'
            if ($filtres['organisees']) {
                $query
                    ->andWhere('rpf.organisateur = :organisateur')
                    ->setParameter('organisateur', $utilisateur->getId());
            }

            // filtre checkbox sorties inscrit => 'inscrit'
            if ($filtres['inscrit']) {
                $query
                    ->andWhere(':idUtilisateur MEMBER OF rpf.participants')
                    ->setParameter('idUtilisateur', $utilisateur->getId());
            }

            // filtre checkbox sorties non-inscrit => 'non_inscrit'
            if ($filtres['non_inscrit']) {
                $query
                    ->andWhere(':idUtilisateur NOT MEMBER OF rpf.participants')
                    ->andWhere('rpf.organisateur != :idUtilisateur')
                    ->setParameter('idUtilisateur', $utilisateur->getId());
            }

            // filtre checkbox sorties passées => 'passees'
            if ($filtres['passees']) {
                $query
                    ->andWhere('rpf.etat = 5');
            }
            else if (!$filtres['passees']) {
                $query
                    ->andWhere('rpf.etat != 5');
            }

//        }

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
