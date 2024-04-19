<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\ApiResource\IntegrationType\Dto\IntegrationTypeCreateInput;
use App\ApiResource\IntegrationType\Dto\IntegrationTypeUpdateInput;
use App\ApiResource\IntegrationType\Processor\IntegrationTypeCreateInputProcessor;
use App\ApiResource\IntegrationType\Processor\IntegrationTypeUpdateInputProcessor;
use App\Repository\IntegrationTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: IntegrationTypeRepository::class)]
#[UniqueEntity(fields: 'integrationCode')]
#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/integration-type/{id}',
            normalizationContext: ['groups' => ['integration-type:view']],
            security: 'is_granted("'.User::ROLE_ADMIN.'") or is_granted("'.User::ROLE_USER.'")',
        ),
        new GetCollection(
            uriTemplate: '/integration-type',
            paginationEnabled: true,
            paginationItemsPerPage: 12,
            normalizationContext: ['groups' => ['integration-type:list']],
            security: 'is_granted("'.User::ROLE_ADMIN.'") or is_granted("'.User::ROLE_USER.'")',
        ),
        new Patch(
            uriTemplate: '/integration-type/{id}',
            normalizationContext: ['groups' => ['integration-type:view']],
            denormalizationContext: ['groups' => ['integration-type:write']],
            security: 'is_granted("'.User::ROLE_ADMIN.'")',
            input: IntegrationTypeUpdateInput::class,
            processor: IntegrationTypeUpdateInputProcessor::class
        ),
        new Post(
            uriTemplate: '/integration-type',
            normalizationContext: ['groups' => ['integration-type:view']],
            denormalizationContext: ['groups' => ['integration-type:write']],
            securityPostDenormalize: 'is_granted("'.User::ROLE_ADMIN.'")',
            input: IntegrationTypeCreateInput::class,
            processor: IntegrationTypeCreateInputProcessor::class
        )
    ],
    normalizationContext: ['groups' => ['integration-type:list', 'integration-type:view', 'integration-type:write'], 'enable_max_depth' => true],
    denormalizationContext: ['groups' => ['integration-type:write']],
    paginationClientEnabled: true,
    paginationClientItemsPerPage: true,
    paginationEnabled: true,
    paginationItemsPerPage: 12
)]
#[ApiFilter(SearchFilter::class, properties: [
    'name' => SearchFilter::STRATEGY_PARTIAL,
    'integrationCode' => SearchFilter::STRATEGY_EXACT
])]
#[ApiFilter(BooleanFilter::class, properties: ['enabled'])]
#[ApiFilter(OrderFilter::class, properties: ['id', 'name', 'integrationCode'], arguments: ['orderParameterName' => 'order'])]
class IntegrationType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups([
        'integration-type:list', 'integration-type:view',
        'user:view',
        'integration:view', 'integration:list',
        'notification:list', 'notification:view',
        'worker:view'
    ])]
    private ?int $id = null;

    #[ORM\Column(length: 60)]
    #[Groups([
        'integration-type:list', 'integration-type:view',
        'integration-type:write', 'user:view',
        'integration:view', 'integration:list',
        'notification:list', 'notification:view',
        'worker:view'
    ])]
    private ?string $name = null;

    #[ORM\Column(length: 60)]
    #[Groups([
        'integration-type:list', 'integration-type:view',
        'integration:view', 'integration:list',
        'notification:list', 'notification:view',
        'worker:view'
    ])]
    private ?string $integrationCode = null;

    #[ORM\Column]
    #[Groups(['integration-type:list', 'integration-type:view', 'integration-type:write'])]
    private ?bool $enabled = null;

    #[ORM\OneToMany(targetEntity: Integration::class, mappedBy: 'integrationType', orphanRemoval: true)]
    private Collection $integrations;

    #[ORM\Column]
    #[Groups(['integration-type:list', 'integration-type:view', 'integration-type:write'])]
    private array $locales = [];

    public function __construct()
    {
        $this->integrations = new ArrayCollection();
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

    public function getIntegrationCode(): ?string
    {
        return $this->integrationCode;
    }

    public function setIntegrationCode(string $integrationCode): static
    {
        $this->integrationCode = $integrationCode;

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

    /**
     * @return Collection<int, Integration>
     */
    public function getIntegrations(): Collection
    {
        return $this->integrations;
    }

    public function addIntegration(Integration $integration): static
    {
        if (!$this->integrations->contains($integration)) {
            $this->integrations->add($integration);
            $integration->setIntegrationType($this);
        }

        return $this;
    }

    public function removeIntegration(Integration $integration): static
    {
        if ($this->integrations->removeElement($integration)) {
            // set the owning side to null (unless already changed)
            if ($integration->getIntegrationType() === $this) {
                $integration->setIntegrationType(null);
            }
        }

        return $this;
    }

    public function getLocales(): array
    {
        return $this->locales;
    }

    public function setLocales(array $locales): static
    {
        $this->locales = $locales;

        return $this;
    }
}
