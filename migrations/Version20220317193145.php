<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220317193145 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE defense ADD user_id INT NOT NULL, ADD status TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE defense ADD CONSTRAINT FK_DBA5F575A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_DBA5F575A76ED395 ON defense (user_id)');
        $this->addSql('ALTER TABLE intervenant ADD user_id INT NOT NULL');
        $this->addSql('ALTER TABLE intervenant ADD CONSTRAINT FK_73D0145CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_73D0145CA76ED395 ON intervenant (user_id)');
        $this->addSql('ALTER TABLE subject ADD intervenant_id INT NOT NULL');
        $this->addSql('ALTER TABLE subject ADD CONSTRAINT FK_FBCE3E7AAB9A1716 FOREIGN KEY (intervenant_id) REFERENCES intervenant (id)');
        $this->addSql('CREATE INDEX IDX_FBCE3E7AAB9A1716 ON subject (intervenant_id)');
        $this->addSql('ALTER TABLE user_comptability ADD user_id INT NOT NULL');
        $this->addSql('ALTER TABLE user_comptability ADD CONSTRAINT FK_33FA8456A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_33FA8456A76ED395 ON user_comptability (user_id)');
        $this->addSql('ALTER TABLE user_grade ADD subject_id INT NOT NULL, ADD user_id INT NOT NULL');
        $this->addSql('ALTER TABLE user_grade ADD CONSTRAINT FK_BB98556C23EDC87 FOREIGN KEY (subject_id) REFERENCES subject (id)');
        $this->addSql('ALTER TABLE user_grade ADD CONSTRAINT FK_BB98556CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_BB98556C23EDC87 ON user_grade (subject_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_BB98556CA76ED395 ON user_grade (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE campus CHANGE name name VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE company CHANGE name name VARCHAR(100) NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE defense DROP FOREIGN KEY FK_DBA5F575A76ED395');
        $this->addSql('DROP INDEX UNIQ_DBA5F575A76ED395 ON defense');
        $this->addSql('ALTER TABLE defense DROP user_id, DROP status');
        $this->addSql('ALTER TABLE intervenant DROP FOREIGN KEY FK_73D0145CA76ED395');
        $this->addSql('DROP INDEX UNIQ_73D0145CA76ED395 ON intervenant');
        $this->addSql('ALTER TABLE intervenant DROP user_id');
        $this->addSql('ALTER TABLE messenger_messages CHANGE body body LONGTEXT NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE headers headers LONGTEXT NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE queue_name queue_name VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE role CHANGE name name VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE speciality CHANGE name name VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE study_level CHANGE name name VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE subject DROP FOREIGN KEY FK_FBCE3E7AAB9A1716');
        $this->addSql('DROP INDEX IDX_FBCE3E7AAB9A1716 ON subject');
        $this->addSql('ALTER TABLE subject DROP intervenant_id, CHANGE name name VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE full_name full_name VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE user CHANGE email email VARCHAR(180) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE password password VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE first_name first_name VARCHAR(45) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE last_name last_name VARCHAR(65) NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE user_comptability DROP FOREIGN KEY FK_33FA8456A76ED395');
        $this->addSql('DROP INDEX UNIQ_33FA8456A76ED395 ON user_comptability');
        $this->addSql('ALTER TABLE user_comptability DROP user_id, CHANGE type type VARCHAR(45) NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE user_extended CHANGE address address VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE region region VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE year_entry year_entry VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE year_exit year_exit VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE user_grade DROP FOREIGN KEY FK_BB98556C23EDC87');
        $this->addSql('ALTER TABLE user_grade DROP FOREIGN KEY FK_BB98556CA76ED395');
        $this->addSql('DROP INDEX UNIQ_BB98556C23EDC87 ON user_grade');
        $this->addSql('DROP INDEX UNIQ_BB98556CA76ED395 ON user_grade');
        $this->addSql('ALTER TABLE user_grade DROP subject_id, DROP user_id');
    }
}
