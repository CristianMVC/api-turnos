<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20190528185326 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */  
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE turno ADD user INT DEFAULT NULL, ADD origen SMALLINT DEFAULT NULL');
        $this->addSql('ALTER TABLE turno ADD CONSTRAINT FK_E79767628D93D649 FOREIGN KEY (user) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_E79767628D93D649 ON turno (user)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('ALTER TABLE turno DROP FOREIGN KEY FK_E79767628D93D649');
        $this->addSql('DROP INDEX IDX_E79767628D93D649 ON turno');
        $this->addSql('ALTER TABLE turno DROP user, DROP origen');
    }
}
