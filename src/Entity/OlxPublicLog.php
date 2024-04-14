<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\OlxPublicLogRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: OlxPublicLogRepository::class)]
#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/log/olx-public/{id}',
            normalizationContext: ['groups' => ['log:olx-public:view']],
            security: 'is_granted("'.User::ROLE_ADMIN.'")',
        ),
        new GetCollection(
            uriTemplate: '/log/olx-public',
            paginationEnabled: true,
            paginationItemsPerPage: 12,
            normalizationContext: ['groups' => ['log:olx-public:list']],
            security: 'is_granted("'.User::ROLE_ADMIN.'")',
        )
    ],
    normalizationContext: ['groups' => ['log:olx-public:list', 'log:olx-public:view'], 'enable_max_depth' => true],
    paginationClientEnabled: true,
    paginationClientItemsPerPage: true,
    paginationEnabled: true,
    paginationItemsPerPage: 12,
)]
#[ApiFilter(SearchFilter::class, properties: [
    'level' => SearchFilter::STRATEGY_EXACT,
])]
#[ApiFilter(DateFilter::class, properties: ['created_at'])]
#[ApiFilter(OrderFilter::class, properties: ['created_at'], arguments: ['orderParameterName' => 'order'])]
#[ApiFilter(OrderFilter::class, properties: ['id'], arguments: ['orderParameterName' => 'order'])]
class OlxPublicLog
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['log:olx-public:view', 'log:olx-public:list'])]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups(['log:olx-public:view', 'log:olx-public:list'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['log:olx-public:view', 'log:olx-public:list'])]
    private ?string $message = null;

    #[ORM\Column(length: 60)]
    #[Groups(['log:olx-public:view', 'log:olx-public:list'])]
    private ?string $level = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function getLevel(): ?string
    {
        return $this->level;
    }

    public function setLevel(string $level): static
    {
        $this->level = $level;

        return $this;
    }
}
