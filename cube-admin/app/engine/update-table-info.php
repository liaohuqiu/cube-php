<?php
/**
 * update config table
 *
 * @author srain
 */
include dirname(dirname(dirname(__FILE__))) . '/cube-admin-boot.php';
class App extends MCore_Cli_ConsoleBase
{
    public function main()
    {
        MEngine_MysqlDeploy::updateDeployInfo();
    }
}
$app = new App();
$app->run();
