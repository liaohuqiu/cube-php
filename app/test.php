<?php
include dirname(dirname(__FILE__)) . '/boot.php';
class App extends MCore_Cli_ConsoleBase
{
    protected function main()
    {
        $t1 = microtime(true);
        $end_point = 'pygments@tcp:127.0.0.1:2016';
        $proxy = MCore_Proxy_CubeProxy::getInstance($end_point);
        $data = array();
        $data['lang'] = 'php';
        $data['code'] = '<?php phpinfo?>';
        $ret = $proxy->request('highlight', $data);
        p($ret);
        $t2 = microtime(true);
        p($t2 - $t1);
    }
}
$app = new App();
$app->run();
