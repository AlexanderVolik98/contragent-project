<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250319144644 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE individual ADD okpo VARCHAR(8) DEFAULT NULL');
        $this->addSql('ALTER TABLE individual ADD okato VARCHAR(11) DEFAULT NULL');
        $this->addSql('ALTER TABLE individual ADD oktmo VARCHAR(11) DEFAULT NULL');
        $this->addSql('ALTER TABLE individual ADD okogu VARCHAR(7) DEFAULT NULL');
        $this->addSql('ALTER TABLE individual ADD okfs VARCHAR(2) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE individual DROP okpo');
        $this->addSql('ALTER TABLE individual DROP okato');
        $this->addSql('ALTER TABLE individual DROP oktmo');
        $this->addSql('ALTER TABLE individual DROP okogu');
        $this->addSql('ALTER TABLE individual DROP okfs');
    }
}
