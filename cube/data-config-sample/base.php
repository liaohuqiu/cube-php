<?php

// deprecated
private static $tableSplitKey = array(
);

private static $splitTableNum = 2;

private static $singleTable = array();

private static $limitTable = 's_user_limit';

private static $userkvTable = 's_user_kv';

// the talbe will use cache one
private static $;
$data = array (
    'userkv_table' => 's_user_kv',

    'cachedSingleTable' = array(
        's_city_index' => array(
            'cacheKeyPart' => 'cache0',     //indentify the memcache key
            'whereFields' => array(
                array('id'),                // all the refer key
                array('x', 'y'),
            )
        ),
    ),
);
return $data;
