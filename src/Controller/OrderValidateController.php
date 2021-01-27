<?php

namespace App\Controller;

use App\Controller\Classe\Cart;
use App\Controller\Classe\Mailjet;
use App\Entity\Order;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderValidateController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/commande/merci/{stripeSessionId}", name="order_validate")
     */
    public function index(Cart $cart, $stripeSessionId): Response
    {
        // On récupère notre commande
        $order = $this->entityManager->getRepository(Order::class)->findOneByStripeSessionId($stripeSessionId);

        // TODO : vérification pour savoir si une commande existe et si l'utilisateur correspond à l'utilisateur actuel
        if (!$order || $order->getUser() != $this->getUser()) {
            return $this->redirectToRoute('home');
        }

        // TODO : modifier le status isPaid de notre commande en mettant le statut à 1

        // Modification si le statut est à zéro
        if ($order->getState() == 0) {
            // TODO : vider la session cart (panier de l'utilisateur)
            $cart->remove();

            // TODO : modification du statut de notre commande : paiement accepté
            $order->setState(1);
            // TODO : mise à jour côté Doctrine
            $this->entityManager->flush();

            // TODO : envoyer un email à notre client pour lui confirmer sa commande
            $mail = new Mailjet();
            $content = "Bonjour " . $order->getUser()->getFirstName() . "<br /> Merci pour votre commande <br /> <br /> Lorem ipsum dolor sit amet, consectetur adipisicing elit. Aliquid dolores ea eaque illum incidunt maiores neque nihil porro provident reprehenderit! Cumque ipsa iure quidem vel? Iure placeat saepe soluta tempora?";
            $mail->send($order->getUser()->getEmail(), $order->getUser()->getFirstName(), 'Votre commande Ma Boutique a bien été validée.', $content);
        }

        return $this->render('order_validate/index.html.twig', [
            // TODO : afficher les quelques informations de la commande de l'utilisateur
            'order' => $order
        ]);
    }
}
