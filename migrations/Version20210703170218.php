<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210703170218 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('CREATE TABLE audit_line_up_stories (id INT AUTO_INCREMENT NOT NULL, object_id INT DEFAULT NULL, type VARCHAR(10) NOT NULL, discriminator VARCHAR(255) DEFAULT NULL, transaction_hash VARCHAR(40) DEFAULT NULL, diffs LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', blame_id VARCHAR(255) DEFAULT NULL, blame_user VARCHAR(255) DEFAULT NULL, blame_user_fqdn VARCHAR(255) DEFAULT NULL, blame_user_firewall VARCHAR(100) DEFAULT NULL, ip VARCHAR(45) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_725CF2A2232D562B (object_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE audit_line_ups (id INT AUTO_INCREMENT NOT NULL, object_id INT DEFAULT NULL, type VARCHAR(10) NOT NULL, discriminator VARCHAR(255) DEFAULT NULL, transaction_hash VARCHAR(40) DEFAULT NULL, diffs LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', blame_id VARCHAR(255) DEFAULT NULL, blame_user VARCHAR(255) DEFAULT NULL, blame_user_fqdn VARCHAR(255) DEFAULT NULL, blame_user_firewall VARCHAR(100) DEFAULT NULL, ip VARCHAR(45) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_E8174A6A232D562B (object_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE folders (id INT AUTO_INCREMENT NOT NULL, ftp_server_id INT DEFAULT NULL, show_id INT DEFAULT NULL, created_by INT DEFAULT NULL, time_zone_id INT DEFAULT NULL, path LONGTEXT DEFAULT NULL, publication_date DATETIME DEFAULT NULL, data_retrieval_at DATETIME DEFAULT NULL, data_retrieval_status VARCHAR(255) DEFAULT NULL, folder VARCHAR(255) DEFAULT NULL, sub_folder VARCHAR(255) DEFAULT NULL, folder_type VARCHAR(255) DEFAULT \'FTP\' NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, is_active TINYINT(1) DEFAULT \'0\' NOT NULL, INDEX IDX_FE37D30FCE5E84A5 (ftp_server_id), INDEX IDX_FE37D30FD0C1FC64 (show_id), INDEX IDX_FE37D30FDE12AB56 (created_by), INDEX IDX_FE37D30FCBAB9ECD (time_zone_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE lineups (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, created_by INT DEFAULT NULL, vne_lineup_id VARCHAR(255) NOT NULL, lineup_duration VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, is_active TINYINT(1) DEFAULT \'0\' NOT NULL, INDEX IDX_60729AABA76ED395 (user_id), INDEX IDX_60729AABDE12AB56 (created_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE lineups_stories (id INT AUTO_INCREMENT NOT NULL, line_up_id INT DEFAULT NULL, story_id INT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, is_active TINYINT(1) DEFAULT \'0\' NOT NULL, INDEX IDX_8B84750DFAAB039F (line_up_id), INDEX IDX_8B84750DAA5D4036 (story_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE audit_line_up_stories ADD CONSTRAINT FK_725CF2A2232D562B FOREIGN KEY (object_id) REFERENCES lineups_stories (id)');
        $this->addSql('ALTER TABLE audit_line_ups ADD CONSTRAINT FK_E8174A6A232D562B FOREIGN KEY (object_id) REFERENCES lineups (id)');
        $this->addSql('ALTER TABLE folders ADD CONSTRAINT FK_FE37D30FCE5E84A5 FOREIGN KEY (ftp_server_id) REFERENCES ftp_servers (id)');
        $this->addSql('ALTER TABLE folders ADD CONSTRAINT FK_FE37D30FD0C1FC64 FOREIGN KEY (show_id) REFERENCES shows (id)');
        $this->addSql('ALTER TABLE folders ADD CONSTRAINT FK_FE37D30FDE12AB56 FOREIGN KEY (created_by) REFERENCES admin_users (id)');
        $this->addSql('ALTER TABLE folders ADD CONSTRAINT FK_FE37D30FCBAB9ECD FOREIGN KEY (time_zone_id) REFERENCES time_zones (id)');
        $this->addSql('ALTER TABLE lineups ADD CONSTRAINT FK_60729AABA76ED395 FOREIGN KEY (user_id) REFERENCES admin_users (id)');
        $this->addSql('ALTER TABLE lineups ADD CONSTRAINT FK_60729AABDE12AB56 FOREIGN KEY (created_by) REFERENCES admin_users (id)');
        $this->addSql('ALTER TABLE lineups_stories ADD CONSTRAINT FK_8B84750DFAAB039F FOREIGN KEY (line_up_id) REFERENCES lineups (id)');
        $this->addSql('ALTER TABLE lineups_stories ADD CONSTRAINT FK_8B84750DAA5D4036 FOREIGN KEY (story_id) REFERENCES stories (id)');
        $this->addSql('DROP TABLE ftp_folders');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE audit_line_ups DROP FOREIGN KEY FK_E8174A6A232D562B');
        $this->addSql('ALTER TABLE lineups_stories DROP FOREIGN KEY FK_8B84750DFAAB039F');
        $this->addSql('ALTER TABLE audit_line_up_stories DROP FOREIGN KEY FK_725CF2A2232D562B');
        $this->addSql('CREATE TABLE ftp_folders (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, time_zone_id INT DEFAULT NULL, path LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, is_active TINYINT(1) DEFAULT \'0\' NOT NULL, show_id INT DEFAULT NULL, data_retrieval_at DATETIME DEFAULT NULL, data_retrieval_status VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, publication_date DATETIME DEFAULT NULL, INDEX IDX_33BAE6BBDE12AB56 (created_by), INDEX IDX_33BAE6BBCBAB9ECD (time_zone_id), INDEX IDX_33BAE6BBD0C1FC64 (show_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE ftp_folders ADD CONSTRAINT FK_33BAE6BBCBAB9ECD FOREIGN KEY (time_zone_id) REFERENCES time_zones (id)');
        $this->addSql('ALTER TABLE ftp_folders ADD CONSTRAINT FK_33BAE6BBDE12AB56 FOREIGN KEY (created_by) REFERENCES admin_users (id)');
        $this->addSql('DROP TABLE audit_line_up_stories');
        $this->addSql('DROP TABLE audit_line_ups');
        $this->addSql('DROP TABLE folders');
        $this->addSql('DROP TABLE lineups');
        $this->addSql('DROP TABLE lineups_stories');
    }
}
