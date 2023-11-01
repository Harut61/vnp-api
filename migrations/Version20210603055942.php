<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210603055942 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE end_users ADD is_apple_private_email TINYINT(1) NOT NULL, ADD apple_private_email VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE registration_log ADD registration_type VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE end_users DROP is_apple_private_email, DROP apple_private_email');
        $this->addSql('ALTER TABLE registration_log DROP registration_type');
    }
}
