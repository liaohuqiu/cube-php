<?php
class MApps_Index extends MApps_BasePageApp
{
    protected function main()
    {
        $this->getResTool()->addFootJs('mw/ALogin.js');
    }

    protected function outputBody()
    {
        $this->getView()->display('app/index.html');
    }
}
