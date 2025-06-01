<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250408221516 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE region (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE subject_address ADD region_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE subject_address ADD CONSTRAINT FK_A8CCC32398260155 FOREIGN KEY (region_id) REFERENCES region (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_A8CCC32398260155 ON subject_address (region_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE subject_address DROP CONSTRAINT FK_A8CCC32398260155');
        $this->addSql('DROP TABLE region');
        $this->addSql('DROP INDEX IDX_A8CCC32398260155');
        $this->addSql('ALTER TABLE subject_address DROP region_id');
    }
}
