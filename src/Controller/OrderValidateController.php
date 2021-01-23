<?php

namespace App\Controller;

use App\Controller\Classe\Cart;
use App\Entity\Order;
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

        // Modification si le status est à zéro
        if (!$order->getIsPaid()) {
            // TODO : vider la session cart (panier de l'utilisateur)
            $cart->remove();

            // TODO : modification du statut isPaid() à notre commande
            $order->setIsPaid(1);
            // TODO : mise à jour côté Doctrine
            $this->entityManager->flush();

            // TODO : envoyer un email à notre client pour lui confirmer sa commande
        }

        return $this->render('order_validate/index.html.twig', [
            // TODO : afficher les quelques informations de la commande de l'utilisateur
            'order' => $order
        ]);
    }
}
