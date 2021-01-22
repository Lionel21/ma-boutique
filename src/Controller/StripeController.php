<?php

namespace App\Controller;

use App\Controller\Classe\Cart;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StripeController extends AbstractController
{
    /**
     * @Route("/commande/create-session", name="stripe_create_session")
     */
    public function index(Cart $cart): Response
    {
        $products_for_stripe = [];
        $your_domain = 'http://127.0.0.1:8000';

        foreach ($cart->getFullCart() as $product) {
            // J'insère les données relatives aux produits sélectionnés que j'envoie à Stripe
            $products_for_stripe[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => $product['product']->getPrice(),
                    'product_data' => [
                        'name' => $product['product']->getName(),
                        'images' => [$your_domain."/uploads/images/products/".$product['product']->getIllustration()],
                    ],
                ],
                'quantity' => $product['quantity'],
            ];
        }

        Stripe::setApiKey('sk_test_51IC9puBDBKaV4yhuUMRahk3QefTPFFRRNfMIMsJ2rt1t13LBuWqRCT83hcaKsoZ8TPMiGq34HKJC0FfEYAduWnpO000zn3xCw0');

        $checkout_session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [
                // On transmet à Stripe la lsite des produits
                $products_for_stripe,
            ],
            'mode' => 'payment',
            'success_url' => $your_domain . '/success.html',
            'cancel_url' => $your_domain . '/cancel.html',
        ]);

        $reponse = new JsonResponse(['id' => $checkout_session->id]);
        return $reponse;
    }
}
