<?php
class MApis_Demo extends MApps_AppBase_BaseApiApp
{
    protected function main()
    {
        $data = array();
        $data['time'] = date('Y-m-d H:i:s');
        $this->setData($data);
    }
}
