<?php

namespace App\Controller;

use App\Form\ChangePasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AccountPasswordController extends AbstractController
{
    private $entityManager;

    /**
     * AccountPasswordController constructor.
     * @param $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/compte/modifier-mon-mot-de-passe", name="account_password")
     */
    public function index(Request $request, UserPasswordEncoderInterface $encoder): Response
    {
        $notification = null;


        // J'appelle l'objet User qui est connecté (je n'en crée pas un nouveau)
        $user = $this->getUser();
        $form = $this->createForm(ChangePasswordType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Je récupère l'ancien mot de passe et je récupère les données de l'input 'old_password'
            $old_password = $form->get('old_password')->getData();

            // isPasswordValid(objet User, mot de passe non crypté)
            if ($encoder->isPasswordValid($user, $old_password)) {
                $new_password = $form->get('new_password')->getData();

                // J'encode mon nouveau mot de passe
                $password = $encoder->encodePassword($user, $new_password);

                // Je fixe le nouveau mot de passe à mon objet User
                $user->setPassword($password);

                $this->entityManager->persist($user);
                $this->entityManager->flush();
                $notification = 'Votre mot de passe a bien été mis à jour';
            } else {
                $notification = 'Votre mot de passe actuel n\'est pas le bon.';
            }
        }

        return $this->render('account/password.html.twig', [
            'form' => $form->createView(),
            'notification' => $notification
        ]);
    }
}
