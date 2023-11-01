<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210615053947 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE media_object (id INT AUTO_INCREMENT NOT NULL, content_url VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE shows ADD delay_news_market_list JSON NOT NULL');
        $this->addSql('ALTER TABLE source_videos ADD media_object INT DEFAULT NULL');
        $this->addSql('ALTER TABLE source_videos ADD CONSTRAINT FK_9F5541D414D43132 FOREIGN KEY (media_object) REFERENCES media_object (id)');
        $this->addSql('CREATE INDEX IDX_9F5541D414D43132 ON source_videos (media_object)');
        $this->addSql('ALTER TABLE sources ADD media_object INT DEFAULT NULL');
        $this->addSql('ALTER TABLE sources ADD CONSTRAINT FK_D25D65F214D43132 FOREIGN KEY (media_object) REFERENCES media_object (id)');
        $this->addSql('CREATE INDEX IDX_D25D65F214D43132 ON sources (media_object)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE source_videos DROP FOREIGN KEY FK_9F5541D414D43132');
        $this->addSql('ALTER TABLE sources DROP FOREIGN KEY FK_D25D65F214D43132');
        $this->addSql('DROP TABLE media_object');
        $this->addSql('ALTER TABLE shows DROP delay_news_market_list');
        $this->addSql('DROP INDEX IDX_9F5541D414D43132 ON source_videos');
        $this->addSql('ALTER TABLE source_videos DROP media_object');
        $this->addSql('DROP INDEX IDX_D25D65F214D43132 ON sources');
        $this->addSql('ALTER TABLE sources DROP media_object');
    }
}
