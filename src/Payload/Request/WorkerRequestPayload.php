<?php

namespace App\Payload\Request;

use App\Entity\Category;
use App\Entity\City;
use App\Entity\User;
use App\Validator\EntityExists;
use Symfony\Component\Validator\Constraints as Assert;

class WorkerRequestPayload
{

    #[EntityExists(identifier: 'id', entityClass: User::class)]
    #[Assert\Type('int')]
    private ?int $user = null;

    #[Assert\NotBlank]
    #[Assert\Type('int')]
    #[EntityExists(identifier: 'id', entityClass: City::class)]
    private ?int $city;

    #[Assert\NotBlank]
    #[Assert\Type('int')]
    #[EntityExists(identifier: 'id', entityClass: Category::class)]
    private ?int $firstSubcategory;

    #[EntityExists(identifier: 'id', entityClass: Category::class)]
    #[Assert\Type('int')]
    private ?int $secondSubcategory = null;

    #[Assert\NotBlank]
    #[Assert\Type('int')]
    private ?int $executionInterval;

    #[Assert\Type('bool')]
    private ?bool $isEnabled = null;

    #[Assert\Type('array')]
    private ?array $attributes = null;


    public function getUser(): ?int
    {
        return $this->user;
    }

    public function setUser(?int $user): static
    {
        $this->user = $user;
        return $this;
    }

    public function getCity(): ?int
    {
        return $this->city;
    }

    public function setCity(?int $city): static
    {
        $this->city = $city;
        return $this;
    }

    public function getFirstSubcategory(): ?int
    {
        return $this->firstSubcategory;
    }

    public function setFirstSubcategory(?int $firstSubcategory): static
    {
        $this->firstSubcategory = $firstSubcategory;
        return $this;
    }

    public function getSecondSubcategory(): ?int
    {
        return $this->secondSubcategory;
    }

    public function setSecondSubcategory(?int $secondSubcategory): static
    {
        $this->secondSubcategory = $secondSubcategory;
        return $this;
    }

    public function getExecutionInterval(): ?int
    {
        return $this->executionInterval;
    }

    public function setExecutionInterval(?int $executionInterval): static
    {
        $this->executionInterval = $executionInterval;
        return $this;
    }

    public function getIsEnabled(): ?bool
    {
        return $this->isEnabled;
    }

    public function setIsEnabled(?bool $isEnabled): static
    {
        $this->isEnabled = $isEnabled;
        return $this;
    }

    public function getAttributes(): ?array
    {
        return $this->attributes;
    }

    public function setAttributes(?array $attributes): static
    {
        $this->attributes = $attributes;
        return $this;
    }

}