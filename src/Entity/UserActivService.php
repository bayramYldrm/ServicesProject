<?php

namespace App\Entity;

use App\Repository\UserActivServiceRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserActivServiceRepository::class)]
class UserActivService
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: UserActiv::class, inversedBy: 'userActivServices')]
    private ?UserActiv $userActiv = null;

    #[ORM\ManyToOne(targetEntity: Service::class)]
    private ?Service $service = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getService(): ?Service
    {
        return $this->service;
    }

    public function setService(?Service $service): self
    {
        $this->service = $service;

        return $this;
    }
}
