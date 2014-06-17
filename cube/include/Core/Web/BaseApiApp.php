<?php
abstract class MCore_Web_BaseApiApp extends MCore_Web_BaseApp
{
    protected $ajaxTool;

    protected function init()
    {
        $this->ajaxTool = new MCore_Web_AjaxTool();
    }

    /**
     * set key => value data or an array
     */
    protected function setData()
    {
        $args = func_get_args();
        $this->ajaxTool->setFuncArgsData($args);
        return $this;
    }

    protected function output()
    {
        $this->ajaxTool->output();
    }

    protected function setError($msg)
    {
        $this->ajaxTool->setError($msg);
    }

    protected function processException($ex)
    {
        $this->ajaxTool->processException($ex);
    }
}
