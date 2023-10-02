<?php

namespace App\Controller;

use App\Entity\Gallery;
use Doctrine\Common\Collections\Collection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;


#[AsController]
class GetNftsFromGalleryController extends AbstractController
{
    #[Route('/galleries/{id}/nfts', name: 'get_nfts_from_gallery', methods: ['GET'])]

    public function __invoke(Gallery $gallery):Collection
    {
        $nfts=  $gallery->getNfts();

       return $nfts;

    }
}
