<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250318124753 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE subject_licence (id SERIAL NOT NULL, individual_id INT DEFAULT NULL, company_id INT DEFAULT NULL, series VARCHAR(255) DEFAULT NULL, number VARCHAR(255) NOT NULL, issue_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, issue_authority VARCHAR(255) NOT NULL, suspend_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, suspend_authority VARCHAR(255) DEFAULT NULL, valid_from TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, valid_to TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, activities JSON NOT NULL, addresses JSON DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B8284AEAAE271C0D ON subject_licence (individual_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B8284AEA979B1AD6 ON subject_licence (company_id)');
        $this->addSql('ALTER TABLE subject_licence ADD CONSTRAINT FK_B8284AEAAE271C0D FOREIGN KEY (individual_id) REFERENCES individual (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE subject_licence ADD CONSTRAINT FK_B8284AEA979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE subject_licence DROP CONSTRAINT FK_B8284AEAAE271C0D');
        $this->addSql('ALTER TABLE subject_licence DROP CONSTRAINT FK_B8284AEA979B1AD6');
        $this->addSql('DROP TABLE subject_licence');
    }
}
