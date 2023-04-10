<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230410071706 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE place ADD CONSTRAINT FK_741D53CD12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_741D53CD12469DE2 ON place (category_id)');
        $this->addSql('ALTER TABLE place_user ADD CONSTRAINT FK_4726A6A5DA6A219 FOREIGN KEY (place_id) REFERENCES place (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE place_user ADD CONSTRAINT FK_4726A6A5A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_4726A6A5DA6A219 ON place_user (place_id)');
        $this->addSql('CREATE INDEX IDX_4726A6A5A76ED395 ON place_user (user_id)');
        $this->addSql('CREATE INDEX IDX_place_title ON place (title)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE place DROP CONSTRAINT FK_741D53CD12469DE2');
        $this->addSql('DROP INDEX IDX_741D53CD12469DE2');
        $this->addSql('ALTER TABLE place_user DROP CONSTRAINT FK_4726A6A5DA6A219');
        $this->addSql('ALTER TABLE place_user DROP CONSTRAINT FK_4726A6A5A76ED395');
        $this->addSql('DROP INDEX IDX_4726A6A5DA6A219');
        $this->addSql('DROP INDEX IDX_4726A6A5A76ED395');
        $this->addSql('DROP INDEX IDX_place_title');
    }
}
