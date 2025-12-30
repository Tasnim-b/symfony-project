<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class CommunauteController extends AbstractController
{
    #[Route('/communaute', name: 'app_communaute')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        // Vérifier si un utilisateur est connecté
        $user = $this->getUser();

        if (!$user) {
            // Rediriger vers la page de connexion si aucun utilisateur n'est connecté
            return $this->redirectToRoute('app_login');
        }

        // S'assurer que l'utilisateur est une instance de votre entité User
        if (!$user instanceof User) {
            throw new AccessDeniedException('Utilisateur non valide');
        }

        // Récupérer TOUS les utilisateurs de la base de données
        $userRepository = $entityManager->getRepository(User::class);
        $allUsers = $userRepository->findAll();
              // Calculer le total de membres (tous les utilisateurs)
        $totalMembers = count($allUsers);

        // Préparer la liste des membres pour la vue
        $members = [];

        foreach ($allUsers as $otherUser) {
            // Ne pas inclure l'utilisateur connecté dans la liste
            if ($otherUser->getId() !== $user->getId()) {
                $avatarUrl = $otherUser->getProfileImage()
                    ? '/uploads/profile_images/' . $otherUser->getProfileImage()
                    : 'https://ui-avatars.com/api/?name=' . urlencode($otherUser->getFullName()) . '&background=2e7d32&color=fff&size=100';

                $members[] = [
                    'id' => $otherUser->getId(),
                    'name' => $otherUser->getFullName() ?? 'Utilisateur sans nom',
                    'avatar' => $avatarUrl,
                    'online' => false, // Tous les utilisateurs existants sont hors ligne par défaut
                    'email' => $otherUser->getEmail()
                ];
            }
        }

        return $this->render('communaute/communaute.html.twig', [
            'user' => $user,
            'members' => $members,
            'totalMembers' => $totalMembers,
        ]);
    }
}
