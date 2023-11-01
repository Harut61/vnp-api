<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201026101217 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE admin_roles (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(255) DEFAULT NULL, title VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_1614D53D77153098 (code), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE admin_users (id INT AUTO_INCREMENT NOT NULL, mobile_number VARCHAR(255) DEFAULT NULL, username VARCHAR(255) NOT NULL, full_name VARCHAR(255) DEFAULT NULL, email VARCHAR(255) NOT NULL, password LONGTEXT DEFAULT NULL, enabled TINYINT(1) NOT NULL, blocked TINYINT(1) NOT NULL, block_reason VARCHAR(255) DEFAULT NULL, user_status VARCHAR(10) DEFAULT \'pending\' NOT NULL, birth_date DATETIME DEFAULT NULL, last_login DATETIME DEFAULT NULL, mobile_number_verified TINYINT(1) NOT NULL, roles JSON NOT NULL, gender VARCHAR(10) DEFAULT \'male\' NOT NULL, otp VARCHAR(6) DEFAULT NULL, number_of_devices INT NOT NULL, email_verified TINYINT(1) NOT NULL, email_verification_token VARCHAR(255) DEFAULT NULL, tokens JSON DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE admin_user_admin_roles (admin_user_id INT NOT NULL, admin_roles_id INT NOT NULL, INDEX IDX_815DBB9F6352511C (admin_user_id), INDEX IDX_815DBB9FAC6756C2 (admin_roles_id), PRIMARY KEY(admin_user_id, admin_roles_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE audit_admin_users (id INT AUTO_INCREMENT NOT NULL, object_id INT DEFAULT NULL, type VARCHAR(10) NOT NULL, discriminator VARCHAR(255) DEFAULT NULL, transaction_hash VARCHAR(40) DEFAULT NULL, diffs JSON DEFAULT NULL, blame_id VARCHAR(255) DEFAULT NULL, blame_user VARCHAR(255) DEFAULT NULL, blame_user_fqdn VARCHAR(255) DEFAULT NULL, blame_user_firewall VARCHAR(100) DEFAULT NULL, ip VARCHAR(45) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_99D66F86232D562B (object_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE audit_admin_users_login (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(10) NOT NULL, action VARCHAR(100) NOT NULL, blame_id VARCHAR(255) DEFAULT NULL, ip VARCHAR(45) DEFAULT NULL, created_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE audit_high_level_subject_tags (id INT AUTO_INCREMENT NOT NULL, object_id INT DEFAULT NULL, type VARCHAR(10) NOT NULL, discriminator VARCHAR(255) DEFAULT NULL, transaction_hash VARCHAR(40) DEFAULT NULL, diffs JSON DEFAULT NULL, blame_id VARCHAR(255) DEFAULT NULL, blame_user VARCHAR(255) DEFAULT NULL, blame_user_fqdn VARCHAR(255) DEFAULT NULL, blame_user_firewall VARCHAR(100) DEFAULT NULL, ip VARCHAR(45) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_DA6AD392232D562B (object_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE audit_sources (id INT AUTO_INCREMENT NOT NULL, object_id INT DEFAULT NULL, type VARCHAR(10) NOT NULL, discriminator VARCHAR(255) DEFAULT NULL, transaction_hash VARCHAR(40) DEFAULT NULL, diffs JSON DEFAULT NULL, blame_id VARCHAR(255) DEFAULT NULL, blame_user VARCHAR(255) DEFAULT NULL, blame_user_fqdn VARCHAR(255) DEFAULT NULL, blame_user_firewall VARCHAR(100) DEFAULT NULL, ip VARCHAR(45) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_C37FB082232D562B (object_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE audit_stories (id INT AUTO_INCREMENT NOT NULL, object_id INT DEFAULT NULL, type VARCHAR(10) NOT NULL, discriminator VARCHAR(255) DEFAULT NULL, transaction_hash VARCHAR(40) DEFAULT NULL, diffs JSON DEFAULT NULL, blame_id VARCHAR(255) DEFAULT NULL, blame_user VARCHAR(255) DEFAULT NULL, blame_user_fqdn VARCHAR(255) DEFAULT NULL, blame_user_firewall VARCHAR(100) DEFAULT NULL, ip VARCHAR(45) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_8DA9482F232D562B (object_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE audit_story_types (id INT AUTO_INCREMENT NOT NULL, object_id INT DEFAULT NULL, type VARCHAR(10) NOT NULL, discriminator VARCHAR(255) DEFAULT NULL, transaction_hash VARCHAR(40) DEFAULT NULL, diffs JSON DEFAULT NULL, blame_id VARCHAR(255) DEFAULT NULL, blame_user VARCHAR(255) DEFAULT NULL, blame_user_fqdn VARCHAR(255) DEFAULT NULL, blame_user_firewall VARCHAR(100) DEFAULT NULL, ip VARCHAR(45) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_A80F1806232D562B (object_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ext_log_entries (id INT AUTO_INCREMENT NOT NULL, action VARCHAR(8) NOT NULL, logged_at DATETIME NOT NULL, object_id VARCHAR(64) DEFAULT NULL, object_class VARCHAR(191) NOT NULL, version INT NOT NULL, data LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', username VARCHAR(191) DEFAULT NULL, INDEX log_class_lookup_idx (object_class), INDEX log_date_lookup_idx (logged_at), INDEX log_user_lookup_idx (username), INDEX log_version_lookup_idx (object_id, object_class, version), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB ROW_FORMAT = DYNAMIC');
        $this->addSql('CREATE TABLE high_level_subject_tags (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, title VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, position INT NOT NULL, is_active TINYINT(1) DEFAULT \'0\' NOT NULL, slug VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_B30E09A3989D9B62 (slug), INDEX IDX_B30E09A3DE12AB56 (created_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE refresh_tokens (id INT AUTO_INCREMENT NOT NULL, refresh_token VARCHAR(128) NOT NULL, username VARCHAR(255) NOT NULL, valid DATETIME NOT NULL, UNIQUE INDEX UNIQ_9BACE7E1C74F2195 (refresh_token), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sources (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, title VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, is_active TINYINT(1) DEFAULT \'0\' NOT NULL, position INT NOT NULL, slug VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_D25D65F2989D9B62 (slug), INDEX IDX_D25D65F2DE12AB56 (created_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE stories (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, source_id INT DEFAULT NULL, story_type_id INT DEFAULT NULL, description LONGTEXT NOT NULL, story_meta JSON NOT NULL, thumbnail_frame INT NOT NULL, story_start VARCHAR(255) NOT NULL, lede_end_frame VARCHAR(255) NOT NULL, story_end VARCHAR(255) NOT NULL, story_rank INT NOT NULL, creation_start DATETIME NOT NULL, creation_end DATETIME NOT NULL, published_at DATETIME NOT NULL, scheduled TINYINT(1) DEFAULT \'0\' NOT NULL, lede_sub_title_text LONGTEXT NOT NULL, rest_story_sub_title_text LONGTEXT NOT NULL, story_status VARCHAR(10) DEFAULT \'queued\' NOT NULL, story_tagging_status VARCHAR(10) DEFAULT \'queued\' NOT NULL, story_qa_status VARCHAR(10) DEFAULT \'PendingQA\' NOT NULL, title VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, INDEX IDX_9C8B9D5FDE12AB56 (created_by), INDEX IDX_9C8B9D5F953C1C61 (source_id), INDEX IDX_9C8B9D5F96B01C11 (story_type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE story_high_level_subject_tag (story_id INT NOT NULL, high_level_subject_tag_id INT NOT NULL, INDEX IDX_AF18079DAA5D4036 (story_id), INDEX IDX_AF18079D72983C54 (high_level_subject_tag_id), PRIMARY KEY(story_id, high_level_subject_tag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE story_types (id INT AUTO_INCREMENT NOT NULL, created_by INT DEFAULT NULL, title VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, is_active TINYINT(1) DEFAULT \'0\' NOT NULL, position INT NOT NULL, slug VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_85702993989D9B62 (slug), INDEX IDX_85702993DE12AB56 (created_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE admin_user_admin_roles ADD CONSTRAINT FK_815DBB9F6352511C FOREIGN KEY (admin_user_id) REFERENCES admin_users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE admin_user_admin_roles ADD CONSTRAINT FK_815DBB9FAC6756C2 FOREIGN KEY (admin_roles_id) REFERENCES admin_roles (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE audit_admin_users ADD CONSTRAINT FK_99D66F86232D562B FOREIGN KEY (object_id) REFERENCES admin_users (id)');
        $this->addSql('ALTER TABLE audit_high_level_subject_tags ADD CONSTRAINT FK_DA6AD392232D562B FOREIGN KEY (object_id) REFERENCES high_level_subject_tags (id)');
        $this->addSql('ALTER TABLE audit_sources ADD CONSTRAINT FK_C37FB082232D562B FOREIGN KEY (object_id) REFERENCES sources (id)');
        $this->addSql('ALTER TABLE audit_stories ADD CONSTRAINT FK_8DA9482F232D562B FOREIGN KEY (object_id) REFERENCES stories (id)');
        $this->addSql('ALTER TABLE audit_story_types ADD CONSTRAINT FK_A80F1806232D562B FOREIGN KEY (object_id) REFERENCES story_types (id)');
        $this->addSql('ALTER TABLE high_level_subject_tags ADD CONSTRAINT FK_B30E09A3DE12AB56 FOREIGN KEY (created_by) REFERENCES admin_users (id)');
        $this->addSql('ALTER TABLE sources ADD CONSTRAINT FK_D25D65F2DE12AB56 FOREIGN KEY (created_by) REFERENCES admin_users (id)');
        $this->addSql('ALTER TABLE stories ADD CONSTRAINT FK_9C8B9D5FDE12AB56 FOREIGN KEY (created_by) REFERENCES admin_users (id)');
        $this->addSql('ALTER TABLE stories ADD CONSTRAINT FK_9C8B9D5F953C1C61 FOREIGN KEY (source_id) REFERENCES sources (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE stories ADD CONSTRAINT FK_9C8B9D5F96B01C11 FOREIGN KEY (story_type_id) REFERENCES story_types (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE story_high_level_subject_tag ADD CONSTRAINT FK_AF18079DAA5D4036 FOREIGN KEY (story_id) REFERENCES stories (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE story_high_level_subject_tag ADD CONSTRAINT FK_AF18079D72983C54 FOREIGN KEY (high_level_subject_tag_id) REFERENCES high_level_subject_tags (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE story_types ADD CONSTRAINT FK_85702993DE12AB56 FOREIGN KEY (created_by) REFERENCES admin_users (id)');

    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE admin_user_admin_roles DROP FOREIGN KEY FK_815DBB9FAC6756C2');
        $this->addSql('ALTER TABLE admin_user_admin_roles DROP FOREIGN KEY FK_815DBB9F6352511C');
        $this->addSql('ALTER TABLE audit_admin_users DROP FOREIGN KEY FK_99D66F86232D562B');
        $this->addSql('ALTER TABLE high_level_subject_tags DROP FOREIGN KEY FK_B30E09A3DE12AB56');
        $this->addSql('ALTER TABLE sources DROP FOREIGN KEY FK_D25D65F2DE12AB56');
        $this->addSql('ALTER TABLE stories DROP FOREIGN KEY FK_9C8B9D5FDE12AB56');
        $this->addSql('ALTER TABLE story_types DROP FOREIGN KEY FK_85702993DE12AB56');
        $this->addSql('ALTER TABLE audit_high_level_subject_tags DROP FOREIGN KEY FK_DA6AD392232D562B');
        $this->addSql('ALTER TABLE story_high_level_subject_tag DROP FOREIGN KEY FK_AF18079D72983C54');
        $this->addSql('ALTER TABLE audit_sources DROP FOREIGN KEY FK_C37FB082232D562B');
        $this->addSql('ALTER TABLE stories DROP FOREIGN KEY FK_9C8B9D5F953C1C61');
        $this->addSql('ALTER TABLE audit_stories DROP FOREIGN KEY FK_8DA9482F232D562B');
        $this->addSql('ALTER TABLE story_high_level_subject_tag DROP FOREIGN KEY FK_AF18079DAA5D4036');
        $this->addSql('ALTER TABLE audit_story_types DROP FOREIGN KEY FK_A80F1806232D562B');
        $this->addSql('ALTER TABLE stories DROP FOREIGN KEY FK_9C8B9D5F96B01C11');
        $this->addSql('DROP TABLE admin_roles');
        $this->addSql('DROP TABLE admin_users');
        $this->addSql('DROP TABLE admin_user_admin_roles');
        $this->addSql('DROP TABLE audit_admin_users');
        $this->addSql('DROP TABLE audit_admin_users_login');
        $this->addSql('DROP TABLE audit_high_level_subject_tags');
        $this->addSql('DROP TABLE audit_sources');
        $this->addSql('DROP TABLE audit_stories');
        $this->addSql('DROP TABLE audit_story_types');
        $this->addSql('DROP TABLE ext_log_entries');
        $this->addSql('DROP TABLE high_level_subject_tags');
        $this->addSql('DROP TABLE refresh_tokens');
        $this->addSql('DROP TABLE sources');
        $this->addSql('DROP TABLE stories');
        $this->addSql('DROP TABLE story_high_level_subject_tag');
        $this->addSql('DROP TABLE story_types');
    }
}
