<?php

namespace App\Controller;


use App\Entity\Sortie;
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
     * @Route("/", name="home")
     */
    public function index(SortieRepository $sortieRepository, SiteRepository $siteRepository): Response
    {
        return $this->render('main/index.html.twig', [
            'sites' => $siteRepository->findAll(),
            'sorties'=> $sortieRepository->listeAccueilInvite(0, 8),
        ]);
    }

    /**
     * @Route("/recherche", name="recherche_sorties")
     */
    public function rechercheSorties(SortieRepository $sortieRepository, SiteRepository $siteRepository): Response
    {
        return $this->render('main/index.html.twig', [
            'sorties' => $sortieRepository->rechercheParFiltres(),
            'sites' => $siteRepository->findAll(),
        ]);
    }

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

