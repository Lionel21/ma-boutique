<?php

namespace App\Controller;

use App\Controller\Classe\Mailjet;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderCancelController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/commande/erreur/{stripeSessionId}", name="order_cancel")
     */
    public function index($stripeSessionId): Response
    {
        $order = $this->entityManager->getRepository(Order::class)->findOneByStripeSessionId($stripeSessionId);

        if (!$order || $order->getUser() != $this->getUser()) {
            return $this->redirectToRoute('home');
        }

        // TODO : envoyer un email à notre utilisateur pour lui indiquer l'échec de paiement
        $mail = new Mailjet();
        $content = "Bonjour " . $order->getUser()->getFirstName() . "<br /> Votre paiement a échoué <br /> <br /> Lorem ipsum dolor sit amet, consectetur adipisicing elit. Aliquid dolores ea eaque illum incidunt maiores neque nihil porro provident reprehenderit! Cumque ipsa iure quidem vel? Iure placeat saepe soluta tempora?";
        $mail->send($order->getUser()->getEmail(), $order->getUser()->getFirstName(), 'Un problème est survenu lors de votre paiement.', $content);


        return $this->render('order_cancel/index.html.twig', [
            'order' => $order
        ]);
    }
}
