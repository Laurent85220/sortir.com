<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Sortie;
use App\Entity\Utilisateur;
use App\Form\SortieType;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/sortie")
 */
class SortieController extends Controller
{
    /**
     * @Route("/", name="sortie_index", methods={"GET"})
     */
    public function index(SortieRepository $sortieRepository): Response
    {
        return $this->render('sortie/index.html.twig', [
            'sorties' => $sortieRepository->findAll(),
        ]);
    }
//-------------------------------------------------------------------------
    /**
     * @Route("/new", name="sortie_new", methods={"GET","POST"})
     */
    public function new(EntityManagerInterface $em, Request $request): Response
    {
        $sortie = new Sortie();
        $etat = $em -> getRepository(Etat::class)->find('1');
        $sortie->setEtat($etat);
        $sortie->setOrganisateur($this->getUser());
        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($sortie);
            $entityManager->flush();

            return $this->redirectToRoute('sortie_index');
        }

        return $this->render('sortie/new.html.twig', [
            'sortie' => $sortie,
            'form' => $form->createView(),
        ]);
    }

 //-----------------------------------------------------------------------

    /**
     * @Route("/afficher{id}", name="sortie_show", methods={"GET"})
     */
    public function show(EntityManagerInterface $em,Sortie $sortie): Response

    {

        $participants=$em->getRepository(Utilisateur::class)
            -> findSortie($sortie);
        return $this->render('sortie/show.html.twig', compact('sortie','participants'))
        ;
    }

    /**
     * @Route("/{id}/edit", name="sortie_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Sortie $sortie): Response
    {
        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('sortie_index', [
                'id' => $sortie->getId(),
            ]);
        }

        return $this->render('sortie/edit.html.twig', [
            'sortie' => $sortie,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="sortie_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Sortie $sortie): Response
    {
        if ($this->isCsrfTokenValid('delete'.$sortie->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($sortie);
            $entityManager->flush();
        }

        return $this->redirectToRoute('sortie_index');
    }
    /**
     * @Route("/ajout{id}", name="ajout_sortie", methods={"GET"})
     */
    public function ajoutMesSorties(EntityManagerInterface $entityManager, Request $request,Sortie $sortie):Response
    {
        /** @var Utilisateur $utilisateur */
        $utilisateur = $this->getUser();
        if (!$this->getUser()->getMesSorties()->contains($sortie)) {
            $utilisateur->addMesSorty($sortie);
            $sortie->addParticipant($utilisateur);
            $this->addFlash("success","Participant ajouté a la sortie ");
        }else{
            $utilisateur->removeMesSorty($sortie);
            $sortie->removeParticipant($utilisateur);
            $this->addFlash("danger","Participant retiré a la sortie ");
        }
        // Sauvegarde la relation
        $entityManager->flush();

        // Redirige l'utilisateur sur les annonces
        return $this->redirectToRoute('home');

    }
}
