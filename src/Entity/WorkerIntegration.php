<?php

namespace App\Entity;

use App\Repository\WorkerIntegrationRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: WorkerIntegrationRepository::class)]
class WorkerIntegration
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups([
        'integration:view',
        'worker:view'
    ])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'workerIntegrations')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['integration:view'])]
    private ?Worker $worker = null;

    #[ORM\ManyToOne(inversedBy: 'workerIntegrations')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['worker:view'])]
    private ?Integration $integration = null;

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

    public function getIntegration(): ?Integration
    {
        return $this->integration;
    }

    public function setIntegration(?Integration $integration): static
    {
        $this->integration = $integration;

        return $this;
    }
}
