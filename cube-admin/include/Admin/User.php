<?php
class MAdmin_User extends MCore_Util_ArrayLike
{
    public function __construct($data)
    {
        parent::__construct($data);
    }

    public function isSystemAdmin()
    {
        return $this['is_sysadmin'];
    }
}
