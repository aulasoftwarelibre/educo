<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190428001229 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DELETE FROM vote WHERE TRUE');
        $this->addSql('ALTER TABLE vote ADD question_id INT NOT NULL');
        $this->addSql('ALTER TABLE vote ADD client_unique_id VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE vote ADD CONSTRAINT FK_5A1085641E27F6BF FOREIGN KEY (question_id) REFERENCES question (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_5A1085641E27F6BF ON vote (question_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5A1085641E27F6BF8E6534C ON vote (question_id, client_unique_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE vote DROP CONSTRAINT FK_5A1085641E27F6BF');
        $this->addSql('DROP INDEX IDX_5A1085641E27F6BF');
        $this->addSql('DROP INDEX UNIQ_5A1085641E27F6BF8E6534C');
        $this->addSql('ALTER TABLE vote DROP question_id');
        $this->addSql('ALTER TABLE vote DROP client_unique_id');
    }
}
