<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250602212801 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE company ALTER COLUMN data TYPE jsonb USING data::jsonb;');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE company ALTER COLUMN data TYPE json USING data::json;');
    }
}
