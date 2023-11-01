<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210909030326 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE end_user_pref_news_source DROP FOREIGN KEY FK_4FA596F5A76ED395');
        $this->addSql('DROP INDEX IDX_4FA596F5A76ED395 ON end_user_pref_news_source');
        $this->addSql('ALTER TABLE end_user_pref_news_source CHANGE user_id created_by INT DEFAULT NULL');
        $this->addSql('ALTER TABLE end_user_pref_news_source ADD CONSTRAINT FK_4FA596F5DE12AB56 FOREIGN KEY (created_by) REFERENCES end_users (id)');
        $this->addSql('CREATE INDEX IDX_4FA596F5DE12AB56 ON end_user_pref_news_source (created_by)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE end_user_pref_news_source DROP FOREIGN KEY FK_4FA596F5DE12AB56');
        $this->addSql('DROP INDEX IDX_4FA596F5DE12AB56 ON end_user_pref_news_source');
        $this->addSql('ALTER TABLE end_user_pref_news_source CHANGE created_by user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE end_user_pref_news_source ADD CONSTRAINT FK_4FA596F5A76ED395 FOREIGN KEY (user_id) REFERENCES end_users (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_4FA596F5A76ED395 ON end_user_pref_news_source (user_id)');
    }
}
