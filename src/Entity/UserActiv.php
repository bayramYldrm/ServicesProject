<?php

namespace App\Entity;

use App\Repository\UserActivRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserActivRepository::class)]
class UserActiv
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $ip = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $projectName = null;

    #[ORM\Column(length: 50)]
    private ?string $owner = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\OneToMany(mappedBy: 'userActiv', targetEntity: UserActivService::class)]
    private Collection $userActivServices;

    #[ORM\OneToMany(mappedBy: 'project', targetEntity: UserProjectTask::class)]
    private Collection $userProjectTasks;

    #[ORM\ManyToOne(inversedBy: 'userActivs')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\OneToMany(mappedBy: 'userActiv', targetEntity: Comment::class)]
    private Collection $comments;

    public function __construct()
    {
        $this->userActivServices = new ArrayCollection();
        $this->userProjectTasks = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function setIp(?string $ip): self
    {
        $this->ip = $ip;

        return $this;
    }

    public function getProjectName(): ?string
    {
        return $this->projectName;
    }

    public function setProjectName(?string $projectName): self
    {
        $this->projectName = $projectName;

        return $this;
    }

    public function getOwner(): ?string
    {
        return $this->owner;
    }

    public function setOwner(string $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUserActivServices(): Collection
    {
        return $this->userActivServices;
    }

    public function addUserActivService(UserActivService $userActivService): self
    {
        if (!$this->userActivServices->contains($userActivService)) {
            $this->userActivServices[] = $userActivService;
            $userActivService->setUserActiv($this);
        }

        return $this;
    }

    public function removeUserActivService(UserActivService $userActivService): self
    {
        if ($this->userActivServices->removeElement($userActivService)) {
            // set the owning side to null (unless already changed)
            if ($userActivService->getUserActiv() === $this) {
                $userActivService->setUserActiv(null);
            }
        }

        return $this;
    }

    public function getUserProjectTasks(): Collection
    {
        return $this->userProjectTasks;
    }

    public function addUserProjectTask(UserProjectTask $userProjectTask): self
    {
        if (!$this->userProjectTasks->contains($userProjectTask)) {
            $this->userProjectTasks[] = $userProjectTask;
            $userProjectTask->setProject($this);
        }

        return $this;
    }

    public function removeUserProjectTask(UserProjectTask $userProjectTask): self
    {
        if ($this->userProjectTasks->removeElement($userProjectTask)) {
            // set the owning side to null (unless already changed)
            if ($userProjectTask->getProject() === $this) {
                $userProjectTask->setProject(null);
            }
        }

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
            $comment->setUserActiv($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getUserActiv() === $this) {
                $comment->setUserActiv(null);
            }
        }

        return $this;
    }
}
