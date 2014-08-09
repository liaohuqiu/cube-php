<?php
class MApps_Admin_Database_TableQueryAjax extends MApps_AdminAjaxBase
{
    private $_list = array();

    protected function main()
    {
        $kind = MCore_Tool_Input::clean("r", "kind", 'str');
        $sql = MCore_Tool_Input::clean("r", "sql", 'str');
        $sql = strtr($sql, array("table_kind" => $kind));
        $data = array();

        $msg = "";
        try
        {
            $iterator = new MEngine_MysqlIterator($kind);
            $iterator->query($sql, array($this, 'iteratorCallback'), true, true);

            if (strpos($sql, "count") !== false)
            {
                $countSubTotal = array();
                foreach ($this->_list as $kind => $dataList)
                {
                    foreach ($dataList as $item);
                    {
                        foreach ($item as $key => $value)
                        {
                            $countSubTotal[$key] += $value;
                        }
                    }
                }
                $msg .= var_export($countSubTotal, true) . "\n\n";
            }
            $msg .= var_export($this->_list, true);
        }
        catch(Exception $ex)
        {
            $msg = $ex->getMessage() . "\n\n" . $ex->getTraceAsString();
        }

        $data['msg']  = "<pre>" . $msg;
        $this->setData($data);
    }

    public function iteratorCallback($dbResult, $tableName)
    {
        foreach ($dbResult as $item)
        {
            $this->_list[$tableName][] = $item;
        }
    }
}
