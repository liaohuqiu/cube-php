<?php
class MApps_Admin_User_LogoutAjax extends MApps_AdminAjaxBase
{
    protected function main()
    {
        MAdmin_UserAuth::logout();
        $this->go2('/admin/user/login');
    }
}
