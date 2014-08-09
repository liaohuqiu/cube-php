<?php
class MApps_Admin_User_ChangePwdDialog extends MApps_AdminDialogBase
{
    protected function main()
    {
        $this->getDialogView()->setSize(500, 400)->setTitle('Change your password')->setHandler('admin/ChangePwd');
        $this->getDialogView()->setProperty('closeWhenESC', false);
        $this->renderBody('admin/user-change-pwd-dialog.html');
    }
}
