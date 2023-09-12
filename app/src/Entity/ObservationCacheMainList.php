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
}
