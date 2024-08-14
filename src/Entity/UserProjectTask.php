<?php

namespace App\Entity;

use App\Repository\UserProjectTaskRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserProjectTaskRepository::class)]
class UserProjectTask
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: UserActiv::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?UserActiv $project = null;

    #[ORM\ManyToOne(targetEntity: Task::class, inversedBy: 'userProjectTasks')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Task $task = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProject(): ?UserActiv
    {
        return $this->project;
    }

    public function setProject(?UserActiv $project): static
    {
        $this->project = $project;

        return $this;
    }

    public function getTask(): ?Task
    {
        return $this->task;
    }

    public function setTask(?Task $task): static
    {
        $this->task = $task;

        return $this;
    }
}


