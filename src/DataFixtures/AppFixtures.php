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

        $provinces = [
            'KINSHASA', 
            'Kongo Central', 
            'Mai-Ndombe', 
            'Haut-Katanga',
            'Kwilu',
            'Kwango',
            'Maniema',
            'Lualaba',
            'Lomami',
            'Kasai',
            'Kasai Central',
            'Kasai Oriental',
            'Tshopo',
            'Tshuapa',
            'Bas-Uele',
            'Haut-Uele',
            'Nord-Ubangi',
            'Sud-Ubangi',
            'Mongala',
            'Sankuru',
            'Nord-Kivu',
            'Sud-Kivu',
            'Tanganyika'
        ];
        foreach($provinces as $province){
            $pObject = new Province();
            $pObject->setNom(strtoupper($province));
            $manager->persist($pObject);
        }

        $manager->flush();
    }
}
