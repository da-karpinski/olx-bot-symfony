<?php

namespace App\Entity;

use App\Repository\WorkerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WorkerRepository::class)]
class Worker
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'workers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?City $city = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Category $category = null;

    #[ORM\Column]
    private ?bool $enabled = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $lastExecutedAt = null;

    #[ORM\Column]
    private ?int $executionInterval = null;

    #[ORM\OneToMany(targetEntity: CategoryAttribute::class, mappedBy: 'worker', orphanRemoval: true)]
    private Collection $categoryAttributes;

    public function __construct()
    {
        $this->categoryAttributes = new ArrayCollection();
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
}
