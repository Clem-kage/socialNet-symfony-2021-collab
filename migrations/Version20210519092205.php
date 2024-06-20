<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210519092205 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE notif (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, origin_id INT NOT NULL, postorigin_id INT DEFAULT NULL, text_content LONGTEXT NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_C0730D6BA76ED395 (user_id), INDEX IDX_C0730D6B56A273CC (origin_id), INDEX IDX_C0730D6BC78FBF0D (postorigin_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE notif ADD CONSTRAINT FK_C0730D6BA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE notif ADD CONSTRAINT FK_C0730D6B56A273CC FOREIGN KEY (origin_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE notif ADD CONSTRAINT FK_C0730D6BC78FBF0D FOREIGN KEY (postorigin_id) REFERENCES post (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE notif');
    }
}
