<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

final class LoginController extends AbstractController
{
    #[Route('/login', name: 'app_login', methods: ['GET', 'POST'])]
    public function index(AuthenticationUtils $authenticationUtils): Response
    {
        // Rediriger si l'utilisateur est déjà connecté
        if ($this->getUser()) {
            return $this->redirectToRoute('app_dashboard');
        }
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('login/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    // #[Route('/login', name: 'app_login_submit', methods: ['POST'])]
    // public function login(Request $request): Response
    // {
    //     // Symfony Security gère automatiquement l'authentification
    //     // Cette route ne sera jamais atteinte car le firewall intercepte la requête
    //     return $this->redirectToRoute('app_login');
    // }

    #[Route('/logout', name: 'app_logout', methods: ['POST'])]
    public function logout(): void
    {
        // Symfony gère automatiquement la déconnexion
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }



     #[Route('/forgot-password', name: 'app_forgot_password', methods: ['GET'])]
    public function forgotPassword(): Response
    {
        // Pour l'instant, redirigez vers la page de connexion
        $this->addFlash('info', 'La fonctionnalité de réinitialisation de mot de passe sera disponible prochainement.');
        return $this->redirectToRoute('app_login');
    }
}
