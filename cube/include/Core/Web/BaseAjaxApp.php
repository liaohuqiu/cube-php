<?php
/**
 *  core/Request.js
 *
 * @author      huqiu
 */
abstract class MCore_Web_BaseAjaxApp extends MCore_Web_BaseApiApp
{
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
}
