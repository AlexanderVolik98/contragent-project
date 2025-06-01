<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250316220827 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE company_history (id SERIAL NOT NULL, company_id INT NOT NULL, successor_company_id INT DEFAULT NULL, predecessor_company_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_7E86A9C979B1AD6 ON company_history (company_id)');
        $this->addSql('CREATE INDEX IDX_7E86A9CB85A85D3 ON company_history (successor_company_id)');
        $this->addSql('CREATE INDEX IDX_7E86A9C96B01E64 ON company_history (predecessor_company_id)');
        $this->addSql('ALTER TABLE company_history ADD CONSTRAINT FK_7E86A9C979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE company_history ADD CONSTRAINT FK_7E86A9CB85A85D3 FOREIGN KEY (successor_company_id) REFERENCES company (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE company_history ADD CONSTRAINT FK_7E86A9C96B01E64 FOREIGN KEY (predecessor_company_id) REFERENCES company (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE company DROP predecessors');
        $this->addSql('ALTER TABLE company DROP successors');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE company_history DROP CONSTRAINT FK_7E86A9C979B1AD6');
        $this->addSql('ALTER TABLE company_history DROP CONSTRAINT FK_7E86A9CB85A85D3');
        $this->addSql('ALTER TABLE company_history DROP CONSTRAINT FK_7E86A9C96B01E64');
        $this->addSql('DROP TABLE company_history');
        $this->addSql('ALTER TABLE company ADD predecessors JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE company ADD successors JSON DEFAULT NULL');
    }
}
