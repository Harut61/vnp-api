<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201229172848 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE vods ADD story_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE vods ADD CONSTRAINT FK_E4062264AA5D4036 FOREIGN KEY (story_id) REFERENCES stories (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E4062264AA5D4036 ON vods (story_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE vods DROP FOREIGN KEY FK_E4062264AA5D4036');
        $this->addSql('DROP INDEX UNIQ_E4062264AA5D4036 ON vods');
        $this->addSql('ALTER TABLE vods DROP story_id');
    }
}
