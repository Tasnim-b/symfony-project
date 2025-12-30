<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class RegisterController extends AbstractController
{
    #[Route('/register', name: 'app_register', methods: ['GET'])]
    public function index(Request $request): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_dashboard');
        }

        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user, [
            'action' => $this->generateUrl('app_register_submit'),
        ]);

        return $this->render('register/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/register', name: 'app_register_submit', methods: ['POST'])]
    public function register(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger,
        UserAuthenticatorInterface $userAuthenticator
    ): Response {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_dashboard');
        }

        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                // Encoder le mot de passe
                $plainPassword = $form->get('plainPassword')->getData();
                $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
                $user->setPassword($hashedPassword);

                // Gérer l'upload de l'image de profil
                $profileImageFile = $form->get('profileImage')->getData();

                if ($profileImageFile) {
                    $originalFilename = pathinfo($profileImageFile->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename.'-'.uniqid().'.'.$profileImageFile->guessExtension();

                    try {
                        $profileImageFile->move(
                            $this->getParameter('kernel.project_dir') . '/public/uploads/profile_images',
                            $newFilename
                        );
                        $user->setProfileImage($newFilename);
                    } catch (FileException $e) {
                        $this->addFlash('error', 'Erreur lors de l\'upload de l\'image: ' . $e->getMessage());
                    }
                }

                // Définir le rôle par défaut
                $user->setRoles(['ROLE_USER']);
                $user->setIsVerified(false);
                $user->setUpdatedAt(new \DateTimeImmutable());

                // Sauvegarder l'utilisateur
                $entityManager->persist($user);
                $entityManager->flush();

                // Connecter automatiquement l'utilisateur
                // On utilise une méthode alternative car l'authenticateur de formulaire de login
                // n'est pas conçu pour être utilisé ici

                // Méthode 1: Créer manuellement la session
                $request->getSession()->set('_security_last_username', $user->getEmail());

                // Méthode 2: Utiliser l'authenticateur Symfony (si vous en avez un)
                // $userAuthenticator->authenticateUser($user, $authenticator, $request);

                // Méthode 3: Simplement rediriger vers le login avec un message
                // Mais comme vous voulez aller directement au dashboard, on va créer la session manuellement

                // Créer un token et l'ajouter à la session
                $token = new \Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken(
                    $user,
                    'main', // le nom du firewall dans security.yaml
                    $user->getRoles()
                );

                $this->container->get('security.token_storage')->setToken($token);

                // Message de succès et redirection vers le dashboard
                $this->addFlash('success', 'Votre compte a été créé avec succès ! Bienvenue sur HealFit.');

                return $this->redirectToRoute('app_dashboard');

            } catch (\Exception $e) {
                $this->addFlash('error', 'Une erreur est survenue lors de la création de votre compte: ' . $e->getMessage());
                return $this->redirectToRoute('app_register');
            }
        }

        // Si le formulaire n'est pas valide, réafficher le formulaire avec les erreurs
        return $this->render('register/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
