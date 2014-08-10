<?php
include dirname(dirname(dirname(__FILE__))) . '/app-boot.php';
class App extends MCore_Cli_ConsoleBase
{
    private $denstination_dir;

    function main()
    {
        $reqs = array(
            'f' => 'auto-gen-res-list-info.json',
            't' => 'the destination directory for version.js',
        );
        $opts = MCore_Cli_Options::create($reqs);
        $file = $opts->get('f');
        $this->denstination_dir = $opts->get('t');
        $rawList = json_decode(file_get_contents($file), true);

        $latestMtime = 0;
        $jsInfoList = array();
        $resVersionList = array();

        foreach ($rawList as $type => $sublist)
        {
            foreach ($sublist as $id => $item)
            {
                $mtime = $item['mtime'];
                $v = date('ymd', $mtime) . substr(md5($mtime), 0, 5);
                $url = '/' . $item['path'] . '?' . $v;
                $item['version'] = $v;
                $item['url'] = $url;

                $list[$type][$id] = $item;
                if ($type == 'js')
                {
                    $latestMtime < $mtime && $latestMtime = $mtime;
                    $jsInfo = array();
                    $jsInfo['url'] = $url;
                    $jsInfo['d'] = $item['dependence'];
                    $jsInfoList[$id] = $jsInfo;
                }

                $resVersionList[$type][$id] = $url;
            }
        }

        // add version info
        $versionJsId = "version";
        $versionJsVersion = date('ymdHi', $latestMtime) . substr(md5($latestMtime), 0, 5);
        $versionJsUrl = '/version.js?' . $versionJsVersion;

        $versionJsInfo = array();
        $versionJsInfo['d'] = array();
        $versionJsInfo['url'] = $versionJsUrl;

        $jsInfoList[$versionJsId] = $versionJsInfo;
        $resVersionList['js'][$versionJsId] = $versionJsUrl;

        $configKey = 'res-info';
        MCore_Tool_Conf::writeDataConfig($configKey,  $resVersionList);
        $this->_outputJsInfoList($jsInfoList);
        return;
    }

    private function _outputJsInfoList($jsInfoList)
    {
        $jsInfo = json_encode($jsInfoList);
        $content = "K.Resource.addJsInfo($jsInfo)";

        $filePath = $this->denstination_dir . '/version.js';
        $tempPath = $filePath . '.tmp';

        $this->printInfo("save version.js:\t$filePath");
        file_put_contents($tempPath, $content);
        $this->execCmd("mv $tempPath $filePath");
    }
}
$app = new App();
$app->run();
