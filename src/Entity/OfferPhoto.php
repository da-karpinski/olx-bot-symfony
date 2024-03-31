<?php

namespace App\Entity;

use App\Repository\OfferPhotoRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OfferPhotoRepository::class)]
class OfferPhoto
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'offerPhotos')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Offer $offer = null;

    #[ORM\Column(length: 255)]
    private ?string $fileName = null;

    #[ORM\Column(length: 255)]
    private ?string $realFileName = null;

    #[ORM\Column]
    private ?int $olxId = null;

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

    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    public function setFileName(string $fileName): static
    {
        $this->fileName = $fileName;

        return $this;
    }

    public function getRealFileName(): ?string
    {
        return $this->realFileName;
    }

    public function setRealFileName(string $realFileName): static
    {
        $this->realFileName = $realFileName;

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
}
