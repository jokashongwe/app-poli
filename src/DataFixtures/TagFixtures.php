<?php

namespace App\DataFixtures;

use App\Entity\Province;
use App\Entity\Tag;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TagFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        //$tag = ['code' => 'GENERAL', 'name' => 'Général'];
        $tag = new Tag();
        $tag->setCode('GENERAL');
        $tag->setName('Général');
        $manager->persist($tag);
        $manager->flush();
    }
}
