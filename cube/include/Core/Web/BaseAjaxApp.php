<?php
/**
 *  core/Request.js
 *
 * @author      huqiu
 */
abstract class MCore_Web_BaseAjaxApp extends MCore_Web_BaseApp
{
    private $ajaxTool;

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

    /**
     * Stop the process in main() function and use this fucntion to set an error message
     */
    protected function setError($msg)
    {
        $this->ajaxTool->setError($msg);
    }

    /**
     * Access to Ajax tool
     */
    protected function getAjaxTool()
    {
        return $this->ajaxTool;
    }

    protected function go2($url)
    {
        $this->ajaxTool->go2($url);
    }

    public function popDialog($type, $msg, $autoClose = 0, $blockHandler = false)
    {
        if ($type != 'succ')
        {
            $type = 'error';
        }
        $info = array();
        $info['type'] = $type;
        $info['title'] = $title;
        $info['msg'] = $msg;
        $info['auto_close'] = $autoClose;
        $info['block_handler'] = $blockHandler;

        $this->ajaxTool->setData('pop_dialog', $info);
    }

    protected function output()
    {
        $this->ajaxTool->output();
    }

    protected function processException($ex)
    {
        $this->ajaxTool->processException($ex);
    }
}
