<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PostControllerTest extends WebTestCase
{
    public function test_post_page_exist() {

        $client = static::createClient();
        $userRepository = static::$container-> get(UserRepository::class);

        $testUser = $userRepository->findOneByUsername('demo');

        $client->loginUser($testUser);
        $client->request('GET', '/posts/626');

        $this->assertSame(200, $client->getResponse()->getStatusCode());
    }

    public function test_post_page_not_exist() {

        $client = static::createClient();
        $userRepository = static::$container-> get(UserRepository::class);

        $testUser = $userRepository->findOneByUsername('demo');

        $client->loginUser($testUser);
        $client->request('GET', '/posts/6260');

        $this->assertSame(404, $client->getResponse()->getStatusCode());
    }
}
