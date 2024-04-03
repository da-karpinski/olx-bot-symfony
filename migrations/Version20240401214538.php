<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240401214538 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE integration (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, created_at DATETIME NOT NULL, user_id INT NOT NULL, integration_type_id INT NOT NULL, INDEX IDX_FDE96D9BA76ED395 (user_id), INDEX IDX_FDE96D9BA86036CA (integration_type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE integration_type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(60) NOT NULL, integration_code VARCHAR(60) NOT NULL, enabled TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE worker_integration (id INT AUTO_INCREMENT NOT NULL, worker_id INT NOT NULL, integration_id INT NOT NULL, INDEX IDX_3461715C6B20BA36 (worker_id), INDEX IDX_3461715C9E82DDEA (integration_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE integration ADD CONSTRAINT FK_FDE96D9BA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE integration ADD CONSTRAINT FK_FDE96D9BA86036CA FOREIGN KEY (integration_type_id) REFERENCES integration_type (id)');
        $this->addSql('ALTER TABLE worker_integration ADD CONSTRAINT FK_3461715C6B20BA36 FOREIGN KEY (worker_id) REFERENCES worker (id)');
        $this->addSql('ALTER TABLE worker_integration ADD CONSTRAINT FK_3461715C9E82DDEA FOREIGN KEY (integration_id) REFERENCES integration (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE integration DROP FOREIGN KEY FK_FDE96D9BA76ED395');
        $this->addSql('ALTER TABLE integration DROP FOREIGN KEY FK_FDE96D9BA86036CA');
        $this->addSql('ALTER TABLE worker_integration DROP FOREIGN KEY FK_3461715C6B20BA36');
        $this->addSql('ALTER TABLE worker_integration DROP FOREIGN KEY FK_3461715C9E82DDEA');
        $this->addSql('DROP TABLE integration');
        $this->addSql('DROP TABLE integration_type');
        $this->addSql('DROP TABLE worker_integration');
    }
}
