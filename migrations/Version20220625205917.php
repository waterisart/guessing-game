<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220625205917 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create player to round (many-to-many) mapping';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            'CREATE TABLE players_rounds 
            (
                player_id INTEGER NOT NULL,
                round_id INTEGER NOT NULL,
                CONSTRAINT player_id_round_id_pk PRIMARY KEY (player_id, round_id)
            )'
        );
        $this->addSql('ALTER TABLE players_rounds ADD FOREIGN KEY (player_id) REFERENCES Player(id)');
        $this->addSql('ALTER TABLE players_rounds ADD FOREIGN KEY (round_id) REFERENCES Round(id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql(
            'DROP TABLE players_rounds'
        );
    }
}
