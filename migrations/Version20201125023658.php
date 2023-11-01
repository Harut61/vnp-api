<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201125023658 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE transcoding_profile (id INT AUTO_INCREMENT NOT NULL, is_default TINYINT(1) DEFAULT \'0\' NOT NULL, title VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE transcoding_profile_transcoding_profile_option (transcoding_profile_id INT NOT NULL, transcoding_profile_option_id INT NOT NULL, INDEX IDX_AC109FD4542F1A5D (transcoding_profile_id), INDEX IDX_AC109FD49F71C6AB (transcoding_profile_option_id), PRIMARY KEY(transcoding_profile_id, transcoding_profile_option_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE transcoding_profile_option (id INT AUTO_INCREMENT NOT NULL, fps VARCHAR(255) DEFAULT NULL, audio_codec VARCHAR(255) DEFAULT NULL, video_codec VARCHAR(255) DEFAULT NULL, video_width INT DEFAULT NULL, video_height INT DEFAULT NULL, video_bitrate INT DEFAULT NULL, audio_bitrate INT DEFAULT NULL, profile VARCHAR(255) DEFAULT NULL, container VARCHAR(255) DEFAULT NULL, title VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, description LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE transcoding_profile_transcoding_profile_option ADD CONSTRAINT FK_AC109FD4542F1A5D FOREIGN KEY (transcoding_profile_id) REFERENCES transcoding_profile (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE transcoding_profile_transcoding_profile_option ADD CONSTRAINT FK_AC109FD49F71C6AB FOREIGN KEY (transcoding_profile_option_id) REFERENCES transcoding_profile_option (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE audit_transcoding_profile DROP FOREIGN KEY FK_69019BF0232D562B');
        $this->addSql('ALTER TABLE transcoding_profile_transcoding_profile_option DROP FOREIGN KEY FK_AC109FD4542F1A5D');
        $this->addSql('ALTER TABLE audit_transcoding_profile_option DROP FOREIGN KEY FK_CB24AA53232D562B');
        $this->addSql('ALTER TABLE transcoding_profile_transcoding_profile_option DROP FOREIGN KEY FK_AC109FD49F71C6AB');
        $this->addSql('DROP TABLE transcoding_profile');
        $this->addSql('DROP TABLE transcoding_profile_transcoding_profile_option');
        $this->addSql('DROP TABLE transcoding_profile_option');
    }
}
