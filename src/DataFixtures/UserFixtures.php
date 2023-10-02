<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\User;


class UserFixtures extends Fixture
{

    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher){
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setUsername('admin_243');
        $user->setNom('Administrateur');
        
        $user->setDatecreation(new \DateTimeImmutable());
        $user->setPrenom("-");
        $user->setPostnom("-");
        $user->setRoles(["ROLE_ADMIN", "SUPER-ADMIN", "ROLE_USER", "ROLE_MEMBRE", "ROLE_DIFFUSION"]);

        $password = $this->hasher->hashPassword($user, '05jqjes558');
        $user->setPassword($password);

        $manager->persist($user);
        $manager->flush();
    }
}
