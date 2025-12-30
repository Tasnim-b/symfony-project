<?php
// src/Twig/AppExtension.php

namespace App\Twig;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('getTotalUsers', [$this, 'getTotalUsers']),
        ];
    }

    public function getTotalUsers(): int
    {
        try {
            $userRepository = $this->entityManager->getRepository(User::class);
            $total = $userRepository->count([]);

            // Log pour debug (vous pouvez retirer plus tard)
            // error_log("Nombre total d'utilisateurs : " . $total);

            return $total;
        } catch (\Exception $e) {
            // En cas d'erreur, retourner 0 pour éviter de casser l'affichage
            error_log("Erreur dans AppExtension::getTotalUsers: " . $e->getMessage());
            return 0;
        }
    }
}
