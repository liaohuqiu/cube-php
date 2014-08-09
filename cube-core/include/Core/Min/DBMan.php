<?php
/**
 *  直连数据库的DBMan实现
 *
 * @package     core
 * @subpackage  min
 * @author      huqiu
 */
class MCore_Min_DBMan implements MCore_Proxy_IDBMan
{
    public function __construct()
    {
    }

    public function sQuery($kind, $hintId, $sql, $cacheTime = 0)
    {
        $sql = $this->_translateSql($kind, $hintId, $sql);

        $cnt = $this->_getConnection($kind, $hintId, $cacheTime);
        $result = $cnt->query($sql);
        return $result;
    }

    public function mQuery($kind, $hintId, $sqls, $cacheTime = 0)
    {
        $cnt = $this->_getConnection($kind, $hintId, $cacheTime);
        $results = array();
        foreach ($sqls as $sql)
        {
            $sql = $this->_translateSql($kind, $hintId, $sql);
            $results[] = $cnt->query($sql);
        }
        return $results;
    }

    protected function _getConnection($kind, $hintId, $cacheTime)
    {
        $slave = $cacheTime >= 0;
        $dbInfo = MCore_Min_TableConfig::getDBInfo($kind, $hintId, $slave);
        $cnt = MCore_Min_DBConection::get($dbInfo);
        return $cnt;
    }

    protected function _translateSql($kind, $hintId, $sql)
    {
        $tableNum = MCore_Min_TableConfig::getTableNum($kind);
        if ($tableNum <= 1)
        {
            return $sql;
        }
        $realKind = $kind . '_' . $hintId % $tableNum;
        return str_replace($kind, $realKind, $sql);
    }

    public function checkInputAndGetSplitId($kind, $input)
    {
        $data = MCore_Min_TableConfig::getDeployData();
        if (!isset($data['tables'][$kind]))
        {
            throw new MCore_Min_DBException('Not such a table in config: ' . $kind);
        }
        $field = $data['tables'][$kind]['id_field'];
        $table_num = $data['tables'][$kind]['table_num'];
        if ($table_num <= 1)
        {
            return 0;
        }
        else
        {
            if (!isset($input[$field]))
            {
                throw new MCore_Min_DBException('Can not find split field value in input data.');
            }
            return $input[$field];
        }
    }

    public function getTableNum($kind)
    {
        return MCore_Min_TableConfig::getTableNum($kind);
    }

    public function getSplitField($kind)
    {
        return MCore_Min_TableConfig::getIdField($kind);
    }
}
