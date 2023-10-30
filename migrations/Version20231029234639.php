<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231029234639 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D16C755722');
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D18DE820D9');
        $this->addSql('DROP INDEX IDX_723705D16C755722 ON transaction');
        $this->addSql('DROP INDEX IDX_723705D18DE820D9 ON transaction');
        $this->addSql('ALTER TABLE transaction ADD buyer_wallet_id INT NOT NULL, ADD seller_wallet_id INT NOT NULL, DROP buyer_id, DROP seller_id');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D1A74A4563 FOREIGN KEY (buyer_wallet_id) REFERENCES wallet (id)');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D19041D3C5 FOREIGN KEY (seller_wallet_id) REFERENCES wallet (id)');
        $this->addSql('CREATE INDEX IDX_723705D1A74A4563 ON transaction (buyer_wallet_id)');
        $this->addSql('CREATE INDEX IDX_723705D19041D3C5 ON transaction (seller_wallet_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D1A74A4563');
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D19041D3C5');
        $this->addSql('DROP INDEX IDX_723705D1A74A4563 ON transaction');
        $this->addSql('DROP INDEX IDX_723705D19041D3C5 ON transaction');
        $this->addSql('ALTER TABLE transaction ADD buyer_id INT NOT NULL, ADD seller_id INT NOT NULL, DROP buyer_wallet_id, DROP seller_wallet_id');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D16C755722 FOREIGN KEY (buyer_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D18DE820D9 FOREIGN KEY (seller_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_723705D16C755722 ON transaction (buyer_id)');
        $this->addSql('CREATE INDEX IDX_723705D18DE820D9 ON transaction (seller_id)');
    }
}
