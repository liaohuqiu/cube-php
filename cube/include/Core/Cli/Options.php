<?php
/**
 *   命令行参数
 *  -k value
 *  --key value
 *  另外支持
 *  -kvalue ,短选项，后直接跟value
 * @author      huqiu
 */
class MCore_Cli_Options
{
    private $_desList = array();
    private $_info;

    public function create($required, $optional = array())
    {
        return new MCore_Cli_Options($required, $optional);
    }

    function __construct($required = array(), $optional = array())
    {
        $help = "\n++++++++++\t参数列表说明:\t++++++++++\n\n";

        $keys = array();
        if(is_array($required) && !empty($required))
        {
            $required = $this->_formatConfig($required);
            $help .= "必选参数:\n";
            foreach($required as $k => $info)
            {
                $des = $info["des"];
                if(!$this->_isMultiCondition($k))
                {
                    $keys[] = $k;
                }
                else
                {
                    $keys = array_merge($keys, $this->_getMultiConditionKeys($k));
                }
                $help .= "\t$k:\t$des\n";
                $this->_desList[$k] = $des;
            }
        }
        if(is_array($optional) && !empty($optional))
        {
            $optional = $this->_formatConfig($optional);
            $help .= "可选参数:\n";
            foreach($optional as $k => $info)
            {
                $des = $info["des"];
                $keys[] = $k;
                $help .= "\t$k:\t$des\n";
                $this->_desList[$k] = $des;
            }
        }
        $this->_help = $help;

        $keys[] = "help";
        $keys = array_unique($keys);

        $this->_info = self::getopt($keys);

        if($this->has('help'))
        {
            $this->_showHelp();
        }

        if(is_array($required) && !empty($required))
        {
            $this->_checkRequired($required);
        }
    }

    private function _formatConfig($conf)
    {
        foreach ($conf as $k => $info)
        {
            !is_array($info) && $info = array("des" => $info);
            !isset($info["des"]) && $info["des"] = $k;
            $conf[$k] = $info;
        }
        return $conf;
    }

    /**
     * 遍历获取组合条件的所有键
     */
    private function _getMultiConditionKeys($opt)
    {
        $condition = MCore_Cli_Condition::parse($opt);
        $list = array();
        if($condition->isCell())
        {
            $list[] = $condition->getContent();
        }
        else
        {
            $subConditions = $condition->getSubConditions();
            foreach($subConditions as $subCondition)
            {
                $ret = $this->_getMultiConditionKeys($subCondition->getContent());
                $list = array_merge($ret, $list);
            }
        }
        return $list;
    }

    private function _isMultiCondition($k)
    {
        $specialChars = array('|', "&" , "(" , ")");
        foreach($specialChars as $char)
        {
            if(strpos($k, $char) !== false)
            {
                return true;
            }
        }
        return false;
    }

    private function _showHelp()
    {
        echo $this->_help . "\n";
        exit;
    }

    private function _checkRequired($required)
    {
        $requiredList = array();
        foreach($required as $k => $info)
        {
            if($this->_isMultiCondition($k))
            {
                $ret = $this->_checkMultiCondition($k);
                if(!$ret)
                {
                    echo "组合条件不满足:\n\t$k\n";;
                    $this->_showHelp();
                }
            }
            else
            {
                $requiredList[$k] = $info;
            }
        }

        foreach($requiredList as $k => $info)
        {
            $des = $this->_desList[$k];
            if(!isset($this->_info[$k]))
            {
                echo "必要参数缺失:\n\t$k\t$des\n";;
                $this->_showHelp();
            }
            else
            {
                if($info["notnull"] && !$this->_info[$k])
                {
                    echo "值不能为空:\n\t$k\t$des\n";;
                    $this->_showHelp();
                }
            }
        }
    }

    private function _checkMultiCondition($cond)
    {
        $condition = MCore_Cli_Condition::parse($cond);
        $type = $condition->getType();
        $content = $condition->getContent();

        $subConditions = $condition->getSubConditions();

        //组合条件，需要一一满足
        if($type == "and")
        {
            foreach($subConditions as $subCondition)
            {
                $subContent = $subCondition->getContent();
                if($subCondition->isCell())
                {
                    if(!isset($this->_info[$subContent]))
                    {
                        return false;
                    }
                }
                else
                {
                    $ret = $this->_checkMultiCondition($subContent);
                    if(!$ret)
                    {
                        return false;
                    }
                }
            }
            return true;
        }
        //并列条件。满足起其中一个即可
        else
        {
            foreach($subConditions as $subCondition)
            {
                $subContent = $subCondition->getContent();
                if($subCondition->isCell())
                {
                    if(isset($this->_info[$subContent]))
                    {
                        return true;
                    }
                }
                else
                {
                    $ret = $this->_checkMultiCondition($subContent);
                    if($ret)
                    {
                        return true;
                    }
                }
            }
            return false;
        }
    }

    function get($k)
    {
        return $this->_info[$k];
    }

    function getData()
    {
        return $this->_info;
    }

    function has($k)
    {
        return isset($this->_info[$k]);
    }

    /**
     * 获取命令行参数
     */
    public static function getopt($keyList)
    {
        $rawArgv = $_SERVER["argv"];
        array_shift($rawArgv);

        $options = array();
        foreach($rawArgv as $index => $str)
        {
            $key = self::_tryGetKey($str, $keyList);
            if($key !== false)
            {
                //long option
                if(strlen($key) > 1)
                {
                    $value = self::_getValueFromNextArgv($rawArgv, $index, $keyList);
                }
                //short option
                else
                {
                    //值紧跟在选项后面
                    if(strlen($str) > 2)
                    {
                        $value = substr($str, 2);
                    }
                    else
                    {
                        $value = self::_getValueFromNextArgv($rawArgv, $index, $keyList);
                    }
                }
                $options[$key][] = $value;
            }
        }
        foreach($options as $key => $values)
        {
            if(count($values) == 1)
            {
                $options[$key] = array_shift($values);
            }
        }
        return $options;
    }

    /**
     * 从后续选项获取值
     */
    private static function _getValueFromNextArgv($rawArgv, $index, $keyList)
    {
        $value = "";

        $len = count($rawArgv);
        $hitFirstValue = false;
        for($i = $index + 1; $i < $len; $i ++)
        {
            $thisIndexValue = $rawArgv[$i];
            $key = self::_tryGetKey($thisIndexValue, $keyList);
            $isValue = $key === false;
            if($isValue)
            {
                $value = $thisIndexValue;
                break;
            }
            else
            {
                break;
            }
        }
        return $value;
    }

    private static function _tryGetKey($str, $keyList)
    {
        $pos = strpos($str, "--");
        //short option
        if($pos === false || $pos !== 0)
        {
            $pos = strpos($str, "-");
            if($pos === false || $pos !== 0)
            {
                return false;
            }
            else
            {
                $key = substr($str, 1, 1);
            }
        }
        //long option
        else
        {
            $key = substr($str, 2);
        }

        if(!in_array($key, $keyList))
        {
            return false;
        }
        return $key;
    }
}
?>
