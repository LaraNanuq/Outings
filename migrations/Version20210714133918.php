<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210714133918 extends AbstractMigration {

    public function getDescription(): string {
        return 'Change "password" length in "user" table';
    }

    public function up(Schema $schema): void {
        $this->addSql('ALTER TABLE user CHANGE password password VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void {
        $this->addSql('ALTER TABLE user CHANGE password password VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
