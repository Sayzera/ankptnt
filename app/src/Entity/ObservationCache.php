<?php

namespace App\Entity;

use App\Repository\ObservationCacheRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ObservationCacheRepository::class)]
class ObservationCache
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $dataSource = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $searchedWord = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $searchedWordHtml = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $trademarkName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $trademarkNameHtml = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $niceClasses = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $applicationNo = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $applicationDate = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $registerDate = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $protectionDate = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $holderName = null;

    #[ORM\Column(nullable: true)]
    private ?int $bulletinNo = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $bulletinPage = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $fileStatus = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $shapeSimilarity = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $phoneticSimilarity = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $isPriority = null;

    #[ORM\ManyToOne(inversedBy: 'observationCaches')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'col_id')]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'observation_cache')]
    private ?ObservationCacheMainList $observationCacheMainList = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDataSource(): ?string
    {
        return $this->dataSource;
    }

    public function setDataSource(?string $dataSource): static
    {
        $this->dataSource = $dataSource;

        return $this;
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

    public function getSearchedWordHtml(): ?string
    {
        return $this->searchedWordHtml;
    }

    public function setSearchedWordHtml(?string $searchedWordHtml): static
    {
        $this->searchedWordHtml = $searchedWordHtml;

        return $this;
    }

    public function getTrademarkName(): ?string
    {
        return $this->trademarkName;
    }

    public function setTrademarkName(?string $trademarkName): static
    {
        $this->trademarkName = $trademarkName;

        return $this;
    }

    public function getTrademarkNameHtml(): ?string
    {
        return $this->trademarkNameHtml;
    }

    public function setTrademarkNameHtml(?string $trademarkNameHtml): static
    {
        $this->trademarkNameHtml = $trademarkNameHtml;

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

    public function getApplicationNo(): ?string
    {
        return $this->applicationNo;
    }

    public function setApplicationNo(?string $applicationNo): static
    {
        $this->applicationNo = $applicationNo;

        return $this;
    }

    public function getApplicationDate(): ?string
    {
        return $this->applicationDate;
    }

    public function setApplicationDate(?string $applicationDate): static
    {
        $this->applicationDate = $applicationDate;

        return $this;
    }

    public function getRegisterDate(): ?string
    {
        return $this->registerDate;
    }

    public function setRegisterDate(?string $registerDate): static
    {
        $this->registerDate = $registerDate;

        return $this;
    }

    public function getProtectionDate(): ?string
    {
        return $this->protectionDate;
    }

    public function setProtectionDate(?string $protectionDate): static
    {
        $this->protectionDate = $protectionDate;

        return $this;
    }

    public function getHolderName(): ?string
    {
        return $this->holderName;
    }

    public function setHolderName(?string $holderName): static
    {
        $this->holderName = $holderName;

        return $this;
    }

    public function getBulletinNo(): ?int
    {
        return $this->bulletinNo;
    }

    public function setBulletinNo(?int $bulletinNo): static
    {
        $this->bulletinNo = $bulletinNo;

        return $this;
    }

    public function getBulletinPage(): ?string
    {
        return $this->bulletinPage;
    }

    public function setBulletinPage(?string $bulletinPage): static
    {
        $this->bulletinPage = $bulletinPage;

        return $this;
    }

    public function getFileStatus(): ?string
    {
        return $this->fileStatus;
    }

    public function setFileStatus(?string $fileStatus): static
    {
        $this->fileStatus = $fileStatus;

        return $this;
    }

    public function getShapeSimilarity(): ?string
    {
        return $this->shapeSimilarity;
    }

    public function setShapeSimilarity(?string $shapeSimilarity): static
    {
        $this->shapeSimilarity = $shapeSimilarity;

        return $this;
    }

    public function getPhoneticSimilarity(): ?string
    {
        return $this->phoneticSimilarity;
    }

    public function setPhoneticSimilarity(?string $phoneticSimilarity): static
    {
        $this->phoneticSimilarity = $phoneticSimilarity;

        return $this;
    }

    public function getIsPriority(): ?string
    {
        return $this->isPriority;
    }

    public function setIsPriority(?string $isPriority): static
    {
        $this->isPriority = $isPriority;

        return $this;
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

    public function getObservationCacheMainList(): ?ObservationCacheMainList
    {
        return $this->observationCacheMainList;
    }

    public function setObservationCacheMainList(?ObservationCacheMainList $observationCacheMainList): static
    {
        $this->observationCacheMainList = $observationCacheMainList;

        return $this;
    }
}
