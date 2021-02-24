<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210224111607 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE agence (id INT AUTO_INCREMENT NOT NULL, compte_id INT DEFAULT NULL, nom_agence VARCHAR(255) NOT NULL, adresse VARCHAR(255) NOT NULL, status TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_64C19AA9F2C56620 (compte_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE client (id INT AUTO_INCREMENT NOT NULL, nom_complet VARCHAR(255) NOT NULL, cni INT NOT NULL, adresse VARCHAR(255) NOT NULL, telephone VARCHAR(255) NOT NULL, status TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE client_transaction (client_id INT NOT NULL, transaction_id INT NOT NULL, INDEX IDX_737C20EA19EB6921 (client_id), INDEX IDX_737C20EA2FC0CB0F (transaction_id), PRIMARY KEY(client_id, transaction_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE compte (id INT AUTO_INCREMENT NOT NULL, admin_systeme_id INT DEFAULT NULL, num_compte VARCHAR(255) NOT NULL, solde INT NOT NULL, status TINYINT(1) NOT NULL, INDEX IDX_CFF65260FC51D1AB (admin_systeme_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE profil (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(255) NOT NULL, status TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE transaction (id INT AUTO_INCREMENT NOT NULL, montant INT NOT NULL, date_envoi DATE DEFAULT NULL, date_retrait DATE DEFAULT NULL, date_annulation DATE DEFAULT NULL, total_commission DOUBLE PRECISION NOT NULL, commission_etat DOUBLE PRECISION NOT NULL, commission_transfere DOUBLE PRECISION NOT NULL, commission_depot DOUBLE PRECISION NOT NULL, commission_retrait DOUBLE PRECISION NOT NULL, status TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE utilisateur (id INT AUTO_INCREMENT NOT NULL, profil_id INT DEFAULT NULL, agence_id INT DEFAULT NULL, email VARCHAR(180) NOT NULL, password VARCHAR(255) NOT NULL, nom_complet VARCHAR(255) NOT NULL, telephone VARCHAR(100) NOT NULL, adresse VARCHAR(255) NOT NULL, genre VARCHAR(255) NOT NULL, status TINYINT(1) NOT NULL, avatar LONGBLOB DEFAULT NULL, type_id INT NOT NULL, UNIQUE INDEX UNIQ_1D1C63B3E7927C74 (email), INDEX IDX_1D1C63B3275ED078 (profil_id), INDEX IDX_1D1C63B3D725330D (agence_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE utilisateur_transaction (utilisateur_id INT NOT NULL, transaction_id INT NOT NULL, INDEX IDX_86D10842FB88E14F (utilisateur_id), INDEX IDX_86D108422FC0CB0F (transaction_id), PRIMARY KEY(utilisateur_id, transaction_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE caissier_compte (caissier_id INT NOT NULL, compte_id INT NOT NULL, INDEX IDX_56EAE243B514973B (caissier_id), INDEX IDX_56EAE243F2C56620 (compte_id), PRIMARY KEY(caissier_id, compte_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE agence ADD CONSTRAINT FK_64C19AA9F2C56620 FOREIGN KEY (compte_id) REFERENCES compte (id)');
        $this->addSql('ALTER TABLE client_transaction ADD CONSTRAINT FK_737C20EA19EB6921 FOREIGN KEY (client_id) REFERENCES client (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE client_transaction ADD CONSTRAINT FK_737C20EA2FC0CB0F FOREIGN KEY (transaction_id) REFERENCES transaction (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE compte ADD CONSTRAINT FK_CFF65260FC51D1AB FOREIGN KEY (admin_systeme_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE utilisateur ADD CONSTRAINT FK_1D1C63B3275ED078 FOREIGN KEY (profil_id) REFERENCES profil (id)');
        $this->addSql('ALTER TABLE utilisateur ADD CONSTRAINT FK_1D1C63B3D725330D FOREIGN KEY (agence_id) REFERENCES agence (id)');
        $this->addSql('ALTER TABLE utilisateur_transaction ADD CONSTRAINT FK_86D10842FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE utilisateur_transaction ADD CONSTRAINT FK_86D108422FC0CB0F FOREIGN KEY (transaction_id) REFERENCES transaction (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE caissier_compte ADD CONSTRAINT FK_56EAE243B514973B FOREIGN KEY (caissier_id) REFERENCES utilisateur (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE caissier_compte ADD CONSTRAINT FK_56EAE243F2C56620 FOREIGN KEY (compte_id) REFERENCES compte (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE utilisateur DROP FOREIGN KEY FK_1D1C63B3D725330D');
        $this->addSql('ALTER TABLE client_transaction DROP FOREIGN KEY FK_737C20EA19EB6921');
        $this->addSql('ALTER TABLE agence DROP FOREIGN KEY FK_64C19AA9F2C56620');
        $this->addSql('ALTER TABLE caissier_compte DROP FOREIGN KEY FK_56EAE243F2C56620');
        $this->addSql('ALTER TABLE utilisateur DROP FOREIGN KEY FK_1D1C63B3275ED078');
        $this->addSql('ALTER TABLE client_transaction DROP FOREIGN KEY FK_737C20EA2FC0CB0F');
        $this->addSql('ALTER TABLE utilisateur_transaction DROP FOREIGN KEY FK_86D108422FC0CB0F');
        $this->addSql('ALTER TABLE compte DROP FOREIGN KEY FK_CFF65260FC51D1AB');
        $this->addSql('ALTER TABLE utilisateur_transaction DROP FOREIGN KEY FK_86D10842FB88E14F');
        $this->addSql('ALTER TABLE caissier_compte DROP FOREIGN KEY FK_56EAE243B514973B');
        $this->addSql('DROP TABLE agence');
        $this->addSql('DROP TABLE client');
        $this->addSql('DROP TABLE client_transaction');
        $this->addSql('DROP TABLE compte');
        $this->addSql('DROP TABLE profil');
        $this->addSql('DROP TABLE transaction');
        $this->addSql('DROP TABLE utilisateur');
        $this->addSql('DROP TABLE utilisateur_transaction');
        $this->addSql('DROP TABLE caissier_compte');
    }
}
