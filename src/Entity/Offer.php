<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\OfferRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: OfferRepository::class)]
#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/offer/{id}',
            normalizationContext: ['groups' => ['offer:view']],
            security: 'is_granted("'.User::ROLE_ADMIN.'") or is_granted("'.User::ROLE_USER.'")',
        ),
        new GetCollection(
            uriTemplate: '/offer',
            paginationEnabled: true,
            paginationItemsPerPage: 12,
            normalizationContext: ['groups' => ['offer:list']],
            security: 'is_granted("'.User::ROLE_ADMIN.'") or is_granted("'.User::ROLE_USER.'")',
        )
    ],
    normalizationContext: ['groups' => ['offer:list', 'offer:view'], 'enable_max_depth' => true],
    paginationClientEnabled: true,
    paginationClientItemsPerPage: true,
    paginationEnabled: true,
    paginationItemsPerPage: 12
)]
#[ApiFilter(SearchFilter::class, properties: [
    'title' => SearchFilter::STRATEGY_PARTIAL,
    'olxId' => SearchFilter::STRATEGY_EXACT,
    'worker.id' => SearchFilter::STRATEGY_EXACT,
    'worker.name' => SearchFilter::STRATEGY_PARTIAL,
])]
#[ApiFilter(OrderFilter::class, properties: [
    'createdAt',
    'lastSeenAt',
    'validTo',
    'price',
])]
#[ApiFilter(DateFilter::class, properties: [
    'createdAt',
    'lastSeenAt',
    'validTo',
])]
class Offer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups([
        'offer:list', 'offer:view',
        'notification:list', 'notification:view'
    ])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'offers')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['offer:list', 'offer:view'])]
    private ?Worker $worker = null;

    #[ORM\Column]
    #[Groups(['offer:list', 'offer:view'])]
    private ?\DateTimeImmutable $lastSeenAt = null;

    #[ORM\Column]
    #[Groups(['offer:list', 'offer:view'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['offer:view'])]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    #[Groups(['offer:list', 'offer:view'])]
    private ?string $url = null;

    #[ORM\Column]
    #[Groups([
        'offer:list', 'offer:view',
        'notification:list', 'notification:view'
    ])]
    private ?int $olxId = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['offer:view'])]
    private ?\DateTimeImmutable $refreshedAt = null;

    #[ORM\Column(length: 255)]
    #[Groups([
        'offer:list', 'offer:view',
        'notification:list', 'notification:view'
    ])]
    private ?string $title = null;

    #[ORM\Column]
    #[Groups(['offer:list', 'offer:view'])]
    private ?\DateTimeImmutable $validTo = null;

    #[ORM\OneToMany(targetEntity: OfferPhoto::class, mappedBy: 'offer', orphanRemoval: true)]
    #[Groups(['offer:list', 'offer:view'])]
    private Collection $offerPhotos;

    #[ORM\Column(nullable: true)]
    #[Groups(['offer:list', 'offer:view'])]
    private ?int $price = null;

    #[ORM\Column(length: 3, nullable: true)]
    #[Groups(['offer:list', 'offer:view'])]
    private ?string $priceCurrency = null;

    #[ORM\OneToMany(targetEntity: OfferParameter::class, mappedBy: 'offer', orphanRemoval: true)]
    #[Groups(['offer:view'])]
    private Collection $offerParameters;

    public function __construct()
    {
        $this->offerPhotos = new ArrayCollection();
        $this->offerParameters = new ArrayCollection();
    }

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

    public function getLastSeenAt(): ?\DateTimeImmutable
    {
        return $this->lastSeenAt;
    }

    public function setLastSeenAt(\DateTimeImmutable $lastSeenAt): static
    {
        $this->lastSeenAt = $lastSeenAt;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): static
    {
        $this->url = $url;

        return $this;
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

    public function getRefreshedAt(): ?\DateTimeImmutable
    {
        return $this->refreshedAt;
    }

    public function setRefreshedAt(?\DateTimeImmutable $refreshedAt): static
    {
        $this->refreshedAt = $refreshedAt;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getValidTo(): ?\DateTimeImmutable
    {
        return $this->validTo;
    }

    public function setValidTo(\DateTimeImmutable $validTo): static
    {
        $this->validTo = $validTo;

        return $this;
    }

    /**
     * @return Collection<int, OfferPhoto>
     */
    public function getOfferPhotos(): Collection
    {
        return $this->offerPhotos;
    }

    public function addOfferPhoto(OfferPhoto $offerPhoto): static
    {
        if (!$this->offerPhotos->contains($offerPhoto)) {
            $this->offerPhotos->add($offerPhoto);
            $offerPhoto->setOffer($this);
        }

        return $this;
    }

    public function removeOfferPhoto(OfferPhoto $offerPhoto): static
    {
        if ($this->offerPhotos->removeElement($offerPhoto)) {
            // set the owning side to null (unless already changed)
            if ($offerPhoto->getOffer() === $this) {
                $offerPhoto->setOffer(null);
            }
        }

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(?int $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getPriceCurrency(): ?string
    {
        return $this->priceCurrency;
    }

    public function setPriceCurrency(?string $priceCurrency): static
    {
        $this->priceCurrency = $priceCurrency;

        return $this;
    }

    /**
     * @return Collection<int, OfferParameter>
     */
    public function getOfferParameters(): Collection
    {
        return $this->offerParameters;
    }

    public function addOfferParameter(OfferParameter $offerParameter): static
    {
        if (!$this->offerParameters->contains($offerParameter)) {
            $this->offerParameters->add($offerParameter);
            $offerParameter->setOffer($this);
        }

        return $this;
    }

    public function removeOfferParameter(OfferParameter $offerParameter): static
    {
        if ($this->offerParameters->removeElement($offerParameter)) {
            // set the owning side to null (unless already changed)
            if ($offerParameter->getOffer() === $this) {
                $offerParameter->setOffer(null);
            }
        }

        return $this;
    }
}
