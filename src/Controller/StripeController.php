<?php

namespace App\Controller;

use App\Controller\Classe\Cart;
use App\Entity\Order;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StripeController extends AbstractController
{
    /**
     * @Route("/commande/create-session/{reference}", name="stripe_create_session")
     */
    public function index(EntityManagerInterface $entityManager, Cart $cart, $reference): Response
    {
        $products_for_stripe = [];
        $your_domain = 'http://127.0.0.1:8000';

        $order = $entityManager->getRepository(Order::class)->findOneByReference($reference);

        if (!$order) {
            // Renvoi d'une erreur concernant l'order si aucune référence n'est trouvée
            new JsonResponse(['error' => 'order']);
        }

        foreach ($order->getOrderDetails()->getValues() as $product) {
            $product_object = $entityManager->getRepository(Product::class)->findOneByName($product->getProduct());
            // J'insère les données relatives aux produits sélectionnés que j'envoie à Stripe
            $products_for_stripe[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => $product->getPrice(),
                    'product_data' => [
                        'name' => $product->getProduct(),
                        'images' => [$your_domain."/uploads/images/products/".$product_object->getIllustration()],
                    ],
                ],
                'quantity' => $product->getQuantity(),
            ];
        }

        $products_for_stripe[] = [
            'price_data' => [
                'currency' => 'eur',
                'unit_amount' => $order->getCarrierPrice(),
                'product_data' => [
                    'name' => $order->getCarrierName(),
                    'images' => [$your_domain]
                ],
            ],
            // Un seul transporteur
            'quantity' => 1
        ];

        Stripe::setApiKey('sk_test_51IC9puBDBKaV4yhuUMRahk3QefTPFFRRNfMIMsJ2rt1t13LBuWqRCT83hcaKsoZ8TPMiGq34HKJC0FfEYAduWnpO000zn3xCw0');

        $checkout_session = Session::create([
            'customer_email' => $this->getUser()->getEmail(),
            'payment_method_types' => ['card'],
            'line_items' => [
                // On transmet à Stripe la lsite des produits
                $products_for_stripe,
            ],
            'mode' => 'payment',
            // Récupération de l'id de la session lors d'une commande
            'success_url' => $your_domain . '/commande/merci/{CHECKOUT_SESSION_ID}',
            'cancel_url' => $your_domain . '/commande/erreur/{CHECKOUT_SESSION_ID}',
        ]);

        $order->setStripeSessionId($checkout_session->id);
        $entityManager->flush();

        $reponse = new JsonResponse(['id' => $checkout_session->id]);
        return $reponse;
    }
}
