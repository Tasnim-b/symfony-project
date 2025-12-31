<?php
// src/Repository/MessageRepository.php

namespace App\Repository;

use App\Entity\Message;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Message>
 */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    /**
     * Trouve les conversations d'un utilisateur
     */
    public function findConversations(User $user): array
    {
        return [];
        // $qb = $this->createQueryBuilder('m');

        // return $qb
        //     ->select('DISTINCT u.id, u.fullName, u.profileImage, m.content as lastMessage, m.createdAt as lastMessageDate')
        //     ->join('m.sender', 's')
        //     ->join('m.receiver', 'r')
        //     ->leftJoin('App\Entity\User', 'u', 'WITH', 'u.id = CASE WHEN m.sender = :user THEN r.id ELSE s.id END')
        //     ->where('m.sender = :user OR m.receiver = :user')
        //     ->orderBy('m.createdAt', 'DESC')
        //     ->setParameter('user', $user)
        //     ->groupBy('u.id')
        //     ->getQuery()
        //     ->getResult();
    }

    /**
     * Trouve les messages entre deux utilisateurs
     */
    public function findMessagesBetweenUsers(User $user1, User $user2): array
    {
        return $this->createQueryBuilder('m')
            ->where('(m.sender = :user1 AND m.receiver = :user2) OR (m.sender = :user2 AND m.receiver = :user1)')
            ->orderBy('m.createdAt', 'ASC')
            ->setParameter('user1', $user1)
            ->setParameter('user2', $user2)
            ->getQuery()
            ->getResult();
    }

    /**
     * Compte les messages non lus
     */
    public function countUnreadMessages(User $user): int
    {
        return (int) $this->createQueryBuilder('m')
            ->select('COUNT(m.id)')
            ->where('m.receiver = :user')
            ->andWhere('m.isRead = false')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Trouve les messages non lus
     */
    public function findUnreadMessages(User $receiver, User $sender): array
    {
        return $this->createQueryBuilder('m')
            ->where('m.receiver = :receiver')
            ->andWhere('m.sender = :sender')
            ->andWhere('m.isRead = false')
            ->orderBy('m.createdAt', 'ASC')
            ->setParameter('receiver', $receiver)
            ->setParameter('sender', $sender)
            ->getQuery()
            ->getResult();
    }

    /**
     * Marque les messages comme lus
     */
    public function markAsRead(User $receiver, User $sender): void
    {
        $this->createQueryBuilder('m')
            ->update()
            ->set('m.isRead', true)
            ->set('m.readAt', ':now')
            ->where('m.receiver = :receiver')
            ->andWhere('m.sender = :sender')
            ->andWhere('m.isRead = false')
            ->setParameter('receiver', $receiver)
            ->setParameter('sender', $sender)
            ->setParameter('now', new \DateTimeImmutable())
            ->getQuery()
            ->execute();
    }


}
