<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker;

class ProductFixtures extends Fixture implements DependentFixtureInterface
{

    const PRODUCT_IMAGES = [
        "https://cdn.pixabay.com/photo/2013/08/11/19/46/coffee-171653_1280.jpg",
        "https://cdn.pixabay.com/photo/2017/09/04/18/39/coffee-2714970_1280.jpg",
        "https://cdn.pixabay.com/photo/2017/01/12/14/22/coffee-1974841_1280.jpg",
        "https://cdn.pixabay.com/photo/2015/03/26/09/40/coffee-690054_1280.jpg"
    ];

    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');

         for ($i = 0; $i <= 40; $i++) {
             $product = new Product();
             $product->setName($slug = $faker->word)
                 ->setSlug($slug)
                 ->setIllustration(self::PRODUCT_IMAGES[rand(0, count(self::PRODUCT_IMAGES)-1)])
                 ->setSubtitle($faker->text(25))
                 ->setDescription($faker->paragraph)
                 ->setPrice($faker->randomNumber(4))
                 ->setCategory($this->getReference('category_' . rand(0, 4)));
             $manager->persist($product);
         }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [CategoryFixtures::class];
    }
}
