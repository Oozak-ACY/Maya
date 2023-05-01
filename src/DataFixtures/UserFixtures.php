<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager): void
    {
        // créer 10 utilisateurs
        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setNom("Dupont" . $i);
            $user->setPrenom("Jean" . $i);
            $user->setTelephone("061234567" . $i);
            $user->setEmail(sprintf('userdemo%d@exemple.com', $i));
            $user->setPassword($this->passwordEncoder->encodePassword(
                $user,
                'userdemo'
            ));
            if ($i == 0) {
                $user->setRoles(array("ROLE_USER", "ROLE_ADMIN"));
            }

            $manager->persist($user);
        }

        $manager->flush();
    }
}
