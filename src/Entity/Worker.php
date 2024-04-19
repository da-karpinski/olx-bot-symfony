<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\ApiResource\Worker\Dto\WorkerCreateInput;
use App\ApiResource\Worker\Dto\WorkerUpdateInput;
use App\ApiResource\Worker\Processor\WorkerCreateInputProcessor;
use App\ApiResource\Worker\Processor\WorkerUpdateInputProcessor;
use App\Repository\WorkerRepository;
use App\Security\Voter\WorkerVoter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: WorkerRepository::class)]
#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/worker/{id}',
            normalizationContext: ['groups' => ['worker:view']],
            security: 'is_granted("'.WorkerVoter::WORKER_GET.'", object)',
        ),
        new GetCollection(
            uriTemplate: '/worker',
            paginationEnabled: true,
            paginationItemsPerPage: 12,
            normalizationContext: ['groups' => ['worker:list']],
            security: 'is_granted("'.User::ROLE_ADMIN.'") or is_granted("'.User::ROLE_USER.'")',
        ),
        new Patch(
            uriTemplate: '/worker/{id}',
            normalizationContext: ['groups' => ['worker:view']],
            denormalizationContext: ['groups' => ['worker:write']],
            security: 'is_granted("'.WorkerVoter::WORKER_EDIT.'", object)',
            input: WorkerUpdateInput::class,
            processor: WorkerUpdateInputProcessor::class
        ),
        new Post(
            uriTemplate: '/worker',
            normalizationContext: ['groups' => ['worker:view']],
            denormalizationContext: ['groups' => ['worker:write']],
            securityPostDenormalize: 'is_granted("'.User::ROLE_ADMIN.'") or is_granted("'.User::ROLE_USER.'")',
            input: WorkerCreateInput::class,
            processor: WorkerCreateInputProcessor::class
        ),
        new Delete(
            uriTemplate: '/worker/{id}',
            security: 'is_granted("'.WorkerVoter::WORKER_DELETE.'", object)',
        )
    ],
    normalizationContext: ['groups' => ['worker:list', 'worker:view', 'worker:write'], 'enable_max_depth' => true],
    denormalizationContext: ['groups' => ['worker:write']],
    paginationClientEnabled: true,
    paginationClientItemsPerPage: true,
    paginationEnabled: true,
    paginationItemsPerPage: 12
)]
#[ApiFilter(SearchFilter::class, properties: [
    'name' => SearchFilter::STRATEGY_PARTIAL,
    'user.id' => SearchFilter::STRATEGY_EXACT,
    'city.id' => SearchFilter::STRATEGY_EXACT,
    'city.name' => SearchFilter::STRATEGY_PARTIAL,
    'category.id' => SearchFilter::STRATEGY_EXACT,
    'category.name' => SearchFilter::STRATEGY_PARTIAL,
    'workerIntegrations.integration.id' => SearchFilter::STRATEGY_EXACT,
    'workerIntegrations.integration.name' => SearchFilter::STRATEGY_PARTIAL,
    'workerIntegrations.integrationType.id' => SearchFilter::STRATEGY_EXACT,
    'workerIntegrations.integrationType.name' => SearchFilter::STRATEGY_PARTIAL,
])]
#[ApiFilter(BooleanFilter::class, properties: ['enabled'])]
#[ApiFilter(OrderFilter::class, properties: [
    'id',
    'name',
    'createdAt',
    'lastExecutedAt',
    'executionInterval'
])]
class Worker
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups([
        'worker:view', 'worker:list',
        'user:view',
        'offer:view', 'offer:list',
        'integration:view',
        'notification:list', 'notification:view'
    ])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'workers')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['worker:view', 'worker:list'])]
    private ?User $user = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['worker:view', 'worker:list'])]
    private ?City $city = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['worker:view', 'worker:list'])]
    private ?Category $category = null;

    #[ORM\Column]
    #[Groups([
        'worker:view', 'worker:list',
        'user:view'
    ])]
    private ?bool $enabled = null;

    #[ORM\Column]
    #[Groups(['worker:view', 'worker:list'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['worker:view', 'worker:list'])]
    private ?\DateTimeImmutable $lastExecutedAt = null;

    #[ORM\Column]
    #[Groups(['worker:view', 'worker:list'])]
    private ?int $executionInterval = null;

    #[ORM\OneToMany(targetEntity: CategoryAttribute::class, mappedBy: 'worker', orphanRemoval: true)]
    #[Groups(['worker:view'])]
    private Collection $categoryAttributes;

    #[ORM\OneToMany(targetEntity: Offer::class, mappedBy: 'worker', orphanRemoval: true)]
    private Collection $offers;

    #[ORM\OneToMany(targetEntity: WorkerIntegration::class, mappedBy: 'worker')]
    #[Groups(['worker:view'])]
    private Collection $workerIntegrations;

    #[ORM\OneToMany(targetEntity: Notification::class, mappedBy: 'worker')]
    private Collection $notifications;

    #[ORM\Column(length: 100)]
    #[Groups([
        'worker:view', 'worker:list',
        'user:view',
        'offer:view', 'offer:list',
        'integration:view',
        'notification:list', 'notification:view'
    ])]
    private ?string $name = null;

    public function __construct()
    {
        $this->categoryAttributes = new ArrayCollection();
        $this->offers = new ArrayCollection();
        $this->workerIntegrations = new ArrayCollection();
        $this->notifications = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getCity(): ?City
    {
        return $this->city;
    }

    public function setCity(?City $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getLastExecutedAt(): ?\DateTimeImmutable
    {
        return $this->lastExecutedAt;
    }

    public function setLastExecutedAt(\DateTimeImmutable $lastExecutedAt): static
    {
        $this->lastExecutedAt = $lastExecutedAt;

        return $this;
    }

    public function getExecutionInterval(): ?int
    {
        return $this->executionInterval;
    }

    public function setExecutionInterval(int $executionInterval): static
    {
        $this->executionInterval = $executionInterval;

        return $this;
    }

    /**
     * @return Collection<int, CategoryAttribute>
     */
    public function getCategoryAttributes(): Collection
    {
        return $this->categoryAttributes;
    }

    public function addCategoryAttribute(CategoryAttribute $categoryAttribute): static
    {
        if (!$this->categoryAttributes->contains($categoryAttribute)) {
            $this->categoryAttributes->add($categoryAttribute);
            $categoryAttribute->setWorker($this);
        }

        return $this;
    }

    public function removeCategoryAttribute(CategoryAttribute $categoryAttribute): static
    {
        if ($this->categoryAttributes->removeElement($categoryAttribute)) {
            // set the owning side to null (unless already changed)
            if ($categoryAttribute->getWorker() === $this) {
                $categoryAttribute->setWorker(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Offer>
     */
    public function getOffers(): Collection
    {
        return $this->offers;
    }

    public function addOffer(Offer $offer): static
    {
        if (!$this->offers->contains($offer)) {
            $this->offers->add($offer);
            $offer->setWorker($this);
        }

        return $this;
    }

    public function removeOffer(Offer $offer): static
    {
        if ($this->offers->removeElement($offer)) {
            // set the owning side to null (unless already changed)
            if ($offer->getWorker() === $this) {
                $offer->setWorker(null);
            }
        }

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
            $workerIntegration->setWorker($this);
        }

        return $this;
    }

    public function removeWorkerIntegration(WorkerIntegration $workerIntegration): static
    {
        if ($this->workerIntegrations->removeElement($workerIntegration)) {
            // set the owning side to null (unless already changed)
            if ($workerIntegration->getWorker() === $this) {
                $workerIntegration->setWorker(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Notification>
     */
    public function getNotifications(): Collection
    {
        return $this->notifications;
    }

    public function addNotification(Notification $notification): static
    {
        if (!$this->notifications->contains($notification)) {
            $this->notifications->add($notification);
            $notification->setWorker($this);
        }

        return $this;
    }

    public function removeNotification(Notification $notification): static
    {
        if ($this->notifications->removeElement($notification)) {
            // set the owning side to null (unless already changed)
            if ($notification->getWorker() === $this) {
                $notification->setWorker(null);
            }
        }

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
