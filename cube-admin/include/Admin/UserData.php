<?php
class MAdmin_UserData extends MCore_Util_ArrayLike
{
    public function __construct($raw)
    {
        $data = array();
        $keys = array('is_sysadmin', 'name', 'auth_keys', 'uid');
        foreach ($keys as $key)
        {
            if (!isset($raw[$key]))
            {
                throw new Exception('key is not found: ' . $key);
            }
            $data[$key] = $raw[$key];
        }
        parent::__construct($data);
    }

    public function getData()
    {
        return $this->data;
    }

    public function isSystemAdmin()
    {
        return $this['is_sysadmin'];
    }
}
