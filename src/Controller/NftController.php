<?php

namespace App\Controller;

use App\Entity\Nft;
use App\Form\NftType;
use App\Repository\NftRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/admin/nft')]
class NftController extends AbstractController
{
    #[Route('/', name: 'app_nft_index', methods: ['GET'])]
    public function index(NftRepository $nftRepository): Response
    {
        return $this->render('nft/index.html.twig', [
            'nfts' => $nftRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_nft_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $nft = new Nft();
        $form = $this->createForm(NftType::class, $nft);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($nft);
            $entityManager->flush();

            return $this->redirectToRoute('app_nft_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('nft/new.html.twig', [
            'nft' => $nft,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_nft_show', methods: ['GET'])]
    public function show(Nft $nft): Response
    {
        return $this->render('nft/show.html.twig', [
            'nft' => $nft,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_nft_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Nft $nft, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(NftType::class, $nft);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_nft_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('nft/edit.html.twig', [
            'nft' => $nft,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_nft_delete', methods: ['DELETE'])]
    public function delete(Request $request, Nft $nft, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$nft->getId(), $request->request->get('_token'))) {
            $entityManager->remove($nft);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_nft_index', [], Response::HTTP_SEE_OTHER);
    }
}
