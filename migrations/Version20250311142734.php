<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250311142734 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE company_management_co (id SERIAL NOT NULL, managed_company_id INT DEFAULT NULL, manager_company_id INT DEFAULT NULL, name VARCHAR(510) DEFAULT NULL, dadata_id VARCHAR(255) DEFAULT NULL, data JSON DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_91CA0E712BF1F10D ON company_management_co (managed_company_id)');
        $this->addSql('CREATE INDEX IDX_91CA0E71C8D4B911 ON company_management_co (manager_company_id)');
        $this->addSql('ALTER TABLE company_management_co ADD CONSTRAINT FK_91CA0E712BF1F10D FOREIGN KEY (managed_company_id) REFERENCES company (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE company_management_co ADD CONSTRAINT FK_91CA0E71C8D4B911 FOREIGN KEY (manager_company_id) REFERENCES company (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE company_management_co DROP CONSTRAINT FK_91CA0E712BF1F10D');
        $this->addSql('ALTER TABLE company_management_co DROP CONSTRAINT FK_91CA0E71C8D4B911');
        $this->addSql('DROP TABLE company_management_co');
    }
}
