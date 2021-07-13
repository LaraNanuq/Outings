<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210713232806 extends AbstractMigration {

    public function getDescription(): string {
        return 'Fill "outing_state" table';
    }

    public function up(Schema $schema): void {
        $this->addSql('INSERT INTO outing_state (label) VALUES ("DRAFT")');
        $this->addSql('INSERT INTO outing_state (label) VALUES ("OPEN")');
        $this->addSql('INSERT INTO outing_state (label) VALUES ("PENDING")');
        $this->addSql('INSERT INTO outing_state (label) VALUES ("ONGOING")');
        $this->addSql('INSERT INTO outing_state (label) VALUES ("FINISHED")');
        $this->addSql('INSERT INTO outing_state (label) VALUES ("CANCELED")');
        $this->addSql('INSERT INTO outing_state (label) VALUES ("ARCHIVED")');
    }

    public function down(Schema $schema): void {
        $this->addSql('TRUNCATE TABLE outing_state');
    }
}
