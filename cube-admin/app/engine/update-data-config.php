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
            'd' => 'data',
        );
        $opts = MCore_Cli_Options::create($reqs);
        $key = $opts->get('k');
        $data = json_decode($opts->get('d'), true);
        MCore_Tool_Conf::writeDataConfig($key, $data);
        $this->printInfo('update data config succcess: ' . $key);
    }
}
$app = new App();
$app->run();
