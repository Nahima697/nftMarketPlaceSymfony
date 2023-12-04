<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\TransactionRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TransactionRepository::class)]
#[ApiResource(
    operations: [
        new Post(
            denormalizationContext: ['groups' => ['write:transaction']]
        ),
        new Get(normalizationContext: ['groups' => ['read:transaction']]),
        new GetCollection(normalizationContext: ['groups' => ['read:transaction']]),
        new Delete(),
        new Patch()
    ]
)]
class Transaction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    #[ORM\ManyToOne(targetEntity: Gallery::class, cascade: ['persist'], inversedBy: 'buyerTransactions')]
    #[Assert\NotBlank]
    #[Groups(['write:transaction', 'read:transaction'])]
    private ?Gallery $buyerGallery = null;

    #[ORM\ManyToOne(targetEntity: Gallery::class, cascade: ['persist'], inversedBy: 'sellerTransactions')]
    #[Assert\NotBlank]
    #[Groups(['write:transaction', 'read:transaction'])]
    private ?Gallery $sellerGallery = null;

    #[ORM\ManyToOne]
    #[Groups(['write:transaction', 'read:transaction'])]
    private ?Nft $nft = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBuyerGallery(): ?Gallery
    {
        return $this->buyerGallery;
    }

    public function setBuyerGallery(?Gallery $buyerGallery): static
    {
        $this->buyerGallery = $buyerGallery;
        return $this;
    }

    public function getSellerGallery(): ?Gallery
    {
        return $this->sellerGallery;
    }

    public function setSellerGallery(?Gallery $sellerGallery): static
    {
        $this->sellerGallery = $sellerGallery;
        return $this;
    }

    public function getNft(): ?Nft
    {
        return $this->nft;
    }

    public function setNft(?Nft $nft): static
    {
        $quantity = $nft->getQuantity();

        if ($quantity >= 1) {
            $nft->setQuantity($quantity - 1);

            $nft->setGallery($this->buyerGallery);
        } else {
            throw new \RuntimeException('Quantité insuffisante pour la transaction.');
        }

        $this->nft = $nft;

        return $this;
    }
}
