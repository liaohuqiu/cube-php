<?php
include dirname(dirname(dirname(__FILE__))) . '/init.php';
class App extends MAdmin_AdminPageBase
{
    private $controller;

    protected function main()
    {
        $this->controller = new MAdmin_Views_DataTableEditController(array(), MCore_Dao_DB::create());
        $this->controller->render();
    }

    protected function outputBody()
    {
        $this->controller->output();
    }
}
$app = new App();
$app->run();
