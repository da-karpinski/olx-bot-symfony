<?php

namespace App\Entity;

use App\Repository\OfferPhotoRepository;
use Doctrine\DBAL\Types\Types;
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

    #[ORM\Column(type: Types::BIGINT)]
    private ?string $olxId = null;

    #[ORM\Column]
    private ?int $photoOrder = null;

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

    public function getOlxId(): ?string
    {
        return $this->olxId;
    }

    public function setOlxId(string $olxId): static
    {
        $this->olxId = $olxId;

        return $this;
    }

    public function getPhotoOrder(): ?int
    {
        return $this->photoOrder;
    }

    public function setPhotoOrder(int $photoOrder): static
    {
        $this->photoOrder = $photoOrder;

        return $this;
    }
}
