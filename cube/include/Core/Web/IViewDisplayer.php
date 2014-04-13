<?php
interface MCore_Web_IViewDisplayer
{
    public function setData($key, $value);
    public function display($template);
    public function render($template);
}
