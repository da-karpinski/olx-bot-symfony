<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\ApiResource\Integration\Dto\IntegrationCreateInput;
use App\ApiResource\Integration\Dto\IntegrationUpdateInput;
use App\ApiResource\Integration\Processor\IntegrationCreateInputProcessor;
use App\ApiResource\Integration\Processor\IntegrationUpdateInputProcessor;
use App\Repository\IntegrationRepository;
use App\Security\Voter\IntegrationVoter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: IntegrationRepository::class)]
#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/integration/{id}',
            normalizationContext: ['groups' => ['integration:view']],
            security: 'is_granted("'.IntegrationVoter::INTEGRATION_GET.'", object)',
        ),
        new GetCollection(
            uriTemplate: '/integration',
            paginationEnabled: true,
            paginationItemsPerPage: 12,
            normalizationContext: ['groups' => ['integration:list']],
            security: 'is_granted("'.User::ROLE_ADMIN.'") or is_granted("'.User::ROLE_USER.'")',
        ),
        new Patch(
            uriTemplate: '/integration/{id}',
            normalizationContext: ['groups' => ['integration:view']],
            denormalizationContext: ['groups' => ['integration:write']],
            security: 'is_granted("'.IntegrationVoter::INTEGRATION_EDIT.'", object)',
            input: IntegrationUpdateInput::class,
            processor: IntegrationUpdateInputProcessor::class
        ),
        new Post(
            uriTemplate: '/integration',
            normalizationContext: ['groups' => ['integration:view']],
            denormalizationContext: ['groups' => ['integration:write']],
            securityPostDenormalize: 'is_granted("'.IntegrationVoter::INTEGRATION_CREATE.'", object)',
            input: IntegrationCreateInput::class,
            processor: IntegrationCreateInputProcessor::class
        ),
        new Delete(
            uriTemplate: '/integration/{id}',
            security: 'is_granted("'.IntegrationVoter::INTEGRATION_DELETE.'", object)',
        )
    ],
    normalizationContext: ['groups' => ['integration:list', 'integration:view', 'integration:write'], 'enable_max_depth' => true],
    denormalizationContext: ['groups' => ['integration:write']],
    paginationClientEnabled: true,
    paginationClientItemsPerPage: true,
    paginationEnabled: true,
    paginationItemsPerPage: 12
)]
#[ApiFilter(SearchFilter::class, properties: [
    'name' => SearchFilter::STRATEGY_PARTIAL,
    'integrationType.name' => SearchFilter::STRATEGY_PARTIAL,
    'integrationType.integrationCode' => SearchFilter::STRATEGY_EXACT,
])]
#[ApiFilter(OrderFilter::class, properties: [
    'id',
    'name',
    'createdAt',
    'integrationType.name',
    'integrationType.code',
])]
class Integration
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups([
        'user:view',
        'integration:view', 'integration:list',
        'notification:list', 'notification:view',
        'worker:view'
    ])]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Groups([
        'user:view',
        'integration:view', 'integration:list',
        'notification:list', 'notification:view',
        'worker:view'
    ])]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'integrations')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['integration:view'])]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'integrations')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups([
        'user:view',
        'integration:view', 'integration:list',
        'notification:list', 'notification:view',
        'worker:view'
    ])]
    private ?IntegrationType $integrationType = null;

    #[ORM\Column]
    #[Groups(['user:list', 'user:view', 'integration:view', 'integration:list'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\OneToMany(targetEntity: WorkerIntegration::class, mappedBy: 'integration')]
    #[Groups(['integration:view'])]
    private Collection $workerIntegrations;

    #[ORM\Column(length: 2)]
    #[Groups(['integration:view', 'integration:list'])]
    private ?string $localeCode = null;

    public function __construct()
    {
        $this->workerIntegrations = new ArrayCollection();
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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getIntegrationType(): ?IntegrationType
    {
        return $this->integrationType;
    }

    public function setIntegrationType(?IntegrationType $integrationType): static
    {
        $this->integrationType = $integrationType;

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
            $workerIntegration->setIntegration($this);
        }

        return $this;
    }

    public function removeWorkerIntegration(WorkerIntegration $workerIntegration): static
    {
        if ($this->workerIntegrations->removeElement($workerIntegration)) {
            // set the owning side to null (unless already changed)
            if ($workerIntegration->getIntegration() === $this) {
                $workerIntegration->setIntegration(null);
            }
        }

        return $this;
    }

    public function getLocaleCode(): ?string
    {
        return $this->localeCode;
    }

    public function setLocaleCode(string $localeCode): static
    {
        $this->localeCode = $localeCode;

        return $this;
    }
}
