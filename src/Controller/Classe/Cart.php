<?php

namespace App\Controller\Classe;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Cart
{
    private $session;

    /**
     * Cart constructor.
     * @param SessionInterface $session
     * Utilisation de la méthode SessionInterface pour gérer nos sessions
     */
    public function __construct(SessionInterface $session)
    {
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

}