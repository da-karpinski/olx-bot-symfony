<?php

namespace App\Integration\Telegram\Entity;

use App\Entity\Integration;
use App\Integration\Telegram\Repository\TelegramIntegrationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: TelegramIntegrationRepository::class)]
class TelegramIntegration
{

    public const SUPPORTED_LOCALES = ['en', 'pl'];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['integration:view', 'integration:list'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'telegramIntegrations')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Integration $integration = null;

    #[ORM\Column(type: Types::BIGINT)]
    #[Groups(['integration:view'])]
    private ?string $chatId = null;

    #[ORM\Column(length: 15)]
    #[Groups(['integration:view'])]
    private ?string $chatType = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $otp = null;

    #[ORM\Column]
    #[Groups(['integration:view', 'integration:list'])]
    private ?bool $active = null;

    #[ORM\Column]
    #[Groups(['integration:view'])]
    private ?\DateTimeImmutable $createdAt = null;

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

    public function getChatId(): ?string
    {
        return $this->chatId;
    }

    public function setChatId(string $chatId): static
    {
        $this->chatId = $chatId;

        return $this;
    }

    public function getChatType(): ?string
    {
        return $this->chatType;
    }

    public function setChatType(string $chatType): static
    {
        $this->chatType = $chatType;

        return $this;
    }

    public function getOtp(): ?string
    {
        return $this->otp;
    }

    public function setOtp(?string $otp): static
    {
        $this->otp = $otp;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): static
    {
        $this->active = $active;

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
}
