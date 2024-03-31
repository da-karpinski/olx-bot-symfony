<?php

namespace App\Entity;

use App\Repository\CategoryAttributeRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CategoryAttributeRepository::class)]
class CategoryAttribute
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['worker:write'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'categoryAttributes', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Worker $worker = null;

    #[ORM\Column(length: 60)]
    #[Groups(['worker:write'])]
    private ?string $attributeCode = null;

    #[ORM\Column(length: 60)]
    #[Groups(['worker:write'])]
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
