<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\CategoryRepository;
use App\Repository\Criteria\FortuneCookiesStillInProduction;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column(length: 255, nullable: false)]
    private string $name;

    #[ORM\Column(length: 32)]
    private string $iconKey;

    /**
     * @var Collection<int, FortuneCookie>
     */
    #[ORM\OneToMany(targetEntity: FortuneCookie::class, mappedBy: 'category', fetch: 'EXTRA_LAZY')]
    private Collection $fortuneCookies;

    public function __construct(string $name, string $iconKey)
    {
        $this->name = $name;
        $this->iconKey = $iconKey;
        $this->fortuneCookies = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getIconKey(): string
    {
        return $this->iconKey;
    }

    public function setIconKey(string $iconKey): self
    {
        $this->iconKey = $iconKey;

        return $this;
    }

    /**
     * @return Collection<int, FortuneCookie>
     */
    public function getFortuneCookies(): Collection
    {
        return $this->fortuneCookies;
    }

    /**
     * @return Collection<int, FortuneCookie>
     */
    public function getFortuneCookiesStillInProduction(): Collection
    {
        return $this->fortuneCookies->matching(FortuneCookiesStillInProduction::create());
    }

    public function addFortuneCookie(FortuneCookie $fortuneCookie): self
    {
        if (! $this->fortuneCookies->contains($fortuneCookie)) {
            $this->fortuneCookies->add($fortuneCookie);
        }

        return $this;
    }

    public function removeFortuneCookie(FortuneCookie $fortuneCookie): self
    {
        $this->fortuneCookies->removeElement($fortuneCookie);

        return $this;
    }
}
