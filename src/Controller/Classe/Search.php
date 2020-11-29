<?php

namespace App\Controller\Classe;

use App\Entity\Category;

/**
 * Class Search
 * @package App\Classe
 * Propriétés publiques
 * Objet qui représente la recherche effectuée par les utilisateurs
 */
class Search
{
    /**
     * @var string
     * input => string
     */
    public $string = '';

    /**
     * @var Category[]
     * Recherche par catégorie => tableau
     */
    public $categories = [];
}