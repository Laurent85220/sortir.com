<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Entity\Utilisateur;
use App\Form\RegistrationFormType;
use App\Form\UtilisateurType;
use App\Repository\UtilisateurRepository;
use App\Security\Authenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * @Route("/utilisateur")
 */
class UtilisateurController extends Controller
{
    /**
     * @Route("/", name="utilisateur_index", methods={"GET"})
     */
    public function index(UtilisateurRepository $utilisateurRepository): Response
    {
        return $this->render('utilisateur/index.html.twig', [
            'utilisateurs' => $utilisateurRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="utilisateur_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $utilisateur = new Utilisateur();
        $form = $this->createForm(UtilisateurType::class, $utilisateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($utilisateur);
            $entityManager->flush();

            return $this->redirectToRoute('home');
        }

        return $this->render('utilisateur/new.html.twig', [
            'utilisateur' => $utilisateur,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/edition", name="utilisateur_edition", methods={"GET","POST"})
     */
    public function edition(Request $request, UserPasswordEncoderInterface $passwordEncoder,GuardAuthenticatorHandler $guardHandler, AuthenticationUtils $authenticationUtils): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(RegistrationFormType::class, $user);

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $passwordEncoder = $this->get('security.password_encoder');

            if ($passwordEncoder->isPasswordValid($user, $user->getOldPassword())) {
                $newEncodedPassword = $passwordEncoder->encodePassword($user, $form->get('plainPassword')->getData());
                $user->setPassword($newEncodedPassword);
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash("success","Votre compte a bien été modifié ");
            return $this->redirectToRoute('utilisateur_edition', [
                'id' => $user->getId(),
            ]);
        }
        } else {
            $form->addError(new FormError('Ancien mot de passe incorrect'));
        }

        return $this->render('utilisateur/edition.html.twig', [
            'registrationForm' => $form->createView(),'error' => $error,'last_username' => $lastUsername,
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="utilisateur_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Utilisateur $utilisateur): Response
    {
        $form = $this->createForm(UtilisateurType::class, $utilisateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('utilisateur_index', [
                'id' => $utilisateur->getId(),
            ]);
        }

        return $this->render('utilisateur/edit.html.twig', [
            'utilisateur' => $utilisateur,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="utilisateur_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Utilisateur $utilisateur): Response
    {
        if ($this->isCsrfTokenValid('delete'.$utilisateur->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($utilisateur);
            $entityManager->flush();
        }

        return $this->redirectToRoute('utilisateur_index');
    }

    /**
     * @Route("/(id)", name="utilisateur_ajout_sortie", methods={"GET","POST"})
     */
    public function ajoutMesSorties(EntityManagerInterface $entityManager, Request $request,Sortie $sortie):Response
    {
        /** @var Utilisateur $utilisateur */
        $utilisateur = $this->getUser();
        if ($utilisateur->getMesSorties()->contains($sortie)) {

            $utilisateur->removeMesSorty($sortie);
        }else{
            $utilisateur->addMesSorty($sortie);
        }
        $entityManager = $this->getDoctrine()->getManager();

        // Sauvegarde la relation
        $entityManager->flush();

        // Redirige l'utilisateur sur les annonces
        return $this->redirectToRoute('home');

    }
}
