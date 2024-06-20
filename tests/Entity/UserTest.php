<?php

namespace App\Tests\Entity;

use App\Entity\Post;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testSomething(): void
    {
        $this->assertTrue(true);
    }

    public function test_post_only_once_post()
    {
        $post = new Post();
        $author = new User();

        $author->addPost($post);
        $author->addPost($post);
        $author->addPost($post);
        $author->addPost($post);
        $author->addPost($post);

        $this->assertEquals(1, $author->getPosts()->count($post));
    }

    public function test_post_count_sending_number_of_post()
    {
        $post = new Post();
        $otherpost = new Post();
        $author = new User();

        $author->addPost($post);
        $author->addPost($post);
        $author->addPost($post);
        $author->addPost($post);
        $author->addPost($otherpost);

        $this->assertEquals(2, $author->getPosts()->count($post,$otherpost));
    }

    public function test_can_like_one_post()
    {
        $post = new Post();
        $author = new User();
        $userWantToLike = new User();

        $author->addPost($post);
        $post->setAuthor($author);
        //$userWantToLike->addLikedPost($post);
        $userWantToLike->like($post);

        // si je suis l'auteur du post je peux liker ce post
        $this->assertTrue($userWantToLike->doesLike($post));

    }

      public function test_cant_like_my_post()
       {
           $post = new Post();
           $author = new User();

           $author->addPost($post);
           $post->setAuthor($author);
           $author->like($post);

           //$this->assertFalse($post->isLikedBy($author));
           $this->assertFalse($author->doesLike($post));
       }

    public function test_reverse_relation_cant_like_my_post()
    {
        $post = new Post();
        $author = new User();

        $author->addPost($post);
        $post->setAuthor($author);
        $post->addLiker($author);

        // si je suis l'auteur du post je ne peux pas liker
        $this->assertFalse($post->isLikedBy($author));
    }


    public function test_can_follow_other_user()
    {
        $userA = new User();
        $userB = new User();

        $userA->addFollower($userB);

        // On vérifie que userA est suivi par userB
        $this->assertTrue($userA->isFollowedBy($userB));
    }

    public function test_reverse_relationship_between_userA_userB()
    {
        $userA = new User();
        $userB = new User();

        $userA->addFollower($userB);

        $this->assertFalse($userB->isFollowedBy($userA));
    }


    public function test_cant_follow_myself()
    {
        $userA = new User();

        $userA->addFollower($userA);

        $this->assertFalse($userA->isFollowedBy($userA));
    }

}
