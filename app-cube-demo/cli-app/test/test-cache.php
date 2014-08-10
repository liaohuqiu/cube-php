<?php
include dirname(dirname(dirname(__FILE__))) . '/app-boot.php';
function fn_getCacheProxy()
{
    // return new MCore_Min_NullCache();
    return MCore_Min_RedisCache::create();
}
class App extends MCore_Cli_ConsoleBase
{
    protected function main()
    {
        $key = 'test1';
        $getFn = function() {
            p('getFn');
            return time();
        };
        $onToLocalFn = function ($time) {
            return $time * 2;
        };
        $ret = MCore_Tool_Cache::fetch($key, $getFn);
        p($ret);

        $ret = MCore_Tool_Cache::fetch($key, $getFn, $onToLocalFn);
        p($ret);

        $cache = MCore_Tool_Cache::getCacheProxy();
        $key_inc = 'inct2';
        $ret = $cache->increment($key_inc);
        p($ret);

        $ret = $cache->increment($key_inc);
        p($ret);

        $ret = $cache->increment($key_inc, 10);
        p($ret);

        p('======');
        $redisCache = MCore_Min_RedisCache::create()->getEngine();
        $redisCache->set('test', true);
        $ret = $redisCache->get('test');
        var_dump((string) true);

    }
}
$app = new App();
$app->run();
