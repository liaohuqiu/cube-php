<?php
class MApps_Admin_Common_TableItemEdit extends MApps_AdminPageBase
{
    private $controller;

    protected function main()
    {
        $this->controller = new MAdmin_Views_DataTableEditController(array(), MCore_Dao_DB::create());
        $this->controller->dispatch();
    }

    protected function outputBody()
    {
        $this->controller->output();
    }
}
