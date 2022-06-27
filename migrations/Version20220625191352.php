<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220625191352 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create round table ';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
            CREATE TABLE round 
            (
                id serial NOT NULL,
                number_to_guess INTEGER NOT NULL,
                winner VARCHAR(255),
                is_finished boolean NOT NULL DEFAULT false,
                CONSTRAINT round_pkey PRIMARY KEY (id)
            )        
        ');

    }

    public function down(Schema $schema): void
    {
        $this->addSql('
            DROP TABLE round
        ');   
    }
}
