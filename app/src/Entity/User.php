<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'tbl_user', schema: 'public')]

class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $col_id = null;

    #[ORM\Column(nullable: true)]
    private ?bool $col_is_deleted = null;

    #[ORM\Column(length: 255, unique: true)]
    private string $col_email;

    #[ORM\Column(length: 30)]
    private string $col_name;

    #[ORM\Column(length: 30)]
    private string $col_surname;

    #[ORM\Column]
    private ?string $col_password = null;

    #[ORM\Column(length: 255)]
    private string $col_username;

    public function getColId(): ?int
    {
        return $this->col_id;
    }

    public function setColId(?int $col_id): void
    {
        $this->col_id = $col_id;
    }

    public function getColIsDeleted(): ?bool
    {
        return $this->col_is_deleted;
    }

    public function setColIsDeleted(?bool $col_is_deleted): void
    {
        $this->col_is_deleted = $col_is_deleted;
    }

    public function getColEmail(): string
    {
        return $this->col_email;
    }

    public function setColEmail(string $col_email): void
    {
        $this->col_email = $col_email;
    }

    public function getColName(): string
    {
        return $this->col_name;
    }

    public function setColName(string $col_name): void
    {
        $this->col_name = $col_name;
    }

    public function getColSurname(): string
    {
        return $this->col_surname;
    }

    public function setColSurname(string $col_surname): void
    {
        $this->col_surname = $col_surname;
    }

    public function getColPassword(): ?string
    {
        return $this->col_password;
    }

    public function setColPassword(?string $col_password): void
    {
        $this->col_password = $col_password;
    }

    public function getColUsername(): string
    {
        return $this->col_username;
    }

    public function setColUsername(string $col_username): void
    {
        $this->col_username = $col_username;
    }

    // get 


    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getUserIdentifier(): string
    {
        return $this->col_username;
    }

    public function getPassword(): ?string
    {
        return $this->col_password;
    }
}
