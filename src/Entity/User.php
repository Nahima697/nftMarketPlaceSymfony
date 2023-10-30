<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Controller\GetNftsFromUsersController;
use App\Controller\PostAvatarDescriptionUserController;
use App\Controller\UserGoogleController;
use App\Repository\UserRepository;
use App\State\UserPasswordHasher;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: UserRepository::class)]

#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/users/{id}/nfts',
            controller: GetNftsFromUsersController::class,
            read: true,
            ),
            new Post(uriTemplate: '/users/{id}/avatar/description',
                inputFormats: ['multipart' => ['multipart/form-data']],
                controller: PostAvatarDescriptionUserController::class, denormalizationContext: [ 'groups'=>['write:creator']],
                validationContext: ['groups' => ['Default', 'postValidation']]
            ),
            new Get(uriTemplate: '/users/google/{googleId}', uriVariables: 'googleId', controller: UserGoogleController::class,
                ),
        new Get(),
        new GetCollection(),
        new Post(denormalizationContext: ['groups' => ['user:create', 'user:update']], validationContext: ['groups' => ['Default', 'user:create']]),
        new Put(processor: UserPasswordHasher::class),
        new Patch(processor: UserPasswordHasher::class),
        new Delete(),
    ],
    normalizationContext: ['groups' => ['top-creator']],
    denormalizationContext: ['groups' => ['user:create', 'user:update']],
    paginationItemsPerPage: 12)
]

#[Vich\Uploadable]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{

    #[Groups(['top-creator','read:nft'])]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]

    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Groups(['top-creator','read:nft','galleries:read','user:create', 'user:update'])]
    #[Assert\NotBlank(message:"Veuillez saisir un identifiant")]
    private ?string $username = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[Assert\NotBlank(groups: ['user:create'])]
    #[Groups(['user:create', 'user:update'])]
    private ?string $plainPassword = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"Veuillez saisir un prénom")]
    #[Groups(['user:create'])]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"Veuillez saisir un nom")]
    #[Groups(['user:create'])]
    private ?string $lastName = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:"Veuillez saisir un email")]
    #[Assert\Email]
    #[Groups(['user:create'])]
    private ?string $email = null;


    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Wallet::class, orphanRemoval: true)]
    private Collection $wallets;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: Gallery::class, fetch: "LAZY", orphanRemoval: true)]
    #[Groups(['top-creator'])]
    private Collection $galleries;


    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['top-creator','galleries:read','read:nft'])]
    #[Assert\Image(
        maxSize: "5M",
        mimeTypes: ['image/jpg','image/jpeg','image/webp','image/png'],
        maxSizeMessage: "Le fichier est trop volumineux. La taille maximale autorisée est {{ maxSize }}.",
        mimeTypesMessage: "Merci de mettre une image au format jpg, jpeg, png ou webp",
        groups: ['postValidation']
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

    #[ORM\Column(length: 255,nullable:true)]
    private ?string $googleId = null;

    #[ORM\Column(length: 255,nullable:true)]
    private ?string $hostedDomain = null;

    public function __construct()
    {
        $this->wallets = new ArrayCollection();
        $this->galleries = new ArrayCollection();
    }
    /**
     * @return int|null
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
    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {

         $this->plainPassword = null;
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
