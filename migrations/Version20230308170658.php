<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230308170658 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE group_stage (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE group_stage_match (id INT AUTO_INCREMENT NOT NULL, team_1_id INT NOT NULL, team_2_id INT DEFAULT NULL, INDEX IDX_DF4468002132A881 (team_1_id), INDEX IDX_DF4468003387076F (team_2_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE group_stage_match ADD CONSTRAINT FK_DF4468002132A881 FOREIGN KEY (team_1_id) REFERENCES book (id)');
        $this->addSql('ALTER TABLE group_stage_match ADD CONSTRAINT FK_DF4468003387076F FOREIGN KEY (team_2_id) REFERENCES book (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE group_stage_match DROP FOREIGN KEY FK_DF4468002132A881');
        $this->addSql('ALTER TABLE group_stage_match DROP FOREIGN KEY FK_DF4468003387076F');
        $this->addSql('DROP TABLE group_stage');
        $this->addSql('DROP TABLE group_stage_match');
    }
}
