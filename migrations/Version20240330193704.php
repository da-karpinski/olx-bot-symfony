<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240330193704 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE category_attribute (id INT AUTO_INCREMENT NOT NULL, attribute_code VARCHAR(60) NOT NULL, attribute_value VARCHAR(60) NOT NULL, worker_id INT NOT NULL, INDEX IDX_3D1A3DCB6B20BA36 (worker_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8');
        $this->addSql('CREATE TABLE worker (id INT AUTO_INCREMENT NOT NULL, enabled TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, last_executed_at DATETIME NOT NULL, execution_interval INT NOT NULL, user_id INT NOT NULL, city_id INT NOT NULL, category_id INT NOT NULL, INDEX IDX_9FB2BF62A76ED395 (user_id), INDEX IDX_9FB2BF628BAC62AF (city_id), INDEX IDX_9FB2BF6212469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8');
        $this->addSql('ALTER TABLE category_attribute ADD CONSTRAINT FK_3D1A3DCB6B20BA36 FOREIGN KEY (worker_id) REFERENCES worker (id)');
        $this->addSql('ALTER TABLE worker ADD CONSTRAINT FK_9FB2BF62A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE worker ADD CONSTRAINT FK_9FB2BF628BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
        $this->addSql('ALTER TABLE worker ADD CONSTRAINT FK_9FB2BF6212469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE category_attribute DROP FOREIGN KEY FK_3D1A3DCB6B20BA36');
        $this->addSql('ALTER TABLE worker DROP FOREIGN KEY FK_9FB2BF62A76ED395');
        $this->addSql('ALTER TABLE worker DROP FOREIGN KEY FK_9FB2BF628BAC62AF');
        $this->addSql('ALTER TABLE worker DROP FOREIGN KEY FK_9FB2BF6212469DE2');
        $this->addSql('DROP TABLE category_attribute');
        $this->addSql('DROP TABLE worker');
    }
}
