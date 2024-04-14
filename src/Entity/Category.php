<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\ExistsFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\ApiResource\Category\Provider\CategoryGetAttributesProvider;
use App\ApiResource\Category\Provider\CategoryGetSubcategoriesProvider;
use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/category/{id}',
            normalizationContext: ['groups' => ['category:view'], 'skip_null_values' => false],
            security: 'is_granted("'.User::ROLE_ADMIN.'") or is_granted("'.User::ROLE_USER.'")',
        ),
        new GetCollection(
            uriTemplate: '/category',
            paginationEnabled: true,
            paginationItemsPerPage: 12,
            normalizationContext: ['groups' => ['category:list'], 'skip_null_values' => false],
            security: 'is_granted("'.User::ROLE_ADMIN.'") or is_granted("'.User::ROLE_USER.'")',
        ),
        new GetCollection(
            uriTemplate: '/category/{id}/subcategories',
            requirements: ['id' => '\d+'],
            paginationEnabled: false,
            normalizationContext: ['groups' => ['category:list']],
            security: 'is_granted("'.User::ROLE_ADMIN.'") or is_granted("'.User::ROLE_USER.'")',
            provider: CategoryGetSubcategoriesProvider::class,
        ),
        new Get(
            uriTemplate: '/category/{id}/attributes',
            requirements: ['id' => '\d+'],
            paginationEnabled: false,
            normalizationContext: ['groups' => ['category:view']],
            security: 'is_granted("'.User::ROLE_ADMIN.'") or is_granted("'.User::ROLE_USER.'")',
            provider: CategoryGetAttributesProvider::class,
        ),
    ],
    normalizationContext: ['groups' => ['category:list', 'category:view'], 'enable_max_depth' => true],
    paginationClientEnabled: true,
    paginationClientItemsPerPage: true,
    paginationEnabled: true,
    paginationItemsPerPage: 12,
)]
#[ApiFilter(SearchFilter::class, properties: [
    'name' => SearchFilter::STRATEGY_PARTIAL,
])]
#[ApiFilter(ExistsFilter::class, properties: [
    'parent'
])]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['category:view', 'category:list'])]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $olxId = null;

    #[ORM\Column(length: 60)]
    #[Groups(['category:view', 'category:list'])]
    private ?string $name = null;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'parentId')]
    #[Groups(['category:view', 'category:list'])]
    private ?self $parent = null;

    #[ORM\OneToMany(targetEntity: self::class, mappedBy: 'category')]
    private Collection $parentId;

    public function __construct()
    {
        $this->parentId = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOlxId(): ?int
    {
        return $this->olxId;
    }

    public function setOlxId(int $olxId): static
    {
        $this->olxId = $olxId;

        return $this;
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

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): static
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getParentId(): Collection
    {
        return $this->parentId;
    }

    public function addParentId(self $parentId): static
    {
        if (!$this->parentId->contains($parentId)) {
            $this->parentId->add($parentId);
            $parentId->setParent($this);
        }

        return $this;
    }

    public function removeParentId(self $parentId): static
    {
        if ($this->parentId->removeElement($parentId)) {
            // set the owning side to null (unless already changed)
            if ($parentId->getParent() === $this) {
                $parentId->setParent(null);
            }
        }

        return $this;
    }
}
