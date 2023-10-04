<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Odm\Filter\DateFilter;
use ApiPlatform\Doctrine\Odm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\RangeFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Controller\GalleryController;
use App\Controller\GetNftsFromGalleryController;
use App\Repository\GalleryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: GalleryRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['galleries:read']],
    paginationClientItemsPerPage: true,
    denormalizationContext: ['groups' => ['galleries:post']],
    operations: [
        new GetCollection(),
        new Get(),
        new Put(),
        new Delete(),
        new Patch()
        ]
    )
, ApiFilter(OrderFilter::class, properties: ['purchase_date'=>'DESC'])]
#[Post(validationContext: ['groups' => ['Default', 'postValidation']])]
class Gallery
{

    #[Groups(['galleries:read'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;


    #[ORM\ManyToOne(inversedBy: 'galleries', fetch:"EAGER")]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['galleries:read','read:nft','galleries:post'])]
    private ?User $owner = null;

    #[Groups(['galleries:read','galleries:post','read:nft'])]
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(groups: ['postValidation'])]
    #[ApiProperty(types: ["https://schema.org/name"])]
    #[ApiFilter(SearchFilter::class, strategy: 'partial')]
    private ?string $name = null;

    #[Groups(['galleries:read','top-creator'])]
    #[Assert\Valid]
    #[ORM\OneToMany(mappedBy: 'gallery', targetEntity: Nft::class)]
    private Collection $nfts;
    #[Groups(['galleries:read'])]
    #[ORM\Column(length: 255)]
    private ?string $description = null;


    #[Groups(['galleries:read','read:nft','top-creator'])]
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOwnerId(): ?int
    {
        return $this->owner ? $this->owner->getId() : null;

    }


    public function getOwner(): ?User
    {
        return $this->owner;
    }


    public function setOwner(?User $owner): static
    {
        $this->owner = $owner;

        return $this;
    }

    #[Groups(['galleries:read','top-creator'])]
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
            $nft->setGallery($this);
        }

        return $this;
    }

    public function removeNft(Nft $nft): static
    {
        if ($this->nfts->removeElement($nft)) {
            // set the owning side to null (unless already changed)
            if ($nft->getGallery() === $this) {
                $nft->setGallery(null);
            }
        }

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }
}







