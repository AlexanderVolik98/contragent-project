<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250313111308 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE manager_id_seq CASCADE');
        $this->addSql('CREATE TABLE company_manager (id SERIAL NOT NULL, managed_company_id INT DEFAULT NULL, manager_id INT NOT NULL, inn VARCHAR(12) DEFAULT NULL, name VARCHAR(502) NOT NULL, post VARCHAR(255) DEFAULT NULL, dadata_id VARCHAR(255) NOT NULL, invalidity JSON DEFAULT NULL, type VARCHAR(255) NOT NULL, start_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_DA763F6E2BF1F10D ON company_manager (managed_company_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_DA763F6E783E3463 ON company_manager (manager_id)');
        $this->addSql('ALTER TABLE company_manager ADD CONSTRAINT FK_DA763F6E2BF1F10D FOREIGN KEY (managed_company_id) REFERENCES company (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE company_manager ADD CONSTRAINT FK_DA763F6E783E3463 FOREIGN KEY (manager_id) REFERENCES individual (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE manager DROP CONSTRAINT fk_fa2425b92bf1f10d');
        $this->addSql('DROP TABLE manager');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SEQUENCE manager_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE manager (id SERIAL NOT NULL, managed_company_id INT DEFAULT NULL, inn VARCHAR(12) DEFAULT NULL, name VARCHAR(502) NOT NULL, post VARCHAR(255) DEFAULT NULL, dadata_id VARCHAR(255) NOT NULL, invalidity JSON DEFAULT NULL, type VARCHAR(255) NOT NULL, start_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_fa2425b92bf1f10d ON manager (managed_company_id)');
        $this->addSql('ALTER TABLE manager ADD CONSTRAINT fk_fa2425b92bf1f10d FOREIGN KEY (managed_company_id) REFERENCES company (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE company_manager DROP CONSTRAINT FK_DA763F6E2BF1F10D');
        $this->addSql('ALTER TABLE company_manager DROP CONSTRAINT FK_DA763F6E783E3463');
        $this->addSql('DROP TABLE company_manager');
    }
}
