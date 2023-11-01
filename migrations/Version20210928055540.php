<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210928055540 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE lineups ADD line_up_content_json JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE vods DROP INDEX UNIQ_E4062264CCC0DAE6, ADD INDEX IDX_E4062264CCC0DAE6 (interstitial_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE lineups DROP line_up_content_json');
        $this->addSql('ALTER TABLE vods DROP INDEX IDX_E4062264CCC0DAE6, ADD UNIQUE INDEX UNIQ_E4062264CCC0DAE6 (interstitial_id)');
    }
}
