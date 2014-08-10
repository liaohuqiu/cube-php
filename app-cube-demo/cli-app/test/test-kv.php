<?php
include dirname(dirname(dirname(__FILE__))) . '/app-boot.php';
class App extends MCore_Cli_ConsoleBase
{
    protected function main()
    {
        $key = 'key1';
        $data = array(date('Y-m-d H:i:s'));

        $toolKv = new MCore_Tool_KV('s_demo_kv');
        $ret = $toolKv->set($key, $data);
        p($ret);

        $ret = $toolKv->getRaw($key);
        p($ret);

        $ret = $toolKv->get($key);
        p($ret);

        $ret = $toolKv->delete($key);
        p($ret);

        $ret = $toolKv->getRaw($key);
        p($ret);

        $ret = $toolKv->get($key);
        p($ret);
        add_debug_log('aaa');
    }
}
$app = new App();
$app->run();
