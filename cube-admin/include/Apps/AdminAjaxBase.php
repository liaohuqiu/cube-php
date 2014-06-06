<?php
/**
 * Basic Page for Admin
 *
 * @author huqiu
 */
abstract class MApps_AdminAjaxBase extends MCore_Web_BaseAjaxApp
{
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
