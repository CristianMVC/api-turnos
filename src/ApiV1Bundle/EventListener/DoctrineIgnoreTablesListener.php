<?php
namespace ApiV1Bundle\EventListener;

use Doctrine\ORM\Tools\Event\GenerateSchemaEventArgs;

class DoctrineIgnoreTablesListener
{
    private $ignoredTables = [
        'view_punto_atencion_fecha_disponible',
        'view_punto_atencion_horarios_disponible',
        'view_punto_atencion_fecha_horarios'
    ];

    public function postGenerateSchema(GenerateSchemaEventArgs $args)
    {
        $schema = $args->getSchema();
        $database = $schema->getName();
        $tablesNames = $schema->getTableNames();
        foreach ($tablesNames as $tableName) {
            $tableName = str_replace($database . '.', '', $tableName);
            if (in_array($tableName, $this->ignoredTables)) {
                $schema->dropTable($tableName);
            }
        }
    }
}
