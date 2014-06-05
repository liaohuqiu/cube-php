<?php
class MEngine_MysqlIterator extends MCore_Min_TableIterator
{
    private $db;
    public function __construct($kind, $db = null)
    {
        parent::__construct($kind);

        if (!$db)
        {
            $db = MEngine_EngineDB::fromConfig();
        }
        $this->db = $db;
    }
    protected function getTableInfos($useSlave)
    {
        $deployData = MEngine_MysqlDeploy::getDeployData($this->db);
        return MCore_Min_TableConfig::getTableInfos($deployData, $this->kind, $useSlave);
    }
}
