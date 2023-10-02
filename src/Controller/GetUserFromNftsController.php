<?php

namespace App\Controller;

use App\Entity\Nft;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

#[AsController]
class GetUserFromNftsController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function __invoke(Nft $nft): JsonResponse
    {
        $gallery = $nft->getGallery();

        $user = $gallery->getOwner();

        $this->entityManager->refresh($user);

        $userData = [
            'username' => $user->getUsername(),
            'avatar' => $user->getAvatar(),
        ];

        return $this->json($userData);
    }
}
