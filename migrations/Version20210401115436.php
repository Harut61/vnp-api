<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210401115436 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ftp_folder_source (ftp_folder_id INT NOT NULL, source_id INT NOT NULL, INDEX IDX_D4EC0865C036DB50 (ftp_folder_id), INDEX IDX_D4EC0865953C1C61 (source_id), PRIMARY KEY(ftp_folder_id, source_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ftp_folder_source ADD CONSTRAINT FK_D4EC0865C036DB50 FOREIGN KEY (ftp_folder_id) REFERENCES ftp_folders (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ftp_folder_source ADD CONSTRAINT FK_D4EC0865953C1C61 FOREIGN KEY (source_id) REFERENCES sources (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE ftpfolder_source');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ftpfolder_source (ftpfolder_id INT NOT NULL, source_id INT NOT NULL, INDEX IDX_5562E03BF42D0682 (ftpfolder_id), INDEX IDX_5562E03B953C1C61 (source_id), PRIMARY KEY(ftpfolder_id, source_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE ftpfolder_source ADD CONSTRAINT FK_5562E03B953C1C61 FOREIGN KEY (source_id) REFERENCES sources (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ftpfolder_source ADD CONSTRAINT FK_5562E03BF42D0682 FOREIGN KEY (ftpfolder_id) REFERENCES ftp_folders (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE ftp_folder_source');
    }
}
