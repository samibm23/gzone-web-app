<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220414021624 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comments RENAME INDEX fk_5f9e962a4b89032c TO post_id');
        $this->addSql('ALTER TABLE join_requests DROP FOREIGN KEY join_requests_ibfk_2');
        $this->addSql('ALTER TABLE join_requests ADD CONSTRAINT FK_36E51262296CD8AE FOREIGN KEY (team_id) REFERENCES teams (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE market_items DROP FOREIGN KEY market_items_ibfk_1');
        $this->addSql('ALTER TABLE market_items ADD CONSTRAINT FK_2A305638B092A811 FOREIGN KEY (store_id) REFERENCES stores (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comments RENAME INDEX post_id TO FK_5F9E962A4B89032C');
        $this->addSql('ALTER TABLE join_requests DROP FOREIGN KEY FK_36E51262296CD8AE');
        $this->addSql('ALTER TABLE join_requests ADD CONSTRAINT join_requests_ibfk_2 FOREIGN KEY (team_id) REFERENCES teams (id)');
        $this->addSql('ALTER TABLE market_items DROP FOREIGN KEY FK_2A305638B092A811');
        $this->addSql('ALTER TABLE market_items ADD CONSTRAINT market_items_ibfk_1 FOREIGN KEY (store_id) REFERENCES stores (id)');
    }
}
