<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250313120551 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE company_manager DROP CONSTRAINT fk_da763f6e783e3463');
        $this->addSql('DROP INDEX uniq_da763f6e783e3463');
        $this->addSql('ALTER TABLE company_manager ADD company_manager_id INT NOT NULL');
        $this->addSql('ALTER TABLE company_manager RENAME COLUMN manager_id TO individual_manager_id');
        $this->addSql('ALTER TABLE company_manager ADD CONSTRAINT FK_DA763F6E3C2E8B7C FOREIGN KEY (individual_manager_id) REFERENCES individual (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE company_manager ADD CONSTRAINT FK_DA763F6EC5B0DDF5 FOREIGN KEY (company_manager_id) REFERENCES company (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_DA763F6E3C2E8B7C ON company_manager (individual_manager_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_DA763F6EC5B0DDF5 ON company_manager (company_manager_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE company_manager DROP CONSTRAINT FK_DA763F6E3C2E8B7C');
        $this->addSql('ALTER TABLE company_manager DROP CONSTRAINT FK_DA763F6EC5B0DDF5');
        $this->addSql('DROP INDEX UNIQ_DA763F6E3C2E8B7C');
        $this->addSql('DROP INDEX UNIQ_DA763F6EC5B0DDF5');
        $this->addSql('ALTER TABLE company_manager ADD manager_id INT NOT NULL');
        $this->addSql('ALTER TABLE company_manager DROP individual_manager_id');
        $this->addSql('ALTER TABLE company_manager DROP company_manager_id');
        $this->addSql('ALTER TABLE company_manager ADD CONSTRAINT fk_da763f6e783e3463 FOREIGN KEY (manager_id) REFERENCES individual (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX uniq_da763f6e783e3463 ON company_manager (manager_id)');
    }
}
