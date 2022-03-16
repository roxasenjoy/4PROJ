<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220316223031 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE campus (id INT AUTO_INCREMENT NOT NULL, users_id INT NOT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_9D09681167B3B43D (users_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE company (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE defense (id INT AUTO_INCREMENT NOT NULL, user_id_id INT NOT NULL, date_begin DATETIME NOT NULL, date_end DATETIME NOT NULL, INDEX IDX_DBA5F5759D86650F (user_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE intervenant (id INT AUTO_INCREMENT NOT NULL, user_id_id INT NOT NULL, UNIQUE INDEX UNIQ_73D0145C9D86650F (user_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE partnership (id INT AUTO_INCREMENT NOT NULL, company_id_id INT NOT NULL, date_begin DATETIME NOT NULL, date_end DATETIME NOT NULL, UNIQUE INDEX UNIQ_8619D6AE38B53C32 (company_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE role (id INT AUTO_INCREMENT NOT NULL, users_id INT NOT NULL, name VARCHAR(255) NOT NULL, permission INT NOT NULL, UNIQUE INDEX UNIQ_57698A6A67B3B43D (users_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE speciality (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE study_level (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE subject (id INT AUTO_INCREMENT NOT NULL, intervenant_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, full_name VARCHAR(255) NOT NULL, points INT NOT NULL, date_begin DATETIME NOT NULL, date_end DATETIME NOT NULL, INDEX IDX_FBCE3E7AAB9A1716 (intervenant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_comptability (id INT AUTO_INCREMENT NOT NULL, user_id_id INT DEFAULT NULL, type VARCHAR(45) NOT NULL, paid TINYINT(1) NOT NULL, payement_due INT NOT NULL, relance TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_33FA84569D86650F (user_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_extended (id INT AUTO_INCREMENT NOT NULL, company_hired_id INT DEFAULT NULL, speciality_id_id INT DEFAULT NULL, user_id_id INT NOT NULL, actual_level_id INT NOT NULL, previous_level_id INT NOT NULL, birthday DATETIME NOT NULL, address VARCHAR(255) NOT NULL, gender INT NOT NULL, region VARCHAR(255) NOT NULL, year_entry VARCHAR(255) NOT NULL, year_exit VARCHAR(255) NOT NULL, nb_abscence INT NOT NULL, is_student TINYINT(1) NOT NULL, has_pro_contract TINYINT(1) NOT NULL, is_hired TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_C530657F5AE950BD (company_hired_id), UNIQUE INDEX UNIQ_C530657FADE0D45C (speciality_id_id), UNIQUE INDEX UNIQ_C530657F9D86650F (user_id_id), UNIQUE INDEX UNIQ_C530657FD7F76087 (actual_level_id), UNIQUE INDEX UNIQ_C530657F405A53A3 (previous_level_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_grade (id INT AUTO_INCREMENT NOT NULL, user_id_id INT DEFAULT NULL, subject_id_id INT DEFAULT NULL, grade DOUBLE PRECISION NOT NULL, status TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_BB98556C9D86650F (user_id_id), UNIQUE INDEX UNIQ_BB98556C6ED75F8F (subject_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE campus ADD CONSTRAINT FK_9D09681167B3B43D FOREIGN KEY (users_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE defense ADD CONSTRAINT FK_DBA5F5759D86650F FOREIGN KEY (user_id_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE intervenant ADD CONSTRAINT FK_73D0145C9D86650F FOREIGN KEY (user_id_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE partnership ADD CONSTRAINT FK_8619D6AE38B53C32 FOREIGN KEY (company_id_id) REFERENCES company (id)');
        $this->addSql('ALTER TABLE role ADD CONSTRAINT FK_57698A6A67B3B43D FOREIGN KEY (users_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE subject ADD CONSTRAINT FK_FBCE3E7AAB9A1716 FOREIGN KEY (intervenant_id) REFERENCES intervenant (id)');
        $this->addSql('ALTER TABLE user_comptability ADD CONSTRAINT FK_33FA84569D86650F FOREIGN KEY (user_id_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_extended ADD CONSTRAINT FK_C530657F5AE950BD FOREIGN KEY (company_hired_id) REFERENCES company (id)');
        $this->addSql('ALTER TABLE user_extended ADD CONSTRAINT FK_C530657FADE0D45C FOREIGN KEY (speciality_id_id) REFERENCES speciality (id)');
        $this->addSql('ALTER TABLE user_extended ADD CONSTRAINT FK_C530657F9D86650F FOREIGN KEY (user_id_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_extended ADD CONSTRAINT FK_C530657FD7F76087 FOREIGN KEY (actual_level_id) REFERENCES study_level (id)');
        $this->addSql('ALTER TABLE user_extended ADD CONSTRAINT FK_C530657F405A53A3 FOREIGN KEY (previous_level_id) REFERENCES study_level (id)');
        $this->addSql('ALTER TABLE user_grade ADD CONSTRAINT FK_BB98556C9D86650F FOREIGN KEY (user_id_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_grade ADD CONSTRAINT FK_BB98556C6ED75F8F FOREIGN KEY (subject_id_id) REFERENCES subject (id)');
        $this->addSql('ALTER TABLE user DROP roles');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE partnership DROP FOREIGN KEY FK_8619D6AE38B53C32');
        $this->addSql('ALTER TABLE user_extended DROP FOREIGN KEY FK_C530657F5AE950BD');
        $this->addSql('ALTER TABLE subject DROP FOREIGN KEY FK_FBCE3E7AAB9A1716');
        $this->addSql('ALTER TABLE user_extended DROP FOREIGN KEY FK_C530657FADE0D45C');
        $this->addSql('ALTER TABLE user_extended DROP FOREIGN KEY FK_C530657FD7F76087');
        $this->addSql('ALTER TABLE user_extended DROP FOREIGN KEY FK_C530657F405A53A3');
        $this->addSql('ALTER TABLE user_grade DROP FOREIGN KEY FK_BB98556C6ED75F8F');
        $this->addSql('DROP TABLE campus');
        $this->addSql('DROP TABLE company');
        $this->addSql('DROP TABLE defense');
        $this->addSql('DROP TABLE intervenant');
        $this->addSql('DROP TABLE partnership');
        $this->addSql('DROP TABLE role');
        $this->addSql('DROP TABLE speciality');
        $this->addSql('DROP TABLE study_level');
        $this->addSql('DROP TABLE subject');
        $this->addSql('DROP TABLE user_comptability');
        $this->addSql('DROP TABLE user_extended');
        $this->addSql('DROP TABLE user_grade');
        $this->addSql('ALTER TABLE messenger_messages CHANGE body body LONGTEXT NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE headers headers LONGTEXT NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE queue_name queue_name VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE user ADD roles JSON NOT NULL, CHANGE email email VARCHAR(180) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE password password VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE first_name first_name VARCHAR(45) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE last_name last_name VARCHAR(65) NOT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
