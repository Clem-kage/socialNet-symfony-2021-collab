<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    public function test_member_page_of_an_user() {

        $client = static::createClient();
        $userRepository = static::$container-> get(UserRepository::class);

        $testUser = $userRepository->findOneByUsername('demo');

        $client->loginUser($testUser);
        $client->request('GET', '/members/demo');

        $this->assertSame(200, $client->getResponse()->getStatusCode());
    }

    public function test_member_page_of_unknown_person() {

        $client = static::createClient();
        $userRepository = static::$container-> get(UserRepository::class);

        $testUser = $userRepository->findOneByUsername('demo');

        $client->loginUser($testUser);
        $client->request('GET', '/members/jeanmicheldu86');

        $this->assertSame(404, $client->getResponse()->getStatusCode());
    }
}
