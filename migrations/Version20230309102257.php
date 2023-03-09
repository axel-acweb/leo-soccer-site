<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230309102257 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE book ADD goal_for INT DEFAULT NULL, ADD score_against INT DEFAULT NULL');
        $this->addSql('ALTER TABLE group_stage_match ADD team_1_score INT DEFAULT NULL, ADD team_2_score INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE book DROP goal_for, DROP score_against');
        $this->addSql('ALTER TABLE group_stage_match DROP team_1_score, DROP team_2_score');
    }
}
