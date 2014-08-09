<?php
/**
 *   遍历数据库表
 *
 * @author      huqiu
 */
class MCore_Min_TableIterator
{
    protected $kind;

    function __construct($kind)
    {
        $kind = trim($kind);
        if (empty($kind))
        {
            throw new MCore_Min_DBException('Table kind can not be empty.');
        }
        $this->kind = $kind;
    }

    /**
     * 对每个分表执行sql
     */
    public function query($sql, $callback = null, $salve = true)
    {
        $tableInfos = MCore_Min_TableConfig::getTableInfos($this->kind, $salve);

        $list = array();
        foreach ($tableInfos as $tableInfo)
        {
            $table_name = $tableInfo['table_name'];
            $db_info = $tableInfo['db_info'];
            $connection = MCore_Min_DBConection::get($db_info);
            $sqlText = str_replace($this->kind, $table_name, $sql);
            $ret = $connection->query($sqlText);

            if ($callback)
            {
                call_user_func($callback, $ret, $table_name);
            }
            else
            {
                $list[$table_name] = $ret;
            }
        }
        return $list;
    }

    public function queryOne($sql, $salve = true)
    {
        $tableInfos = MCore_Min_TableConfig::getTableInfos($this->kind, $salve);

        foreach ($tableInfos as $tableInfo)
        {
            $db_info = $tableInfo['db_info'];
            $table_name = $tableInfo['table_name'];
            $connection = MCore_Min_DBConection::get($db_info);
            $sqlText = str_replace($this->kind, $table_name, $sql);
            $ret = $connection->query($sqlText);
            return $ret;
        }
    }
}
