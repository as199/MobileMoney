<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210223160719 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE compte ADD admin_systeme_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE compte ADD CONSTRAINT FK_CFF65260FC51D1AB FOREIGN KEY (admin_systeme_id) REFERENCES utilisateur (id)');
        $this->addSql('CREATE INDEX IDX_CFF65260FC51D1AB ON compte (admin_systeme_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE compte DROP FOREIGN KEY FK_CFF65260FC51D1AB');
        $this->addSql('DROP INDEX IDX_CFF65260FC51D1AB ON compte');
        $this->addSql('ALTER TABLE compte DROP admin_systeme_id');
    }
}
