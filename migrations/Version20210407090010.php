<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210407090010 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ftp_folders CHANGE published_at published_at TIME DEFAULT NULL');
        $this->addSql('ALTER TABLE ftp_servers CHANGE protocol protocol VARCHAR(10) DEFAULT \'ftp\' NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ftp_folders CHANGE published_at published_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE ftp_servers CHANGE protocol protocol VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
