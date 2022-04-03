<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220403192115 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE subject ADD CONSTRAINT FK_FBCE3E7A5FB14BA7 FOREIGN KEY (level_id) REFERENCES study_level (id)');
        $this->addSql('CREATE INDEX IDX_FBCE3E7A5FB14BA7 ON subject (level_id)');
        $this->addSql('ALTER TABLE subject_date ADD CONSTRAINT FK_3854B3A1A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_3854B3A1A76ED395 ON subject_date (user_id)');
        $this->addSql('ALTER TABLE user ADD user_extended_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6497BF45D6 FOREIGN KEY (user_extended_id) REFERENCES user_extended (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D6497BF45D6 ON user (user_extended_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE campus CHANGE name name VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE messenger_messages CHANGE body body LONGTEXT NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE headers headers LONGTEXT NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE queue_name queue_name VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE role CHANGE name name VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE study_level CHANGE name name VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE subject DROP FOREIGN KEY FK_FBCE3E7A5FB14BA7');
        $this->addSql('DROP INDEX IDX_FBCE3E7A5FB14BA7 ON subject');
        $this->addSql('ALTER TABLE subject CHANGE name name VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE full_name full_name VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE subject_date DROP FOREIGN KEY FK_3854B3A1A76ED395');
        $this->addSql('DROP INDEX IDX_3854B3A1A76ED395 ON subject_date');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6497BF45D6');
        $this->addSql('DROP INDEX UNIQ_8D93D6497BF45D6 ON user');
        $this->addSql('ALTER TABLE user DROP user_extended_id, CHANGE first_name first_name VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE last_name last_name VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE email email VARCHAR(180) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE password password VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE user_extended CHANGE address address VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE region region VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE year_entry year_entry VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE year_exit year_exit VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
