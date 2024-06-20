<?php

namespace App\Tests\Controller;

use App\Entity\Post;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PageControllerTest extends WebTestCase
{
    public function testSomething(): void
    {
        $client = static::createClient();
        $crawler= $client->request('GET', '/about-us');

        //$this->assertResponseIsSuccessful();
       // $this->assertSelectorTextContains('h1', "About Us");
        $h1 =$crawler->filter('h1');
        //dump($h1); exit;
        $this->assertCount(1, $h1);
    }

    public function test_homepage_is_up() {
       // Création d'un client
       $client = static::createClient();
       $client->request('GET', '/');

       $this->assertSame(200, $client->getResponse()->getStatusCode());
    }

    public function test_homepage() {
        $client = static::createClient();
        $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', "Salut visiteur anonyme !");
    }

    public function test_connection() {
        $client = static::createClient();
        $userRepository = static::$container-> get(UserRepository::class);

        $testUser = $userRepository->findOneByUsername('demo');
        //$testUser = $userRepository->findOneByPassword('$argon2id$v=19$m=65536,t=4,p=1$FsSFzx1937L1CosReaj4SA$QtYsoNF94TJpaBOWxNZw1TFapGpJGuxBPHg8gnCLasU');

        $client->loginUser($testUser);
        $client->request('GET', '/');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', "Salut Demo User !");

        //$this->assertSelectorTextContains('h1', "Salut visiteur anonyme !");
    }

    public function test_connection_to_private_page() {
        $client = static::createClient();
        $userRepository = static::$container-> get(UserRepository::class);

        // On indique ici que l'on se connecte avec un user valide (si non valide echec du test)
        $testUser = $userRepository->findOneByUsername('demo');

        //Possible aussi de se connecter via le password hashé (ne fonctionne pas avec le mdp 'azeaze')
        //$testUser = $userRepository->findOneByPassword('$argon2id$v=19$m=65536,t=4,p=1$FsSFzx1937L1CosReaj4SA$QtYsoNF94TJpaBOWxNZw1TFapGpJGuxBPHg8gnCLasU');

        $client->loginUser($testUser);

        // Connection sur une page nécéssitant d'être connecté
        $client->request('GET', '/posts/new');
        $this->assertResponseIsSuccessful();
    }


    /**
     * @uses only
     */
    public function test_redirection_on_private_page() {

        $client = static::createClient();

        // On essaye d'accéder à une page privée, sans etre authentifié
        $crawler = $client->request('GET', '/members');

        // On vérfie qu'on est bien redirigé sur la page login.
        $this->assertResponseRedirects('/login');

        // On suit la redirection => on est emmené sur la page login.
        $client->followRedirect();

        // On rempli et on soumet le formulaire ...
        $client->submitForm('Sign in', [
            'username' => 'demo',
            'password' => 'azeaze'
        ]);

        // On vérifie qu'on est bien renvoyé la ou on voulait aller initialement.
        $this->assertSame('http://localhost/members', $client->getResponse()->headers->get('Location'));

    }


/*
    public function test_search() {
        $client = static::createClient();
        $userRepository = static::$container-> get(UserRepository::class);

        $testUser = $userRepository->findOneByUsername('demo');

        $client->loginUser($testUser);
        $client->request('GET', '/search');


        $form['getName(name)'] = 'demo';

        $client->submit($form);


        $this->assertSame('demo', $client->);
        $this->assertResponseIsSuccessful();

    }
*/

}