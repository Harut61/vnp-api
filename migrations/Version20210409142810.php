<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210409142810 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE end_users (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(255) NOT NULL, first_name VARCHAR(255) DEFAULT NULL, last_name VARCHAR(255) DEFAULT NULL, mobile_number VARCHAR(255) DEFAULT NULL, email VARCHAR(255) NOT NULL, password LONGTEXT DEFAULT NULL, enabled TINYINT(1) NOT NULL, blocked TINYINT(1) NOT NULL, block_reason VARCHAR(255) DEFAULT NULL, user_status VARCHAR(10) DEFAULT \'pending\' NOT NULL, birth_date DATETIME DEFAULT NULL, last_login DATETIME DEFAULT NULL, mobile_number_verified TINYINT(1) NOT NULL, roles JSON NOT NULL, gender VARCHAR(10) DEFAULT \'male\' NOT NULL, otp VARCHAR(6) DEFAULT NULL, number_of_devices INT NOT NULL, email_verified TINYINT(1) NOT NULL, email_verification_token VARCHAR(255) DEFAULT NULL, tokens JSON DEFAULT NULL, profile_pic_url LONGTEXT DEFAULT NULL, facebook_token LONGTEXT DEFAULT NULL, google_token LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE end_users');
    }
}
