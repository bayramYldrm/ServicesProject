<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $Email = null;

    #[ORM\Column(length: 50, unique: true)]
    private ?string $Username = null;

    #[ORM\Column(length: 255)]
    private ?string $Password = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Comment::class)]
    private Collection $comments;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: UserActiv::class)]
    private Collection $userActivs;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->userActivs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->Email;
    }

    public function setEmail(string $Email): static
    {
        $this->Email = $Email;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->Username;
    }

    public function setUsername(string $Username): static
    {
        $this->Username = $Username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->Password;
    }

    public function setPassword(string $Password): static
    {
        $this->Password = $Password;

        return $this;
    }

    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }

    public function eraseCredentials()
    {
        // Eğer geçici hassas veri saklıyorsanız, burada temizleyin
    }

    public function getUserIdentifier(): string
    {
        return $this->Username;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setUser($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getUser() === $this) {
                $comment->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, UserActiv>
     */
    public function getUserActivs(): Collection
    {
        return $this->userActivs;
    }

    public function addUserActiv(UserActiv $userActiv): self
    {
        if (!$this->userActivs->contains($userActiv)) {
            $this->userActivs[] = $userActiv;
            $userActiv->setUser($this);
        }

        return $this;
    }

    public function removeUserActiv(UserActiv $userActiv): self
    {
        if ($this->userActivs->removeElement($userActiv)) {
            // set the owning side to null (unless already changed)
            if ($userActiv->getUser() === $this) {
                $userActiv->setUser(null);
            }
        }

        return $this;
    }
}
