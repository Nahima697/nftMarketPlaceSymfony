<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Nft;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Constraints\DateTime;

class AppFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $admin = new User();
        $admin->setUsername('admin');
        $admin->setFirstName('nahima');
        $admin->setLastName('toumi');
        $admin->setEmail('nahima@gmail.com');
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin'));
        $admin->setRoles(['ROLE_ADMIN']);
        $manager->persist($admin);

        $user = new User();
        $user->setUsername('user');
        $user->setFirstName('swan');
        $user->setLastName('daphne');
        $user->setEmail('swan@gmail.com');
        $user->setPassword($this->passwordHasher->hashPassword($user, 'user'));
        $user->setRoles(['ROLE_USER']);
        $manager->persist($user);

        $category = new Category();
        $category->setWording('Category');
        $manager->persist($category);

        for ($i =1; $i< 50; $i++) {
            $nft = new Nft();
            $nft->setName('Nft' . $i);
            $nft->setImage("uploads/image (".$i.").png");
            $nft->setPrice(mt_rand(1200,12700)/100);
            $nft->setQuantity(1);
            $nft->setCategory($category);
            $nft->setDropDate(new \DateTime('2022131'));

            $manager->persist($nft);
        }
        $manager->flush();
    }
}

