<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250311162506 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE company ADD okpo VARCHAR(8) DEFAULT NULL');
        $this->addSql('ALTER TABLE company ADD okato VARCHAR(11) DEFAULT NULL');
        $this->addSql('ALTER TABLE company ADD oktmo VARCHAR(11) DEFAULT NULL');
        $this->addSql('ALTER TABLE company ADD okogu VARCHAR(7) DEFAULT NULL');
        $this->addSql('ALTER TABLE company ADD okfs VARCHAR(2) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE company DROP okpo');
        $this->addSql('ALTER TABLE company DROP okato');
        $this->addSql('ALTER TABLE company DROP oktmo');
        $this->addSql('ALTER TABLE company DROP okogu');
        $this->addSql('ALTER TABLE company DROP okfs');
    }
}
