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
        $sqlText = $iterator->queryOne($sql);
        $tableInfos = $iterator->getTableInfos(false);
        $list = array();

        foreach ($tableInfos as $item)
        {
            $tableDBInfo = $item->getDBInfo();
            $info = array();
            $info['tableName'] = $item->getTableName();
            $info['sid'] = $item->getSid();
            $info['host'] = $tableDBInfo['h'];
            $info['connectionStr'] = $tableDBInfo->getConnectionStr();
            $list[] = $info;
        }

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
