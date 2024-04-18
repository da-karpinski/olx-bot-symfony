<?php

namespace App\Integration\Email\Entity;

use App\Entity\Integration;
use App\Integration\Email\Repository\EmailIntegrationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EmailIntegrationRepository::class)]
class EmailIntegration
{

    public const SUPPORTED_LOCALES = ['en', 'pl'];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'emailIntegrations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Integration $integration = null;

    #[ORM\Column(length: 100)]
    private ?string $recipientAddress = null;

    #[ORM\Column(nullable: true)]
    private ?array $ccAddresses = null;

    #[ORM\Column(nullable: true)]
    private ?array $bccAddresses = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIntegration(): ?Integration
    {
        return $this->integration;
    }

    public function setIntegration(?Integration $integration): static
    {
        $this->integration = $integration;

        return $this;
    }

    public function getRecipientAddress(): ?string
    {
        return $this->recipientAddress;
    }

    public function setRecipientAddress(string $recipientAddress): static
    {
        $this->recipientAddress = $recipientAddress;

        return $this;
    }

    public function getCcAddresses(): ?array
    {
        return $this->ccAddresses;
    }

    public function setCcAddresses(?array $ccAddresses): static
    {
        $this->ccAddresses = $ccAddresses;

        return $this;
    }

    public function getBccAddresses(): ?array
    {
        return $this->bccAddresses;
    }

    public function setBccAddresses(?array $bccAddresses): static
    {
        $this->bccAddresses = $bccAddresses;

        return $this;
    }
}
