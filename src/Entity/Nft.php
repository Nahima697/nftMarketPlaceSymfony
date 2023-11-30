<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\RangeFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\Get;
use Symfony\Component\HttpFoundation\File\File;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Controller\GetUserFromNftsController;
use App\Controller\NftUploadController;
use App\Repository\NftRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;

#[ORM\Entity(repositoryClass: NftRepository::class)]
#[ApiResource(
    types: ['https://schema.org/images'],
    operations: [
        new GetCollection(
            uriTemplate: '/nfts/{id}/user',
            controller: GetUserFromNftsController::class,
            read: true,
        ),
        new Post(inputFormats: ['multipart' => ['multipart/form-data']],
            controller: NftUploadController::class,
            denormalizationContext: [ 'groups'=>['write:nft']]),
        new GetCollection(paginationClientItemsPerPage: true, normalizationContext: ['groups' => ['read:nft']]),
        new Put(securityPostDenormalize: "is_granted('ROLE_ADMIN') or (object.owner == user and previous_object.owner == user)"),
        new Get(),
        new Delete(),
        new Patch()
    ],
    normalizationContext: ['groups' => ['read:nft']]
)
]

#[Vich\Uploadable]
class Nft
{
    #[Groups(['read:nft','top-creator','galleries:read'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(groups: ['postValidation'])]
    #[Groups( ['galleries:read','categories:read','read:nft','top-creator','write:nft']),
    Assert\Length(min:3)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(groups: ['postValidation'])]

    #[ApiProperty(types: ['https://schema.org/image'])]
    #[Groups( ['galleries:read','categories:read','read:nft','top-creator','read:trend-nft'])]
    private ?string $image = null;

    #[Assert\Image(
        maxSize: "5M",
        mimeTypes: ['image/jpg','image/jpeg','image/webp','image/png'],
        maxSizeMessage: "Le fichier est trop volumineux. La taille maximale autorisée est {{ maxSize }}.",
        mimeTypesMessage: "Merci de mettre une image au format jpg, jpeg, png ou webp",
        groups: ['postValidation']
    )]

    #[ApiFilter(SearchFilter::class, properties: [
        'name'=> 'partial',
        'category.wording'=> 'partial',
        'gallery.name'=>'partial',
        'gallery.owner.username'=>'partial'
    ])]

    #[ Vich\UploadableField(mapping: 'images_upload',fileNameProperty: "image")]
    #[Groups(['write:nft'])]
    public ?File $file = null;

    /**
     * @return File|null
     */
    public function getFile(): ?File
    {
        return $this->file;
    }

    /**
     * @param File|null $file
     */
    public function setFile(?File $file): void
    {
        $this->file = $file;
    }

    /**
     * @return File|null
     */


    #[ORM\ManyToOne(inversedBy: 'nfts')]
    #[Groups( ['galleries:read','write:nft'])]
    private ?Category $category = null;

    #[ORM\Column]
    #[Groups( ['galleries:read','categories:read','read:nft','read:trend-nft','top-creator','write:nft'])]
    #[Assert\Type(type: 'integer', message: 'La quantité doit être un entier.')]
    private ?int $quantity = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups( ['read:nft','write:nft','read:trend-nft','top:creator'])]
    #[ApiFilter(OrderFilter::class,properties: ['dropDate' =>'DESC'])]
    private ?\DateTimeInterface $dropDate = null;

    #[ORM\Column]
    #[Groups( ['galleries:read','categories:read','read:nft','read:trend-nft','top-creator','write:nft'])]
    #[ApiFilter(RangeFilter::class)]
    private ?float $price = null;

    #[Groups( ['categories:read','top-creator','write:nft','read:nft'])]
    #[Assert\Valid]
    #[ORM\ManyToOne(inversedBy: 'nfts')]
    private ?Gallery $gallery = null;

    #[Groups( ['read:nft','write:nft'])]
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;



    public function getId(): ?int
    {
        return $this->id;
    }


    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

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
    public function setFilePath(string $filepath): static
    {
        $this->filePath = $filepath;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }



    #[Groups(['read:nft','read:trend-nft'])]
    public function getCategoryName(): ?string
    {
        return $this->category ? $this->category->getWording() : null;
    }


    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getDropDate(): ?\DateTimeInterface
    {
        return $this->dropDate;
    }

    public function setDropDate(\DateTimeInterface $dropDate): static
    {
        $this->dropDate = $dropDate;

        return $this;
    }


    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getGallery(): ?Gallery
    {
        return $this->gallery;
    }


    public function setGallery(?Gallery $gallery): static
    {
        $this->gallery = $gallery;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }





}
