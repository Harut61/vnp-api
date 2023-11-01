<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210108164535 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE source_videos ADD time_zone_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE source_videos ADD CONSTRAINT FK_9F5541D4CBAB9ECD FOREIGN KEY (time_zone_id) REFERENCES time_zones (id)');
        $this->addSql('CREATE INDEX IDX_9F5541D4CBAB9ECD ON source_videos (time_zone_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE source_videos DROP FOREIGN KEY FK_9F5541D4CBAB9ECD');
        $this->addSql('DROP INDEX IDX_9F5541D4CBAB9ECD ON source_videos');
        $this->addSql('ALTER TABLE source_videos DROP time_zone_id');
    }
}
