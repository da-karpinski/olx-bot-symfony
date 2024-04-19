<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\CityRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CityRepository::class)]
#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/city/{id}',
            normalizationContext: ['groups' => ['city:view']],
            security: 'is_granted("'.User::ROLE_ADMIN.'") or is_granted("'.User::ROLE_USER.'")',
        ),
        new GetCollection(
            uriTemplate: '/city',
            paginationEnabled: true,
            paginationItemsPerPage: 12,
            normalizationContext: ['groups' => ['city:list']],
            security: 'is_granted("'.User::ROLE_ADMIN.'") or is_granted("'.User::ROLE_USER.'")',
        )
    ],
    normalizationContext: ['groups' => ['city:list', 'city:view'], 'enable_max_depth' => true],
    paginationClientEnabled: true,
    paginationClientItemsPerPage: true,
    paginationEnabled: true,
    paginationItemsPerPage: 12,
)]
#[ApiFilter(SearchFilter::class, properties: [
    'name' => SearchFilter::STRATEGY_PARTIAL,
    'region.id' => SearchFilter::STRATEGY_EXACT,
])]
class City
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups([
        'city:view', 'city:list',
        'worker:view', 'worker:list'
    ])]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $olxId = null;

    #[ORM\ManyToOne(inversedBy: 'cities')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups([
        'city:view', 'city:list',
        'worker:view', 'worker:list'])]
    private ?CountryRegion $region = null;

    #[ORM\Column(length: 60)]
    #[Groups([
        'city:view', 'city:list',
        'worker:view', 'worker:list'
    ])]
    private ?string $name = null;

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

    public function getRegion(): ?CountryRegion
    {
        return $this->region;
    }

    public function setRegion(?CountryRegion $region): static
    {
        $this->region = $region;

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
}
