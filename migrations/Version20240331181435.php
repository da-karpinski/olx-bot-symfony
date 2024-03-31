<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240331181435 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE offer (id INT AUTO_INCREMENT NOT NULL, last_seen_at DATETIME NOT NULL, created_at DATETIME NOT NULL, description LONGTEXT DEFAULT NULL, url VARCHAR(255) NOT NULL, olx_id INT NOT NULL, refreshed_at DATETIME DEFAULT NULL, title VARCHAR(255) NOT NULL, valid_to DATETIME NOT NULL, price INT DEFAULT NULL, price_currency VARCHAR(3) DEFAULT NULL, worker_id INT NOT NULL, INDEX IDX_29D6873E6B20BA36 (worker_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8');
        $this->addSql('CREATE TABLE offer_parameter (id INT AUTO_INCREMENT NOT NULL, parameter_key VARCHAR(60) NOT NULL, parameter_name VARCHAR(60) NOT NULL, value_key VARCHAR(60) NOT NULL, value_label VARCHAR(60) NOT NULL, offer_id INT NOT NULL, INDEX IDX_8952ECD453C674EE (offer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8');
        $this->addSql('CREATE TABLE offer_photo (id INT AUTO_INCREMENT NOT NULL, file_name VARCHAR(255) NOT NULL, real_file_name VARCHAR(255) NOT NULL, olx_id INT NOT NULL, offer_id INT NOT NULL, INDEX IDX_979AF9F153C674EE (offer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8');
        $this->addSql('ALTER TABLE offer ADD CONSTRAINT FK_29D6873E6B20BA36 FOREIGN KEY (worker_id) REFERENCES worker (id)');
        $this->addSql('ALTER TABLE offer_parameter ADD CONSTRAINT FK_8952ECD453C674EE FOREIGN KEY (offer_id) REFERENCES offer (id)');
        $this->addSql('ALTER TABLE offer_photo ADD CONSTRAINT FK_979AF9F153C674EE FOREIGN KEY (offer_id) REFERENCES offer (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE offer DROP FOREIGN KEY FK_29D6873E6B20BA36');
        $this->addSql('ALTER TABLE offer_parameter DROP FOREIGN KEY FK_8952ECD453C674EE');
        $this->addSql('ALTER TABLE offer_photo DROP FOREIGN KEY FK_979AF9F153C674EE');
        $this->addSql('DROP TABLE offer');
        $this->addSql('DROP TABLE offer_parameter');
        $this->addSql('DROP TABLE offer_photo');
    }
}
