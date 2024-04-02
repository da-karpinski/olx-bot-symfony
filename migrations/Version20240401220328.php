<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240401220328 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE notification (id INT AUTO_INCREMENT NOT NULL, additional_data JSON DEFAULT NULL, created_at DATETIME NOT NULL, sent_at DATETIME DEFAULT NULL, title VARCHAR(255) DEFAULT NULL, message LONGTEXT DEFAULT NULL, worker_id INT NOT NULL, offer_id INT NOT NULL, integration_id INT NOT NULL, INDEX IDX_BF5476CA6B20BA36 (worker_id), INDEX IDX_BF5476CA53C674EE (offer_id), INDEX IDX_BF5476CA9E82DDEA (integration_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CA6B20BA36 FOREIGN KEY (worker_id) REFERENCES worker (id)');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CA53C674EE FOREIGN KEY (offer_id) REFERENCES offer (id)');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CA9E82DDEA FOREIGN KEY (integration_id) REFERENCES integration (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CA6B20BA36');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CA53C674EE');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CA9E82DDEA');
        $this->addSql('DROP TABLE notification');
    }
}
