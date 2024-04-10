<?php

namespace App\Entity;

use App\Repository\IntegrationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: IntegrationRepository::class)]
class Integration
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'integrations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'integrations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?IntegrationType $integrationType = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\OneToMany(targetEntity: WorkerIntegration::class, mappedBy: 'integration')]
    private Collection $workerIntegrations;

    #[ORM\Column(length: 2)]
    private ?string $localeCode = null;

    public function __construct()
    {
        $this->workerIntegrations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getIntegrationType(): ?IntegrationType
    {
        return $this->integrationType;
    }

    public function setIntegrationType(?IntegrationType $integrationType): static
    {
        $this->integrationType = $integrationType;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return Collection<int, WorkerIntegration>
     */
    public function getWorkerIntegrations(): Collection
    {
        return $this->workerIntegrations;
    }

    public function addWorkerIntegration(WorkerIntegration $workerIntegration): static
    {
        if (!$this->workerIntegrations->contains($workerIntegration)) {
            $this->workerIntegrations->add($workerIntegration);
            $workerIntegration->setIntegration($this);
        }

        return $this;
    }

    public function removeWorkerIntegration(WorkerIntegration $workerIntegration): static
    {
        if ($this->workerIntegrations->removeElement($workerIntegration)) {
            // set the owning side to null (unless already changed)
            if ($workerIntegration->getIntegration() === $this) {
                $workerIntegration->setIntegration(null);
            }
        }

        return $this;
    }

    public function getLocaleCode(): ?string
    {
        return $this->localeCode;
    }

    public function setLocaleCode(string $localeCode): static
    {
        $this->localeCode = $localeCode;

        return $this;
    }
}
