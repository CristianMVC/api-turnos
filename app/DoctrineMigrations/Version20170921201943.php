<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170921201943 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql('
        -- 
        -- Base stored procedures
        -- 
        
        DROP PROCEDURE IF EXISTS generate_series_date_day_base;        
        CREATE PROCEDURE generate_series_date_day_base (IN n_first DATETIME, IN n_last DATETIME, IN n_increment CHAR(40))
        BEGIN
            -- Create tmp table
            DROP TEMPORARY TABLE IF EXISTS series_tmp;
            CREATE TEMPORARY TABLE series_tmp (
                series DATETIME
            ) engine = memory;
            
            WHILE n_first  <= n_last DO
                -- Insert in tmp table
                INSERT INTO series_tmp (series) VALUES (n_first);
        
                -- Increment value by one
                SELECT DATE_ADD(n_first, INTERVAL +n_increment day) INTO n_first;
            END WHILE;
        END  ;
        
        -- Generate Series Date Day
        
        DROP PROCEDURE IF EXISTS generate_series_date_day;        
        CREATE PROCEDURE generate_series_date_day (IN n_first DATETIME, IN n_last DATETIME, IN n_increment CHAR(40))
        BEGIN
            -- Call base stored procedure
            CALL generate_series_date_day_base(n_first, n_last, n_increment);
            
            -- Output
            SELECT * FROM series_tmp;
        END ;');

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql('DROP PROCEDURE IF EXISTS generate_series_date_day;');
        $this->addSql('DROP PROCEDURE IF EXISTS generate_series_date_day_base;');
    }
}
