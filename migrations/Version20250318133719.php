<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250318133719 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX uniq_b8284aea979b1ad6');
        $this->addSql('ALTER TABLE subject_licence ALTER issue_authority DROP NOT NULL');
        $this->addSql('CREATE INDEX IDX_B8284AEA979B1AD6 ON subject_licence (company_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP INDEX IDX_B8284AEA979B1AD6');
        $this->addSql('ALTER TABLE subject_licence ALTER issue_authority SET NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX uniq_b8284aea979b1ad6 ON subject_licence (company_id)');
    }
}
