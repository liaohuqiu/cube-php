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
        catch (Exception $ex)
        {
            $this->_deleteSetting($kind);
            throw $ex;
        }
        $this->setData('kind', $kind);
    }

    private function _deleteSetting($kind)
    {
        $dataOne = MEngine_EngineDB::fromConfig();
        $dataOne->delete('sys_table_info', array('name' => $kind));
    }
}
