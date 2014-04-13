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

    /**
     * pop up a sucess style dialog with title and message
     */
    public function setSuccDialogInfo($content, $title = '', $blockHandler, $autoClose = 0, $height = '350', $width = '')
    {
        $this->_setPopDialogInfo('succ', $content, $title, $blockHandler, $autoClose, $height, $width);
        return $this;
    }

    /**
     * pop up a fail style dialog with title and message
     */
    public function setFailDialogInfo($content, $title = '', $blockHandler, $autoClose = 0, $height = '350', $width = '')
    {
        $this->_setPopDialogInfo('fail', $content, $title, $blockHandler, $autoClose, $height, $width);
        return $this;
    }

    private function _setPopDialogInfo($type, $content, $title = '', $blockHandler, $autoClose = 0, $height = '350', $width = '')
    {
        $info = array();
        $info['type'] = $type;
        $info['title'] = $title;
        $info['content'] = $content;
        $info['height'] = $height;
        $info['width'] = $width;
        $info['autoClose'] = $autoClose;
        $info['blockHandler'] = $blockHandler;

        $this->ajaxTool->setData('popDialogInfo', $info);
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
