<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230308171304 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE groupe_stage_match DROP FOREIGN KEY FK_7521040F2BA89DEC');
        $this->addSql('DROP TABLE groupe_stage_match');
        $this->addSql('ALTER TABLE group_stage_match ADD group_stage_id INT NOT NULL');
        $this->addSql('ALTER TABLE group_stage_match ADD CONSTRAINT FK_DF4468002BA89DEC FOREIGN KEY (group_stage_id) REFERENCES group_stage (id)');
        $this->addSql('CREATE INDEX IDX_DF4468002BA89DEC ON group_stage_match (group_stage_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE groupe_stage_match (id INT AUTO_INCREMENT NOT NULL, group_stage_id INT NOT NULL, INDEX IDX_7521040F2BA89DEC (group_stage_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE groupe_stage_match ADD CONSTRAINT FK_7521040F2BA89DEC FOREIGN KEY (group_stage_id) REFERENCES group_stage (id)');
        $this->addSql('ALTER TABLE group_stage_match DROP FOREIGN KEY FK_DF4468002BA89DEC');
        $this->addSql('DROP INDEX IDX_DF4468002BA89DEC ON group_stage_match');
        $this->addSql('ALTER TABLE group_stage_match DROP group_stage_id');
    }
}
