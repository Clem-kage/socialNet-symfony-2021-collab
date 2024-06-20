<?php

namespace App\Tests\Entity;

use App\Entity\Post;
use PHPUnit\Framework\TestCase;

class PostTest extends TestCase
{
    public function testSomething(): void
    {
        $this->assertTrue(true);
    }

    public function test_content_ucfirst(): void
    {
        $post = new Post();

        $content= 'salut';

        $post->setContent($content);

        $this->assertEquals('Salut',$post->getContent());
    }

    public function test_content_trim(): void
    {
        $post = new Post();

        $content= '    salut';

        $post->setContent($content);

        $this->assertEquals('Salut',$post->getContent());
    }

    // La gestion de l'envoi d'un message vide est effectuée directement par l'entité post
}