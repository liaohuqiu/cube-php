<?php
/**
 * Basic Dialog for Admin
 *
 * @author huqiu
 */
abstract class MApps_AdminDialogBase extends MCore_Web_BaseDialogApp
{
    private $renderView;

    protected function init()
    {
        if (MCore_Tool_Conf::getDataConfigByEnv('mix', 'disable_admin', false))
        {
            header('Status: 403 Forbidden');
            echo '<h1>403 Forbidden</h1>';
            exit;
        }
        parent::init();
        $this->renderView = MApps_AdminPageBase::createDisplayView();
    }

    protected function checkAuth()
    {
        $user = MAdmin_UserAuth::checkLoginByGetUser();
        if (!$user)
        {
            $this->onNoAuth();
            return;
        }
        $this->user = $user;
        $this->moduleMan = new MAdmin_Module($this->request->getPath(), $user);
        if (!$this->moduleMan->userHasAuth())
        {
            $this->onNoAuth();
        }
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
