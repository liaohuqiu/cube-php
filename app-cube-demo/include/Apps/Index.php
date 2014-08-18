<?php
class MApps_Index extends MApps_AppBase_BasePageApp
{
    protected function main()
    {
        $this->getResTool()->addFootJs('cube-demo/AIndex.js');
    }

    protected function outputBody()
    {
        $this->getView()->display('index.html');
    }
}
