<?php

namespace App\Controller;

use App\Controller\Classe\Mailjet;
use App\Entity\ResetPassword;
use App\Entity\User;
use App\Form\ResetPasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ResetPasswordController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/mot-de-passe-oublie", name="reset_password")
     */
    public function index(Request $request): Response
    {
        // Si l'utilisateur est connecté => redirection vers la page d'accueil
        if ($this->getUser()) {
            return $this->redirectToRoute('home');
        }

        // Je récupère le name de l'input "inputEmail"
        if ($request->get('email')) {
            // Vérification si l'utilisateur existe
            $user = $this->entityManager->getRepository(User::class)->findOneByEmail($request->get('email'));

            // Si l'utilisateur existe =>
            if ($user) {
                // TODO : enregistrement en base de données la demande de reset_password avec user, token et createdAt
                $reset_password = new ResetPassword();
                $reset_password->setUser($user);
                $reset_password->setToken(uniqid());
                $reset_password->setCreatedAt(new \DateTime());
                $this->entityManager->persist($reset_password);
                $this->entityManager->flush();

                // TODO : envoyer un email à l'utilisateur avec un lien lui permettant de mettre à jour son mot de passe

                // On génère un URL pour la réinitialisation du mot de passe grâce à la méthode generateUrl
                // 2 params : nom de la route, le token (tableau)
                $url = $this->generateUrl('update_password',  [
                    'token' => $reset_password->getToken()
                ]);

                $content = "Bonjour " . $user->getFirstname() . ' ' . $user->getLastname() . "<br /> Vous avez demandé de réinitialiser votre mot de passe sur le site Ma Boutique <br /> <br />";
                $content .= "Merci de bien vouloir cliquer sur le lien suivant pour <a href='".$url."'> mettre à jour votre mot de passe.</a>";

                $mail = new Mailjet();
                $mail->send($user->getEmail(), $user->getFirstname() . ' ' . $user->getLastname(), 'Réinitialiser votre mot de passe sur Ma Boutique', $content);
                $this->addFlash('notice', 'Vous allez recevoir un email avec la procédure pour réinitialiser votre mot de passe.');
            } else {
                $this->addFlash('notice', 'Cette adresse email est inconnue.');
            }
        }

        return $this->render('reset_password/index.html.twig');
    }

    /**
     * @Route("/modifier-mon-mot-de-passe/{token}", name="update_password")
     */
    public function update(Request $request, $token, UserPasswordEncoderInterface $encoder)
    {
        // Récupération du token
        $reset_password = $this->entityManager->getRepository(ResetPassword::class)->findOneByToken($token);

        if (!$reset_password) {
            return $this->redirectToRoute('reset_password');
        }

        // TODO : vérifier si le createdAt < 3 heures (délai de 3 heures pour pouvoir réinitialiser le mot de passe)
        $currentTime = new \DateTime();
        if ($currentTime >  $reset_password->getcreatedAt()->modify('+ 3 hours')) {
            // Temps de réinitialisation expiré
            $this->addFlash('notice', 'Votre demande de réinitialisation de mot de passe a expiré. Merci de la renouveler');
            return $this->redirectToRoute('reset_password');
        }

        // TODO : rendre une vue avec mot de passe et confirmation de changement de mot de passe
        $form = $this->createForm(ResetPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $new_password = $form->get('new_password')->getData();

            // TODO : encodage des mots de passe
            $password = $encoder->encodePassword($reset_password->getUser(), $new_password);
            $reset_password->getUser()->setPassword($password);

            // TODO : flush en base de données
            $this->entityManager->flush();

            // TODO : redirection de l'utilisateur vers la page de connexion
            $this->addFlash('notice', 'Votre mot de passe a bien été mise à jour.');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('reset_password/update.html.twig', [
            'form' => $form->createView()
        ]);

    }
}
