<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180507141524 extends AbstractMigration
{
    private function executeIfIdDoesntExists($sql) {
        $this->addSql("
            SET @dbname = DATABASE();
            SET @tablename = \"punto_tramite\";
            SET @columnname = \"id\";
            SET @preparedStatement = (SELECT IF(
              (
                SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
                WHERE
                  (table_name = @tablename)
                  AND (table_schema = @dbname)
                  AND (column_name = @columnname)
              ) > 0,
              \"SELECT 1\",
              \"$sql\"
            ));
            PREPARE alterIfNotExists FROM @preparedStatement;
            EXECUTE alterIfNotExists;
            DEALLOCATE PREPARE alterIfNotExists;
        ");
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->executeIfIdDoesntExists('ALTER TABLE punto_tramite DROP PRIMARY KEY');
        $this->executeIfIdDoesntExists('ALTER TABLE punto_tramite ADD id INT AUTO_INCREMENT NOT NULL PRIMARY KEY');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
