<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
// use Symfony\Component\Security\Http\Attribute\IsGranted;

// #[Route('/dashboard')]
// #[IsGranted('ROLE_USER')]
final class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]//, methods: ['GET']
    public function index(): Response
    {   //recupérer l'utilisateur connecté
        // $user = $this->getUser();
        return $this->render('dashboard/dashboard.html.twig', [
            'controller_name' => 'DashboardController',
           // 'user' => $user,
        ]);
    }
}
