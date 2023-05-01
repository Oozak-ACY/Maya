<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230409133211 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE animaux ADD CONSTRAINT FK_9ABE194D6E59D40D FOREIGN KEY (race_id) REFERENCES races_animaux (id)');
        $this->addSql('CREATE INDEX IDX_9ABE194D6E59D40D ON animaux (race_id)');
        $this->addSql('ALTER TABLE produit ADD description VARCHAR(255) DEFAULT NULL, ADD cru TINYINT(1) NOT NULL, ADD cuit TINYINT(1) NOT NULL, ADD bio TINYINT(1) NOT NULL, ADD debut_disponibilite DATE DEFAULT NULL, ADD fin_disponibilite DATE DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE animaux DROP FOREIGN KEY FK_9ABE194D6E59D40D');
        $this->addSql('DROP INDEX IDX_9ABE194D6E59D40D ON animaux');
        $this->addSql('ALTER TABLE produit DROP description, DROP cru, DROP cuit, DROP bio, DROP debut_disponibilite, DROP fin_disponibilite');
    }
}