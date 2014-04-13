<?php
/**
 *   数据库表信息
 *
 * @author      huqiu
 */
class MCore_Min_TableInfo
{
    protected $_kind;
    protected $_index;
    protected $_tableName;
    protected $_sid;
    protected $_dbInfo;

    function __construct($kind, $index, $tableName, $sid, $dbInfo)
    {
        $kind = trim($kind);
        if (empty($kind))
        {
            throw new MCore_Min_DBException('Table kind can not be empty.');
        }
        $this->_kind = $kind;
        $this->_index = $index;
        $this->_tableName = $tableName;
        $this->_sid = $sid;
        $this->_dbInfo = $dbInfo;
    }

    public function getKind()
    {
        return $this->_kind;
    }

    public function getTableName()
    {
        return $this->_tableName;
    }

    public function getIndex()
    {
        return $this->_index;
    }

    public function getDBInfo()
    {
        return $this->_dbInfo;
    }

    public function getSid()
    {
        return $this->_sid;
    }
}
?>
