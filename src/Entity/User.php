<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Controller\GetNftsFromUsersController;
use App\Controller\PostAvatarDescriptionUserController;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ApiResource(normalizationContext: ['groups' => ['top-creator']],paginationItemsPerPage: 12)]
//#[ApiFilter(OrderFilter::class, properties: ['totalSales'=>'DESC'])]
#[ApiResource(
    operations: [
        new GetCollection(
            controller: GetNftsFromUsersController::class,
            uriTemplate:'/users/{id}/nfts',
            read: true,
            ),
            new Post(validationContext: ['groups' => ['Default', 'postValidation']],
                denormalizationContext:[ 'groups'=>['write:creator']],
                uriTemplate:'users/{id}/avatar/description',controller:PostAvatarDescriptionUserController::class,
                inputFormats: ['multipart' => ['multipart/form-data']],

            )])]



#[Vich\Uploadable]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{

    #[Groups(['top-creator','read:nft'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Groups(['top-creator','read:nft','galleries:read'])]
    private ?string $username = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    #[Assert\NotBlank]

    private ?string $password = null;

    #[ORM\Column(length: 255)]

    private ?string $firstName = null;

    #[ORM\Column(length: 255)]


    private ?string $lastName = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]

    private ?string $email = null;


    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Wallet::class, orphanRemoval: true)]
    private Collection $wallets;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: Gallery::class, orphanRemoval: true, fetch:"LAZY")]
    #[Groups(['top-creator'])]
    private Collection $galleries;


    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['top-creator','galleries:read','read:nft'])]
    #[Assert\Image(
        groups: ['postValidation'],
        mimeTypes:['image/jpg','image/jpeg','image/webp','image/png'],
        mimeTypesMessage: "Merci de mettre une image au format jpg, jpeg, png ou webp",
        maxSize:"5M",
        maxSizeMessage: "Le fichier est trop volumineux. La taille maximale autorisée est {{ maxSize }}."
    )]
    #[ApiProperty(types: ['https://schema.org/avatar'])]
    private ?string $avatar = null;

    #[ Vich\UploadableField(mapping: 'images_upload',fileNameProperty: 'avatar')]
    #[Groups(['write:creator'])]
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


    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    #[Groups(['top-creator'])]
    private ?string $totalSales = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['top-creator','read:nft'])]
    private ?int $artworksCount = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['write:creator','top-creator','read:nft'])]
    private ?string $description = null;


    #[ORM\Column(length: 255)]
    private ?string $googleId = null;

    #[ORM\Column(length: 255)]
    private ?string $hostedDomain = null;

    public function __construct()
    {
        $this->wallets = new ArrayCollection();
        $this->galleries = new ArrayCollection();
    }

    /**
     * @return DateTime|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

//    #[Groups(['read:nft'])]
    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }


    /**
     * @return Collection<int, Wallet>
     */
    public function getWallets(): Collection
    {
        return $this->wallets;
    }

    public function addWallet(Wallet $wallet): static
    {
        if (!$this->wallets->contains($wallet)) {
            $this->wallets->add($wallet);
            $wallet->setUser($this);
        }

        return $this;
    }

    public function removeWallet(Wallet $wallet): static
    {
        if ($this->wallets->removeElement($wallet)) {
            // set the owning side to null (unless already changed)
            if ($wallet->getUser() === $this) {
                $wallet->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Gallery>
     */
    public function getGalleries(): Collection
    {
        return $this->galleries;
    }


    public function addGallery(Gallery $gallery): static
    {
        if (!$this->galleries->contains($gallery)) {
            $this->galleries->add($gallery);
            $gallery->setOwner($this);
        }

        return $this;
    }

    public function removeGallery(Gallery $gallery): static
    {
        if ($this->galleries->removeElement($gallery)) {
            // set the owning side to null (unless already changed)
            if ($gallery->getOwner() === $this) {
                $gallery->setOwner(null);
            }
        }

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): static
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function getTotalSales(): ?string
    {
        return $this->totalSales;
    }

    public function setTotalSales(?string $totalSales): static
    {
        $this->totalSales = $totalSales;

        return $this;
    }

    public function getArtworksCount(): ?int
    {
        return $this->artworksCount;
    }

    public function setArtworksCount(?int $artworksCount): static
    {
        $this->artworksCount = $artworksCount;

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

    public function getGoogleId(): ?string
    {
        return $this->googleId;
    }

    public function setGoogleId(string $googleId): static
    {
        $this->googleId = $googleId;

        return $this;
    }

    public function getHostedDomain(): ?string
    {
        return $this->hostedDomain;
    }

    public function setHostedDomain(string $hostedDomain): static
    {
        $this->hostedDomain = $hostedDomain;

        return $this;
    }

    public function __toString() {
        return $this->firstName. '' .$this->lastName;
    }

}
