<?php

namespace App\DataFixtures;

use App\Entity\Province;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        $provinces = ['KINSHASA', 'Kongo-Central', 'Mai-Ndombe'];
        foreach($provinces as $province){
            $pObject = new Province();
            $pObject->setNom($province);
            $manager->persist($pObject);
        }

        $manager->flush();
    }
}
