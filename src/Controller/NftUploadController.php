<?php

namespace App\Controller;

use ApiPlatform\Api\IriConverterInterface;
use App\Entity\Nft;
use App\Repository\GalleryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;


#[AsController]
class NftUploadController
{
    private $entityManager;
    private $galleryRepository;
    private $uploaderHelper;

    public function __construct(EntityManagerInterface $entityManager,
                                GalleryRepository $galleryRepository,
                                private SerializerInterface $serializer,
                                private IriConverterInterface $iriConverter)
    {
        $this->entityManager = $entityManager;
        $this->galleryRepository = $galleryRepository;
    }

    #[Route('/api/nfts', name: 'create_nft', defaults: [
        '_api_resource_class' => Nft::class,
    ], methods: ['POST'])]
    public function __invoke(Request $request): JsonResponse
    {
        $data = $request->request->get('data');
        $dataArray = json_decode($data, true);

        if (json_last_error() === JSON_ERROR_NONE && is_array($dataArray)) {
            $nft = $this->serializer->deserialize(json_encode($dataArray), Nft::class, 'json');
            $nft->setName($dataArray['name']);
            $categoryIri = $dataArray['category'];
            if ($categoryIri) {
                $category = $this->iriConverter->getResourceFromIri($categoryIri);
                $nft->setCategory($category);
            } else {
                return new JsonResponse(['error' => "La catégorie n'existe pas"], JsonResponse::HTTP_NOT_FOUND);
            }
            $galleryIri = $dataArray['gallery'];
            if ($galleryIri) {
                $gallery = $this->iriConverter->getResourceFromIri($galleryIri);
                $nft->setGallery($gallery);
            } else {
                return new JsonResponse(['error' => "La gallery n'existe pas"], JsonResponse::HTTP_NOT_FOUND);
            }


            $nft->setQuantity($dataArray['quantity']);
            $nft->setPrice($dataArray['price']);
            $nft->setDropDate(new \DateTime($dataArray['dropDate']));

        } else {
            return new JsonResponse(['error' => 'Les données sont incorrectes'], JsonResponse::HTTP_NOT_FOUND);
        }

        $imageFile = $request->files->get('image');
        if ($imageFile) {

            $nft->setFile($imageFile);
        }

        $this->entityManager->persist($nft);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'NFT created successfully'], JsonResponse::HTTP_CREATED);
    }
}
