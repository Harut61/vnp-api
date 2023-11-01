<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201103012906 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE audit_shows (id INT AUTO_INCREMENT NOT NULL, object_id INT DEFAULT NULL, type VARCHAR(10) NOT NULL, discriminator VARCHAR(255) DEFAULT NULL, transaction_hash VARCHAR(40) DEFAULT NULL, diffs JSON DEFAULT NULL, blame_id VARCHAR(255) DEFAULT NULL, blame_user VARCHAR(255) DEFAULT NULL, blame_user_fqdn VARCHAR(255) DEFAULT NULL, blame_user_firewall VARCHAR(100) DEFAULT NULL, ip VARCHAR(45) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_6B7095B3232D562B (object_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE audit_sub_titles (id INT AUTO_INCREMENT NOT NULL, object_id INT DEFAULT NULL, type VARCHAR(10) NOT NULL, discriminator VARCHAR(255) DEFAULT NULL, transaction_hash VARCHAR(40) DEFAULT NULL, diffs JSON DEFAULT NULL, blame_id VARCHAR(255) DEFAULT NULL, blame_user VARCHAR(255) DEFAULT NULL, blame_user_fqdn VARCHAR(255) DEFAULT NULL, blame_user_firewall VARCHAR(100) DEFAULT NULL, ip VARCHAR(45) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_ECA1610232D562B (object_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE audit_vods (id INT AUTO_INCREMENT NOT NULL, object_id INT DEFAULT NULL, type VARCHAR(10) NOT NULL, discriminator VARCHAR(255) DEFAULT NULL, transaction_hash VARCHAR(40) DEFAULT NULL, diffs JSON DEFAULT NULL, blame_id VARCHAR(255) DEFAULT NULL, blame_user VARCHAR(255) DEFAULT NULL, blame_user_fqdn VARCHAR(255) DEFAULT NULL, blame_user_firewall VARCHAR(100) DEFAULT NULL, ip VARCHAR(45) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_C2A6CC60232D562B (object_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE shows (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, title VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, is_active TINYINT(1) DEFAULT \'0\' NOT NULL, position INT NOT NULL, slug VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_6C3BF144989D9B62 (slug), INDEX IDX_6C3BF144DE12AB56 (created_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE source_videos (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, show_id INT DEFAULT NULL, major_source_id INT DEFAULT NULL, vod_id INT DEFAULT NULL, published_at DATETIME NOT NULL, status VARCHAR(10) DEFAULT \'CREATED\' NOT NULL, title VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, INDEX IDX_9F5541D4DE12AB56 (created_by), INDEX IDX_9F5541D4D0C1FC64 (show_id), INDEX IDX_9F5541D48B3F2B6E (major_source_id), UNIQUE INDEX UNIQ_9F5541D45A9FD395 (vod_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE source_video_source (source_video_id INT NOT NULL, source_id INT NOT NULL, INDEX IDX_B13703066AE1369C (source_video_id), INDEX IDX_B1370306953C1C61 (source_id), PRIMARY KEY(source_video_id, source_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE source_video_story (source_video_id INT NOT NULL, story_id INT NOT NULL, INDEX IDX_584B5D3A6AE1369C (source_video_id), INDEX IDX_584B5D3AAA5D4036 (story_id), PRIMARY KEY(source_video_id, story_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE source_video_sub_title (source_video_id INT NOT NULL, sub_title_id INT NOT NULL, INDEX IDX_A3124F4C6AE1369C (source_video_id), INDEX IDX_A3124F4CB03069E4 (sub_title_id), PRIMARY KEY(source_video_id, sub_title_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE story_sub_title (story_id INT NOT NULL, sub_title_id INT NOT NULL, INDEX IDX_3EC826A3AA5D4036 (story_id), INDEX IDX_3EC826A3B03069E4 (sub_title_id), PRIMARY KEY(story_id, sub_title_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sub_titles (id INT AUTO_INCREMENT NOT NULL, sub_lang VARCHAR(255) DEFAULT NULL, resource_url VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE vods (id INT AUTO_INCREMENT NOT NULL, original_extension VARCHAR(255) DEFAULT NULL, original_file_name VARCHAR(255) DEFAULT NULL, original_file_path VARCHAR(255) DEFAULT NULL, playBackId VARCHAR(255) DEFAULT NULL, total_size INT DEFAULT 0 NOT NULL, video_width INT DEFAULT NULL, video_height INT DEFAULT NULL, duration VARCHAR(255) DEFAULT NULL, video_codec VARCHAR(255) DEFAULT NULL, videofps VARCHAR(255) DEFAULT NULL, video_bitrate VARCHAR(255) DEFAULT NULL, display_aspect_ration VARCHAR(255) DEFAULT NULL, audio_codec VARCHAR(255) DEFAULT NULL, audio_bitrate VARCHAR(255) DEFAULT NULL, video_path VARCHAR(255) DEFAULT NULL, resolutions LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', title VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, online TINYINT(1) DEFAULT \'0\' NOT NULL, status VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE audit_shows ADD CONSTRAINT FK_6B7095B3232D562B FOREIGN KEY (object_id) REFERENCES shows (id)');
        $this->addSql('ALTER TABLE audit_sub_titles ADD CONSTRAINT FK_ECA1610232D562B FOREIGN KEY (object_id) REFERENCES sub_titles (id)');
        $this->addSql('ALTER TABLE audit_vods ADD CONSTRAINT FK_C2A6CC60232D562B FOREIGN KEY (object_id) REFERENCES vods (id)');
        $this->addSql('ALTER TABLE shows ADD CONSTRAINT FK_6C3BF144DE12AB56 FOREIGN KEY (created_by) REFERENCES admin_users (id)');
        $this->addSql('ALTER TABLE source_videos ADD CONSTRAINT FK_9F5541D4DE12AB56 FOREIGN KEY (created_by) REFERENCES admin_users (id)');
        $this->addSql('ALTER TABLE source_videos ADD CONSTRAINT FK_9F5541D4D0C1FC64 FOREIGN KEY (show_id) REFERENCES shows (id)');
        $this->addSql('ALTER TABLE source_videos ADD CONSTRAINT FK_9F5541D48B3F2B6E FOREIGN KEY (major_source_id) REFERENCES sources (id)');
        $this->addSql('ALTER TABLE source_videos ADD CONSTRAINT FK_9F5541D45A9FD395 FOREIGN KEY (vod_id) REFERENCES vods (id)');
        $this->addSql('ALTER TABLE source_video_source ADD CONSTRAINT FK_B13703066AE1369C FOREIGN KEY (source_video_id) REFERENCES source_videos (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE source_video_source ADD CONSTRAINT FK_B1370306953C1C61 FOREIGN KEY (source_id) REFERENCES sources (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE source_video_story ADD CONSTRAINT FK_584B5D3A6AE1369C FOREIGN KEY (source_video_id) REFERENCES source_videos (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE source_video_story ADD CONSTRAINT FK_584B5D3AAA5D4036 FOREIGN KEY (story_id) REFERENCES stories (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE source_video_sub_title ADD CONSTRAINT FK_A3124F4C6AE1369C FOREIGN KEY (source_video_id) REFERENCES source_videos (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE source_video_sub_title ADD CONSTRAINT FK_A3124F4CB03069E4 FOREIGN KEY (sub_title_id) REFERENCES sub_titles (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE story_sub_title ADD CONSTRAINT FK_3EC826A3AA5D4036 FOREIGN KEY (story_id) REFERENCES stories (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE story_sub_title ADD CONSTRAINT FK_3EC826A3B03069E4 FOREIGN KEY (sub_title_id) REFERENCES sub_titles (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE stories DROP FOREIGN KEY FK_9C8B9D5F953C1C61');
        $this->addSql('ALTER TABLE stories DROP FOREIGN KEY FK_9C8B9D5F96B01C11');
        $this->addSql('ALTER TABLE stories ADD source_video_id INT DEFAULT NULL, ADD vod_id INT DEFAULT NULL, ADD show_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE stories ADD CONSTRAINT FK_9C8B9D5F6AE1369C FOREIGN KEY (source_video_id) REFERENCES source_videos (id)');
        $this->addSql('ALTER TABLE stories ADD CONSTRAINT FK_9C8B9D5F5A9FD395 FOREIGN KEY (vod_id) REFERENCES vods (id)');
        $this->addSql('ALTER TABLE stories ADD CONSTRAINT FK_9C8B9D5FD0C1FC64 FOREIGN KEY (show_id) REFERENCES shows (id)');
        $this->addSql('ALTER TABLE stories ADD CONSTRAINT FK_9C8B9D5F953C1C61 FOREIGN KEY (source_id) REFERENCES sources (id)');
        $this->addSql('ALTER TABLE stories ADD CONSTRAINT FK_9C8B9D5F96B01C11 FOREIGN KEY (story_type_id) REFERENCES story_types (id)');
        $this->addSql('CREATE INDEX IDX_9C8B9D5F6AE1369C ON stories (source_video_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9C8B9D5F5A9FD395 ON stories (vod_id)');
        $this->addSql('CREATE INDEX IDX_9C8B9D5FD0C1FC64 ON stories (show_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE audit_shows DROP FOREIGN KEY FK_6B7095B3232D562B');
        $this->addSql('ALTER TABLE source_videos DROP FOREIGN KEY FK_9F5541D4D0C1FC64');
        $this->addSql('ALTER TABLE stories DROP FOREIGN KEY FK_9C8B9D5FD0C1FC64');
        $this->addSql('ALTER TABLE source_video_source DROP FOREIGN KEY FK_B13703066AE1369C');
        $this->addSql('ALTER TABLE source_video_story DROP FOREIGN KEY FK_584B5D3A6AE1369C');
        $this->addSql('ALTER TABLE source_video_sub_title DROP FOREIGN KEY FK_A3124F4C6AE1369C');
        $this->addSql('ALTER TABLE stories DROP FOREIGN KEY FK_9C8B9D5F6AE1369C');
        $this->addSql('ALTER TABLE audit_sub_titles DROP FOREIGN KEY FK_ECA1610232D562B');
        $this->addSql('ALTER TABLE source_video_sub_title DROP FOREIGN KEY FK_A3124F4CB03069E4');
        $this->addSql('ALTER TABLE story_sub_title DROP FOREIGN KEY FK_3EC826A3B03069E4');
        $this->addSql('ALTER TABLE audit_vods DROP FOREIGN KEY FK_C2A6CC60232D562B');
        $this->addSql('ALTER TABLE source_videos DROP FOREIGN KEY FK_9F5541D45A9FD395');
        $this->addSql('ALTER TABLE stories DROP FOREIGN KEY FK_9C8B9D5F5A9FD395');
        $this->addSql('DROP TABLE audit_shows');
        $this->addSql('DROP TABLE audit_sub_titles');
        $this->addSql('DROP TABLE audit_vods');
        $this->addSql('DROP TABLE shows');
        $this->addSql('DROP TABLE source_videos');
        $this->addSql('DROP TABLE source_video_source');
        $this->addSql('DROP TABLE source_video_story');
        $this->addSql('DROP TABLE source_video_sub_title');
        $this->addSql('DROP TABLE story_sub_title');
        $this->addSql('DROP TABLE sub_titles');
        $this->addSql('DROP TABLE vods');
        $this->addSql('ALTER TABLE stories DROP FOREIGN KEY FK_9C8B9D5F953C1C61');
        $this->addSql('ALTER TABLE stories DROP FOREIGN KEY FK_9C8B9D5F96B01C11');
        $this->addSql('DROP INDEX IDX_9C8B9D5F6AE1369C ON stories');
        $this->addSql('DROP INDEX UNIQ_9C8B9D5F5A9FD395 ON stories');
        $this->addSql('DROP INDEX IDX_9C8B9D5FD0C1FC64 ON stories');
        $this->addSql('ALTER TABLE stories DROP source_video_id, DROP vod_id, DROP show_id');
        $this->addSql('ALTER TABLE stories ADD CONSTRAINT FK_9C8B9D5F953C1C61 FOREIGN KEY (source_id) REFERENCES sources (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE stories ADD CONSTRAINT FK_9C8B9D5F96B01C11 FOREIGN KEY (story_type_id) REFERENCES story_types (id) ON DELETE SET NULL');
    }
}
