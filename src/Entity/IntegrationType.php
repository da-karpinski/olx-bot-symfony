<?php

namespace App\Entity;

use App\Repository\IntegrationTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: IntegrationTypeRepository::class)]
class IntegrationType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 60)]
    private ?string $name = null;

    #[ORM\Column(length: 60)]
    private ?string $integrationCode = null;

    #[ORM\Column]
    private ?bool $enabled = null;

    #[ORM\OneToMany(targetEntity: Integration::class, mappedBy: 'integrationType', orphanRemoval: true)]
    private Collection $integrations;

    #[ORM\Column]
    private array $locales = [];

    public function __construct()
    {
        $this->integrations = new ArrayCollection();
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

    public function getIntegrationCode(): ?string
    {
        return $this->integrationCode;
    }

    public function setIntegrationCode(string $integrationCode): static
    {
        $this->integrationCode = $integrationCode;

        return $this;
    }

    public function isEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): static
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * @return Collection<int, Integration>
     */
    public function getIntegrations(): Collection
    {
        return $this->integrations;
    }

    public function addIntegration(Integration $integration): static
    {
        if (!$this->integrations->contains($integration)) {
            $this->integrations->add($integration);
            $integration->setIntegrationType($this);
        }

        return $this;
    }

    public function removeIntegration(Integration $integration): static
    {
        if ($this->integrations->removeElement($integration)) {
            // set the owning side to null (unless already changed)
            if ($integration->getIntegrationType() === $this) {
                $integration->setIntegrationType(null);
            }
        }

        return $this;
    }

    public function getLocales(): array
    {
        return $this->locales;
    }

    public function setLocales(array $locales): static
    {
        $this->locales = $locales;

        return $this;
    }
}
