<?php
namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
    class UserGoogleController extends AbstractController
    {
    #[Route('/api/users/google/{googleId}', name: 'app_get_user_id_by_google_id', methods: ['GET'])]
    public function __invoke(string $googleId, UserRepository $userRepository): JsonResponse
    {
    $user = $userRepository->findOneBy(['googleId' => $googleId]);

    if (!$user) {
    return new JsonResponse(['error' => 'User not found'], 404);
    }

    return $this->json(['id' => $user->getId()]);
    }
}
