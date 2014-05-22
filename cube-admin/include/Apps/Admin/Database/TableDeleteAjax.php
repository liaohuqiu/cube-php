<?php
class MApps_Admin_Database_TableDeleteAjax extends MApps_AdminAjaxBase
{
    protected function main()
    {
        $kind = MCore_Tool_Input::clean("r", "kind", 'str');
        try
        {
            $sql = "drop table $kind";
            $iterator = new MEngine_MysqlIterator($kind);
            $iterator->query($sql, null, false, false);
            $this->_deleteSetting($kind);
        }
        catch(Exception $ex)
        {
            $this->_deleteSetting($kind);
            throw $ex;
        }
        $this->setData('kind', $kind);
        MEngine_MysqlDeploy::updateDeployInfo();
    }

    private function _deleteSetting($kind)
    {
        $dataOne = MEngine_EngineDB::create();
        $dataOne->delete('kind_setting', array('kind' => $kind));
        $dataOne->delete('table_setting', array('kind' => $kind));
    }
}