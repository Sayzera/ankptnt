<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230911130729 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ObservationCacheMainList (id INT AUTO_INCREMENT NOT NULL, searchedWord VARCHAR(255) DEFAULT NULL, niceClasses VARCHAR(255) DEFAULT NULL, bulletinNo VARCHAR(20) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ObservationCache ADD observationCacheMainList_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ObservationCache ADD CONSTRAINT FK_A8C9F13014614C6E FOREIGN KEY (observationCacheMainList_id) REFERENCES ObservationCacheMainList (id)');
        $this->addSql('CREATE INDEX IDX_A8C9F13014614C6E ON ObservationCache (observationCacheMainList_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ObservationCache DROP FOREIGN KEY FK_A8C9F13014614C6E');
        $this->addSql('DROP TABLE ObservationCacheMainList');
        $this->addSql('DROP INDEX IDX_A8C9F13014614C6E ON ObservationCache');
        $this->addSql('ALTER TABLE ObservationCache DROP observationCacheMainList_id');
    }
}
