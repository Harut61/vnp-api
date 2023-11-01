<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201228042809 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE source_videos ADD mark_up_status TINYINT(1) DEFAULT \'0\' NOT NULL, ADD being_marked_up_by JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE stories CHANGE description description LONGTEXT DEFAULT NULL, CHANGE story_meta story_meta JSON DEFAULT NULL, CHANGE story_start story_start VARCHAR(255) DEFAULT NULL, CHANGE lede_end_frame lede_end_frame INT NOT NULL, CHANGE story_end story_end VARCHAR(255) DEFAULT NULL, CHANGE story_rank story_rank INT DEFAULT NULL, CHANGE creation_start creation_start DATETIME DEFAULT NULL, CHANGE creation_end creation_end DATETIME DEFAULT NULL, CHANGE published_at published_at DATETIME DEFAULT NULL, CHANGE scheduled scheduled TINYINT(1) DEFAULT \'0\', CHANGE lede_sub_title_text lede_sub_title_text LONGTEXT DEFAULT NULL, CHANGE rest_story_sub_title_text rest_story_sub_title_text LONGTEXT DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE source_videos DROP mark_up_status, DROP being_marked_up_by');
        $this->addSql('ALTER TABLE stories CHANGE description description LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE story_meta story_meta JSON NOT NULL, CHANGE lede_end_frame lede_end_frame VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE story_start story_start VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE story_end story_end VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE story_rank story_rank INT NOT NULL, CHANGE creation_start creation_start DATETIME NOT NULL, CHANGE creation_end creation_end DATETIME NOT NULL, CHANGE published_at published_at DATETIME NOT NULL, CHANGE scheduled scheduled TINYINT(1) DEFAULT \'0\' NOT NULL, CHANGE lede_sub_title_text lede_sub_title_text LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE rest_story_sub_title_text rest_story_sub_title_text LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
