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

        $deployData = MEngine_MysqlDeploy::getDeployData($this->db);
        MCore_Min_TableConfig::setDeployData($deployData);
    }
}
