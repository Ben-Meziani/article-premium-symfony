<?php

namespace App\DataFixtures;

use App\Entity\Article;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;
use Faker;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('fr_FR');
        // $product = new Product();
        // $manager->persist($product);
        for($i = 0; $i < 10; $i++){
            $user = new User;
            $user
                ->setLastname($faker->firstName)
                ->setFirstname($faker->name)
                ->setEmail($faker->firstName.$i . '@gmail.com')
                ->setRoles(["ROLE_USER"])
                ->setPassword($this->passwordHasher->hashPassword($user, $i .'password'));
                $manager->persist($user);
        }
        for($i = 0; $i < 100; $i++){
            $article = new Article;
            $article
                ->setTitle($faker->firstName)
                ->setDescription($faker->text)
                ->setPremium(rand(0, 1));
                $manager->persist($article);
        }

        $manager->flush();
    }
}
