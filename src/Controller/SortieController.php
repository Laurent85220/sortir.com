<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\Utilisateur;
use App\Form\AnnulerSortieType;
use App\Form\LieuType;
use App\Form\SortieType;
use App\Repository\LieuRepository;
use App\Repository\SortieRepository;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\GenericEvent;
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
    public function new(EntityManagerInterface $em, LieuRepository $lieux, VilleRepository $villes, Request $request): Response
    {
        $sortie = new Sortie();
        $organisateur = $this->getUser();
        $etat = $em->getRepository(Etat::class)->find('1');
        $sortie->setEtat($etat);
        $sortie->setOrganisateur($organisateur);
        $sortie->setCentreFormation($organisateur->getCentreFormation());
        $formSortie = $this->createForm(SortieType::class, $sortie);
        $formSortie->handleRequest($request);
        $lieu = new Lieu();
        $formLieu= $this->createForm(LieuType::class, $lieu);
        $formLieu->handleRequest($request);

        if ($formLieu->isSubmitted() && $formLieu->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($lieu);
            $entityManager->flush();

            return $this->redirectToRoute('sortie_new');
        }

        if ($formSortie->isSubmitted() && $formSortie->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
          if ($formSortie ->get('publier')->isClicked()) {
              $etat2 = $em -> getRepository(Etat::class)->find('2');
              $sortie->setEtat($etat2);
            }
            $entityManager->persist($sortie);
            $entityManager->flush();

            return $this->redirectToRoute('home');
        }

        return $this->render('sortie/new.html.twig', [
            'lieux' => $lieux->findAll(),
            'villes' => $villes->findAll(),
            'sortie' => $sortie,
            'formSortie' => $formSortie->createView(),
            'organisateur' => $organisateur,
            'lieu' => $lieu,
            'formLieu' => $formLieu->createView(),

        ]);
    }

    //-----------------------------------------------------------------------

    /**
     * @Route("/afficher{id}", name="sortie_show", methods={"GET"})
     */
    public function show(EntityManagerInterface $em, Sortie $sortie): Response

    {

        $participants = $em->getRepository(Utilisateur::class)
            ->findSortie($sortie);
        return $this->render('sortie/show.html.twig', compact('sortie', 'participants'));
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
        if ($this->isCsrfTokenValid('delete' . $sortie->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($sortie);
            $entityManager->flush();
        }

        return $this->redirectToRoute('sortie_index');
    }

    /**
     * @Route("/ajout{id}", name="ajout_sortie", methods={"GET"})
     */
    public function ajoutMesSorties(EntityManagerInterface $entityManager, Request $request, Sortie $sortie): Response
    {
        /** @var Utilisateur $utilisateur */
        $utilisateur = $this->getUser();
        if (!$this->getUser()->getMesSorties()->contains($sortie)) {
            $utilisateur->addMesSorty($sortie);
            $sortie->addParticipant($utilisateur);
            if ($sortie->getParticipants()->count() === $sortie->getNbInscriptionsMax()) {
                $etat = $entityManager->getRepository(Etat::class)->find('3');
                $sortie->setEtat($etat);
            }
            $this->addFlash("success", "Participant ajouté a la sortie ");
        } else {
            $utilisateur->removeMesSorty($sortie);
            $sortie->removeParticipant($utilisateur);
            if ($sortie->getParticipants()->count() <= $sortie->getNbInscriptionsMax()) {
                $etat = $entityManager->getRepository(Etat::class)->find('2');
                $sortie->setEtat($etat);
            }
            $this->addFlash("danger", "Participant retiré a la sortie ");
        }
        // Sauvegarde la relation
        $entityManager->flush();

        // Redirige l'utilisateur sur les annonces
        return $this->redirectToRoute('home');

    }

    /**
     * @Route("/annuler{id}", name="annuler_sortie", methods={"GET","POST"})
     */
    public function annulerSortie(EntityManagerInterface $entityManager, Request $request, Sortie $sortie): Response
    {

        $formannul = $this->createForm(AnnulerSortieType::class, $sortie);
        $formannul->handleRequest($request);

        if ($formannul->isSubmitted() && $formannul->isValid()) {
            if ($sortie->getOrganisateur() === $this->getUser()) {

                //ajout du message flash informant de l'annulation de la sortie
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->flush();


                return $this->render('sortie/mail.html.twig', [
                    'sortie' => $sortie,]);
            }
        }
        return $this->render('sortie/annuler.html.twig',
            [
                'sortie' => $sortie,
                'form' => $formannul->createView(),]);

    }



    /**
     * @Route("/mail{id}", name="mail_sortie", methods={"GET","POST"})
     */
    public function validmail(EntityManagerInterface $entityManager, Request $request, Sortie $sortie)
    {

        //changement de l'état de la sortie à 'annulée'
        $etat = $entityManager->getRepository(Etat::class)->find('6');
        $sortie->setEtat($etat);
        // envoi du mail aux participants
        $this->get('event_dispatcher')->dispatch('annulation_sortie', new GenericEvent($sortie));
        //ajout du message flash informant de l'annulation de la sortie
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();
        $this->addFlash("danger", "Votre sortie a été annulé et un mail informant les participants de l'annulation vient d'etre envoyé. ");
        return $this->redirectToRoute('home');
    }
    /**
     * @Route("/cancel_mail{id}", name="cancel_mail", methods={"GET","POST"})
     */
    public function annulermail(EntityManagerInterface $entityManager, Request $request, Sortie $sortie)
    {
        $sortie->setMotif(null);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();

        return $this->redirectToRoute('home');

    }
    /**
     *  @IsGranted("ROLE_ADMIN")
     * @Route("/archiver", name="archiver", methods={"GET","POST"})
     */
    public function archiver(EntityManagerInterface $entityManager, Request $request): Response
    {
        $sorties = $entityManager->getRepository(Sortie::class)
            ->findSortiearchive();
        return $this->render('sortie/archives.html.twig', compact('sorties', ));
    }

}
