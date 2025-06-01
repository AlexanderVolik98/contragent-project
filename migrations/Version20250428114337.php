<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250428114337 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create GIN index on company.name using pg_trgm';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE EXTENSION IF NOT EXISTS pg_trgm;');
        $this->addSql('CREATE INDEX idx_company_name_trgm ON company USING GIN (name gin_trgm_ops);');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX idx_company_name_trgm;');
    }
}
