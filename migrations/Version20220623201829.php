<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220623201829 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create player table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
            CREATE TABLE player
            (
                id serial NOT NULL,
                username VARCHAR(255) NOT NULL,
                roles json NOT NULL,
                password VARCHAR(255) NOT NULL,
                CONSTRAINT player_pkey PRIMARY KEY (id)
            )
        ');

    }

    public function down(Schema $schema): void
    {
        $this->addSql('
            DROP TABLE player
        ');
    }
}
