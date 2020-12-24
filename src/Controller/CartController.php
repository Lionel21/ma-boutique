<?php

namespace App\Controller;

use App\Controller\Classe\Cart;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    /**
     * @Route("/mon-panier", name="cart")
     */
    public function index(Cart $cart): Response
    {
        // Récupération du panier
        dd($cart->get());
        return $this->render('cart/index.html.twig');
    }

    /**
     * @Route("/cart/add/{id}", name="add_to_cart")
     * Méthode pour ajouter un produit dans mon panier et redirection vers le récapitulatif du panier
     */
    public function add(Cart $cart, $id)
    {
        $cart->add($id);

        // Affichage du panier (récapitulatif)
        return $this->redirectToRoute('cart');
    }

    /**
     * @Route("/cart/remove", name="remove_my_cart")
     * Méthode pour supprimer l'ensemble du panier
     */
    public function remove(Cart $cart)
    {
        $cart->remove();

        // Redirection vers les produits
        return $this->redirectToRoute('products');
    }
}
