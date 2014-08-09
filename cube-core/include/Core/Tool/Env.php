<?php
/**
 *   development environment
 *
 * @author      huqiu
 */
class MCore_Tool_Env
{
    public static function isProd()
    {
        return 'prod' == ENV_TAG;
    }
}
