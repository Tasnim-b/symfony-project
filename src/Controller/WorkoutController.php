<?php
// src/Controller/WorkoutController.php

namespace App\Controller;

use App\Entity\WorkoutProfile;
use App\Form\WorkoutProfileType;
use App\Service\WorkoutGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class WorkoutController extends AbstractController
{
    #[Route('/workout/personnalise', name: 'app_workout_personalized')]
    public function index(Request $request, EntityManagerInterface $entityManager, WorkoutGenerator $workoutGenerator): Response
    {
        $workoutProfile = new WorkoutProfile();
        $form = $this->createForm(WorkoutProfileType::class, $workoutProfile);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Générer le programme d'entraînement
            $generatedWorkout = $workoutGenerator->generateWorkout($workoutProfile);
            $workoutProfile->setGeneratedWorkout($generatedWorkout);

            // Sauvegarder en base de données
            $entityManager->persist($workoutProfile);
            $entityManager->flush();

            return $this->render('workout/generated.html.twig', [
                'workoutProfile' => $workoutProfile,
                'generatedWorkout' => $generatedWorkout,
            ]);
        }

        return $this->render('workout/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/workout/mon-programme/{id}', name: 'app_workout_show')]
    public function show(WorkoutProfile $workoutProfile): Response
    {
        return $this->render('workout/show.html.twig', [
            'workoutProfile' => $workoutProfile,
        ]);
    }
}
