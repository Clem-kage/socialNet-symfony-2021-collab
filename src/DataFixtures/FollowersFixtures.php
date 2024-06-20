<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class FollowersFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        /** @var User[] $users */
        $users = $manager->getRepository(User::class)->findAll();

        foreach ($users as $user) {

            $followers = rand(1, 10);
            for ($i = 0; $i < $followers; $i++) {
                $follower = $users[array_rand($users)];

                $user->addFollower($follower);
            }

        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [UserFixtures::class];
    }
}
