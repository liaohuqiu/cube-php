<?php
class MApps_Admin_Database_TableEditAjax extends MApps_AdminAjaxBase
{
    protected function main()
    {
        $kind = $this->request->getData('kind');
        $cmd = $this->request->getData('cmd');
        $editSql = $this->request->getData('sql');

        $iterator = new MEngine_MysqlIterator($kind);
        if ($cmd == 'alter')
        {
            $sql_list = explode(";", $editSql);
            foreach ($sql_list as $sql)
            {
                $sql = trim($sql);
                if (!empty($sql))
                {
                    $ret = $iterator->query($sql, null, false, false);
                }
            }
            $this->popDialog('succ', 'Table has been updated', 2000);
        }

        $sql = "show create table ". $kind;
        $ret = $iterator->query($sql, null, false, false);
        $ret = reset($ret);
        $firstItem = array_values($ret['data'][0]);
        $sqlText = trim($firstItem[1]);
        $this->setData('msg', $sqlText);
    }
}
