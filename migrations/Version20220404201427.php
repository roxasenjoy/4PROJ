<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220404201427 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE intervenant (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, subject_id INT NOT NULL, INDEX IDX_73D0145CA76ED395 (user_id), INDEX IDX_73D0145C23EDC87 (subject_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE intervenant ADD CONSTRAINT FK_73D0145CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE intervenant ADD CONSTRAINT FK_73D0145C23EDC87 FOREIGN KEY (subject_id) REFERENCES subject (id)');
        $this->addSql('ALTER TABLE subject ADD CONSTRAINT FK_FBCE3E7A5FB14BA7 FOREIGN KEY (level_id) REFERENCES study_level (id)');
        $this->addSql('CREATE INDEX IDX_FBCE3E7A5FB14BA7 ON subject (level_id)');
        $this->addSql('ALTER TABLE subject_date ADD CONSTRAINT FK_3854B3A1A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_3854B3A1A76ED395 ON subject_date (user_id)');
        $this->addSql('ALTER TABLE user_extended CHANGE user_id user_id INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE intervenant');
        $this->addSql('ALTER TABLE campus CHANGE name name VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE messenger_messages CHANGE body body LONGTEXT NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE headers headers LONGTEXT NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE queue_name queue_name VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE role CHANGE name name VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE study_level CHANGE name name VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE subject DROP FOREIGN KEY FK_FBCE3E7A5FB14BA7');
        $this->addSql('DROP INDEX IDX_FBCE3E7A5FB14BA7 ON subject');
        $this->addSql('ALTER TABLE subject CHANGE name name VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE full_name full_name VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE subject_date DROP FOREIGN KEY FK_3854B3A1A76ED395');
        $this->addSql('DROP INDEX IDX_3854B3A1A76ED395 ON subject_date');
        $this->addSql('ALTER TABLE user CHANGE first_name first_name VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE last_name last_name VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE email email VARCHAR(180) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE password password VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE user_extended CHANGE user_id user_id INT DEFAULT NULL, CHANGE address address VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE region region VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE year_entry year_entry VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE year_exit year_exit VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
