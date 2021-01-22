<?php

namespace App\Controller;

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
    public function index(): Response
    {
        // On récupère notre commande (REVOIR LA METHODE DE FIND)
        $order = $this->entityManager->getRepository(Order::class)->findOneBy([]);

        // Vérification pour savoir si une commande existe et si l'utilisateur correspond à l'utilisateur actuel
        if (!$order || $order->getUser() != $this->getUser()) {
            return $this->redirectToRoute('home');
        }

        // TODO : modifier le status isPaid de notre commande en mettant le statut à 1

        // Modification si le status est à zéro
        if (!$order->getIsPaid()) {
            // Modification du statut isPaid() à notre commande
            $order->setIsPaid(1);
            // Mise à jour côté Doctrine
            $this->entityManager->flush();

            // TODO 2 : envoyer un email à notre client pour lui confirmer sa commande
        }

        return $this->render('order_validate/index.html.twig', [
            // TODO 3 : afficher les quelques informations de la commande de l'utilisateur
            'order' => $order
        ]);
    }
}
