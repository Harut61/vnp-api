<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210302040918 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE source_video_story');
        $this->addSql('ALTER TABLE source_videos ADD uploaded_at DATETIME DEFAULT NULL, ADD status_updated_at DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE source_video_story (source_video_id INT NOT NULL, story_id INT NOT NULL, INDEX IDX_584B5D3AAA5D4036 (story_id), INDEX IDX_584B5D3A6AE1369C (source_video_id), PRIMARY KEY(source_video_id, story_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE source_video_story ADD CONSTRAINT FK_584B5D3A6AE1369C FOREIGN KEY (source_video_id) REFERENCES source_videos (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE source_video_story ADD CONSTRAINT FK_584B5D3AAA5D4036 FOREIGN KEY (story_id) REFERENCES stories (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE source_videos DROP uploaded_at, DROP status_updated_at');
    }
}
