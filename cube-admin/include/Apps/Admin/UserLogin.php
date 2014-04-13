<?php
class MApps_Admin_UserLogin extends MApps_AdminPageBase
{
    protected function checkAuth()
    {
    }

    protected function main()
    {
        $user = MAdmin_UserAuth::getUser();
        if ($user)
        {
            $this->go2('/admin');
        }

        $email = $this->getRequest()->getData('email', 'r', 'str');
        $pwd = $this->getRequest()->getData('pwd', 'r', 'str');
        $uid = MAdmin_UserRaw::checkUserThenGetUid($email, $pwd);
        if ($uid)
        {
            ADD_DEBUG_LOG('setlogin');
            MAdmin_UserAuth::setLogin($uid, $salt, false);
            $this->go2('/admin');
        }
    }

    protected function outputBody()
    {
        $this->getView()->display('admin/user-login.html');
    }
}
