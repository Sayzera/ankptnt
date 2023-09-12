<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230911123855 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ObservationCache (id INT AUTO_INCREMENT NOT NULL, dataSource VARCHAR(50) DEFAULT NULL, searchedWord VARCHAR(255) DEFAULT NULL, searchedWordHtml VARCHAR(255) DEFAULT NULL, trademarkName VARCHAR(255) DEFAULT NULL, trademarkNameHtml VARCHAR(255) DEFAULT NULL, niceClasses LONGTEXT DEFAULT NULL, applicationNo VARCHAR(255) DEFAULT NULL, applicationDate VARCHAR(100) DEFAULT NULL, registerDate VARCHAR(255) DEFAULT NULL, protectionDate VARCHAR(100) DEFAULT NULL, holderName LONGTEXT DEFAULT NULL, bulletinNo INT DEFAULT NULL, bulletinPage VARCHAR(50) DEFAULT NULL, fileStatus VARCHAR(100) DEFAULT NULL, shapeSimilarity VARCHAR(50) DEFAULT NULL, phoneticSimilarity VARCHAR(50) DEFAULT NULL, isPriority VARCHAR(50) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE ObservationCache');
    }
}
