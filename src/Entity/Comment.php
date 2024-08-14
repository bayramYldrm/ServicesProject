<?php

namespace App\Entity;

use App\Repository\CommentRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: CommentRepository::class)]
#[ORM\Table(name: "comment")]
class Comment
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\Column(type: "text")]
    #[Assert\NotBlank()]
    #[Assert\Length(max: 65535)]
    private ?string $content = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "id", nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: UserActiv::class)]
    #[ORM\JoinColumn(name: "user_activ_id", referencedColumnName: "id", nullable: false)]
    private ?UserActiv $userActiv = null;

    #[ORM\Column(type: "datetime")]
    private ?DateTimeInterface $createdAt = null;

    #[ORM\Column(type: "datetime", nullable: true)]
    private ?DateTimeInterface $updatedAt = null;

    #[ORM\Column(type: "string", length: 20)]
    #[Assert\NotBlank()]
    private ?string $status = null;

    #[ORM\Column(type: "integer")]
    private int $likes = 0;

    #[ORM\Column(type: "integer")]
    private int $dislikes = 0;

    #[ORM\Column(type: "integer")]
    private int $reported = 0;

    #[ORM\OneToMany(mappedBy: "parent", targetEntity: Comment::class, orphanRemoval: true)]
    private Collection $replies; // Yorumun yanıtları

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: "replies")]
    #[ORM\JoinColumn(name: "parent_id", referencedColumnName: "id", nullable: true)]
    private ?self $parent = null;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: "likedComments")]
    #[ORM\JoinTable(name: "comment_likes")]
    private Collection $likesUsers;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: "dislikedComments")]
    #[ORM\JoinTable(name: "comment_dislikes")]
    private Collection $dislikesUsers;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: "reportedComments")]
    #[ORM\JoinTable(name: "comment_reports")]
    private Collection $reportedUsers;

    public function __construct()
    {
        $this->replies = new ArrayCollection();
        $this->likesUsers = new ArrayCollection();
        $this->dislikesUsers = new ArrayCollection();
        $this->reportedUsers = new ArrayCollection();
    }

    // Getter ve Setter metodları
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
        $this->content = $content;
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

    public function getUserActiv(): ?UserActiv
    {
        return $this->userActiv;
    }

    public function setUserActiv(?UserActiv $userActiv): self
    {
        $this->userActiv = $userActiv;
        return $this;
    }

    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getLikes(): int
    {
        return $this->likes;
    }

    public function setLikes(int $likes): self
    {
        $this->likes = $likes;
        return $this;
    }

    public function getDislikes(): int
    {
        return $this->dislikes;
    }

    public function setDislikes(int $dislikes): self
    {
        $this->dislikes = $dislikes;
        return $this;
    }

    public function getReported(): int
    {
        return $this->reported;
    }

    public function setReported(int $reported): self
    {
        $this->reported = $reported;
        return $this;
    }

    public function getReplies(): Collection
    {
        return $this->replies;
    }

    public function setReplies(Collection $replies): self
    {
        $this->replies = $replies;
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

    public function getLikesUsers(): Collection
    {
        return $this->likesUsers;
    }

    public function addLikeUser(User $user): self
    {
        if (!$this->likesUsers->contains($user)) {
            $this->likesUsers->add($user);
        }
        return $this;
    }

    public function removeLikeUser(User $user): self
    {
        $this->likesUsers->removeElement($user);
        return $this;
    }

    public function getDislikesUsers(): Collection
    {
        return $this->dislikesUsers;
    }

    public function addDislikeUser(User $user): self
    {
        if (!$this->dislikesUsers->contains($user)) {
            $this->dislikesUsers->add($user);
        }
        return $this;
    }

    public function removeDislikeUser(User $user): self
    {
        $this->dislikesUsers->removeElement($user);
        return $this;
    }

    public function getReportedUsers(): Collection
    {
        return $this->reportedUsers;
    }

    public function addReportedUser(User $user): self
    {
        if (!$this->reportedUsers->contains($user)) {
            $this->reportedUsers->add($user);
            $this->reported += 1; // Rapor sayısını artır
        }
        return $this;
    }


    public function removeReportedUser(User $user): self
    {
        if ($this->reportedUsers->contains($user)) {
            $this->reportedUsers->removeElement($user);
            $this->reported -= 1; // Rapor sayısını azalt
        }
        return $this;
    }

}
