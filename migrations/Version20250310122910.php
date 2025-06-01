<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250310122910 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE company (id SERIAL NOT NULL, parent_company_id INT DEFAULT NULL, dadata_id VARCHAR(255) DEFAULT NULL, inn BIGINT DEFAULT NULL, kpp BIGINT DEFAULT NULL, is_main BOOLEAN NOT NULL, ogrn BIGINT DEFAULT NULL, opf VARCHAR(45) DEFAULT NULL, name VARCHAR(510) NOT NULL, phones JSON DEFAULT NULL, emails JSON DEFAULT NULL, slug VARCHAR(255) NOT NULL, data JSON DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4FBF094F515E1A61 ON company (dadata_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4FBF094F989D9B62 ON company (slug)');
        $this->addSql('CREATE INDEX IDX_4FBF094FD0D89E86 ON company (parent_company_id)');
        $this->addSql('CREATE INDEX idx_dadata_id ON company (dadata_id)');
        $this->addSql('CREATE INDEX idx_inn ON company (inn)');
        $this->addSql('CREATE INDEX idx_kpp ON company (kpp)');
        $this->addSql('CREATE INDEX idx_ogrn ON company (ogrn)');
        $this->addSql('CREATE UNIQUE INDEX uniq_inn_kpp ON company (inn, kpp)');
        $this->addSql('CREATE TABLE company_capital (id SERIAL NOT NULL, company_id INT DEFAULT NULL, type VARCHAR(255) NOT NULL, value DOUBLE PRECISION NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_102EA071979B1AD6 ON company_capital (company_id)');
        $this->addSql('CREATE TABLE founder (id SERIAL NOT NULL, company_id INT DEFAULT NULL, individual_id INT DEFAULT NULL, founded_company_id INT NOT NULL, name VARCHAR(510) DEFAULT NULL, type VARCHAR(255) NOT NULL, dadata_id VARCHAR(255) DEFAULT NULL, share JSON DEFAULT NULL, share_type VARCHAR(255) DEFAULT NULL, invalidity JSON DEFAULT NULL, data JSON DEFAULT NULL, start_date DATE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_8F3C18AF979B1AD6 ON founder (company_id)');
        $this->addSql('CREATE INDEX IDX_8F3C18AFAE271C0D ON founder (individual_id)');
        $this->addSql('CREATE INDEX IDX_8F3C18AF3178990 ON founder (founded_company_id)');
        $this->addSql('COMMENT ON COLUMN founder.start_date IS \'(DC2Type:date_immutable)\'');
        $this->addSql('CREATE TABLE individual (id SERIAL NOT NULL, inn VARCHAR(12) DEFAULT NULL, name VARCHAR(255) NOT NULL, surname VARCHAR(255) NOT NULL, patronymic VARCHAR(255) DEFAULT NULL, slug VARCHAR(255) NOT NULL, gender VARCHAR(255) DEFAULT NULL, data JSON DEFAULT NULL, phones JSON DEFAULT NULL, emails JSON DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8793FC17989D9B62 ON individual (slug)');
        $this->addSql('CREATE TABLE manager (id SERIAL NOT NULL, managed_company_id INT DEFAULT NULL, inn VARCHAR(12) DEFAULT NULL, name VARCHAR(502) NOT NULL, post VARCHAR(255) DEFAULT NULL, dadata_id VARCHAR(255) NOT NULL, invalidity JSON DEFAULT NULL, type VARCHAR(255) NOT NULL, start_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_FA2425B92BF1F10D ON manager (managed_company_id)');
        $this->addSql('CREATE TABLE okved (code VARCHAR(10) NOT NULL, parent_code VARCHAR(10) DEFAULT NULL, section VARCHAR(5) NOT NULL, name TEXT NOT NULL, comment TEXT NOT NULL, PRIMARY KEY(code))');
        $this->addSql('CREATE TABLE subject_address (id SERIAL NOT NULL, company_id INT DEFAULT NULL, individual_id INT DEFAULT NULL, street VARCHAR(255) NOT NULL, city VARCHAR(100) NOT NULL, country VARCHAR(100) NOT NULL, full_address TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A8CCC323979B1AD6 ON subject_address (company_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A8CCC323AE271C0D ON subject_address (individual_id)');
        $this->addSql('CREATE TABLE subject_authority (id SERIAL NOT NULL, company_id INT DEFAULT NULL, individual_id INT DEFAULT NULL, type VARCHAR(255) NOT NULL, code VARCHAR(50) NOT NULL, name VARCHAR(255) NOT NULL, address TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_9A8FED6979B1AD6 ON subject_authority (company_id)');
        $this->addSql('CREATE INDEX IDX_9A8FED6AE271C0D ON subject_authority (individual_id)');
        $this->addSql('CREATE TABLE subject_document (id SERIAL NOT NULL, company_id INT DEFAULT NULL, individual_id INT DEFAULT NULL, type VARCHAR(50) NOT NULL, series VARCHAR(20) DEFAULT NULL, number VARCHAR(50) DEFAULT NULL, issue_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, issue_authority VARCHAR(20) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_E014CA1E979B1AD6 ON subject_document (company_id)');
        $this->addSql('CREATE INDEX IDX_E014CA1EAE271C0D ON subject_document (individual_id)');
        $this->addSql('COMMENT ON COLUMN subject_document.issue_date IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE subject_finance (id SERIAL NOT NULL, company_id INT DEFAULT NULL, individual_id INT DEFAULT NULL, tax_system VARCHAR(10) DEFAULT NULL, income DOUBLE PRECISION DEFAULT NULL, expense DOUBLE PRECISION DEFAULT NULL, revenue DOUBLE PRECISION DEFAULT NULL, debt DOUBLE PRECISION DEFAULT NULL, penalty DOUBLE PRECISION DEFAULT NULL, year INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6BAA4642979B1AD6 ON subject_finance (company_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6BAA4642AE271C0D ON subject_finance (individual_id)');
        $this->addSql('CREATE TABLE subject_okved (id SERIAL NOT NULL, company_id INT DEFAULT NULL, individual_id INT DEFAULT NULL, okved_id VARCHAR(10) NOT NULL, is_primary BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_22DD881A979B1AD6 ON subject_okved (company_id)');
        $this->addSql('CREATE INDEX IDX_22DD881AAE271C0D ON subject_okved (individual_id)');
        $this->addSql('CREATE INDEX IDX_22DD881A44B51FF6 ON subject_okved (okved_id)');
        $this->addSql('CREATE TABLE subject_state (id SERIAL NOT NULL, company_id INT DEFAULT NULL, individual_id INT DEFAULT NULL, status VARCHAR(255) DEFAULT NULL, code VARCHAR(50) DEFAULT NULL, actuality_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, registration_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, liquidation_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B2001323979B1AD6 ON subject_state (company_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B2001323AE271C0D ON subject_state (individual_id)');
        $this->addSql('COMMENT ON COLUMN subject_state.actuality_date IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN subject_state.registration_date IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN subject_state.liquidation_date IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE company ADD CONSTRAINT FK_4FBF094FD0D89E86 FOREIGN KEY (parent_company_id) REFERENCES company (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE company_capital ADD CONSTRAINT FK_102EA071979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE founder ADD CONSTRAINT FK_8F3C18AF979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE founder ADD CONSTRAINT FK_8F3C18AFAE271C0D FOREIGN KEY (individual_id) REFERENCES individual (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE founder ADD CONSTRAINT FK_8F3C18AF3178990 FOREIGN KEY (founded_company_id) REFERENCES company (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE manager ADD CONSTRAINT FK_FA2425B92BF1F10D FOREIGN KEY (managed_company_id) REFERENCES company (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE subject_address ADD CONSTRAINT FK_A8CCC323979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE subject_address ADD CONSTRAINT FK_A8CCC323AE271C0D FOREIGN KEY (individual_id) REFERENCES individual (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE subject_authority ADD CONSTRAINT FK_9A8FED6979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE subject_authority ADD CONSTRAINT FK_9A8FED6AE271C0D FOREIGN KEY (individual_id) REFERENCES individual (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE subject_document ADD CONSTRAINT FK_E014CA1E979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE subject_document ADD CONSTRAINT FK_E014CA1EAE271C0D FOREIGN KEY (individual_id) REFERENCES individual (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE subject_finance ADD CONSTRAINT FK_6BAA4642979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE subject_finance ADD CONSTRAINT FK_6BAA4642AE271C0D FOREIGN KEY (individual_id) REFERENCES individual (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE subject_okved ADD CONSTRAINT FK_22DD881A979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE subject_okved ADD CONSTRAINT FK_22DD881AAE271C0D FOREIGN KEY (individual_id) REFERENCES individual (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE subject_okved ADD CONSTRAINT FK_22DD881A44B51FF6 FOREIGN KEY (okved_id) REFERENCES okved (code) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE subject_state ADD CONSTRAINT FK_B2001323979B1AD6 FOREIGN KEY (company_id) REFERENCES company (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE subject_state ADD CONSTRAINT FK_B2001323AE271C0D FOREIGN KEY (individual_id) REFERENCES individual (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE company DROP CONSTRAINT FK_4FBF094FD0D89E86');
        $this->addSql('ALTER TABLE company_capital DROP CONSTRAINT FK_102EA071979B1AD6');
        $this->addSql('ALTER TABLE founder DROP CONSTRAINT FK_8F3C18AF979B1AD6');
        $this->addSql('ALTER TABLE founder DROP CONSTRAINT FK_8F3C18AFAE271C0D');
        $this->addSql('ALTER TABLE founder DROP CONSTRAINT FK_8F3C18AF3178990');
        $this->addSql('ALTER TABLE manager DROP CONSTRAINT FK_FA2425B92BF1F10D');
        $this->addSql('ALTER TABLE subject_address DROP CONSTRAINT FK_A8CCC323979B1AD6');
        $this->addSql('ALTER TABLE subject_address DROP CONSTRAINT FK_A8CCC323AE271C0D');
        $this->addSql('ALTER TABLE subject_authority DROP CONSTRAINT FK_9A8FED6979B1AD6');
        $this->addSql('ALTER TABLE subject_authority DROP CONSTRAINT FK_9A8FED6AE271C0D');
        $this->addSql('ALTER TABLE subject_document DROP CONSTRAINT FK_E014CA1E979B1AD6');
        $this->addSql('ALTER TABLE subject_document DROP CONSTRAINT FK_E014CA1EAE271C0D');
        $this->addSql('ALTER TABLE subject_finance DROP CONSTRAINT FK_6BAA4642979B1AD6');
        $this->addSql('ALTER TABLE subject_finance DROP CONSTRAINT FK_6BAA4642AE271C0D');
        $this->addSql('ALTER TABLE subject_okved DROP CONSTRAINT FK_22DD881A979B1AD6');
        $this->addSql('ALTER TABLE subject_okved DROP CONSTRAINT FK_22DD881AAE271C0D');
        $this->addSql('ALTER TABLE subject_okved DROP CONSTRAINT FK_22DD881A44B51FF6');
        $this->addSql('ALTER TABLE subject_state DROP CONSTRAINT FK_B2001323979B1AD6');
        $this->addSql('ALTER TABLE subject_state DROP CONSTRAINT FK_B2001323AE271C0D');
        $this->addSql('DROP TABLE company');
        $this->addSql('DROP TABLE company_capital');
        $this->addSql('DROP TABLE founder');
        $this->addSql('DROP TABLE individual');
        $this->addSql('DROP TABLE manager');
        $this->addSql('DROP TABLE okved');
        $this->addSql('DROP TABLE subject_address');
        $this->addSql('DROP TABLE subject_authority');
        $this->addSql('DROP TABLE subject_document');
        $this->addSql('DROP TABLE subject_finance');
        $this->addSql('DROP TABLE subject_okved');
        $this->addSql('DROP TABLE subject_state');
    }
}
