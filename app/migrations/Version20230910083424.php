<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230910083424 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE Lang (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(10) DEFAULT NULL, created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', status TINYINT(1) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE LangMessages (id INT AUTO_INCREMENT NOT NULL, lang_id INT NOT NULL, created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', status TINYINT(1) DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, message LONGTEXT DEFAULT NULL, INDEX IDX_6F9C0EE1B213FA4 (lang_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE SystemSettings (id INT AUTO_INCREMENT NOT NULL, logo VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, address LONGTEXT DEFAULT NULL, phone VARCHAR(60) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE User (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, col_expiry_date VARCHAR(50) DEFAULT NULL, col_is_customer_representative TINYINT(1) DEFAULT NULL, col_is_deleted TINYINT(1) DEFAULT NULL, col_last_login VARCHAR(50) DEFAULT NULL, col_level VARCHAR(10) DEFAULT NULL, col_name VARCHAR(255) DEFAULT NULL, col_startingof_employment VARCHAR(50) DEFAULT NULL, col_surname VARCHAR(255) DEFAULT NULL, col_workgroup_id INT DEFAULT NULL, col_department_id INT DEFAULT NULL, col_username VARCHAR(255) DEFAULT NULL, col_unix_username VARCHAR(255) DEFAULT NULL, col_first_page VARCHAR(255) DEFAULT NULL, col_registration_number VARCHAR(255) DEFAULT NULL, col_watch_auth VARCHAR(10) DEFAULT NULL, col_is_working_on VARCHAR(10) DEFAULT NULL, col_expiry_date_watch VARCHAR(50) DEFAULT NULL, UNIQUE INDEX UNIQ_2DA17977E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE LangMessages ADD CONSTRAINT FK_6F9C0EE1B213FA4 FOREIGN KEY (lang_id) REFERENCES Lang (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE LangMessages DROP FOREIGN KEY FK_6F9C0EE1B213FA4');
        $this->addSql('DROP TABLE Lang');
        $this->addSql('DROP TABLE LangMessages');
        $this->addSql('DROP TABLE SystemSettings');
        $this->addSql('DROP TABLE User');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
