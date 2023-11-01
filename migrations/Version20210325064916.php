<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210325064916 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ftp_folders (id INT AUTO_INCREMENT NOT NULL, source_id INT DEFAULT NULL, created_by INT DEFAULT NULL, time_zone_id INT DEFAULT NULL, path LONGTEXT DEFAULT NULL, published_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, is_active TINYINT(1) DEFAULT \'0\' NOT NULL, show_id INT DEFAULT NULL, INDEX IDX_33BAE6BBD0C1FC64 (show_id), INDEX IDX_33BAE6BB953C1C61 (source_id), INDEX IDX_33BAE6BBDE12AB56 (created_by), INDEX IDX_33BAE6BBCBAB9ECD (time_zone_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ftpfolder_source (ftpfolder_id INT NOT NULL, source_id INT NOT NULL, INDEX IDX_5562E03BF42D0682 (ftpfolder_id), INDEX IDX_5562E03B953C1C61 (source_id), PRIMARY KEY(ftpfolder_id, source_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ftp_servers (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, contact_name VARCHAR(255) NOT NULL, contact_email VARCHAR(255) NOT NULL, contact_phone VARCHAR(255) NOT NULL, port VARCHAR(255) NOT NULL, host VARCHAR(255) NOT NULL, protocol VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, is_active TINYINT(1) DEFAULT \'0\' NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ftp_folders ADD CONSTRAINT FK_33BAE6BB953C1C61 FOREIGN KEY (source_id) REFERENCES sources (id)');
        $this->addSql('ALTER TABLE ftp_folders ADD CONSTRAINT FK_33BAE6BBDE12AB56 FOREIGN KEY (created_by) REFERENCES admin_users (id)');
        $this->addSql('ALTER TABLE ftp_folders ADD CONSTRAINT FK_33BAE6BBCBAB9ECD FOREIGN KEY (time_zone_id) REFERENCES time_zones (id)');
        $this->addSql('ALTER TABLE ftpfolder_source ADD CONSTRAINT FK_5562E03BF42D0682 FOREIGN KEY (ftpfolder_id) REFERENCES ftp_folders (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ftpfolder_source ADD CONSTRAINT FK_5562E03B953C1C61 FOREIGN KEY (source_id) REFERENCES sources (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ftpfolder_source DROP FOREIGN KEY FK_5562E03BF42D0682');
        $this->addSql('DROP TABLE ftp_folders');
        $this->addSql('DROP TABLE ftpfolder_source');
        $this->addSql('DROP TABLE ftp_servers');
    }
}
