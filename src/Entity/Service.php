<?php

namespace App\Entity;

use App\Repository\ServiceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ServiceRepository::class)]
class Service
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $Name = null;

    #[ORM\Column(length: 255)]
    private ?string $Version = null;

    #[ORM\Column(length: 255)]
    private ?string $ServiceName = null;

    #[ORM\Column(length: 100)]
    private ?string $Category = null;

    #[ORM\Column]
    private ?bool $IsActive = null;

    #[ORM\OneToMany(mappedBy: 'service', targetEntity: UserActivService::class)]
    private Collection $userActivServices;

    public function __construct()
    {
        $this->userActivServices = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->Name;
    }

    public function setName(string $Name): self
    {
        $this->Name = $Name;

        return $this;
    }

    public function getVersion(): ?string
    {
        return $this->Version;
    }

    public function setVersion(string $Version): self
    {
        $this->Version = $Version;

        return $this;
    }

    public function getServiceName(): ?string
    {
        return $this->ServiceName;
    }

    public function setServiceName(string $ServiceName): self
    {
        $this->ServiceName = $ServiceName;

        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->Category;
    }

    public function setCategory(string $Category): self
    {
        $this->Category = $Category;

        return $this;
    }

    public function isIsActive(): ?bool
    {
        return $this->IsActive;
    }

    public function setIsActive(bool $IsActive): self
    {
        $this->IsActive = $IsActive;

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
            $userActivService->setService($this);
        }

        return $this;
    }

    public function removeUserActivService(UserActivService $userActivService): self
    {
        if ($this->userActivServices->removeElement($userActivService)) {
            // set the owning side to null (unless already changed)
            if ($userActivService->getService() === $this) {
                $userActivService->setService(null);
            }
        }

        return $this;
    }
}
