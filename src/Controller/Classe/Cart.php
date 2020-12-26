<?php

namespace App\Controller\Classe;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Cart
{
    private $session;

    /**
     * Cart constructor.
     * @param SessionInterface $session
     * Utilisation de la méthode SessionInterface pour gérer nos sessions
     */
    public function __construct(EntityManagerInterface $entityManager, SessionInterface $session)
    {
        $this->entityManager = $entityManager;
        $this->session = $session;
    }

    /**
     * @param $id
     * Fonction pour ajouter au panier
     */
    public function add($id)
    {
        // J'ajoute une session que je nomme 'cart' en lui associant un tableau contenant
        // les produits de mon panier

        // Je récupère la session 'cart' et je renvoie un tableau
        $cart = $this->session->get('cart', []);

        // Si on possède dans notre panier un produit déjà inséré, on effectue une indentation
        if (!empty($cart[$id])) {
            $cart[$id]++;
        } else {
            $cart[$id] = 1;
        }

        // Set du panier
        $this->session->set('cart', $cart);
    }

    /**
     * @return mixed
     * Fonction pour récupérer mon panier
     */
    public function get()
    {
        return $this->session->get('cart');
    }

    public function remove()
    {
        return $this->session->remove('cart');
    }

    public function delete($id)
    {
        // On récupère notre cart
        $cart = $this->session->get('cart', []);

        // On retire tu tableau cart l'entrée cart correspondant à l'id que l'on souhaite supprimer
        unset($cart[$id]);

        // Set du nouveau cart après suppression d'un produit
        return $this->session->set('cart', $cart);
    }

    public function decrease($id)
    {
        $cart = $this->session->get('cart', []);
        if ($cart[$id] > 1) {
            // Retirer une quantité
            $cart[$id]--;

        } else {
            // Supprimer mon produit
            unset($cart[$id]);
        }

        // Retour à ma session cart avec le nouveau panier
        return $this->session->set('cart', $cart);
    }

    public function getFullCart()
    {
        $cartComplete = [];

        // Si un panier est retourné
        if ($this->get()) {
            // Itération pour avoir accès à tout mon objet Product
            foreach ($this->get() as $id => $quantity) {
                $product_object = $this->entityManager->getRepository(Product::class)->findOneById($id);

                // Si le produit n'existe pas => le supprimer directement de la session
                if (!$product_object) {
                    $this->delete($id);
                    continue;
                }

                $cartComplete[] = [
                    // Appel de l'objet Product
                    'product' => $product_object,
                    'quantity' => $quantity
                ];
            }
        }

        return $cartComplete;

    }

}