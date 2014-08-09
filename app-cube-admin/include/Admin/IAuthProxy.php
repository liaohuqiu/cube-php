<?php
interface MAdmin_IAuthProxy
{
    /**
     * return MAdmin_UserData / false
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
}
