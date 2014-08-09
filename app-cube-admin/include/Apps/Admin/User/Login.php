<?php
class MApps_Admin_User_Login extends MApps_AdminPageBase
{
    protected function checkAuth()
    {
        if (!MAdmin_Init::checkInit())
        {
            $this->go2('/init');
        }
    }

    protected function main()
    {
        $user = MAdmin_UserAuth::checkLoginByGetUser();
        if ($user)
        {
            $this->go2('/admin');
        }

        $email = $this->getRequest()->getData('email', 'r', 'str');
        $pwd = $this->getRequest()->getData('pwd', 'r', 'str');
        $uid = MAdmin_UserRaw::checkUserThenGetUid($email, $pwd);
        if ($uid)
        {
            MAdmin_UserAuth::setLogin($uid, $salt, false);
            $this->go2('/admin');
        }
    }

    protected function outputBody()
    {
        $this->getView()->display('admin/user-login.html');
    }
}
