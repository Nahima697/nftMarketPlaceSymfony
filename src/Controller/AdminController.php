<?php

namespace App\Controller;

use App\Form\SearchType;
use App\Repository\NftRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(NftRepository $nftRepository, Request $request): Response
    {
        $nfts= $nftRepository->findAll();
        $form= $this->createForm(SearchType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $filters = $form->getData();
            $nfts = $nftRepository->searchEngine($filters);
        }
        return $this->render('admin/index.html.twig', [
            'nfts' => $nfts,
            'searchForm' =>$form->createView(),
        ]);
    }

}
