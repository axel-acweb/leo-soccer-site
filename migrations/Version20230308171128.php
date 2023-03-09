<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230308171128 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE championship (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE championship_match (id INT AUTO_INCREMENT NOT NULL, team_1_id INT NOT NULL, team_2_id INT NOT NULL, INDEX IDX_757F3A042132A881 (team_1_id), INDEX IDX_757F3A043387076F (team_2_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE groupe_stage_match (id INT AUTO_INCREMENT NOT NULL, group_stage_id INT NOT NULL, INDEX IDX_7521040F2BA89DEC (group_stage_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE championship_match ADD CONSTRAINT FK_757F3A042132A881 FOREIGN KEY (team_1_id) REFERENCES book (id)');
        $this->addSql('ALTER TABLE championship_match ADD CONSTRAINT FK_757F3A043387076F FOREIGN KEY (team_2_id) REFERENCES book (id)');
        $this->addSql('ALTER TABLE groupe_stage_match ADD CONSTRAINT FK_7521040F2BA89DEC FOREIGN KEY (group_stage_id) REFERENCES group_stage (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE championship_match DROP FOREIGN KEY FK_757F3A042132A881');
        $this->addSql('ALTER TABLE championship_match DROP FOREIGN KEY FK_757F3A043387076F');
        $this->addSql('ALTER TABLE groupe_stage_match DROP FOREIGN KEY FK_7521040F2BA89DEC');
        $this->addSql('DROP TABLE championship');
        $this->addSql('DROP TABLE championship_match');
        $this->addSql('DROP TABLE groupe_stage_match');
    }
}
