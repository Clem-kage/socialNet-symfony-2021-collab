<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(fields={"username"}, message="There is already an account with this username")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\NotBlank()
     */
    private $username;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $fullName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $phone;

    /**
     * @ORM\Column(type="date")
     */
    private $birthdate;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $gender;

    /**
     * @ORM\OneToMany(targetEntity=Post::class, mappedBy="author", orphanRemoval=true, fetch="EXTRA_LAZY")
     */
    private $posts;

    /**
     * @ORM\ManyToMany(targetEntity=Post::class, inversedBy="likers", fetch="EXTRA_LAZY")
     */
    private $likedPosts;

    /**
     * @ORM\OneToMany(targetEntity=Notif::class, mappedBy="user", orphanRemoval=true)
     */
    private $notifs;

    /**
     * @ORM\OneToMany(targetEntity=Notif::class, mappedBy="origin", orphanRemoval=true)
     */
    private $origin;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="followings")
     */
    private $followers;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, mappedBy="followers")
     */
    private $followings;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $profilePicture;



    public function __construct()
    {
        $this->posts = new ArrayCollection();
        $this->setCreatedAt(new \DateTime());
        $this->likedPosts = new ArrayCollection();

        $this->notifs = new ArrayCollection();
        $this->origin = new ArrayCollection();
        $this->followers = new ArrayCollection();
        $this->followings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    public function setUsername(?string $username=null): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(string $fullName): self
    {
        $this->fullName = $fullName;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getBirthdate(): ?\DateTimeInterface
    {
        return $this->birthdate;
    }

    public function setBirthdate(\DateTimeInterface $birthdate): self
    {
        $this->birthdate = $birthdate;

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

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(?string $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * Obtenir la liste des Posts écrits par ce User
     * @return Collection|Post[]
     */
    public function getPosts(): Collection
    {

        return $this->posts;
    }

    public function addPost(Post $post): self
    {
        if (!$this->posts->contains($post)) {
            $this->posts[] = $post;
            $post->setAuthor($this);
        }

        return $this;
    }

    public function removePost(Post $post): self
    {
        if ($this->posts->removeElement($post)) {
            // set the owning side to null (unless already changed)
            if ($post->getAuthor() === $this) {
                $post->setAuthor(null);
            }
        }

        return $this;
    }

    // "Apprend" à PHP à convertir un User en chaine de caractère
    public function __toString()
    {
        return $this->getUsername() . " (" . $this->getFullName() . ")";
    }

    /**
     * Obtenir la liste des Posts likés par ce User
     * @return Collection|Post[]
     */
    public function getLikedPosts(): Collection
    {
        return $this->likedPosts;
    }

    public function addLikedPost(Post $likedPost): self
    {
        if (!$this->likedPosts->contains($likedPost) && $this !== $likedPost->getAuthor()) {

            $this->likedPosts[] = $likedPost;
        }
        return $this;
    }

    /**
     * Alias de la fonction "addLikedPost"
     * @param Post $post
     * @return $this
     */
    public function like(Post $post): User
    {
        return $this->addLikedPost($post);
    }

    public function removeLikedPost(Post $likedPost): self
    {
        $this->likedPosts->removeElement($likedPost);

        return $this;
    }

    /**
     * Alias de la fonction "removeLikedPost"
     * @param Post $post
     * @return $this
     */
    public function dislike(Post $post): User
    {
        return $this->removeLikedPost($post);
    }

    public function doesLike(Post $post): bool {
        if ($this->likedPosts->contains($post)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return Collection|Notif[]
     */
    public function getNotifs(): Collection
    {
        return $this->notifs;
    }

    public function addNotif(Notif $notif): self
    {
        if (!$this->notifs->contains($notif)) {
            $this->notifs[] = $notif;
            $notif->setUser($this);
        }
        return $this;
    }

    public function removeNotif(Notif $notif): self
    {
        if ($this->notifs->removeElement($notif)) {
            // set the owning side to null (unless already changed)
            if ($notif->getUser() === $this) {
                $notif->setUser(null);
            }
        }
    }

/**
     * @return Collection|Notif[]
     */
    public function getOrigin(): Collection
    {
        return $this->origin;
    }

    public function addOrigin(Notif $origin): self
    {
        if (!$this->origin->contains($origin)) {
            $this->origin[] = $origin;
            $origin->setOrigin($this);
        }
        return $this;
    }

    public function removeOrigin(Notif $origin): self
    {
        if ($this->origin->removeElement($origin)) {
            // set the owning side to null (unless already changed)
            if ($origin->getOrigin() === $this) {
                $origin->setOrigin(null);
            }
        }
        return $this;
    }

    public function getProfilePicture(): ?string
    {
        return $this->profilePicture;
    }

    public function setProfilePicture(string $profilePicture): self
    {
        $this->profilePicture = $profilePicture;

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getFollowers(): Collection
    {
        return $this->followers;
    }

    public function addFollower(self $follower): self
    {
        if (!$this->followers->contains($follower) && $this !== $follower) {
            $this->followers[] = $follower;
        }

        return $this;
    }

    public function removeFollower(self $follower): self
    {
        $this->followers->removeElement($follower);

        return $this;
    }

    /**
     * Determines if this user if followed by otherUser
     * @param User|null $otherUser
     * @return bool
     */
    public function isFollowedBy(User $otherUser = null): bool {

        if (
            $otherUser !== null &&
            $this->followers->contains($otherUser)) {
            return true;
        }

        return false;
    }

    /**
     * Get all Users that this user follows
     * @return Collection|self[]
     */
    public function getFollowings(): Collection
    {
        return $this->followings;
    }

    public function addFollowing(self $followings): self
    {
        if (!$this->followings->contains($followings)) {
            $this->followings[] = $followings;
            $followings->addFollower($this);
        }
        return $this;
    }

    public function follow(User $user): User
    {
        return $this->addFollowing($user);
    }

    public function removeFollowing(self $followings): self
    {
        if ($this->followings->removeElement($followings)) {
            $followings->removeFollower($this);
        }

        return $this;
    }

    public function unfollow(User $user): User
    {
        return $this->removeFollowing($user);
    }

    /**
     * Determines if this user follows an otherUser
     * @param User|null $otherUser
     * @return bool
     */
    public function doesFollow(User $otherUser = null): bool {

        if (
            $otherUser !== null &&
            $this->followings->contains($otherUser)) {
            return true;
        }

        return false;
    }

    public function canSeePostsOf(User $user): bool {

        // On peut voir ses propres Posts
        if ($this === $user) return true;

        // On peut voir les Posts de nos followings
        if ($this->doesFollow($user)) return true;

        return false;
    }
}
