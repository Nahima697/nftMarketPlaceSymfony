<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231202225328 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE transaction ADD buyer_gallery_id INT NOT NULL, ADD seller_gallery_id INT NOT NULL');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D1F43E53AE FOREIGN KEY (buyer_gallery_id) REFERENCES gallery (id)');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D1CBBC5EE5 FOREIGN KEY (seller_gallery_id) REFERENCES gallery (id)');
        $this->addSql('CREATE INDEX IDX_723705D1F43E53AE ON transaction (buyer_gallery_id)');
        $this->addSql('CREATE INDEX IDX_723705D1CBBC5EE5 ON transaction (seller_gallery_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D1F43E53AE');
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D1CBBC5EE5');
        $this->addSql('DROP INDEX IDX_723705D1F43E53AE ON transaction');
        $this->addSql('DROP INDEX IDX_723705D1CBBC5EE5 ON transaction');
        $this->addSql('ALTER TABLE transaction DROP buyer_gallery_id, DROP seller_gallery_id');
    }
}
