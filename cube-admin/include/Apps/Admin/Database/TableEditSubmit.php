<?php
class MApps_Admin_Database_TableEditSubmit extends MApps_AdminPageBase
{
    protected function main()
    {
        $action = MCore_Tool_Input::clean('r', 'action', 'str');
        $kind = MCore_Tool_Input::clean('r', 'kind', 'str');
        $editSql = MCore_Tool_Input::clean('r', 'sql', 'str');

        if ($action == 'edit' && $editSql)
        {
            $iterator = new MEngine_MysqlIterator($kind);
            $sql_list = explode(";", $editSql);
            foreach ($sql_list as $sql)
            {
                $sql = trim($sql);
                if (!empty($sql))
                {
                    $ret = $iterator->query($sql, null, false, false);
                }
            }
        }
        $this->go2('table-edit', array('kind' => $kind));
        exit;
    }
}
