<?php
/**
 * Basic Page for Admin
 *
 * @author huqiu
 */
abstract class MApps_AdminAjaxBase extends MCore_Web_BaseAjaxApp
{
    protected function init()
    {
        if (MCore_Tool_Conf::getDataConfigByEnv('mix', 'disable_admin', false))
        {
            header('Status: 403 Forbidden');
            echo '<h1>403 Forbidden</h1>';
            exit;
        }
        parent::init();
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

    private function onNoAuth()
    {
        throw new Exception('Unauthorized');
    }
}
