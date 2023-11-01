<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210920103150 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE lineups DROP FOREIGN KEY FK_60729AABA76ED395');
        $this->addSql('ALTER TABLE lineups ADD first_line_up TINYINT(1) NOT NULL, ADD longitude DOUBLE PRECISION DEFAULT NULL, ADD latitude DOUBLE PRECISION DEFAULT NULL, ADD ip_address VARCHAR(255) DEFAULT NULL, CHANGE vne_lineup_id vne_lineup_id VARCHAR(255) DEFAULT NULL, CHANGE lineup_duration lineup_duration VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE lineups ADD CONSTRAINT FK_60729AABA76ED395 FOREIGN KEY (user_id) REFERENCES end_users (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE lineups DROP FOREIGN KEY FK_60729AABA76ED395');
        $this->addSql('ALTER TABLE lineups DROP first_line_up, DROP longitude, DROP latitude, DROP ip_address, CHANGE vne_lineup_id vne_lineup_id VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE lineup_duration lineup_duration VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE lineups ADD CONSTRAINT FK_60729AABA76ED395 FOREIGN KEY (user_id) REFERENCES admin_users (id)');
    }
}
