<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210722070955 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE lineups ADD requested_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE story_types ADD title_for_marker VARCHAR(255) DEFAULT NULL, ADD title_for_end_user VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE lineups DROP requested_at');
        $this->addSql('ALTER TABLE story_types DROP title_for_marker, DROP title_for_end_user');
    }
}
