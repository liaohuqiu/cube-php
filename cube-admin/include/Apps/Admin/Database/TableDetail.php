<?php
/**
 *
 *
 * @author  huqiu
 */
class MApps_Admin_Database_TableDetail extends MApps_AdminPageBase
{
    protected function main()
    {
        $kind = MCore_Tool_Input::clean("r", "kind", 'str');

        $iterator = new MEngine_MysqlIterator($kind);
        $sql = "show create table ". $kind;
        $ret = $iterator->queryOne($sql)->first();
        $sqlText = $ret['Create Table'];
        $list = array();

        $view = $this->getView();
        $view->setData('sql', $sqlText);
        $view->setData('kind', $kind);
        $view->setData('list', $list);
    }

    protected function outputBody()
    {
        $this->getView()->display('admin/database/table_detail.html');
    }
}
