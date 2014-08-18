<?php
/**
 * Basic Ajax Page
 *
 * @author huqiu
 */
abstract class MApps_AppBase_BaseAjaxApp extends MCore_Web_BaseAjaxApp
{
    protected function checkAuth()
    {
        // TODO
        // check auth has not been complemented.
    }

    private function onNoAuth()
    {
        throw new Exception('Unauthorized');
    }
}
