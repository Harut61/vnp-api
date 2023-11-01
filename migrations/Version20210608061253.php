<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210608061253 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE show_source (show_id INT NOT NULL, source_id INT NOT NULL, INDEX IDX_D0ED50E8D0C1FC64 (show_id), INDEX IDX_D0ED50E8953C1C61 (source_id), PRIMARY KEY(show_id, source_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE show_source ADD CONSTRAINT FK_D0ED50E8D0C1FC64 FOREIGN KEY (show_id) REFERENCES shows (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE show_source ADD CONSTRAINT FK_D0ED50E8953C1C61 FOREIGN KEY (source_id) REFERENCES sources (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE ftp_folder_source');
        $this->addSql('DROP TABLE source_video_source');
        $this->addSql('ALTER TABLE end_users CHANGE is_apple_private_email is_apple_private_email TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE ftp_folders DROP FOREIGN KEY FK_33BAE6BB953C1C61');
        $this->addSql('DROP INDEX IDX_33BAE6BB953C1C61 ON ftp_folders');
        $this->addSql('ALTER TABLE ftp_folders DROP source_id');
        $this->addSql('ALTER TABLE shows ADD source_id INT DEFAULT NULL, ADD show_duration VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE shows ADD CONSTRAINT FK_6C3BF144953C1C61 FOREIGN KEY (source_id) REFERENCES sources (id)');
        $this->addSql('CREATE INDEX IDX_6C3BF144953C1C61 ON shows (source_id)');
        $this->addSql('ALTER TABLE source_videos DROP FOREIGN KEY FK_9F5541D4953C1C61');
        $this->addSql('DROP INDEX IDX_9F5541D4953C1C61 ON source_videos');
        $this->addSql('ALTER TABLE source_videos DROP source_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ftp_folder_source (ftp_folder_id INT NOT NULL, source_id INT NOT NULL, INDEX IDX_D4EC0865C036DB50 (ftp_folder_id), INDEX IDX_D4EC0865953C1C61 (source_id), PRIMARY KEY(ftp_folder_id, source_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE source_video_source (source_video_id INT NOT NULL, source_id INT NOT NULL, INDEX IDX_B13703066AE1369C (source_video_id), INDEX IDX_B1370306953C1C61 (source_id), PRIMARY KEY(source_video_id, source_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE ftp_folder_source ADD CONSTRAINT FK_D4EC0865953C1C61 FOREIGN KEY (source_id) REFERENCES sources (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ftp_folder_source ADD CONSTRAINT FK_D4EC0865C036DB50 FOREIGN KEY (ftp_folder_id) REFERENCES ftp_folders (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE source_video_source ADD CONSTRAINT FK_B13703066AE1369C FOREIGN KEY (source_video_id) REFERENCES source_videos (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE source_video_source ADD CONSTRAINT FK_B1370306953C1C61 FOREIGN KEY (source_id) REFERENCES sources (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE show_source');
        $this->addSql('ALTER TABLE end_users CHANGE is_apple_private_email is_apple_private_email TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE ftp_folders ADD source_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ftp_folders ADD CONSTRAINT FK_33BAE6BB953C1C61 FOREIGN KEY (source_id) REFERENCES sources (id)');
        $this->addSql('CREATE INDEX IDX_33BAE6BB953C1C61 ON ftp_folders (source_id)');
        $this->addSql('ALTER TABLE shows DROP FOREIGN KEY FK_6C3BF144953C1C61');
        $this->addSql('DROP INDEX IDX_6C3BF144953C1C61 ON shows');
        $this->addSql('ALTER TABLE shows DROP source_id, DROP show_duration');
        $this->addSql('ALTER TABLE source_videos ADD source_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE source_videos ADD CONSTRAINT FK_9F5541D4953C1C61 FOREIGN KEY (source_id) REFERENCES sources (id)');
        $this->addSql('CREATE INDEX IDX_9F5541D4953C1C61 ON source_videos (source_id)');
    }
}
