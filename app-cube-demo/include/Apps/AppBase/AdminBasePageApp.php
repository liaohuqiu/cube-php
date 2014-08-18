<?php
abstract class MApps_AppBase_AdminBasePageApp extends MApps_AdminPageBase
{
    protected function createView()
    {
        $view = parent::createView();
        $view->addDir(APP_ROOT_DIR . '/template');
        return $view;
    }
}
