<?php
class MApps_Admin_User_ChangePwdAjax extends MApps_AdminAjaxBase
{
    protected function main()
    {
        $uid = $this->user['uid'];
        $old_pwd = $this->request->getData('old_pwd', 'r', 'str');
        $new_pwd1 = $this->request->getData('new_pwd1', 'r', 'str');
        $new_pwd2 = $this->request->getData('new_pwd2', 'r', 'str');

        $user_info = MAdmin_UserRaw::checkPwdById($uid, $old_pwd);
        if (!$user_info)
        {
            // $this->setError('STOP: The old password is wrong.');
            // return;
        }

        if (!$new_pwd2 || !$new_pwd1 || $new_pwd1 != $new_pwd2)
        {
            $this->setError('STOP: The new password is illegal.');
            return;
        }

        MAdmin_UserRaw::updatePwd($uid, $new_pwd1);
        MAdmin_UserAuth::logout();
        $this->setData('msg', 'Your password has been updated, You need to relogin');
        $this->setData('redirect', '/admin/user/login');
    }
}
