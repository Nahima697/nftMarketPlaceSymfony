<?php

namespace App\Controller;

use App\Entity\Category;
use Doctrine\Common\Collections\Collection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class GetNftsFromCategoryController extends AbstractController
{
    #[Route('/category/{id}/nfts', name: 'get_nfts_from_category', methods: ['GET'])]

    public function __invoke(Category $category):Collection
    {
        $nfts=  $category->getNfts();

        return $nfts;

    }
}
