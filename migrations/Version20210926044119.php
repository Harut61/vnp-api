<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210926044119 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE audit_line_up_stories DROP FOREIGN KEY FK_725CF2A2232D562B');
        $this->addSql('CREATE TABLE lineup_content (id INT AUTO_INCREMENT NOT NULL, line_up_id INT DEFAULT NULL, story_id INT DEFAULT NULL, interstitial_id INT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, INDEX IDX_1179B1ADFAAB039F (line_up_id), INDEX IDX_1179B1ADAA5D4036 (story_id), INDEX IDX_1179B1ADCCC0DAE6 (interstitial_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE line_up_story (line_up_id INT NOT NULL, story_id INT NOT NULL, INDEX IDX_6E3589CBFAAB039F (line_up_id), INDEX IDX_6E3589CBAA5D4036 (story_id), PRIMARY KEY(line_up_id, story_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE line_up_interstitial (line_up_id INT NOT NULL, interstitial_id INT NOT NULL, INDEX IDX_7AAA1656FAAB039F (line_up_id), INDEX IDX_7AAA1656CCC0DAE6 (interstitial_id), PRIMARY KEY(line_up_id, interstitial_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE lineup_content ADD CONSTRAINT FK_1179B1ADFAAB039F FOREIGN KEY (line_up_id) REFERENCES lineups (id)');
        $this->addSql('ALTER TABLE lineup_content ADD CONSTRAINT FK_1179B1ADAA5D4036 FOREIGN KEY (story_id) REFERENCES stories (id)');
        $this->addSql('ALTER TABLE lineup_content ADD CONSTRAINT FK_1179B1ADCCC0DAE6 FOREIGN KEY (interstitial_id) REFERENCES interstitial (id)');
        $this->addSql('ALTER TABLE line_up_story ADD CONSTRAINT FK_6E3589CBFAAB039F FOREIGN KEY (line_up_id) REFERENCES lineups (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE line_up_story ADD CONSTRAINT FK_6E3589CBAA5D4036 FOREIGN KEY (story_id) REFERENCES stories (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE line_up_interstitial ADD CONSTRAINT FK_7AAA1656FAAB039F FOREIGN KEY (line_up_id) REFERENCES lineups (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE line_up_interstitial ADD CONSTRAINT FK_7AAA1656CCC0DAE6 FOREIGN KEY (interstitial_id) REFERENCES interstitial (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE audit_line_up_stories');
        $this->addSql('DROP TABLE lineups_stories');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE audit_line_up_stories (id INT AUTO_INCREMENT NOT NULL, object_id INT DEFAULT NULL, type VARCHAR(10) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, discriminator VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, transaction_hash VARCHAR(40) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, diffs JSON CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, blame_id VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, blame_user VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, blame_user_fqdn VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, blame_user_firewall VARCHAR(100) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, ip VARCHAR(45) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_725CF2A2232D562B (object_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE lineups_stories (id INT AUTO_INCREMENT NOT NULL, line_up_id INT DEFAULT NULL, story_id INT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, is_active TINYINT(1) DEFAULT \'0\' NOT NULL, INDEX IDX_8B84750DAA5D4036 (story_id), INDEX IDX_8B84750DFAAB039F (line_up_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE audit_line_up_stories ADD CONSTRAINT FK_725CF2A2232D562B FOREIGN KEY (object_id) REFERENCES lineups_stories (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE lineups_stories ADD CONSTRAINT FK_8B84750DAA5D4036 FOREIGN KEY (story_id) REFERENCES stories (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE lineups_stories ADD CONSTRAINT FK_8B84750DFAAB039F FOREIGN KEY (line_up_id) REFERENCES lineups (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('DROP TABLE lineup_content');
        $this->addSql('DROP TABLE line_up_story');
        $this->addSql('DROP TABLE line_up_interstitial');
    }
}
