<?php

namespace App\Controller;
use App\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
class GetNftsFromUsersController extends AbstractController


{
    #[Route('/api/users/{id}/nfts', name: 'app_gallery_by_user',methods: ['GET'])]

public function __invoke(User $user):?Collection
{
    $galleries = $user->getGalleries();
    $nfts = [];

    foreach ($galleries as $gallery) {
        $galleryNfts = $gallery->getNfts();

        foreach ($galleryNfts as $nft) {
            $nfts[] = $nft;
        }
    }


    return new ArrayCollection($nfts);
}
}
