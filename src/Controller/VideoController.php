<?php
// src/Controller/VideoController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class VideoController extends AbstractController
{
    #[Route('/videos-exercices', name: 'app_videos_exercices')]
    public function index(): Response
    {
        // Données des catégories
        $categories = [
            [
                'id' => 1,
                'title' => 'Cardio Training',
                'description' => 'Exercices intensifs pour améliorer votre endurance cardiovasculaire',
                'icon' => 'fas fa-heartbeat',
                'color' => '#f44336'
            ],
            [
                'id' => 2,
                'title' => 'Yoga & Méditation',
                'description' => 'Séances de yoga pour flexibilité et paix intérieure',
                'icon' => 'fas fa-spa',
                'color' => '#9c27b0'
            ],
            [
                'id' => 3,
                'title' => 'Pilates & Posture',
                'description' => 'Renforcez votre sangle abdominale et posture',
                'icon' => 'fas fa-ring',
                'color' => '#009688'
            ],
            [
                'id' => 4,
                'title' => 'Pour Débutants',
                'description' => 'Programmes progressifs spécialement conçus pour les débutants',
                'icon' => 'fas fa-baby',
                'color' => '#ff9800'
            ],
            [
                'id' => 5,
                'title' => 'Pour Experts',
                'description' => 'Défis avancés pour repousser vos limites',
                'icon' => 'fas fa-fire',
                'color' => '#d32f2f'
            ],
        ];

        // Données des vidéos (exemple avec des vidéos YouTube)
        $videos = [
            [
                'video_id' => 'dQw4w9WgXcQ',
                'title' => 'Cardio intense pour débutants - 20 min',
                'description' => 'Session de cardio complète pour brûler des calories',
                'thumbnail' => 'https://img.youtube.com/vi/dQw4w9WgXcQ/maxresdefault.jpg',
                'duration' => '20:15',
                'views' => '150K',
                'likes' => '8.2K',
                'channel' => 'HealFit Coach',
                'category' => 'cardio',
                'level' => 'debutant',
                'duration_type' => 'medium'
            ],
            [
                'video_id' => 'UBMk30rjy0o',
                'title' => 'Yoga matinal - Réveil en douceur',
                'description' => 'Séquence de yoga pour commencer la journée avec énergie',
                'thumbnail' => 'https://img.youtube.com/vi/UBMk30rjy0o/maxresdefault.jpg',
                'duration' => '25:30',
                'views' => '89K',
                'likes' => '5.3K',
                'channel' => 'Yoga avec Sarah',
                'category' => 'yoga',
                'level' => 'debutant',
                'duration_type' => 'medium'
            ],
            [
                'video_id' => 'sTANio_2E0Q',
                'title' => 'Pilates pour le dos - Soulager les tensions',
                'description' => 'Exercices de pilates pour renforcer le dos et améliorer la posture',
                'thumbnail' => 'https://img.youtube.com/vi/sTANio_2E0Q/maxresdefault.jpg',
                'duration' => '30:45',
                'views' => '120K',
                'likes' => '7.8K',
                'channel' => 'Pilates Pro',
                'category' => 'pilates',
                'level' => 'intermediaire',
                'duration_type' => 'medium'
            ],
            [
                'video_id' => 'ml6cT4AZdqI',
                'title' => 'HIIT Cardio Expert - Brûle Graisse Intense',
                'description' => 'Session HIIT avancée pour athlètes confirmés',
                'thumbnail' => 'https://img.youtube.com/vi/ml6cT4AZdqI/maxresdefault.jpg',
                'duration' => '45:20',
                'views' => '210K',
                'likes' => '12.5K',
                'channel' => 'FitExtreme',
                'category' => 'cardio',
                'level' => 'expert',
                'duration_type' => 'long'
            ],
            [
                'video_id' => 'z6X5oEJnC9c',
                'title' => 'Yoga Vinyasa Flow - Niveau Intermédiaire',
                'description' => 'Flow dynamique pour améliorer force et flexibilité',
                'thumbnail' => 'https://img.youtube.com/vi/z6X5oEJnC9c/maxresdefault.jpg',
                'duration' => '35:15',
                'views' => '95K',
                'likes' => '6.2K',
                'channel' => 'Yoga Flow',
                'category' => 'yoga',
                'level' => 'intermediaire',
                'duration_type' => 'medium'
            ],
            [
                'video_id' => 'CVp4tqdwY_I',
                'title' => 'Pilates Abs Avancé - Défi 30 jours',
                'description' => 'Programme intensif pour des abdominaux en acier',
                'thumbnail' => 'https://img.youtube.com/vi/CVp4tqdwY_I/maxresdefault.jpg',
                'duration' => '28:40',
                'views' => '180K',
                'likes' => '10.3K',
                'channel' => 'Core Power',
                'category' => 'pilates',
                'level' => 'expert',
                'duration_type' => 'medium'
            ],
        ];

        return $this->render('video/index.html.twig', [
            'categories' => $categories,
            'videos' => $videos,
        ]);
    }

    #[Route('/videos/category/{categoryId}', name: 'app_videos_by_category')]
    public function byCategory(int $categoryId): Response
    {
        // Logique pour filtrer par catégorie
        return $this->render('video/index.html.twig', [
            'categoryId' => $categoryId,
        ]);
    }
}
