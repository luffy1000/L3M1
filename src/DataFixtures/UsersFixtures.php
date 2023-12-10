<?php

namespace App\DataFixtures;

use App\Entity\Users;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker;

;

class UsersFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $passwordEncoder
        )
    {
        

    }
    public function load(ObjectManager $manager): void
    {
        $admin = new Users();
        $admin->setEmail("admin@gmail.com");
        $admin->setNom("admin");
        $admin->setPrenom("test");
        $admin->setTelephone("90200381");
        $admin->setIsVerified(1);
        $admin->setPassword(
            $this->passwordEncoder->hashPassword($admin,'admin')
        );

        $admin->setRoles(["ROLE_ADMIN"]);

        $manager->persist($admin);

        $faker = Faker\Factory::create('fr_FR');

        for($usr=1;$usr<=5;$usr++){
            $user = new Users();
            $user->setEmail($faker->email);
            $user->setNom($faker->lastname);
            $user->setPrenom($faker->firstname);
            $user->setTelephone($faker->phoneNumber());
            $user->setIsVerified($faker->boolean());
            $user->setPassword(
                $this->passwordEncoder->hashPassword($user,'secret')
            );
    
            $user->setRoles(['ROLE_ETUDIANT']);
    
            $manager->persist($user);
        }


        $manager->flush();
    }
}
