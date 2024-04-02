<?php

declare(strict_types=1);

namespace App\Integration\Migration;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240401222545 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Migration for email integration entity';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE email_integration (id INT AUTO_INCREMENT NOT NULL, recipient_address VARCHAR(100) NOT NULL, cc_addresses JSON DEFAULT NULL, bcc_addresses JSON DEFAULT NULL, integration_id INT NOT NULL, INDEX IDX_EB29112C9E82DDEA (integration_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8');
        $this->addSql('ALTER TABLE email_integration ADD CONSTRAINT FK_EB29112C9E82DDEA FOREIGN KEY (integration_id) REFERENCES integration (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE email_integration DROP FOREIGN KEY FK_EB29112C9E82DDEA');
        $this->addSql('DROP TABLE email_integration');
    }
}
