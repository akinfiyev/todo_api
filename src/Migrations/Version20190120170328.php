<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190120170328 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE item_list_label (item_list_id INT NOT NULL, label_id INT NOT NULL, INDEX IDX_6A85ECE136F330DF (item_list_id), INDEX IDX_6A85ECE133B92F39 (label_id), PRIMARY KEY(item_list_id, label_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE label (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE attachment (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE item_list_label ADD CONSTRAINT FK_6A85ECE136F330DF FOREIGN KEY (item_list_id) REFERENCES item_list (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE item_list_label ADD CONSTRAINT FK_6A85ECE133B92F39 FOREIGN KEY (label_id) REFERENCES label (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE item ADD attachment_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE item ADD CONSTRAINT FK_1F1B251E464E68B FOREIGN KEY (attachment_id) REFERENCES attachment (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1F1B251E464E68B ON item (attachment_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE item_list_label DROP FOREIGN KEY FK_6A85ECE133B92F39');
        $this->addSql('ALTER TABLE item DROP FOREIGN KEY FK_1F1B251E464E68B');
        $this->addSql('DROP TABLE item_list_label');
        $this->addSql('DROP TABLE label');
        $this->addSql('DROP TABLE attachment');
        $this->addSql('DROP INDEX UNIQ_1F1B251E464E68B ON item');
        $this->addSql('ALTER TABLE item DROP attachment_id');
    }
}
