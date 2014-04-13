<?php
/**
 *
 *
 * @author  huqiu
 */
class MApps_Admin_Database_TableDetail extends MApps_AdminPageBase
{
    protected $kind;

    private function getCreateTableInfo()
    {
        $iterator = new MCore_Min_TableIterator($this->kind);
        $sql = "show create table ". $this->kind;
        $ret = $iterator->query($sql, null, false, false);
        $ret = reset($ret);
        $firstItem = array_values($ret['data'][0]);
        $sqlText = $firstItem[1];

        $view = $this->getView();
        $view->setData('sql', $sqlText);
    }

    protected function main()
    {
        $this->kind = MCore_Tool_Input::clean("r", "kind", 'str');

        $this->getCreateTableInfo();

        $tableInfos = MEngine_MysqlDeploy::queryTableInfos($this->kind, false);
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
        $view->setData('kind', $this->kind);
        $view->setData('list', $list);
    }

    protected function outputBody()
    {
        $this->getView()->display('admin/database/table_detail.html');
    }
}
