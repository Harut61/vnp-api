<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210718202100 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE interstitial (id INT AUTO_INCREMENT NOT NULL, segment INT DEFAULT NULL, vod_id INT DEFAULT NULL, title VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, slug VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_89077A80989D9B62 (slug), INDEX IDX_89077A801881F565 (segment), UNIQUE INDEX UNIQ_89077A805A9FD395 (vod_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE segment (id INT AUTO_INCREMENT NOT NULL, vne_id VARCHAR(255) NOT NULL, vne_title VARCHAR(255) DEFAULT NULL, title VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE interstitial ADD CONSTRAINT FK_89077A801881F565 FOREIGN KEY (segment) REFERENCES segment (id)');
        $this->addSql('ALTER TABLE interstitial ADD CONSTRAINT FK_89077A805A9FD395 FOREIGN KEY (vod_id) REFERENCES vods (id)');
        $this->addSql('ALTER TABLE stories ADD playlist_length INT DEFAULT NULL, ADD last_chunk_duration DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE vods ADD interstitial_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE vods ADD CONSTRAINT FK_E4062264CCC0DAE6 FOREIGN KEY (interstitial_id) REFERENCES interstitial (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E4062264CCC0DAE6 ON vods (interstitial_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE vods DROP FOREIGN KEY FK_E4062264CCC0DAE6');
        $this->addSql('ALTER TABLE interstitial DROP FOREIGN KEY FK_89077A801881F565');
        $this->addSql('DROP TABLE interstitial');
        $this->addSql('DROP TABLE segment');
        $this->addSql('ALTER TABLE stories DROP playlist_length, DROP last_chunk_duration');
        $this->addSql('DROP INDEX UNIQ_E4062264CCC0DAE6 ON vods');
        $this->addSql('ALTER TABLE vods DROP interstitial_id');
    }
}
