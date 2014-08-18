<?php
class MApis_ApiList extends MApps_AppBase_BaseApiApp
{
    protected function main()
    {
        $map = array(
            'api/demo' => array(
                'des' => '',
                'params' => array(
                ),
            ),
        );

        $host = $_SERVER['HTTP_HOST'];
        foreach ($map as $url => $item)
        {
            $data = MCore_Tool_Array::getFields($item['params'], 'demo_vaule', true);
            $demo_url = MCore_Tool_Http::buildGetUrl($data, $url);
            $item['demo_url'] = 'http://' . $host . '/'. $demo_url;
            $map[$url] = $item;
        }
        $data = $map;
        $this->setData($data);
    }
}
