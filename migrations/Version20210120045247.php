<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210120045247 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE source_videos DROP FOREIGN KEY FK_9F5541D48B3F2B6E');
        $this->addSql('DROP INDEX IDX_9F5541D48B3F2B6E ON source_videos');
        $this->addSql('ALTER TABLE source_videos CHANGE major_source_id source_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE source_videos ADD CONSTRAINT FK_9F5541D4953C1C61 FOREIGN KEY (source_id) REFERENCES sources (id)');
        $this->addSql('CREATE INDEX IDX_9F5541D4953C1C61 ON source_videos (source_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE source_videos DROP FOREIGN KEY FK_9F5541D4953C1C61');
        $this->addSql('DROP INDEX IDX_9F5541D4953C1C61 ON source_videos');
        $this->addSql('ALTER TABLE source_videos CHANGE source_id major_source_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE source_videos ADD CONSTRAINT FK_9F5541D48B3F2B6E FOREIGN KEY (major_source_id) REFERENCES sources (id)');
        $this->addSql('CREATE INDEX IDX_9F5541D48B3F2B6E ON source_videos (major_source_id)');
    }
}
