<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201130022758 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE audit_transcoding_profile (id INT AUTO_INCREMENT NOT NULL, object_id INT DEFAULT NULL, type VARCHAR(10) NOT NULL, discriminator VARCHAR(255) DEFAULT NULL, transaction_hash VARCHAR(40) DEFAULT NULL, diffs JSON DEFAULT NULL, blame_id VARCHAR(255) DEFAULT NULL, blame_user VARCHAR(255) DEFAULT NULL, blame_user_fqdn VARCHAR(255) DEFAULT NULL, blame_user_firewall VARCHAR(100) DEFAULT NULL, ip VARCHAR(45) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_69019BF0232D562B (object_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE audit_transcoding_profile_option (id INT AUTO_INCREMENT NOT NULL, object_id INT DEFAULT NULL, type VARCHAR(10) NOT NULL, discriminator VARCHAR(255) DEFAULT NULL, transaction_hash VARCHAR(40) DEFAULT NULL, diffs JSON DEFAULT NULL, blame_id VARCHAR(255) DEFAULT NULL, blame_user VARCHAR(255) DEFAULT NULL, blame_user_fqdn VARCHAR(255) DEFAULT NULL, blame_user_firewall VARCHAR(100) DEFAULT NULL, ip VARCHAR(45) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_CB24AA53232D562B (object_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE transcoding_queue (id INT AUTO_INCREMENT NOT NULL, vod_id INT DEFAULT NULL, aws_job_id VARCHAR(255) DEFAULT NULL, job_details JSON DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_467367DE5A9FD395 (vod_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE audit_transcoding_profile ADD CONSTRAINT FK_69019BF0232D562B FOREIGN KEY (object_id) REFERENCES transcoding_profile (id)');
        $this->addSql('ALTER TABLE audit_transcoding_profile_option ADD CONSTRAINT FK_CB24AA53232D562B FOREIGN KEY (object_id) REFERENCES transcoding_profile_option (id)');
        $this->addSql('ALTER TABLE transcoding_queue ADD CONSTRAINT FK_467367DE5A9FD395 FOREIGN KEY (vod_id) REFERENCES vods (id)');
        $this->addSql('ALTER TABLE source_videos CHANGE published_at published_at DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE audit_transcoding_profile');
        $this->addSql('DROP TABLE audit_transcoding_profile_option');
        $this->addSql('DROP TABLE transcoding_queue');
        $this->addSql('ALTER TABLE source_videos CHANGE published_at published_at DATETIME NOT NULL');
    }
}
