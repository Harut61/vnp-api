<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210910053136 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE source_video_qa_issues DROP FOREIGN KEY FK_C7D277FDB03A8386');
        $this->addSql('ALTER TABLE source_video_qa_issues DROP FOREIGN KEY FK_C7D277FDE64C4568');
        $this->addSql('DROP INDEX IDX_C7D277FDB03A8386 ON source_video_qa_issues');
        $this->addSql('DROP INDEX IDX_C7D277FDE64C4568 ON source_video_qa_issues');
        $this->addSql('ALTER TABLE source_video_qa_issues ADD source_id INT DEFAULT NULL, ADD created_by INT DEFAULT NULL, ADD assign_id INT DEFAULT NULL, ADD status TINYINT(1) DEFAULT NULL, DROP source_id_id, DROP created_by_id, CHANGE created_at created_at DATETIME NOT NULL, CHANGE updated_at updated_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE source_video_qa_issues ADD CONSTRAINT FK_C7D277FD953C1C61 FOREIGN KEY (source_id) REFERENCES sources (id)');
        $this->addSql('ALTER TABLE source_video_qa_issues ADD CONSTRAINT FK_C7D277FDDE12AB56 FOREIGN KEY (created_by) REFERENCES admin_users (id)');
        $this->addSql('ALTER TABLE source_video_qa_issues ADD CONSTRAINT FK_C7D277FD145EB451 FOREIGN KEY (assign_id) REFERENCES admin_users (id)');
        $this->addSql('CREATE INDEX IDX_C7D277FD953C1C61 ON source_video_qa_issues (source_id)');
        $this->addSql('CREATE INDEX IDX_C7D277FDDE12AB56 ON source_video_qa_issues (created_by)');
        $this->addSql('CREATE INDEX IDX_C7D277FD145EB451 ON source_video_qa_issues (assign_id)');
        $this->addSql('ALTER TABLE source_video_qa_issues_type DROP FOREIGN KEY FK_9D018B2CB03A8386');
        $this->addSql('DROP INDEX IDX_9D018B2CB03A8386 ON source_video_qa_issues_type');
        $this->addSql('ALTER TABLE source_video_qa_issues_type CHANGE title title VARCHAR(255) DEFAULT NULL, CHANGE created_at created_at DATETIME NOT NULL, CHANGE updated_at updated_at DATETIME NOT NULL, CHANGE created_by_id created_by INT DEFAULT NULL');
        $this->addSql('ALTER TABLE source_video_qa_issues_type ADD CONSTRAINT FK_9D018B2CDE12AB56 FOREIGN KEY (created_by) REFERENCES admin_users (id)');
        $this->addSql('CREATE INDEX IDX_9D018B2CDE12AB56 ON source_video_qa_issues_type (created_by)');
        $this->addSql('ALTER TABLE story_qa_issue DROP FOREIGN KEY FK_2AB79ABA3043EF4');
        $this->addSql('ALTER TABLE story_qa_issue DROP FOREIGN KEY FK_2AB79ABB03A8386');
        $this->addSql('DROP INDEX IDX_2AB79ABA3043EF4 ON story_qa_issue');
        $this->addSql('DROP INDEX IDX_2AB79ABB03A8386 ON story_qa_issue');
        $this->addSql('ALTER TABLE story_qa_issue ADD story_id INT DEFAULT NULL, ADD created_by INT DEFAULT NULL, ADD status TINYINT(1) DEFAULT NULL, DROP story_id_id, DROP created_by_id, CHANGE created_at created_at DATETIME NOT NULL, CHANGE updated_at updated_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE story_qa_issue ADD CONSTRAINT FK_2AB79ABAA5D4036 FOREIGN KEY (story_id) REFERENCES stories (id)');
        $this->addSql('ALTER TABLE story_qa_issue ADD CONSTRAINT FK_2AB79ABDE12AB56 FOREIGN KEY (created_by) REFERENCES admin_users (id)');
        $this->addSql('CREATE INDEX IDX_2AB79ABAA5D4036 ON story_qa_issue (story_id)');
        $this->addSql('CREATE INDEX IDX_2AB79ABDE12AB56 ON story_qa_issue (created_by)');
        $this->addSql('ALTER TABLE story_qa_issues_type DROP FOREIGN KEY FK_355A271AB03A8386');
        $this->addSql('DROP INDEX IDX_355A271AB03A8386 ON story_qa_issues_type');
        $this->addSql('ALTER TABLE story_qa_issues_type CHANGE title title VARCHAR(255) DEFAULT NULL, CHANGE created_at created_at DATETIME NOT NULL, CHANGE updated_at updated_at DATETIME NOT NULL, CHANGE created_by_id created_by INT DEFAULT NULL');
        $this->addSql('ALTER TABLE story_qa_issues_type ADD CONSTRAINT FK_355A271ADE12AB56 FOREIGN KEY (created_by) REFERENCES admin_users (id)');
        $this->addSql('CREATE INDEX IDX_355A271ADE12AB56 ON story_qa_issues_type (created_by)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE source_video_qa_issues DROP FOREIGN KEY FK_C7D277FD953C1C61');
        $this->addSql('ALTER TABLE source_video_qa_issues DROP FOREIGN KEY FK_C7D277FDDE12AB56');
        $this->addSql('ALTER TABLE source_video_qa_issues DROP FOREIGN KEY FK_C7D277FD145EB451');
        $this->addSql('DROP INDEX IDX_C7D277FD953C1C61 ON source_video_qa_issues');
        $this->addSql('DROP INDEX IDX_C7D277FDDE12AB56 ON source_video_qa_issues');
        $this->addSql('DROP INDEX IDX_C7D277FD145EB451 ON source_video_qa_issues');
        $this->addSql('ALTER TABLE source_video_qa_issues ADD source_id_id INT DEFAULT NULL, ADD created_by_id INT DEFAULT NULL, DROP source_id, DROP created_by, DROP assign_id, DROP status, CHANGE created_at created_at DATETIME DEFAULT NULL, CHANGE updated_at updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE source_video_qa_issues ADD CONSTRAINT FK_C7D277FDB03A8386 FOREIGN KEY (created_by_id) REFERENCES admin_users (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE source_video_qa_issues ADD CONSTRAINT FK_C7D277FDE64C4568 FOREIGN KEY (source_id_id) REFERENCES sources (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_C7D277FDB03A8386 ON source_video_qa_issues (created_by_id)');
        $this->addSql('CREATE INDEX IDX_C7D277FDE64C4568 ON source_video_qa_issues (source_id_id)');
        $this->addSql('ALTER TABLE source_video_qa_issues_type DROP FOREIGN KEY FK_9D018B2CDE12AB56');
        $this->addSql('DROP INDEX IDX_9D018B2CDE12AB56 ON source_video_qa_issues_type');
        $this->addSql('ALTER TABLE source_video_qa_issues_type CHANGE title title VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE created_at created_at DATETIME DEFAULT NULL, CHANGE updated_at updated_at DATETIME DEFAULT NULL, CHANGE created_by created_by_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE source_video_qa_issues_type ADD CONSTRAINT FK_9D018B2CB03A8386 FOREIGN KEY (created_by_id) REFERENCES admin_users (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_9D018B2CB03A8386 ON source_video_qa_issues_type (created_by_id)');
        $this->addSql('ALTER TABLE story_qa_issue DROP FOREIGN KEY FK_2AB79ABAA5D4036');
        $this->addSql('ALTER TABLE story_qa_issue DROP FOREIGN KEY FK_2AB79ABDE12AB56');
        $this->addSql('DROP INDEX IDX_2AB79ABAA5D4036 ON story_qa_issue');
        $this->addSql('DROP INDEX IDX_2AB79ABDE12AB56 ON story_qa_issue');
        $this->addSql('ALTER TABLE story_qa_issue ADD story_id_id INT DEFAULT NULL, ADD created_by_id INT DEFAULT NULL, DROP story_id, DROP created_by, DROP status, CHANGE created_at created_at DATETIME DEFAULT NULL, CHANGE updated_at updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE story_qa_issue ADD CONSTRAINT FK_2AB79ABA3043EF4 FOREIGN KEY (story_id_id) REFERENCES stories (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE story_qa_issue ADD CONSTRAINT FK_2AB79ABB03A8386 FOREIGN KEY (created_by_id) REFERENCES admin_users (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_2AB79ABA3043EF4 ON story_qa_issue (story_id_id)');
        $this->addSql('CREATE INDEX IDX_2AB79ABB03A8386 ON story_qa_issue (created_by_id)');
        $this->addSql('ALTER TABLE story_qa_issues_type DROP FOREIGN KEY FK_355A271ADE12AB56');
        $this->addSql('DROP INDEX IDX_355A271ADE12AB56 ON story_qa_issues_type');
        $this->addSql('ALTER TABLE story_qa_issues_type CHANGE title title VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE created_at created_at DATETIME DEFAULT NULL, CHANGE updated_at updated_at DATETIME DEFAULT NULL, CHANGE created_by created_by_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE story_qa_issues_type ADD CONSTRAINT FK_355A271AB03A8386 FOREIGN KEY (created_by_id) REFERENCES admin_users (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_355A271AB03A8386 ON story_qa_issues_type (created_by_id)');
    }
}
