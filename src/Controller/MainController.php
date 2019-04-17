<?php

namespace App\Controller;


use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends Controller
{
        /**
         * @Route("/", name="home")
         */
        public function index(SortieRepository $sortieRepository): Response
        {
            return $this->render('main/index.html.twig', [
                'sorties' => $sortieRepository->findAll(),
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

