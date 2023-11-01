<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201109022629 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
         $this->addSql('ALTER TABLE vods ADD video_height_txt VARCHAR(255) DEFAULT NULL, ADD media_type VARCHAR(255) DEFAULT NULL, ADD media_info JSON DEFAULT NULL, ADD video_fps VARCHAR(255) DEFAULT NULL, ADD video_fps_txt VARCHAR(255) DEFAULT NULL, ADD video_bitrate_txt VARCHAR(255) DEFAULT NULL, ADD audio_language VARCHAR(255) DEFAULT NULL, CHANGE videofps video_width_txt VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE vods ADD videofps VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, DROP video_width_txt, DROP video_height_txt, DROP media_type, DROP media_info, DROP video_fps, DROP video_fps_txt, DROP video_bitrate_txt, DROP audio_language');
    }
}
