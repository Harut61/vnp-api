<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210907051947 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE source_video_qa_issues (id INT AUTO_INCREMENT NOT NULL, source_id_id INT DEFAULT NULL, issue_type_id INT DEFAULT NULL, created_by_id INT DEFAULT NULL, comment VARCHAR(255) NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, INDEX IDX_C7D277FDE64C4568 (source_id_id), INDEX IDX_C7D277FD60B4C972 (issue_type_id), INDEX IDX_C7D277FDB03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE source_video_qa_issues_type (id INT AUTO_INCREMENT NOT NULL, created_by_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, INDEX IDX_9D018B2CB03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE story_qa_issue (id INT AUTO_INCREMENT NOT NULL, story_id_id INT DEFAULT NULL, issue_type_id INT DEFAULT NULL, created_by_id INT DEFAULT NULL, comment VARCHAR(255) NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, INDEX IDX_2AB79ABA3043EF4 (story_id_id), INDEX IDX_2AB79AB60B4C972 (issue_type_id), INDEX IDX_2AB79ABB03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE story_qa_issues_type (id INT AUTO_INCREMENT NOT NULL, created_by_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, INDEX IDX_355A271AB03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE source_video_qa_issues ADD CONSTRAINT FK_C7D277FDE64C4568 FOREIGN KEY (source_id_id) REFERENCES sources (id)');
        $this->addSql('ALTER TABLE source_video_qa_issues ADD CONSTRAINT FK_C7D277FD60B4C972 FOREIGN KEY (issue_type_id) REFERENCES source_video_qa_issues_type (id)');
        $this->addSql('ALTER TABLE source_video_qa_issues ADD CONSTRAINT FK_C7D277FDB03A8386 FOREIGN KEY (created_by_id) REFERENCES admin_users (id)');
        $this->addSql('ALTER TABLE source_video_qa_issues_type ADD CONSTRAINT FK_9D018B2CB03A8386 FOREIGN KEY (created_by_id) REFERENCES admin_users (id)');
        $this->addSql('ALTER TABLE story_qa_issue ADD CONSTRAINT FK_2AB79ABA3043EF4 FOREIGN KEY (story_id_id) REFERENCES stories (id)');
        $this->addSql('ALTER TABLE story_qa_issue ADD CONSTRAINT FK_2AB79AB60B4C972 FOREIGN KEY (issue_type_id) REFERENCES story_qa_issues_type (id)');
        $this->addSql('ALTER TABLE story_qa_issue ADD CONSTRAINT FK_2AB79ABB03A8386 FOREIGN KEY (created_by_id) REFERENCES admin_users (id)');
        $this->addSql('ALTER TABLE story_qa_issues_type ADD CONSTRAINT FK_355A271AB03A8386 FOREIGN KEY (created_by_id) REFERENCES admin_users (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE source_video_qa_issues DROP FOREIGN KEY FK_C7D277FD60B4C972');
        $this->addSql('ALTER TABLE story_qa_issue DROP FOREIGN KEY FK_2AB79AB60B4C972');
        $this->addSql('DROP TABLE source_video_qa_issues');
        $this->addSql('DROP TABLE source_video_qa_issues_type');
        $this->addSql('DROP TABLE story_qa_issue');
        $this->addSql('DROP TABLE story_qa_issues_type');
    }
}
