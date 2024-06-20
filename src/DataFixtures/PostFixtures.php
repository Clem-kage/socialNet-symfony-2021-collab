<?php

namespace App\DataFixtures;

use App\Entity\Post;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class PostFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create("fr_FR");

        // Algorithme 1 :
        // Récupérer les Users existants
        // Pour chaque User, lui créer une dizaine de Posts

        // => Les ids des Posts vont se suivre par User (1-10 pour Alice, de 11 a 20 pour Bob, 21 a 30 pour Charles, ...)
        // => On est sur que chaque User possède une dizaine de Posts
        // => On aura environ 10 * Users posts

        // ------

        // Algorithme 2 :
        // Faire une boucle de 100 tours
        // A chaque Tour
            // choisir un User au hasard
            // et lui associer un nouveau Post

        // => On peut avoir des déséquilibres (un User peut avoir plein de Posts, ou peu, ou aucun)

        $users = $manager->getRepository(User::class)->findAll();

        for ($i = 1; $i <= 100; $i++) {

            // choisir un User au hasard
            $randomIndex = array_rand($users);
            /** @var User $author */
            $author = $users[$randomIndex];
            // TODO: éventuellement, faire sélectionner un User aléatoire a Doctrine
            //  @see https://stackoverflow.com/questions/10762538/how-to-select-randomly-with-doctrine

            $content = $faker->sentences(rand(1, 5), true);
            $content = substr($content, 0, 280); // On bride le contenu a 280 caractères

            // On crée une nouvelle instance de l'entité Post ...
            $post = new Post();
            $post->setAuthor($author);
            $post->setContent($content);
            $post->setCreatedAt($faker->dateTimeThisYear());

            // On informe le manager qu'il y a une nouvelle entité à gérer
            $manager->persist($post);

            // Créer une dizaine de commentaires à ce post
            $nbComments = rand(0, 12);
            for ($j = 0; $j < $nbComments; $j++) {

                // créér un commentaire
                $comment = new Post();
                // on lui associe un user aléatoire, un contenu, et une date
                $comment->setAuthor($users[array_rand($users)]);
                $comment->setContent($faker->text(280));
                $comment->setCreatedAt(
                    $faker->dateTimeBetween($post->getCreatedAt(), 'now')
                );

                $comment->setParent($post);
                // ou bien
                $post->addComment($comment);


                $manager->persist($comment);
            }

        }

        // ---------

        // On ordonne au manager d'enregistrer tous les changements
        // sur les entités (ici ce la fera une seule requête INSERT INTO
        // avec 100 lignes)
        $manager->flush();
    }


    // Ici, on indique au framework qu'il faut d'abord charger les fixtures de User
    // avant de charger les Posts fixtures.
    public function getDependencies(): array
    {
        return [
            UserFixtures::class
        ];
    }
}
