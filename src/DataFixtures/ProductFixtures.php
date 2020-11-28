<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker;

class ProductFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');

         for ($i = 0; $i <= 40; $i++) {
             $product = new Product();
             $product->setName($slug = $faker->word)
                 ->setSlug($slug)
                 ->setIllustration($faker->imageUrl($width = 640, $height = 480))
                 ->setSubtitle($faker->text(25))
                 ->setDescription($faker->paragraph)
                 ->setPrice($faker->randomNumber(2))
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
