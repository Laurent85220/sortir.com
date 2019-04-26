<?php

namespace App\Controller;


use App\Entity\Sortie;
use App\Form\SortieType;
use App\Form\RechercherType;
use App\Repository\SiteRepository;
use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends Controller
{
    /**
     * @Route("/", name="home", methods={"GET","POST"})
     */
    public function index(SortieRepository $sortieRepository, SiteRepository $siteRepository, Request $request): Response
    {
        // récupérer le centre de formation de l'utilisateur pour l'afficher par défaut dans les filtres de recherche
        $utilisateur = $this->getUser();
//        $centreParDefaut = $utilisateur->getCentreFormation();
        // la page d'accueil est différente si l'on est un utilisateur identifié ou non
        if ($this->getUser()) {
            $sorties = $sortieRepository->listeToutesSorties($this->getUser());
        } else {
            // note: avec la fonction listeAccueilInvite, on peut limiter le nombre de résultats
            $sorties = $sortieRepository ->listeAccueilInvite();
        }

        $formRechercher = $this->createForm(RechercherType::class);
        $formRechercher->handleRequest($request);
        if ($formRechercher->isSubmitted()) {
            // récupérer les données du formulaire
            $filtres = $formRechercher->getData();

            // envoyer la requête et retourner la liste de sorties filtrée
            return $this->render('main/index.html.twig', [
                'sorties'=>$sortieRepository->rechercheParFiltres($filtres, $utilisateur),
                'formRechercher'=>$formRechercher->createView(),
            ]);
        }

        return $this->render('main/index.html.twig', [
//            'sites' => $siteRepository->findAll(),
            'sorties'=> $sorties,
            'formRechercher'=>$formRechercher->createView(),
        ]);
    }

//    /**
//     * @Route("/recherche", name="recherche_sorties", methods={"GET","POST"})
//     */
//    public function rechercheSorties(SortieRepository $sortieRepository, SiteRepository $siteRepository, Request $request): Response
//    {
//        $form = $this->createForm(RechercheType::class, );
//        $form->handleRequest($request);
//        return $this->render('main/index.html.twig', [
//            'sorties' => $sortieRepository->rechercheParFiltres(),
//            'sites' => $siteRepository->findAll(),
//            'formRecherche' => $form,
//        ]);
//    }

    /**
     * @Route("/fail")
     * */
    public function fail() {
        echo "helloworld";

    }

    /**
     * @Route("/success", name="success")
     */
    public function success()
    {
        return new Response('<html><body>Hello world ! kiss</body></html>');
    }

    /**
     * @Route("/hello")
     * */
    public function helloWorld()
    {
        return  $this->render('main/hello.html.twig');
    }

    /**
     * @Route("/securite")
     */
    public function securite(Request $request)
    {
       $comment= $request->get('comment');
        return $this->render(
            'main/securite.html.twig',
        compact('comment')
    );
    }
}

