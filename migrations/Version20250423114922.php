<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250423114922 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE opf (id SERIAL NOT NULL, abbreviation VARCHAR(10) NOT NULL, full_name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE company ADD opf_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE company DROP opf');
        $this->addSql('ALTER TABLE company ADD CONSTRAINT FK_4FBF094F36FF481E FOREIGN KEY (opf_id) REFERENCES opf (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_4FBF094F36FF481E ON company (opf_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE company DROP CONSTRAINT FK_4FBF094F36FF481E');
        $this->addSql('DROP TABLE opf');
        $this->addSql('DROP INDEX IDX_4FBF094F36FF481E');
        $this->addSql('ALTER TABLE company ADD opf VARCHAR(45) DEFAULT NULL');
        $this->addSql('ALTER TABLE company DROP opf_id');
    }
}
