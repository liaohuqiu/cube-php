<?php
class MApps_Admin_UserLogoutAjax extends MApps_AdminAjaxBase
{
    protected function main()
    {
        MAdmin_UserAuth::logout();
        $this->go2('/admin/user-login');
    }
}
