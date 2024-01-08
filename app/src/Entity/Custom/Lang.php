<?php

namespace App\Entity\Custom;

use App\Repository\Custom\LangRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LangRepository::class)]
class Lang
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updated_at = null;

    #[ORM\Column(nullable: true)]
    private ?bool $status = null;


    #[ORM\OneToMany(mappedBy: 'lang', targetEntity: LangMessages::class)]
    private Collection $langMessages;

    public function __toString(): string
    {
        // TODO: Implement __toString() method.
        return $this->getName() ?? '';
    }

    public function __construct()
    {
        $this->langMessages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(?\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updated_at): static
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function isStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(?bool $status): static
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection<int, LangMessages>
     */
    public function getLangMessages(): Collection
    {
        return $this->langMessages;
    }

    public function addLangMessage(LangMessages $langMessage): static
    {
        if (!$this->langMessages->contains($langMessage)) {
            $this->langMessages->add($langMessage);
            $langMessage->setLang($this);
        }

        return $this;
    }

    public function removeLangMessage(LangMessages $langMessage): static
    {
        if ($this->langMessages->removeElement($langMessage)) {
            // set the owning side to null (unless already changed)
            if ($langMessage->getLang() === $this) {
                $langMessage->setLang(null);
            }
        }

        return $this;
    }
}
