<?php

namespace App\Controller;

use App\Controller\Classe\Cart;
use App\Entity\Order;
use App\Entity\OrderDetails;
use App\Form\OrderType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/commande", name="order")
     */
    public function index(Cart $cart): Response
    {
        // Vérification si l'utilisateur possède une adresse
        if (!$this->getUser()->getAddresses()->getValues()) {
            return $this->redirectToRoute('account_address_add');
        }

        // null => pas d'instance de classe liée à notre formulaire
        $form = $this->createForm(OrderType::class, null, [
            // Permet de récupérer l'utilisateur connecté (en cours)
            'user' => $this->getUser()
        ]);

        return $this->render('order/index.html.twig', [
            'form' => $form->createView(),
            // getFullCart() => méthode récupérant l'objet du produit concerné
            'cart' => $cart->getFullCart()
        ]);
    }

    /**
     * @Route("/commande/recapitulatif", name="order_resume", methods={"POST"})
     * Méthode pour créer la commande en base de données
     */
    public function add(Cart $cart, Request $request): Response
    {
        $form = $this->createForm(OrderType::class, null, [
            'user' => $this->getUser()
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $date = new \DateTime();
            // Notre form possède les données carriers -> on le récupère
            $carriers = $form->get('carriers')->getData();
            $delivery = $form->get('addresses')->getData();
            // Construction chaîne de caractères pour les données 'addresses'
            $delivery_content = $delivery->getFirstName() . ' ' . $delivery->getLastName();
            $delivery_content .= '<br />' . $delivery->getPhone();

            if ($delivery->getCompany()) {
                $delivery_content .= '<br />' . $delivery->getCompany(); // Rappel => nom de l'entreprise = optionnel
            }

            $delivery_content .= '<br />' . $delivery->getAddress();
            $delivery_content .= '<br />' . $delivery->getPostcard() . ' ' . $delivery->getCity();
            $delivery_content .= '<br />' . $delivery->getCountry();

            // Enregistrement de la commande => entité Order()
            $order = new Order();
            $order->setUser($this->getUser());
            $order->setCreateAt($date); // Stockage de la date actuelle
            $order->setCarrierName($carriers->getName());
            $order->setCarrierPrice($carriers->getPrice());
            $order->setDelivery($delivery_content);
            $order->setIsPaid(0);

            $this->entityManager->persist($order);

            // Enregistrement des produits => entité OrderDetails()
            foreach ($cart->getFullCart() as $product) {
                $orderDetails = new OrderDetails();
                $orderDetails->setMyOrder($order); // setMyOrder => ManyToOne => prend en paramètre notre commande
                $orderDetails->setProduct($product['product']->getName()); // On récupère le nom du produit (tableau)
                $orderDetails->setQuantity($product['quantity']); // 'quantity' se trouve dans notre entrée quantity
                $orderDetails->setPrice($product['product']->getPrice());
                $orderDetails->setTotal($product['product']->getPrice() * $product['quantity']);
                $this->entityManager->persist($orderDetails);
            }

//            $this->entityManager->flush();

            return $this->render('order/add.html.twig', [
                'cart' => $cart->getFullCart(),
                'carrier' => $carriers,
                'delivery' => $delivery_content
            ]);
        }

        return $this->redirectToRoute('cart');


    }

}
