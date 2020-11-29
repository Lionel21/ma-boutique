<?php

namespace App\Classe;

use App\Entity\Category;

/**
 * Class Search
 * @package App\Classe
 * Propriétés publiques
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