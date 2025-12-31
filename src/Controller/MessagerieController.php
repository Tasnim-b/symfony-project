<?php
// src/Controller/MessagerieController.php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\User;
use App\Form\MessageType;
use App\Repository\MessageRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class MessagerieController extends AbstractController
{
    #[Route('/messagerie', name: 'app_messagerie')]
    public function index(
        #[CurrentUser] User $currentUser,
        UserRepository $userRepository,
        MessageRepository $messageRepository
    ): Response {
        // Récupérer tous les utilisateurs (sauf l'utilisateur connecté)
        $users = $userRepository->findAllExcept($currentUser);

        // Récupérer les conversations (utilisateurs avec qui j'ai échangé)
        $conversations = $userRepository->findUsersWithConversations($currentUser);


        // Compter les messages non lus
        $unreadCount = $messageRepository->countUnreadMessages($currentUser);

        return $this->render('messagerie/messagerie.html.twig', [
            'currentUser' => $currentUser,
            'users' => $users,
            'conversations' => $conversations,
            'unreadCount' => $unreadCount,
        ]);
    }

    #[Route('/messagerie/conversation/{id}', name: 'app_messagerie_conversation')]
    public function conversation(
        User $correspondant,
        #[CurrentUser] User $currentUser,
        MessageRepository $messageRepository,
        EntityManagerInterface $entityManager
    ): Response {
        // Récupérer les messages entre les deux utilisateurs
        $messages = $messageRepository->findMessagesBetweenUsers($currentUser, $correspondant);

        // Marquer les messages comme lus
        $messageRepository->markAsRead($currentUser, $correspondant);

        // Créer le formulaire d'envoi de message
        $message = new Message();
        $message->setSender($currentUser);
        $message->setReceiver($correspondant);

        $form = $this->createForm(MessageType::class, $message);

        return $this->render('messagerie/conversation.html.twig', [
            'currentUser' => $currentUser,
            'correspondant' => $correspondant,
            'messages' => $messages,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/messagerie/send/{id}', name: 'app_messagerie_send', methods: ['POST'])]
    public function sendMessage(
        User $receiver,
        Request $request,
        #[CurrentUser] User $sender,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $content = $request->request->get('content');

        if (empty(trim($content))) {
            return $this->json(['success' => false, 'message' => 'Le message ne peut pas être vide']);
        }

        $message = new Message();
        $message->setSender($sender);
        $message->setReceiver($receiver);
        $message->setContent(trim($content));

        $entityManager->persist($message);
        $entityManager->flush();

        return $this->json([
            'success' => true,
            'message' => [
                'id' => $message->getId(),
                'content' => $message->getContent(),
                'sender' => $sender->getFullName(),
                'senderId' => $sender->getId(),
                'createdAt' => $message->getFormattedCreatedAt(),
                'date' => $message->getFormattedDate(),
            ]
        ]);
    }

    #[Route('/messagerie/check-new/{correspondantId}', name: 'app_messagerie_check_new')]
    public function checkNewMessages(
        int $correspondantId,
        #[CurrentUser] User $currentUser,
        UserRepository $userRepository,
        MessageRepository $messageRepository,
        EntityManagerInterface $entityManager
    ): JsonResponse {
        $correspondant = $userRepository->find($correspondantId);

        if (!$correspondant) {
            return $this->json(['success' => false]);
        }

        $newMessages = $messageRepository->findUnreadMessages($currentUser, $correspondant);

        $messages = [];
        foreach ($newMessages as $message) {
            $messages[] = [
                'id' => $message->getId(),
                'content' => $message->getContent(),
                'senderId' => $message->getSender()->getId(),
                'createdAt' => $message->getFormattedCreatedAt(),
                'date' => $message->getFormattedDate(),
            ];
            // Marquer comme lu
            $message->setIsRead(true);
        }

        // $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();

        return $this->json([
            'success' => true,
            'messages' => $messages,
            'count' => count($messages)
        ]);
    }

    #[Route('/messagerie/unread-count', name: 'app_messagerie_unread_count')]
    public function getUnreadCount(
        #[CurrentUser] User $currentUser,
        MessageRepository $messageRepository
    ): JsonResponse {
        $count = $messageRepository->countUnreadMessages($currentUser);

        return $this->json(['count' => $count]);
    }
}
