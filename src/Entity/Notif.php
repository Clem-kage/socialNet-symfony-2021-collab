<?php

namespace App\Entity;

use App\Repository\NotifRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=NotifRepository::class)
 */
class Notif
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $text_content;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="notifs")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="origin")
     * @ORM\JoinColumn(nullable=false)
     */
    private $origin;

    /**
     * @ORM\ManyToOne(targetEntity=Post::class, inversedBy="notifiedposts")
     */
    private $postorigin;





    /**
     * Notif constructor.
     *
     */
    public function __construct()
    {
        $this->setCreatedAt(new \DateTime());
        $this->setOrigin($this->getUser());

    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTextContent(): ?string
    {
        return $this->text_content;
    }

    public function setTextContent(string $text_content): self
    {
        $this->text_content = $text_content;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getOrigin(): ?User
    {
        return $this->origin;
    }

    public function setOrigin(?User $origin): self
    {
        $this->origin = $origin;

        return $this;
    }

    public function getPostorigin(): ?Post
    {
        return $this->postorigin;
    }

    public function setPostorigin(?Post $postorigin): self
    {
        $this->postorigin = $postorigin;

        return $this;
    }




}
