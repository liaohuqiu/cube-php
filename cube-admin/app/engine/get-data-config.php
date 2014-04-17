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
        $reqs = array(
            'k' => 'key',
        );
        $ops = array(
            'env' => 'enviroment',
        );
        $opts = MCore_Cli_Options::create($reqs, $ops);
        $key = $opts->get('k');
        $env = $opts->get('k');
        if ($env)
        {
            $data = MCore_Tool_Conf::getDataConfigByEnv($key);
        }
        else
        {
            $data = MCore_Tool_Conf::getDataConfig($key);
        }
        echo json_encode($data);
    }
}
$app = new App();
$app->run();
