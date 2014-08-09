<?php
class MCore_Proxy_Exception extends Exception
{
    private $detail;
    public function __construct($msg, $code, $detail)
    {
        parent::__construct($msg, $code);
        $this->detail = $detail;
    }

    public function getDetail()
    {
        return $this->detail;
    }
}
