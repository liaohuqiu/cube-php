<?php
abstract class MApps_BaseApiApp extends MCore_Web_BaseApiApp
{
    private $user_info;

    protected function cUserInfo()
    {
        return $this->user_info;
    }

    protected function cuid()
    {
        return $this->user_info['uid'];
    }

    protected function checkAuth()
    {
        $token = $this->getRequest()->getData('token');
        $user_info = MModel_UserModel::tryGetUser($token);
        if (!$user_info)
        {
            $this->setError('auth fail');
            $this->output();
            exit;
        }
        $this->user_info = $user_info;
    }
}
