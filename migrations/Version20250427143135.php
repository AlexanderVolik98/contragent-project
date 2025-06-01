<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250427143135 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE okved ALTER code TYPE VARCHAR(100)');
        $this->addSql('ALTER TABLE okved ALTER parent_code TYPE VARCHAR(100)');
        $this->addSql('ALTER TABLE opf ALTER abbreviation TYPE VARCHAR(100)');
        $this->addSql('ALTER TABLE subject_okved ALTER okved_id TYPE VARCHAR(100)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE subject_okved ALTER okved_id TYPE VARCHAR(10)');
        $this->addSql('ALTER TABLE opf ALTER abbreviation TYPE VARCHAR(10)');
        $this->addSql('ALTER TABLE okved ALTER code TYPE VARCHAR(10)');
        $this->addSql('ALTER TABLE okved ALTER parent_code TYPE VARCHAR(10)');
    }
}
