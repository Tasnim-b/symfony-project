<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241230100000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Initial migration for existing database schema';
    }

    public function up(Schema $schema): void
    {
        // Cette migration ne fait rien car les tables existent déjà
        // Elle sert juste à initialiser l'historique des migrations
        $this->addSql('SELECT 1');
    }

    public function down(Schema $schema): void
    {
        // Ne rien faire
        $this->addSql('SELECT 1');
    }
}
