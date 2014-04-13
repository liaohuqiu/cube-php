<?php
/**
 *   development environment
 *
 * @author      huqiu
 */
class MCore_Tool_Env
{
    public static function isDev()
    {
        return 'dev' == ENV_TAG;
    }

    public static function isTest()
    {
        return 'test' == ENV_TAG;
    }

    public static function isProd()
    {
        return 'prod' == ENV_TAG;
    }

    public static function getEnvTag()
    {
        return ENV_TAG;
    }
}
