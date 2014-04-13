<?php
class MEngine_MysqlIterator extends MCore_Min_TableIterator
{
    protected function getTableInfos($useSlave)
    {
        return MEngine_MysqlDeploy::queryTableInfos($this->kind, $useSlave);
    }
}
