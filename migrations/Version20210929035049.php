<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210929035049 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE stories ADD story_start_image INT DEFAULT NULL, ADD story_end_image INT DEFAULT NULL, ADD story_lede_image INT DEFAULT NULL, ADD story_thumbnail INT DEFAULT NULL');
        $this->addSql('ALTER TABLE stories ADD CONSTRAINT FK_9C8B9D5F40407611 FOREIGN KEY (story_start_image) REFERENCES media_object (id)');
        $this->addSql('ALTER TABLE stories ADD CONSTRAINT FK_9C8B9D5F4D18792B FOREIGN KEY (story_end_image) REFERENCES media_object (id)');
        $this->addSql('ALTER TABLE stories ADD CONSTRAINT FK_9C8B9D5F6AB63790 FOREIGN KEY (story_lede_image) REFERENCES media_object (id)');
        $this->addSql('ALTER TABLE stories ADD CONSTRAINT FK_9C8B9D5F3AED1C97 FOREIGN KEY (story_thumbnail) REFERENCES media_object (id)');
        $this->addSql('CREATE INDEX IDX_9C8B9D5F40407611 ON stories (story_start_image)');
        $this->addSql('CREATE INDEX IDX_9C8B9D5F4D18792B ON stories (story_end_image)');
        $this->addSql('CREATE INDEX IDX_9C8B9D5F6AB63790 ON stories (story_lede_image)');
        $this->addSql('CREATE INDEX IDX_9C8B9D5F3AED1C97 ON stories (story_thumbnail)');
        $this->addSql('ALTER TABLE vods DROP INDEX IDX_E4062264CCC0DAE6, ADD UNIQUE INDEX UNIQ_E4062264CCC0DAE6 (interstitial_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE stories DROP FOREIGN KEY FK_9C8B9D5F40407611');
        $this->addSql('ALTER TABLE stories DROP FOREIGN KEY FK_9C8B9D5F4D18792B');
        $this->addSql('ALTER TABLE stories DROP FOREIGN KEY FK_9C8B9D5F6AB63790');
        $this->addSql('ALTER TABLE stories DROP FOREIGN KEY FK_9C8B9D5F3AED1C97');
        $this->addSql('DROP INDEX IDX_9C8B9D5F40407611 ON stories');
        $this->addSql('DROP INDEX IDX_9C8B9D5F4D18792B ON stories');
        $this->addSql('DROP INDEX IDX_9C8B9D5F6AB63790 ON stories');
        $this->addSql('DROP INDEX IDX_9C8B9D5F3AED1C97 ON stories');
        $this->addSql('ALTER TABLE stories DROP story_start_image, DROP story_end_image, DROP story_lede_image, DROP story_thumbnail');
        $this->addSql('ALTER TABLE vods DROP INDEX UNIQ_E4062264CCC0DAE6, ADD INDEX IDX_E4062264CCC0DAE6 (interstitial_id)');
    }
}
