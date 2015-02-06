<?php
include dirname(dirname(dirname(__FILE__))) . '/app-boot.php';
class App extends MCore_Cli_ConsoleBase
{
    protected function main()
    {
        $app_uid = 200;
        foreach (range(1, 100) as $i)
        {
            $ret = MAdmin_UserRaw::getInfoByAppUid($app_uid);
        }
        p($ret);
    }
}
$app = new App();
$app->run();
