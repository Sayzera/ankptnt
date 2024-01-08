<?php

namespace App\Entity;

use App\Repository\ObservationCacheMainListRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ObservationCacheMainListRepository::class)]
class ObservationCacheMainList
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $searchedWord = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $niceClasses = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $bulletinNo = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $ref_account_id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $trademark_id = null;

    #[ORM\Column(nullable: true)]
    private ?bool $yim_marka = false;

    #[ORM\Column(nullable: true)]
    private ?bool $yda_marka = false;

    #[ORM\Column(nullable: true)]
    private ?string $company_email = null;

    #[ORM\Column(nullable: true)]
    private ?bool $is_foreign_company = null;

    #[ORM\Column(nullable: true)]
    private ?bool $is_completed = null;


    #[ORM\OneToMany(mappedBy: 'observationCacheMainList', targetEntity: ObservationCache::class)]
    private Collection $observation_cache;

    public function __construct()
    {
        $this->observation_cache = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSearchedWord(): ?string
    {
        return $this->searchedWord;
    }

    public function setSearchedWord(?string $searchedWord): static
    {
        $this->searchedWord = $searchedWord;

        return $this;
    }

    public function getNiceClasses(): ?string
    {
        return $this->niceClasses;
    }

    public function setNiceClasses(?string $niceClasses): static
    {
        $this->niceClasses = $niceClasses;

        return $this;
    }

    public function getBulletinNo(): ?string
    {
        return $this->bulletinNo;
    }

    public function setBulletinNo(?string $bulletinNo): static
    {
        $this->bulletinNo = $bulletinNo;

        return $this;
    }

    /**
     * @return Collection<int, ObservationCache>
     */
    public function getObservationCache(): Collection
    {
        return $this->observation_cache;
    }

    public function addObservationCache(ObservationCache $observationCache): static
    {
        if (!$this->observation_cache->contains($observationCache)) {
            $this->observation_cache->add($observationCache);
            $observationCache->setObservationCacheMainList($this);
        }

        return $this;
    }

    public function removeObservationCache(ObservationCache $observationCache): static
    {
        if ($this->observation_cache->removeElement($observationCache)) {
            // set the owning side to null (unless already changed)
            if ($observationCache->getObservationCacheMainList() === $this) {
                $observationCache->setObservationCacheMainList(null);
            }
        }

        return $this;
    }

    /**
     * Get the value of refAccountId
     *
     * @return ?string
     */
    public function getRefAccountId(): ?string
    {
        return $this->ref_account_id;
    }

    /**
     * Set the value of refAccountId
     *
     * @param ?string $refAccountId
     *
     * @return self
     */
    public function setRefAccountId(?string $refAccountId): self
    {
        $this->ref_account_id = $refAccountId;

        return $this;
    }

    /**
     * Get the value of trademarkId
     *
     * @return ?string
     */
    public function getTrademarkId(): ?string
    {
        return $this->trademark_id;
    }

    /**
     * Set the value of trademarkId
     *
     * @param ?string $trademarkId
     *
     * @return self
     */
    public function setTrademarkId(?string $trademarkId): self
    {
        $this->trademark_id = $trademarkId;

        return $this;
    }

    /**
     * Get the value of yim_marka
     *
     * @return ?bool
     */
    public function getYimMarka(): ?bool
    {
        return $this->yim_marka;
    }

    /**
     * Set the value of yim_marka
     *
     * @param ?bool $yim_marka
     *
     * @return self
     */
    public function setYimMarka(?bool $yim_marka): self
    {
        $this->yim_marka = $yim_marka;

        return $this;
    }

    /**
     * Get the value of yda_marka
     *
     * @return ?bool
     */
    public function getYdaMarka(): ?bool
    {
        return $this->yda_marka;
    }

    /**
     * Set the value of yda_marka
     *
     * @param ?bool $yda_marka
     *
     * @return self
     */
    public function setYdaMarka(?bool $yda_marka): self
    {
        $this->yda_marka = $yda_marka;

        return $this;
    }

    /**
     * Get the value of company_email
     *
     * @return ?string
     */
    public function getCompanyEmail(): ?string
    {
        return $this->company_email;
    }

    /**
     * Set the value of company_email
     *
     * @param ?string $company_email
     *
     * @return self
     */
    public function setCompanyEmail(?string $company_email): self
    {
        $this->company_email = $company_email;

        return $this;
    }

    /**
     * Get the value of is_foreign_company
     *
     * @return ?bool
     */
    public function getIsForeignCompany(): ?bool
    {
        return $this->is_foreign_company;
    }

    /**
     * Set the value of is_foreign_company
     *
     * @param ?bool $is_foreign_company
     *
     * @return self
     */
    public function setIsForeignCompany(?bool $is_foreign_company): self
    {
        $this->is_foreign_company = $is_foreign_company;

        return $this;
    }

    /**
     * Get the value of is_completed
     *
     * @return ?bool
     */
    public function getIsCompleted(): ?bool
    {
        return $this->is_completed;
    }

    /**
     * Set the value of is_completed
     *
     * @param ?bool $is_completed
     *
     * @return self
     */
    public function setIsCompleted(?bool $is_completed): self
    {
        $this->is_completed = $is_completed;

        return $this;
    }
}
