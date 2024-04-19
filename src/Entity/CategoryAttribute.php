<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use App\ApiResource\CategoryAttribute\Provider\GetCategoryAttributesForWorkerProvider;
use App\Repository\CategoryAttributeRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CategoryAttributeRepository::class)]
#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/category-attribute/{id}', //worker ID required
            requirements: ['id' => '\d+'],
            normalizationContext: ['groups' => ['category-attribute:list'], 'skip_null_values' => false],
            security: 'is_granted("'.User::ROLE_ADMIN.'") or is_granted("'.User::ROLE_USER.'")',
            provider: GetCategoryAttributesForWorkerProvider::class
        ),
    ],
    normalizationContext: ['groups' => ['category-attribute:list', 'category-attribute:view'], 'enable_max_depth' => true],
    paginationClientEnabled: true,
    paginationClientItemsPerPage: true,
    paginationEnabled: true,
    paginationItemsPerPage: 12,
)]
class CategoryAttribute
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups([
        'category-attribute:list',
        'worker:view'
    ])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'categoryAttributes', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Worker $worker = null;

    #[ORM\Column(length: 60)]
    #[Groups([
        'category-attribute:list',
        'worker:view'
    ])]
    private ?string $attributeCode = null;

    #[ORM\Column(length: 60)]
    #[Groups([
        'category-attribute:list',
        'worker:view'
    ])]
    private ?string $attributeValue = null;

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

    public function getAttributeCode(): ?string
    {
        return $this->attributeCode;
    }

    public function setAttributeCode(string $attributeCode): static
    {
        $this->attributeCode = $attributeCode;

        return $this;
    }

    public function getAttributeValue(): ?string
    {
        return $this->attributeValue;
    }

    public function setAttributeValue(string $attributeValue): static
    {
        $this->attributeValue = $attributeValue;

        return $this;
    }
}
