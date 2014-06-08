<?php
class MApps_Sample_AjaxUpload extends MApps_AdminPageBase
{
    protected function main()
    {
        $this->getResTool()->addFootJs('admin/AAjaxUpload.js');
    }

    protected function outputBody()
    {
        $this->getView()->display('sample/ajax-upload.html');
    }
}
