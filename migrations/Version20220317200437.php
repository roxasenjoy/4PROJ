<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220317200437 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE partnership ADD company_id INT NOT NULL');
        $this->addSql('ALTER TABLE partnership ADD CONSTRAINT FK_8619D6AE979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8619D6AE979B1AD6 ON partnership (company_id)');
        $this->addSql('ALTER TABLE user_extended ADD user_id INT NOT NULL, ADD company_id INT NOT NULL, ADD previous_level_id INT NOT NULL, ADD actual_level_id INT NOT NULL, ADD speciality_id INT NOT NULL');
        $this->addSql('ALTER TABLE user_extended ADD CONSTRAINT FK_C530657FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_extended ADD CONSTRAINT FK_C530657F979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id)');
        $this->addSql('ALTER TABLE user_extended ADD CONSTRAINT FK_C530657F405A53A3 FOREIGN KEY (previous_level_id) REFERENCES study_level (id)');
        $this->addSql('ALTER TABLE user_extended ADD CONSTRAINT FK_C530657FD7F76087 FOREIGN KEY (actual_level_id) REFERENCES study_level (id)');
        $this->addSql('ALTER TABLE user_extended ADD CONSTRAINT FK_C530657F3B5A08D7 FOREIGN KEY (speciality_id) REFERENCES speciality (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C530657FA76ED395 ON user_extended (user_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C530657F979B1AD6 ON user_extended (company_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C530657F405A53A3 ON user_extended (previous_level_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C530657FD7F76087 ON user_extended (actual_level_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C530657F3B5A08D7 ON user_extended (speciality_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE campus CHANGE name name VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE company CHANGE name name VARCHAR(100) NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE messenger_messages CHANGE body body LONGTEXT NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE headers headers LONGTEXT NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE queue_name queue_name VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE partnership DROP FOREIGN KEY FK_8619D6AE979B1AD6');
        $this->addSql('DROP INDEX UNIQ_8619D6AE979B1AD6 ON partnership');
        $this->addSql('ALTER TABLE partnership DROP company_id');
        $this->addSql('ALTER TABLE role CHANGE name name VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE speciality CHANGE name name VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE study_level CHANGE name name VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE subject CHANGE name name VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE full_name full_name VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE user CHANGE email email VARCHAR(180) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE password password VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE first_name first_name VARCHAR(45) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE last_name last_name VARCHAR(65) NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE user_comptability CHANGE type type VARCHAR(45) NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE user_extended DROP FOREIGN KEY FK_C530657FA76ED395');
        $this->addSql('ALTER TABLE user_extended DROP FOREIGN KEY FK_C530657F979B1AD6');
        $this->addSql('ALTER TABLE user_extended DROP FOREIGN KEY FK_C530657F405A53A3');
        $this->addSql('ALTER TABLE user_extended DROP FOREIGN KEY FK_C530657FD7F76087');
        $this->addSql('ALTER TABLE user_extended DROP FOREIGN KEY FK_C530657F3B5A08D7');
        $this->addSql('DROP INDEX UNIQ_C530657FA76ED395 ON user_extended');
        $this->addSql('DROP INDEX UNIQ_C530657F979B1AD6 ON user_extended');
        $this->addSql('DROP INDEX UNIQ_C530657F405A53A3 ON user_extended');
        $this->addSql('DROP INDEX UNIQ_C530657FD7F76087 ON user_extended');
        $this->addSql('DROP INDEX UNIQ_C530657F3B5A08D7 ON user_extended');
        $this->addSql('ALTER TABLE user_extended DROP user_id, DROP company_id, DROP previous_level_id, DROP actual_level_id, DROP speciality_id, CHANGE address address VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE region region VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE year_entry year_entry VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE year_exit year_exit VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
