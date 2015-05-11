<?php
interface MAdmin_IAuthProxy
{
    /**
     * return app_admin_key / name;
     * unlogin return false
     */
    public function checkLoginByGetUser();

    /**
     * return a list of item, with href and name as two field keys
     */
    public function getRightLinks();

    /**
     * logout
     */
    public function logout();

    /**
     * when admin information update
     */
    public function invalidateAdmin($app_admin_key);

    /**
     * when admin list update
     */
    public function invalidateAdminList();

    /**
     * login url for user to login
     */
    public function getLoginUrl();

    /**
     * the authorization keys for app
     */
    public function getAppAuthKeys();
}
