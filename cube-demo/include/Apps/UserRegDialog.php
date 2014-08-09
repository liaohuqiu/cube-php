<?php
class MApps_UserRegDialog extends MApps_BaseDialogApp
{
    protected function main()
    {
        $this->getDialogView()->setProperty('closeWhenESC', false)->noTitle()->setWidth(410)
            ->setHandler('mw/RegDialog');
        $this->renderBody('app/user-reg-dialog.html');
    }
}
