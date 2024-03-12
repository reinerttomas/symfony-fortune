<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\FortuneCookieRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FortuneCookieRepository::class)]
class FortuneCookie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\ManyToOne(inversedBy: 'fortuneCookies')]
    #[ORM\JoinColumn(nullable: false)]
    private Category $category;

    #[ORM\Column(length: 255, nullable: false)]
    private string $fortune;

    #[ORM\Column(nullable: false)]
    private int $numberPrinted;

    #[ORM\Column(nullable: false)]
    private bool $discontinued;

    #[ORM\Column(nullable: false)]
    private DateTimeImmutable $createdAt;

    public function __construct(
        Category $category,
        string $fortune,
        int $numberPrinted,
        bool $discontinued,
    ) {
        $this->category = $category;
        $this->fortune = $fortune;
        $this->numberPrinted = $numberPrinted;
        $this->discontinued = $discontinued;
        $this->createdAt = new DateTimeImmutable();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getCategory(): Category
    {
        return $this->category;
    }

    public function getFortune(): string
    {
        return $this->fortune;
    }

    public function setFortune(string $fortune): self
    {
        $this->fortune = $fortune;

        return $this;
    }

    public function getNumberPrinted(): int
    {
        return $this->numberPrinted;
    }

    public function setNumberPrinted(int $numberPrinted): self
    {
        $this->numberPrinted = $numberPrinted;

        return $this;
    }

    public function isDiscontinued(): bool
    {
        return $this->discontinued;
    }

    public function setDiscontinued(bool $discontinued): self
    {
        $this->discontinued = $discontinued;

        return $this;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
}
