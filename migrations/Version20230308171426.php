<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230308171426 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE championship_match ADD championship_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE championship_match ADD CONSTRAINT FK_757F3A0494DDBCE9 FOREIGN KEY (championship_id) REFERENCES championship (id)');
        $this->addSql('CREATE INDEX IDX_757F3A0494DDBCE9 ON championship_match (championship_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE championship_match DROP FOREIGN KEY FK_757F3A0494DDBCE9');
        $this->addSql('DROP INDEX IDX_757F3A0494DDBCE9 ON championship_match');
        $this->addSql('ALTER TABLE championship_match DROP championship_id');
    }
}
