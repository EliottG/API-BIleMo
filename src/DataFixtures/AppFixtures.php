<?php

namespace App\DataFixtures;

use App\Entity\Client;
use App\Entity\Mobile;
use App\Entity\User;
use App\Repository\ClientRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $passwordEncoderInterface;
    public function __construct(UserPasswordEncoderInterface $passwordEncoderInterface)
    {
        $this->passwordEncoderInterface = $passwordEncoderInterface;
    }
    public function load(ObjectManager $manager)
    {
        for ($i = 1; $i <= 20; $i++) {
            $mobile = Mobile::create(
                'Mobile ' . $i,
                'Wata gud description',
                random_int(90, 450)
            );

            $manager->persist($mobile);
        }
        $manager->flush();
        for ($i = 1; $i <= 5; $i++) {
            $client = Client::create(
                'Client' . $i,
                'email' . $i . '@mail.com'
            );
            for ($j = random_int(1, 3); $j <= 3; $j++) {
                $user = User::create(
                    "username" . $j,
                    "user.email" . $j . "@mail.com"
                );
                $manager->persist($user);
                $client->addUser($user);
            }
            $client->setPassword($this->passwordEncoderInterface->encodePassword($client, 'password'));
            $manager->persist($client);
        }
        $manager->flush();
    }
}
