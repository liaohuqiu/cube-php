<?php
include dirname(dirname(dirname(__FILE__))) . '/app-boot.php';
function fn_getCacheProxy()
{
    // return new MCore_Min_NullCache();
    // return MCore_Min_RedisCache::create();
    return MCore_Min_Memcached::create();
}
class App extends MCore_Cli_ConsoleBase
{
    protected function main()
    {
        $key = 'limit1';
        $max = 10;

        $ret = MCore_Tool_Limit::getCount($key);
        p($ret);

        $ret = MCore_Tool_Limit::testAdd($key, $max, 1);
        p($ret);

        $ret = MCore_Tool_Limit::testAdd($key, $max, 11);
        p($ret);

        $ret = MCore_Tool_Limit::addAndTest($key, $max, 5);
        p($ret);

        $ret = MCore_Tool_Limit::addAndTest($key, $max, 11);
        p($ret);

        $ret = MCore_Tool_Limit::getCount($key);
        p($ret);

        $ret = MCore_Tool_Limit::setCount($key, 5);
        p($ret);

        $ret = MCore_Tool_Limit::testAdd($key, $max, 5);
        p($ret);

        $ret = MCore_Tool_Limit::testAdd($key, $max, 6);
        p($ret);

        $ret = MCore_Tool_Limit::addAndTest($key, $max, 5);
        p($ret);

        MCore_Tool_Limit::delete($key);

        p('test for daily limit, key: ' . $key);
        p('getCountDaily');
        $ret = MCore_Tool_Limit::getCountDaily($key);
        p($ret);

        p('testAddDaily: ' . 10);
        $ret = MCore_Tool_Limit::testAddDaily($key, $max, 10);
        p($ret);

        p('testAddDaily: ' . 11);
        $ret = MCore_Tool_Limit::testAddDaily($key, $max, 11);
        p($ret);

        p('addAndTestDaily: ' . 5);
        $ret = MCore_Tool_Limit::addAndTestDaily($key, $max, 5);
        p($ret);
        p('getCountDaily');
        $ret = MCore_Tool_Limit::getCountDaily($key);
        p($ret);

        p('addAndTestDaily: ' . 6);
        $ret = MCore_Tool_Limit::addAndTest($key, $max, 6);
        p($ret);
        p('getCountDaily');
        $ret = MCore_Tool_Limit::getCountDaily($key);
        p($ret);

        p('setCountDaily: ' . 6);
        $ret = MCore_Tool_Limit::setCountDaily($key, 6);
        p($ret);

        p('getCountDaily');
        $ret = MCore_Tool_Limit::getCountDaily($key);
        p($ret);
    }
}
$app = new App();
$app->run();
