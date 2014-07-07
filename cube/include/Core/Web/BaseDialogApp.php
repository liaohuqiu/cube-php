<?php
/**
 *
 * @author      huqiu
 */
abstract class MCore_Web_BaseDialogApp extends MCore_Web_BaseApp
{
    private $dialogView;

    protected function init()
    {
        $this->dialogView = new MCore_Web_DialogView();
    }

    protected function output()
    {
        $this->dialogView->show();
    }

    protected function getDialogView()
    {
        return $this->dialogView;
    }

    protected function processException($ex)
    {
        $this->dialogView->processException($ex);
    }
}
?>
