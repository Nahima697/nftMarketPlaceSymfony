<?php

namespace App\Controller;

use App\Entity\Gallery;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\SerializerInterface;

class GalleryController extends AbstractController
{
    public function __construct(private SerializerInterface $serializer)
    {
    }

    #[Route('/users/{userId}/galleries', name: 'app_gallery_by_user')]
    public function getGalleriesByUser($userId, UserRepository $userRepository): JsonResponse
    {
        $user = $userRepository->find($userId);
        if (!$user) {
            return new JsonResponse(['message' => 'User not found'], 404);
        }
        $galleries = $user->getGalleries();
        $galleriesSerialized = json_decode($this->serializer->serialize($galleries, 'json'));
        return $this->json($galleriesSerialized);

    }



}

