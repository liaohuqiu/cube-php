<?php
class MApps_Admin_User_Logout extends MApps_AdminPageBase
{
    protected function main()
    {
        MAdmin_UserAuth::logout();
        $this->go2('/admin/user/login');
    }
}
