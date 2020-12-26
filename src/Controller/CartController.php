<?php

namespace App\Controller;

use App\Controller\Classe\Cart;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/mon-panier", name="cart")
     */
    public function index(Cart $cart): Response
    {
        return $this->render('cart/index.html.twig', [
            'cart' => $cart->getFullCart(),
        ]);
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

    /**
     * @Route("/cart/delete/{id}", name="delete_to_cart")
     * Méthode pour supprimer un produit à la fois
     */
    public function delete(Cart $cart, $id)
    {
        $cart->delete($id);

        // Redirection vers les produits
        return $this->redirectToRoute('cart');
    }

    /**
     * @Route("/cart/decrease/{id}", name="decrease_to_cart")
     * @param $id
     * Méthode pour diminuer la quantité d'un produit
     */
    public function decrease(Cart $cart, $id)
    {
        $cart->decrease($id);

        return $this->redirectToRoute('cart');
    }


}
