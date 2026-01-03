<?php
// src/Controller/AdminNutritionnisteController.php

namespace App\Controller;

use App\Entity\Nutritionniste;
use App\Form\NutritionnisteType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/nutritionniste')]
class AdminNutritionnisteController extends AbstractController
{
    #[Route('/', name: 'app_admin_nutritionniste_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        // Récupère tous les nutritionnistes
        $nutritionnistes = $entityManager
            ->getRepository(Nutritionniste::class)
            ->findAll();

        return $this->render('admin_nutritionniste/index.html.twig', [
            'nutritionnistes' => $nutritionnistes,
        ]);
    }

    #[Route('/new', name: 'app_admin_nutritionniste_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $nutritionniste = new Nutritionniste();
        $form = $this->createForm(NutritionnisteType::class, $nutritionniste);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($nutritionniste);
            $entityManager->flush();

            $this->addFlash('success', 'Nutritionniste ajouté avec succès !');

            return $this->redirectToRoute('app_admin_nutritionniste_index');
        }

        return $this->render('admin_nutritionniste/new.html.twig', [
            'nutritionniste' => $nutritionniste,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_nutritionniste_show', methods: ['GET'])]
    public function show(Nutritionniste $nutritionniste): Response
    {
        return $this->render('admin_nutritionniste/show.html.twig', [
            'nutritionniste' => $nutritionniste,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_nutritionniste_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Nutritionniste $nutritionniste, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(NutritionnisteType::class, $nutritionniste);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Nutritionniste modifié avec succès !');

            return $this->redirectToRoute('app_admin_nutritionniste_index');
        }

        return $this->render('admin_nutritionniste/edit.html.twig', [
            'nutritionniste' => $nutritionniste,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_nutritionniste_delete', methods: ['POST'])]
    public function delete(Request $request, Nutritionniste $nutritionniste, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$nutritionniste->getId(), $request->request->get('_token'))) {
            $entityManager->remove($nutritionniste);
            $entityManager->flush();

            $this->addFlash('success', 'Nutritionniste supprimé avec succès !');
        }

        return $this->redirectToRoute('app_admin_nutritionniste_index');
    }
}
