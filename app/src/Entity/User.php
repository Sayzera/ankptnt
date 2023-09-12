<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $col_expiry_date = null;

    #[ORM\Column(nullable: true)]
    private ?bool $col_is_customer_representative = null;

    #[ORM\Column(nullable: true)]
    private ?bool $col_is_deleted = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $col_last_login = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $col_level = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $col_name = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $col_startingof_employment = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $col_surname = null;

    #[ORM\Column(nullable: true)]
    private ?int $col_workgroup_id = null;

    #[ORM\Column(nullable: true)]
    private ?int $col_department_id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $col_username = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $col_unix_username = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $col_first_page = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $col_registration_number = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $col_watch_auth = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $col_is_working_on = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $col_expiry_date_watch = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: ObservationCache::class)]
    private Collection $observationCaches;

    public function __construct()
    {
        $this->observationCaches = new ArrayCollection();
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
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

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

    public function getColExpiryDate(): ?string
    {
        return $this->col_expiry_date;
    }

    public function setColExpiryDate(?string $col_expiry_date): static
    {
        $this->col_expiry_date = $col_expiry_date;

        return $this;
    }

    public function isColIsCustomerRepresentative(): ?bool
    {
        return $this->col_is_customer_representative;
    }

    public function setColIsCustomerRepresentative(?bool $col_is_customer_representative): static
    {
        $this->col_is_customer_representative = $col_is_customer_representative;

        return $this;
    }

    public function isColIsDeleted(): ?bool
    {
        return $this->col_is_deleted;
    }

    public function setColIsDeleted(?bool $col_is_deleted): static
    {
        $this->col_is_deleted = $col_is_deleted;

        return $this;
    }

    public function getColLastLogin(): ?string
    {
        return $this->col_last_login;
    }

    public function setColLastLogin(?string $col_last_login): static
    {
        $this->col_last_login = $col_last_login;

        return $this;
    }

    public function getColLevel(): ?string
    {
        return $this->col_level;
    }

    public function setColLevel(?string $col_level): static
    {
        $this->col_level = $col_level;

        return $this;
    }

    public function getColName(): ?string
    {
        return $this->col_name;
    }

    public function setColName(?string $col_name): static
    {
        $this->col_name = $col_name;

        return $this;
    }

    public function getColStartingofEmployment(): ?string
    {
        return $this->col_startingof_employment;
    }

    public function setColStartingofEmployment(?string $col_startingof_employment): static
    {
        $this->col_startingof_employment = $col_startingof_employment;

        return $this;
    }

    public function getColSurname(): ?string
    {
        return $this->col_surname;
    }

    public function setColSurname(?string $col_surname): static
    {
        $this->col_surname = $col_surname;

        return $this;
    }

    public function getColWorkgroupId(): ?int
    {
        return $this->col_workgroup_id;
    }

    public function setColWorkgroupId(?int $col_workgroup_id): static
    {
        $this->col_workgroup_id = $col_workgroup_id;

        return $this;
    }

    public function getColDepartmentId(): ?int
    {
        return $this->col_department_id;
    }

    public function setColDepartmentId(?int $col_department_id): static
    {
        $this->col_department_id = $col_department_id;

        return $this;
    }

    public function getColUsername(): ?string
    {
        return $this->col_username;
    }

    public function setColUsername(?string $col_username): static
    {
        $this->col_username = $col_username;

        return $this;
    }

    public function getColUnixUsername(): ?string
    {
        return $this->col_unix_username;
    }

    public function setColUnixUsername(?string $col_unix_username): static
    {
        $this->col_unix_username = $col_unix_username;

        return $this;
    }

    public function getColFirstPage(): ?string
    {
        return $this->col_first_page;
    }

    public function setColFirstPage(?string $col_first_page): static
    {
        $this->col_first_page = $col_first_page;

        return $this;
    }

    public function getColRegistrationNumber(): ?string
    {
        return $this->col_registration_number;
    }

    public function setColRegistrationNumber(?string $col_registration_number): static
    {
        $this->col_registration_number = $col_registration_number;

        return $this;
    }

    public function getColWatchAuth(): ?string
    {
        return $this->col_watch_auth;
    }

    public function setColWatchAuth(?string $col_watch_auth): static
    {
        $this->col_watch_auth = $col_watch_auth;

        return $this;
    }

    public function getColIsWorkingOn(): ?string
    {
        return $this->col_is_working_on;
    }

    public function setColIsWorkingOn(?string $col_is_working_on): static
    {
        $this->col_is_working_on = $col_is_working_on;

        return $this;
    }

    public function getColExpiryDateWatch(): ?string
    {
        return $this->col_expiry_date_watch;
    }

    public function setColExpiryDateWatch(?string $col_expiry_date_watch): static
    {
        $this->col_expiry_date_watch = $col_expiry_date_watch;

        return $this;
    }

    /**
     * @return Collection<int, ObservationCache>
     */
    public function getObservationCaches(): Collection
    {
        return $this->observationCaches;
    }

    public function addObservationCache(ObservationCache $observationCache): static
    {
        if (!$this->observationCaches->contains($observationCache)) {
            $this->observationCaches->add($observationCache);
            $observationCache->setUser($this);
        }

        return $this;
    }

    public function removeObservationCache(ObservationCache $observationCache): static
    {
        if ($this->observationCaches->removeElement($observationCache)) {
            // set the owning side to null (unless already changed)
            if ($observationCache->getUser() === $this) {
                $observationCache->setUser(null);
            }
        }

        return $this;
    }
}
