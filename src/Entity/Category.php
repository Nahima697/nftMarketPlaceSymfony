<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]

#[ApiResource(normalizationContext: ['groups' => ['categories:read']])]

class Category
{
    #[Groups(['categories:read'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['categories:read','read:nft'])]
    #[ApiFilter(SearchFilter::class, strategy: 'partial')]
    private ?string $wording = null;

    #[Groups(['categories:read'])]
    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'childs')]
    private ?self $parent = null;
    #[Groups(['categories:read'])]
    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: self::class)]
    private Collection $childs;

    #[Groups(['categories:read'])]
    #[ORM\OneToMany(mappedBy: 'category', targetEntity: Nft::class)]
    private Collection $nfts;

    #[Groups(['categories:read'])]
    #[ORM\Column(length: 255)]
    private ?string $image = null;

    public function __construct()
    {
        $this->childs = new ArrayCollection();
        $this->nfts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    #[Groups(['categories:read'])]
    public function getWording(): ?string
    {
        return $this->wording;
    }

    public function setWording(string $wording): static
    {
        $this->wording = $wording;

        return $this;
    }
    public function __toString(){
        return $this->wording;
    }
    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): static
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */


    public function getChilds(): Collection
    {
        return $this->childs;
    }

    public function addChild(self $child): static
    {
        if (!$this->childs->contains($child)) {
            $this->childs->add($child);
            $child->setParent($this);
        }

        return $this;
    }

    public function removeChild(self $child): static
    {
        if ($this->childs->removeElement($child)) {
            // set the owning side to null (unless already changed)
            if ($child->getParent() === $this) {
                $child->setParent(null);
            }
        }

        return $this;
    }


    /**
     * @return Collection<int, Nft>
     */
    public function getNfts(): Collection
    {
        return $this->nfts;
    }

    public function addNft(Nft $nft): static
    {
        if (!$this->nfts->contains($nft)) {
            $this->nfts->add($nft);
            $nft->setCategory($this);
        }

        return $this;
    }

    public function removeNft(Nft $nft): static
    {
        if ($this->nfts->removeElement($nft)) {
            // set the owning side to null (unless already changed)
            if ($nft->getCategory() === $this) {
                $nft->setCategory(null);
            }
        }

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

        return $this;
    }


}
