<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/dashboard')]
#[IsGranted('ROLE_USER')]
final class DashboardController extends AbstractController
{
    #[Route('/', name: 'app_dashboard')]
    public function index(): Response
    {
        // Récupérer l'utilisateur connecté
        $user = $this->getUser();

        // Vérifier si l'utilisateur est connecté (normalement garanti par IsGranted)
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        // Données simulées pour les graphiques et statistiques
        $dashboardData = [
            'weekly_progress' => [
                'labels' => ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'],
                'calories' => [1200, 1900, 1500, 2100, 1800, 2300, 1950],
                'distance' => [3, 5, 4, 6, 5, 7, 6]
            ],
            'nutrition' => [
                'proteins' => 35,
                'carbs' => 45,
                'fats' => 20
            ],
            'quick_stats' => [
                'calories_burned' => 1250,
                'distance' => 28,
                'avg_bpm' => 72,
                'workouts_this_week' => 8
            ],
            'workouts' => [
                [
                    'name' => 'Cardio Training',
                    'duration' => 30,
                    'calories' => 300,
                    'status' => 'completed',
                    'time' => null
                ],
                [
                    'name' => 'Musculation',
                    'duration' => 45,
                    'type' => 'Haut du corps',
                    'status' => 'pending',
                    'time' => '18:00'
                ],
                [
                    'name' => 'Natation',
                    'duration' => 40,
                    'type' => 'Endurance',
                    'status' => 'upcoming',
                    'time' => 'Demain'
                ]
            ],
            'meals' => [
                [
                    'time' => 'Petit-déjeuner',
                    'name' => 'Smoothie protéiné',
                    'calories' => 350,
                    'proteins' => 25,
                    'completed' => true
                ],
                [
                    'time' => 'Déjeuner',
                    'name' => 'Poulet et quinoa',
                    'calories' => 450,
                    'proteins' => 35,
                    'completed' => true
                ],
                [
                    'time' => 'Dîner',
                    'name' => 'Saumon et légumes',
                    'calories' => 400,
                    'proteins' => 30,
                    'completed' => false
                ],
                [
                    'time' => 'Collation',
                    'name' => 'Yaourt grec',
                    'calories' => 150,
                    'proteins' => 15,
                    'completed' => false
                ]
            ]
        ];

        return $this->render('dashboard/dashboard.html.twig', [
            'controller_name' => 'DashboardController',
            'user' => $user,
            'dashboard' => $dashboardData
        ]);
    }
}
