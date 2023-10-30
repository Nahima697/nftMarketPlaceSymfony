<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\TransactionRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TransactionRepository::class)]
#[ApiResource]
class Transaction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Wallet::class)]
    #[ORM\JoinColumn(nullable: false, name: 'buyer_wallet_id')]
    #[Assert\NotBlank]
    private ?Wallet $buyerWallet = null;

    #[ORM\ManyToOne(targetEntity: Wallet::class)]
    #[ORM\JoinColumn(name: 'seller_wallet_id', nullable: false)]
    #[Assert\NotBlank]
    private ?Wallet $sellerWallet = null;

    #[ORM\ManyToOne(targetEntity: Nft::class)]
    #[ORM\JoinColumn(name: 'nft_id', nullable: false)]
    #[Assert\NotBlank]
    private ?Nft $nft = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    #[Assert\NotBlank]
    private ?string $price = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBuyerWallet(): ?Wallet
    {
        return $this->buyerWallet;
    }

    public function setBuyerWallet(?Wallet $buyerWallet): static
    {
        $this->buyerWallet = $buyerWallet;
        return $this;
    }

    public function getSellerWallet(): ?Wallet
    {
        return $this->sellerWallet;
    }

    public function setSellerWallet(?Wallet $sellerWallet): static
    {
        $this->sellerWallet = $sellerWallet;
        return $this;
    }

    public function getNft(): ?Nft
    {
        return $this->nft;
    }

    public function setNft(?Nft $nft): static
    {
        $this->nft = $nft;
        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): static
    {
        $this->price = $price;
        return $this;
    }
}
