<?php

namespace App\Entity;

use App\Repository\PostRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=PostRepository::class)
 */
class Post
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     * @Assert\Length(min=2,max=280)
     * @Assert\NotBlank()
     */
    private $content;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="posts", fetch="EAGER")
     * @ORM\JoinColumn(nullable=false)
     */
    private $author;

    /**
     * @ORM\ManyToOne(targetEntity=Post::class, inversedBy="comments")
     */
    private $parent;

    /**
     * @ORM\OneToMany(targetEntity=Post::class, mappedBy="parent", cascade="remove", fetch="EXTRA_LAZY")
     */
    private $comments;

    private $commentsCount = null;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, mappedBy="likedPosts", fetch="EXTRA_LAZY")
     */
    private $likers;

    private $likersCount = null;

    /**
     * @ORM\OneToMany(targetEntity=Notif::class, mappedBy="postorigin")
     */
    private $notifiedposts;

    private $isLiked = null;

    public function __construct()
    {
        // On défini automatiquement la date de création de cette instance
        $this->setCreatedAt(new \DateTime());
        $this->comments = new ArrayCollection();
        $this->likers = new ArrayCollection();
        $this->notifiedposts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = ucfirst(trim($content));
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(self $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setParent($this);
        }

        return $this;
    }

    public function removeComment(self $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getParent() === $this) {
                $comment->setParent(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getLikers(): Collection
    {
        return $this->likers;
    }

    public function addLiker(User $liker): self
    {
        if (!$this->likers->contains($liker) && $this->getAuthor() !== $liker) {
            $this->likers[] = $liker;
            $liker->addLikedPost($this);
        }
        return $this;
    }

    public function removeLiker(User $liker): self
    {
        if ($this->likers->removeElement($liker)) {
            $liker->removeLikedPost($this);
        }

        return $this;
    }

    public function isLikedBy(?User $user = null): bool {
        if($user === null){return false;}

        if($this->isLiked===null){
            $this->isLiked=$this->likers->contains($user);
        }

        return $this->isLiked;

    }

    public function setIsLiked(bool $isLiked)
    {
        $this->isLiked = $isLiked;
    }

    /**
     * @return Collection|Notif[]
     */
    public function getNotifiedposts(): Collection
    {
        return $this->notifiedposts;
    }

    public function addNotifiedpost(Notif $notifiedpost): self
    {
        if (!$this->notifiedposts->contains($notifiedpost)) {
            $this->notifiedposts[] = $notifiedpost;
            $notifiedpost->setPostorigin($this);
        }

        return $this;
    }

    public function removeNotifiedpost(Notif $notifiedpost): self
    {
        if ($this->notifiedposts->removeElement($notifiedpost)) {
            // set the owning side to null (unless already changed)
            if ($notifiedpost->getPostorigin() === $this) {
                $notifiedpost->setPostorigin(null);
            }
        }
        return $this;
    }

    public function getCommentsCount() {
        if ($this->commentsCount === null) {
            return $this->comments->count();
        }

        return $this->commentsCount;
    }

    /**
     * @param int $commentsCount
     * @return Post
     */
    public function setCommentsCount(int $commentsCount)
    {
        $this->commentsCount = $commentsCount;
        return $this;
    }

    /**
     * @return null
     */
    public function getLikersCount()
    {
        if ($this->likersCount === null) {
            return $this->likers->count();
        }

        return $this->likersCount;
    }

    /**
     * @param int $likersCount
     */
    public function setLikersCount(int $likersCount): self
    {
        $this->likersCount = $likersCount;
        return $this;
    }
}
