<?php
/**
 * Basic Dialog
 *
 * @author huqiu
 */
abstract class MApps_BaseDialogApp extends MCore_Web_BaseDialogApp
{
    private $renderView;

    protected function init()
    {
        parent::init();
        $this->renderView = MApps_BasePageApp::createDisplayView();
    }

    protected function checkAuth()
    {
        // level checkAuth empty first
    }

    protected function renderBody($template, $data = array())
    {
        $this->renderView->setPageData($data);
        $body = $this->renderView->render($template);
        $this->getDialogView()->setBody($body);
    }

    private function onNoAuth()
    {
        throw new Exception('Unauthorized');
    }
}
