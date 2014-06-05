<?php
class MApps_Admin_Database_TableEdit extends MApps_AdminPageBase
{
    protected function main()
    {
        $kind = MCore_Tool_Input::clean("r", "kind", 'str');
        if ($kind)
        {
            $iterator = new MEngine_MysqlIterator($kind);
            $sql = "show create table ". $kind;
            $ret = $iterator->query($sql, null, false, false);
            $ret = reset($ret);
            $firstItem = array_values($ret['data'][0]);
            $sqlText = trim($firstItem[1]);

            $view = $this->getView();
            $view->setPageData('kind', $kind);
            $view->setPageData('msg', $sqlText);
        }
        $this->getResTool()->addFootJs('admin/ATableEdit');
    }

    protected function outputBody()
    {
        $this->getView()->display('admin/database/table_edit.html');
    }
}
