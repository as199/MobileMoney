<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210301111231 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE transaction ADD client_envoi_id INT DEFAULT NULL, ADD client_recepteur_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D11171DC20 FOREIGN KEY (client_envoi_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D18A43DA2C FOREIGN KEY (client_recepteur_id) REFERENCES client (id)');
        $this->addSql('CREATE INDEX IDX_723705D11171DC20 ON transaction (client_envoi_id)');
        $this->addSql('CREATE INDEX IDX_723705D18A43DA2C ON transaction (client_recepteur_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D11171DC20');
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D18A43DA2C');
        $this->addSql('DROP INDEX IDX_723705D11171DC20 ON transaction');
        $this->addSql('DROP INDEX IDX_723705D18A43DA2C ON transaction');
        $this->addSql('ALTER TABLE transaction DROP client_envoi_id, DROP client_recepteur_id');
    }
}
