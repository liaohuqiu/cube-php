<?php
class MApps_ItemDetail extends MApps_AppBase_BasePageApp
{
    protected function main()
    {
    }

    protected function outputBody()
    {
        $this->getView()->setPageData('args', var_export($this->getRequest()->getArgs(), true));
        $this->getView()->display('item-detail.html');
    }
}
