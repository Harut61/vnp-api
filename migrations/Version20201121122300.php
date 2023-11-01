<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201121122300 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs

        $this->addSql('ALTER TABLE vods ADD source_video_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE vods ADD CONSTRAINT FK_E40622646AE1369C FOREIGN KEY (source_video_id) REFERENCES source_videos (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E40622646AE1369C ON vods (source_video_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE vods DROP FOREIGN KEY FK_E40622646AE1369C');
        $this->addSql('DROP INDEX UNIQ_E40622646AE1369C ON vods');
        $this->addSql('ALTER TABLE vods DROP source_video_id');
    }
}
