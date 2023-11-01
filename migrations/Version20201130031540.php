<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201130031540 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE transcoding_queue DROP INDEX UNIQ_467367DE5A9FD395, ADD INDEX IDX_467367DE5A9FD395 (vod_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE transcoding_queue DROP INDEX IDX_467367DE5A9FD395, ADD UNIQUE INDEX UNIQ_467367DE5A9FD395 (vod_id)');
    }
}
