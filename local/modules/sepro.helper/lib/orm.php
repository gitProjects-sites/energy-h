<?
namespace Sepro;

use \Bitrix\Main\Application;

class ORMFactory
{
    private static $ORMEntity = array();
    private static $DB_TABLES = array();

    final public static function compile($table_name = false)
    {
        if(!self::getTableName($table_name)) return false;

        if(empty(static::$ORMEntity[$table_name]))
        {
            $className = self::getClassName($table_name);
            $entityName = "\\Sepro\\".$className;

            if(!class_exists($entityName))
            {
                $entity = '
                namespace Sepro;
                
                class '.$className.' extends \Bitrix\Main\Entity\DataManager
                {
                    public static function getClassName()
                    {
                        return __CLASS__;
                    }
                
                    public static function getFilePath()
                    {
                        return __FILE__;
                    }
                
                    public static function getTableName()
                    {
                        return \''.$table_name.'\';
                    }
                    
                    public static function getMap()
                    {
                        return '.var_export(self::GetMap($table_name), true).';
                    }
                }';
                eval($entity);
            }

            static::$ORMEntity[$table_name] = $entityName;
        }

        return static::$ORMEntity[$table_name];
    }

    private static function getTableName($table_name)
    {
        if(empty(static::$DB_TABLES[$table_name]))
        {
            self::getTables();
        }

        return static::$DB_TABLES[$table_name] ? $table_name : false;
    }

    public static function getTables()
    {
        if(empty(static::$DB_TABLES))
        {
            $rsResult = Application::getConnection()->query('SHOW TABLES');

            while($arTable = $rsResult->fetch())
            {
                foreach($arTable as $key => $table)
                {
                    static::$DB_TABLES[$table] = true;
                }
            }
        }

        return array_keys(static::$DB_TABLES);
    }

    private static function getClassName($table_name, $prefix = 'ORM', $suffix = 'Table')
    {
        $className = explode('_', $table_name);
        $BPrefix = array_shift($className);

        if($BPrefix === 'b')
        {
            $BPrefix = $prefix;
        }

        $className = array_map('ucfirst', $className);
        $className = $BPrefix.implode('', $className).$suffix;

        return $className;
    }

    private static function GetMap($table_name)
    {
        // TODO: SET PARENT Expression
        // TODO: SET VALIDATION Fields
        // TODO: SET Extended fields

        $arMap = array();
        $arIndexes = array();

        $connection = Application::getConnection();

        $rsIndexes = $connection->query(
            sprintf(
                'SHOW INDEXES FROM %s',
                $connection->getSqlHelper()->forSql($table_name)
            )
        );

        while($arIndex = $rsIndexes->fetch())
        {
            if (intval($arIndex["Non_unique"])) continue;

            $arIndexes[$arIndex["Column_name"]] = $arIndex;
        }

        $rsResult = $connection->query(
            sprintf(
                'DESCRIBE %s',
                $connection->getSqlHelper()->forSql($table_name)
            )
        );

        while($arColumn = $rsResult->fetch())
        {
            $arField = array();

            if($arColumn['Key'] === 'PRI' || !empty($arIndexes[$arColumn['Field']]))
            {
                $arField['primary'] = true;
            }

            if(!empty($arColumn["Default"]))
            {
                $arField['default_value'] = $arField["Default"];
            }

            if($arColumn["Null"] === "NO" && !$arField['primary'])
            {
                $arField['required'] = true;
            }

            if($arColumn['Extra'] === 'auto_increment')
            {
                $arField['autocomplete'] = true;
            }

            switch(true)
            {
                case preg_match("/^(varchar|char|varbinary)/", $arColumn["Type"]):

                    $arField["data_type"] = "string";

                    break;

                case preg_match("/^(text|longtext|mediumtext|longblob)/", $arColumn["Type"]):

                    $arField["data_type"] = "text";

                    break;

                case preg_match("/^(datetime|timestamp)/", $arColumn["Type"]):

                    $arField["data_type"] = "datetime";

                    break;

                case preg_match("/^(date)/", $arColumn["Type"]):

                    $arField["data_type"] = "date";

                    break;

                case preg_match("/^(int|smallint|bigint|tinyint)/", $arColumn["Type"]):

                    $arField["data_type"] = "integer";

                    break;

                case preg_match("/^(float|double|decimal)/", $arColumn["Type"]):

                    $arField["data_type"] = "float";

                    break;

                default:

                    $arField["data_type"] = "UNKNOWN";
            }

            if($arField['data_type'] === 'string')
            {
                preg_match("/\((\d+)\)$/", $arColumn["Type"], $match);

                if($match[1] == 1 && in_array($arColumn["Default"], array('N', 'Y')))
                {
                    $arField["data_type"] = "boolean";
                    $arField["values"] = array('N', 'Y');
                }
            }

            $arMap[$arColumn['Field']] = $arField;

        }

        return $arMap;
    }
}