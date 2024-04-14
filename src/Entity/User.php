<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\ApiResource\User\Dto\UserCreateInput;
use App\ApiResource\User\Dto\UserUpdateInput;
use App\ApiResource\User\Processor\UserCreateInputProcessor;
use App\ApiResource\User\Processor\UserUpdateInputProcessor;
use App\ApiResource\User\Provider\UserGetMeProvider;
use App\Repository\UserRepository;
use App\Security\Voter\UserVoter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/user/me',
            normalizationContext: ['groups' => ['user:view']],
            security: 'is_granted("'.self::ROLE_ADMIN.'") or is_granted("'.self::ROLE_USER.'")',
            provider: UserGetMeProvider::class,
        ),
        new Get(
            uriTemplate: '/user/{id}',
            normalizationContext: ['groups' => ['user:view']],
            security: 'is_granted("'.UserVoter::USER_GET.'", object)',
        ),
        new GetCollection(
            uriTemplate: '/user',
            paginationEnabled: true,
            paginationItemsPerPage: 12,
            normalizationContext: ['groups' => ['user:list']],
            security: 'is_granted("'.self::ROLE_ADMIN.'")',
        ),
        new Patch(
            uriTemplate: '/user/{id}',
            normalizationContext: ['groups' => ['user:view']],
            denormalizationContext: ['groups' => ['user:write']],
            security: 'is_granted("'.UserVoter::USER_EDIT.'", object)',
            input: UserUpdateInput::class,
            processor: UserUpdateInputProcessor::class
        ),
        new Post(
            uriTemplate: '/user',
            normalizationContext: ['groups' => ['user:view']],
            denormalizationContext: ['groups' => ['user:write']],
            securityPostDenormalize: 'is_granted("'.UserVoter::USER_CREATE.'",object)',
            input: UserCreateInput::class,
            processor: UserCreateInputProcessor::class
        ),
        new Delete(
            uriTemplate: '/user/{id}',
            security: 'is_granted("'.UserVoter::USER_DELETE.'", object)',
        )
    ],
    normalizationContext: ['groups' => ['user:list', 'user:view', 'user:write'], 'enable_max_depth' => true],
    denormalizationContext: ['groups' => ['user:write']],
    paginationClientEnabled: true,
    paginationClientItemsPerPage: true,
    paginationEnabled: true,
    paginationItemsPerPage: 12
)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    const ROLE_ADMIN = 'ROLE_ADMIN';
    const ROLE_USER = 'ROLE_USER';

    const ROLES = [self::ROLE_ADMIN, self::ROLE_USER];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user:list', 'user:view', 'user:write'])]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    #[Groups(['user:list', 'user:view', 'user:write'])]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    #[Groups(['user:list', 'user:view', 'user:write'])]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    #[Groups(['user:write'])]
    private ?string $password = null;

    #[ORM\Column(length: 100)]
    #[Groups(['user:list', 'user:view', 'user:write'])]
    private ?string $name = null;

    #[ORM\OneToMany(targetEntity: Worker::class, mappedBy: 'user', orphanRemoval: true)]
    #[Groups(['user:view'])]
    private Collection $workers;

    #[ORM\OneToMany(targetEntity: Integration::class, mappedBy: 'user', orphanRemoval: true)]
    #[Groups(['user:view'])]
    private Collection $integrations;

    public function __construct()
    {
        $this->workers = new ArrayCollection();
        $this->integrations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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

    /**
     * @return Collection<int, Worker>
     */
    public function getWorkers(): Collection
    {
        return $this->workers;
    }

    public function addWorker(Worker $worker): static
    {
        if (!$this->workers->contains($worker)) {
            $this->workers->add($worker);
            $worker->setUser($this);
        }

        return $this;
    }

    public function removeWorker(Worker $worker): static
    {
        if ($this->workers->removeElement($worker)) {
            // set the owning side to null (unless already changed)
            if ($worker->getUser() === $this) {
                $worker->setUser(null);
            }
        }

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
            $integration->setUser($this);
        }

        return $this;
    }

    public function removeIntegration(Integration $integration): static
    {
        if ($this->integrations->removeElement($integration)) {
            // set the owning side to null (unless already changed)
            if ($integration->getUser() === $this) {
                $integration->setUser(null);
            }
        }

        return $this;
    }
}
