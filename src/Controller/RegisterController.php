<?php

namespace App\Controller;

use App\Controller\Classe\Mailjet;
use App\Entity\User;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegisterController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/inscription", name="register")
     */
    public function index(Request $request, UserPasswordEncoderInterface $encoder): Response
    {
        $notification = null;

        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();

            // Récupération de la liste des emails pour chaque utilisateur
            $search_email = $this->entityManager->getRepository(User::class)->findOneByEmail($user->getEmail());
//            dd($search_email);

            if (!$search_email) {
                // encodePassword (Objet User, Mot de Passe à encoder)
                $password = $encoder->encodePassword($user, $user->getPassword());

                // Injection du mot de passe crypté dans l'objet User
                $user->setPassword($password);

                $this->entityManager->persist($user);
                $this->entityManager->flush();

                $mail = new Mailjet();
                $content = "Bonjour" . $user->getFirstName() . "<br /> Bienvenue sur la boutique dédiée au made in France. <br /> Lorem ipsum dolor sit amet, consectetur adipisicing elit. Architecto, delectus ea fuga fugit illum impedit ipsam itaque nostrum odit, optio pariatur possimus quibusdam quod reiciendis rerum ut velit vero voluptas?";
                $mail->send($user->getEmail(), $user->getFirstName(), 'Bienvenue sur Ma Boutique', $content);

                $notification = "Votre inscription s'est correctement déroulée. Vous pouvez dès à présent vous connecter à votre compte.";
            } else {
                $notification = "L'email que vous avez renseigné existe déjà.";
            }



        }

        return $this->render('register/index.html.twig', [
            'form' => $form->createView(),
            'notification' => $notification
            ]);
    }
}
