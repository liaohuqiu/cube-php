<?php
interface MAdmin_IAuthProxy
{
    /**
     * return MAdmin_UserData
     */
    public function checkLoginByGetUser();

    /**
     * return a list of item, with href and name as two field keys
     */
    public function getRightLinks();

    /**
     * check if has logined
     */
    public function checkLogin();

    public function logout();
}
