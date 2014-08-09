<?php
class MEngine_EngineDBMan extends MCore_Min_DBMan
{
    private $_connection;

    public function __construct($connection)
    {
        $this->_connection = $connection;
    }

    protected function _getConnection($kind, $hintId, $cacheTime)
    {
        return $this->_connection;
    }

    protected function _translateSql($kind, $hintId, $sql)
    {
        return $sql;
    }

    public function checkInputAndGetSplitId($kind, $input)
    {
        return 0;
    }

    public function getTableNum($kind)
    {
        return 1;
    }
}
