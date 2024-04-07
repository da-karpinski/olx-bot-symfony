<?php

declare(strict_types=1);

namespace App\Integration\Migration;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240407224822 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE telegram_integration (id INT AUTO_INCREMENT NOT NULL, chat_id BIGINT NOT NULL, chat_type VARCHAR(15) NOT NULL, otp VARCHAR(10) DEFAULT NULL, active TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, integration_id INT DEFAULT NULL, INDEX IDX_4D6BE5089E82DDEA (integration_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE telegram_integration ADD CONSTRAINT FK_4D6BE5089E82DDEA FOREIGN KEY (integration_id) REFERENCES integration (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE telegram_integration DROP FOREIGN KEY FK_4D6BE5089E82DDEA');
        $this->addSql('DROP TABLE telegram_integration');
    }
}
