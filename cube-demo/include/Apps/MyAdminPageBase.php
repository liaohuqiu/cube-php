<?php
abstract class MApps_MyAdminPageBase extends MApps_AdminPageBase
{
    protected function createView()
    {
        $view = parent::createView();
        $view->addDir(ROOT_DIR . '/template');
        return $view;
    }
}
