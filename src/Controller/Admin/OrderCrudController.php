<?php

namespace App\Controller\Admin;

use App\Controller\Classe\Mailjet;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\CrudUrlGenerator;

class OrderCrudController extends AbstractCrudController
{
    private $entityManager;

    // Permet de gérer l'url de redirection une fois le traitement effectué dans la méthode updatePreparation()
    private $crudUrlGenerator;

    public function __construct(EntityManagerInterface $entityManager, CrudUrlGenerator $crudUrlGenerator)
    {
        $this->entityManager = $entityManager;
        $this->crudUrlGenerator = $crudUrlGenerator;
    }

    public static function getEntityFqcn(): string
    {
        return Order::class;
    }

    /**
     * @param Actions $actions
     * @return Actions
     * Méthode pour gérer les différentes actions d'une commande
     */
    public function configureActions(Actions $actions): Actions
    {
        // Personnalisation statut des commandes
        // linkToCrudAction() : permet de faire un lien avec une méthode
        $updatePreparation = Action::new('updatePreparation', 'Préparation en cours', 'fas fa-box-open')->linkToCrudAction('updatePreparation');
        $updateDelivery = Action::new('updateDelivery', 'Livraison en cours', 'fas fa-truck')->linkToCrudAction('updateDelivery');

        return $actions
            ->add('detail', $updatePreparation)
            ->add('detail', $updateDelivery)
            // Indication de la route et le nom de l'action
            ->add('index', 'detail');
    }

    /**
     * Méthode pour modifier notre entité Order() avec la méthode AdminContext
     */
    public function updatePreparation(AdminContext $context)
    {
        // Je récupère mon entité
        $order = $context->getEntity()->getInstance();
        $order->setState(2);
        $this->entityManager->flush();

        $this->addFlash('notice', "<span style='color: green;'><strong>La commande ".$order->getReference()." est bien <u>en cours de préparation</u></strong></span>");

        // Redirection de notre utilisateur vers la vue index
        $url = $this->crudUrlGenerator->build()
            // Indication du Controller où nous nous situons
            ->setController(OrderCrudController::class)
            // Action désirée => vue index
            ->setAction('index')
            ->generateUrl();

        return $this->redirect($url);
    }

    public function updateDelivery(AdminContext $context)
    {
        // Je récupère mon entité
        $order = $context->getEntity()->getInstance();
        $order->setState(3);
        $this->entityManager->flush();

        $this->addFlash('notice', "<span style='color: lightblue;'><strong>La commande ".$order->getReference()." est bien <u>en cours de livraison</u></strong></span>");

        // Redirection de notre utilisateur vers la vue index
        $url = $this->crudUrlGenerator->build()
            // Indication du Controller où nous nous situons
            ->setController(OrderCrudController::class)
            // Action désirée => vue index
            ->setAction('index')
            ->generateUrl();

        return $this->redirect($url);
    }

    /**
     * @param Crud $crud
     * @return Crud
     * Tri des id par ordre décroissant
     */
    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setDefaultSort(['id' => 'DESC']);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            DateTimeField::new('createAt', 'Passé le'),
            TextField::new('user.getFullName', 'Utilisateur'),
            TextEditorField::new('delivery', 'Adresse de livraison')->onlyOnDetail(), // TextEditorField : permet d'interpréter le HTML de la propriété delivery
            MoneyField::new('total', 'Total')->setCurrency('EUR'),
            TextField::new('carrierName', 'Transporteur'),
            MoneyField::new('carrierPrice', 'Frais de port')->setCurrency('EUR'),
            ChoiceField::new('state')->setChoices([
                'Non payée' => 0,
                'Payée' => 1,
                'Préparation en cours' => 2,
                'Livraison en cours' => 3
            ]),
            ArrayField::new('orderDetails', 'Produits achetés')->hideOnIndex()
        ];
    }

}
