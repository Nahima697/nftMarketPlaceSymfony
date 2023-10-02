<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

#[AsController]
class PostAvatarDescriptionUserController
{
    private $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager,
        private SerializerInterface $serializer
    ) {
        $this->entityManager = $entityManager;
    }

    #[Route('api/users/{id}/avatar/description/', name: 'app_post_avatar_description_user', methods: ['POST'])]
    public function __invoke(
        Request $request,
        ValidatorInterface $validator,
        UploaderHelper $uploaderHelper,
        int $id,
        LoggerInterface $logger
    ): JsonResponse {
        $user = $this->entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            return new JsonResponse(['error' => 'Utilisateur non trouvé'], JsonResponse::HTTP_NOT_FOUND);
        }


        $description = $request->request->get('description');
        $imageFile = $request->files->get('avatar');


        $user->setDescription($description);

        if ($imageFile) {

            $user->setFile($imageFile);
            $user->setAvatar($uploaderHelper->asset($user, 'file'));
        }

        // Valider l'entité User
        $errors = $validator->validate($user);

        if (count($errors) > 0) {

            $errorMessages = [];

            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }

            return new JsonResponse(['errors' => $errorMessages], JsonResponse::HTTP_BAD_REQUEST);
        }


        $this->entityManager->flush();

        return new JsonResponse(['message' => 'User mis à jour avec succès'], JsonResponse::HTTP_CREATED);
    }
}
