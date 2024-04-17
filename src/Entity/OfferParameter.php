<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use App\ApiResource\OfferParameter\Provider\GetOfferParametersForOfferProvider;
use App\Repository\OfferParameterRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: OfferParameterRepository::class)]
#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/offer-parameter/{id}', //offer ID required
            requirements: ['id' => '\d+'],
            normalizationContext: ['groups' => ['offer-parameter:list'], 'skip_null_values' => false],
            security: 'is_granted("'.User::ROLE_ADMIN.'") or is_granted("'.User::ROLE_USER.'")',
            provider: GetOfferParametersForOfferProvider::class
        ),
    ],
    normalizationContext: ['groups' => ['offer-parameter:list'], 'enable_max_depth' => true],
    paginationClientEnabled: true,
    paginationClientItemsPerPage: true,
    paginationEnabled: true,
    paginationItemsPerPage: 12,
)]
class OfferParameter
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['offer:view', 'offer-parameter:list'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'offerParameters')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Offer $offer = null;

    #[ORM\Column(length: 60)]
    #[Groups(['offer:view', 'offer-parameter:list'])]
    private ?string $parameterKey = null;

    #[ORM\Column(length: 60)]
    #[Groups(['offer:view', 'offer-parameter:list'])]
    private ?string $parameterName = null;

    #[ORM\Column(length: 60)]
    #[Groups(['offer:view', 'offer-parameter:list'])]
    private ?string $valueKey = null;

    #[ORM\Column(length: 60)]
    #[Groups(['offer:view', 'offer-parameter:list'])]
    private ?string $valueLabel = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getParameterKey(): ?string
    {
        return $this->parameterKey;
    }

    public function setParameterKey(string $parameterKey): static
    {
        $this->parameterKey = $parameterKey;

        return $this;
    }

    public function getParameterName(): ?string
    {
        return $this->parameterName;
    }

    public function setParameterName(string $parameterName): static
    {
        $this->parameterName = $parameterName;

        return $this;
    }

    public function getValueKey(): ?string
    {
        return $this->valueKey;
    }

    public function setValueKey(string $valueKey): static
    {
        $this->valueKey = $valueKey;

        return $this;
    }

    public function getValueLabel(): ?string
    {
        return $this->valueLabel;
    }

    public function setValueLabel(string $valueLabel): static
    {
        $this->valueLabel = $valueLabel;

        return $this;
    }
}
