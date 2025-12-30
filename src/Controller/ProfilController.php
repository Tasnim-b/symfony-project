<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ProfileType;
use App\Form\ChangePasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class ProfilController extends AbstractController
{
    #[Route('/profil', name: 'app_profil')]
    public function index(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
        SluggerInterface $slugger
    ): Response {
        // Récupérer l'utilisateur connecté et forcer le typage
        /** @var User $user */
        $user = $this->getUser();

        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecté pour accéder à votre profil.');
            return $this->redirectToRoute('app_login');
        }

        // Vérifier que c'est bien une instance de User
        if (!$user instanceof User) {
            $this->addFlash('error', 'Erreur de type d\'utilisateur.');
            return $this->redirectToRoute('app_home');
        }

        // Formulaire de modification du profil
        $profileForm = $this->createForm(ProfileType::class, $user);
        $profileForm->handleRequest($request);

        // Formulaire de changement de mot de passe
        $passwordForm = $this->createForm(ChangePasswordType::class);
        $passwordForm->handleRequest($request);

        // Traitement du formulaire de profil
        if ($profileForm->isSubmitted() && $profileForm->isValid()) {
            // Gérer l'upload de l'image de profil
            $profileImageFile = $profileForm->get('profileImage')->getData();

            if ($profileImageFile) {
                $originalFilename = pathinfo($profileImageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$profileImageFile->guessExtension();

                try {
                    $profileImageFile->move(
                        $this->getParameter('kernel.project_dir') . '/public/uploads/profile_images',
                        $newFilename
                    );

                    // Supprimer l'ancienne image si elle existe
                    if ($user->getProfileImage()) {
                        $oldImagePath = $this->getParameter('kernel.project_dir') . '/public/uploads/profile_images/' . $user->getProfileImage();
                        if (file_exists($oldImagePath) && $user->getProfileImage() !== 'default-avatar.jpg') {
                            unlink($oldImagePath);
                        }
                    }

                    $user->setProfileImage($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors du téléchargement de l\'image: ' . $e->getMessage());
                }
            }

            $entityManager->flush();
            $this->addFlash('success', 'Votre profil a été mis à jour avec succès !');

            return $this->redirectToRoute('app_profil');
        }

        // Traitement du formulaire de mot de passe
        if ($passwordForm->isSubmitted() && $passwordForm->isValid()) {
            $data = $passwordForm->getData();

            // Vérifier si l'ancien mot de passe est correct
            if (!$passwordHasher->isPasswordValid($user, $data['oldPassword'])) {
                $this->addFlash('error', 'L\'ancien mot de passe est incorrect.');
                return $this->redirectToRoute('app_profil');
            }

            // Encoder et sauvegarder le nouveau mot de passe
            $newHashedPassword = $passwordHasher->hashPassword($user, $data['newPassword']);
            $user->setPassword($newHashedPassword);

            $entityManager->flush();
            $this->addFlash('success', 'Votre mot de passe a été changé avec succès !');

            return $this->redirectToRoute('app_profil');
        }

        return $this->render('profil/profile.html.twig', [
            'profileForm' => $profileForm->createView(),
            'passwordForm' => $passwordForm->createView(),
            'user' => $user,
        ]);
    }
}
