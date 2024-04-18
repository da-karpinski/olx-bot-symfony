<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\ExistsFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\NotificationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: NotificationRepository::class)]
#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/notification/{id}',
            normalizationContext: ['groups' => ['notification:view']],
            security: 'is_granted("'.User::ROLE_ADMIN.'") or is_granted("'.User::ROLE_USER.'")',
        ),
        new GetCollection(
            uriTemplate: '/notification',
            paginationEnabled: true,
            paginationItemsPerPage: 12,
            normalizationContext: ['groups' => ['notification:list']],
            security: 'is_granted("'.User::ROLE_ADMIN.'") or is_granted("'.User::ROLE_USER.'")',
        )
    ],
    normalizationContext: ['groups' => ['notification:list', 'notification:view'], 'enable_max_depth' => true],
    paginationClientEnabled: true,
    paginationClientItemsPerPage: true,
    paginationEnabled: true,
    paginationItemsPerPage: 12
)]
#[ApiFilter(SearchFilter::class, properties: [
    'worker.id' => SearchFilter::STRATEGY_EXACT,
    'offer.id' => SearchFilter::STRATEGY_EXACT,
    'integration.id' => SearchFilter::STRATEGY_EXACT,
    'worker.name' => SearchFilter::STRATEGY_PARTIAL,
    'offer.title' => SearchFilter::STRATEGY_PARTIAL,
    'integration.name' => SearchFilter::STRATEGY_PARTIAL,
    'integration.integrationType.id' => SearchFilter::STRATEGY_EXACT,
    'integration.integrationType.name' => SearchFilter::STRATEGY_PARTIAL,
    'integration.integrationType.integrationCode' => SearchFilter::STRATEGY_EXACT,
])]
#[ApiFilter(OrderFilter::class, properties: [
    'id',
    'createdAt',
    'sentAt',
    'title',
    'offer.id',
    'offer.title',
    'worker.id',
    'worker.name',
    'integration.id',
    'integration.name',
    'integration.integrationType.id',
    'integration.integrationType.name',
])]
#[ApiFilter(ExistsFilter::class, properties: ['sentAt'])]
class Notification
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['notification:list', 'notification:view'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'notifications')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['notification:list', 'notification:view'])]
    private ?Worker $worker = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['notification:list', 'notification:view'])]
    private ?Offer $offer = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['notification:list', 'notification:view'])]
    private ?Integration $integration = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['notification:view'])]
    private ?array $additionalData = null;

    #[ORM\Column]
    #[Groups(['notification:list', 'notification:view'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['notification:list', 'notification:view'])]
    private ?\DateTimeImmutable $sentAt = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['notification:list', 'notification:view'])]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['notification:view'])]
    private ?string $message = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWorker(): ?Worker
    {
        return $this->worker;
    }

    public function setWorker(?Worker $worker): static
    {
        $this->worker = $worker;

        return $this;
    }

    public function getOffer(): ?Offer
    {
        return $this->offer;
    }

    public function setOffer(?Offer $offer): static
    {
        $this->offer = $offer;

        return $this;
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

    public function getAdditionalData(): ?array
    {
        return $this->additionalData;
    }

    public function setAdditionalData(?array $additionalData): static
    {
        $this->additionalData = $additionalData;

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

    public function getSentAt(): ?\DateTimeImmutable
    {
        return $this->sentAt;
    }

    public function setSentAt(?\DateTimeImmutable $sentAt): static
    {
        $this->sentAt = $sentAt;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): static
    {
        $this->message = $message;

        return $this;
    }
}
