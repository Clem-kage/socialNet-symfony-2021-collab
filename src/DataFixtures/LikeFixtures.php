<?php

namespace App\DataFixtures;

use App\Entity\Post;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class LikeFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {


        /** @var Post[] $posts */
        $posts = $manager->getRepository(Post::class)->findAll();

        /** @var User[] $users */
        $users = $manager->getRepository(User::class)->findAll();

        foreach ($posts as $post) {
            $nbLikers = rand(0, 10);
            for ($i = 0; $i < $nbLikers; $i++) {
                $user = $users[array_rand($users)];

                // On créé l'association entre nos entités
                $post->addLiker($user); // Le post est liké par le user !
                // ou
                // $user->addLikedPost($post);

            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            PostFixtures::class,
        ];
    }
}
