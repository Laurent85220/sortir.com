<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Entity\Utilisateur;
use App\Form\RegistrationFormType;
use App\Form\UtilisateurType;
use App\Repository\SortieRepository;
use App\Repository\UtilisateurRepository;
use App\Security\Authenticator;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
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
        // Save old path
        $oldPath = $user->getFile();

        // Remove Path
        $user->setFile(null);
        $form = $this->createForm(RegistrationFormType::class, $user);

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $file */
            $file = $user->getFile();

            if (!\is_null($file)) {

                // Génération d'un nom unique
                $fileName = md5(uniqid()) . '.' . $file->guessExtension();

                // Déplacement du fichier dans le répertoire demandé
                $file->move($this->getParameter('upload_directory'), $fileName);

                // Modification du champs "File"
                $user->setFile($fileName);
            } else {
                $user->setFile($oldPath);
            }

            $this->getDoctrine()->getManager()->flush();
            $this->addFlash("success","Votre compte a bien été modifié ");
            return $this->redirectToRoute('utilisateur_edition', [
                'id' => $user->getId(),
                'utilisateur'=> $user,
            ]);
        }



        return $this->render('utilisateur/edition.html.twig', [
            'registrationForm' => $form->createView(),'error' => $error,'last_username' => $lastUsername,
            'user' => $user,
        ]);
    }

    /**
     * @Route("/{id}/profil", name="utilisateur_profil", methods={"GET","POST"})
     */
    public function profil(Request $request, Utilisateur $utilisateur): Response
    {
        return $this->render('utilisateur/profil.html.twig', [
            'utilisateur' => $utilisateur,
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
     * @Route("/delete{id}", name="utilisateur_delete", methods={"DELETE"})
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
     *  @IsGranted("ROLE_ADMIN")
     * @Route("/liste", name="utilisateur_liste", methods={"GET"})
     */
    public function lister(UtilisateurRepository $utilisateurRepository): Response
    {
        return $this->render('utilisateur/liste.html.twig', [
            'utilisateurs' => $utilisateurRepository->findAll(),
        ]);
    }
    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/supprimer/{id}", name="supprimer", requirements={"id": "\d+"}))
     */
    public function remove(Utilisateur $utilisateur,EntityManagerInterface $entityManager,TokenStorageInterface $token)
    {
        $entityManager->remove($utilisateur);
        $entityManager->flush();
        $this->addFlash("danger","Ce compte a bien été supprimé ");


        return $this->redirectToRoute('utilisateur_liste');

    }
    /**
     *  @IsGranted("ROLE_ADMIN")
     * @Route("/inscrire_manuellement", name="utilisateur_manuel", methods={"GET"})
     */
    public function manuellement(UtilisateurRepository $utilisateurRepository,SortieRepository $sortieRepository): Response
    {
        return $this->render('utilisateur/inscrire_manuellement.html.twig', [
            'utilisateurs' => $utilisateurRepository->findAll(),
            'sorties' => $sortieRepository->findAll()
        ]);
    }


}
