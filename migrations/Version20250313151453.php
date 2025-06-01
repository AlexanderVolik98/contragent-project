<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250313151453 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX uniq_da763f6ec5b0ddf5');
        $this->addSql('DROP INDEX uniq_da763f6e3c2e8b7c');
        $this->addSql('ALTER TABLE company_manager ALTER company_manager_id SET NOT NULL');
        $this->addSql('CREATE INDEX IDX_DA763F6E3C2E8B7C ON company_manager (individual_manager_id)');
        $this->addSql('CREATE INDEX IDX_DA763F6EC5B0DDF5 ON company_manager (company_manager_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP INDEX IDX_DA763F6E3C2E8B7C');
        $this->addSql('DROP INDEX IDX_DA763F6EC5B0DDF5');
        $this->addSql('ALTER TABLE company_manager ALTER company_manager_id DROP NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX uniq_da763f6ec5b0ddf5 ON company_manager (company_manager_id)');
        $this->addSql('CREATE UNIQUE INDEX uniq_da763f6e3c2e8b7c ON company_manager (individual_manager_id)');
    }
}
