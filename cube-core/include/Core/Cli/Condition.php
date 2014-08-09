<?php
/**
 *   组合条件
 *
 * @author      huqiu
 */
class MCore_Cli_Condition
{
    private $_str;
    private $_type;
    private $_subConditions = array();

    public static function parse($str)
    {
        return new MCore_Cli_Condition($str);
    }

    public function __construct($str)
    {
        $this->_str = $str;

        if (!$this->_splitBracePair())
        {
            $this->_porcessNoBrace();
        }
    }

    /**
     * 处理没有括号的情况
     */
    private function _porcessNoBrace()
    {
        $pos1 = strpos($this->_str, "&");
        $pos2 = strpos($this->_str, "|");

        //没有位运算符了，本身为最小单元
        if ($pos1 === false && $pos2 === false)
        {
            $this->_type = "cell";
        }
        else
        {
            //计算类型，分隔为最小单元
            if ($pos1 !== false)
            {
                $this->_type = "and";
                $delemiter = "&";
            }
            else
            {
                $this->_type = "or";
                $delemiter = "|";
            }
            $subStrs = explode($delemiter, $this->_str);
            foreach ($subStrs as $subStr)
            {
                $this->_subConditions[] = MCore_Cli_Condition::parse($subStr);
            }
        }
    }

    /**
     * 过滤括号，并尝试分隔子项
     */
    private function _splitBracePair()
    {
        //1.    查找成对括号的位置，如果括号不存在，则是最简单单元
        //2.    试图除尽最外侧的括号对
        $len = 0;
        $pos1 = 0;
        $pos2 = 0;
        while(1)
        {
            $str = $this->_str;
            $len = strlen($str);
            $pos1 = strpos($str,"(");
            $pos2 = strrpos($str,")");

            //不存在括号
            if ($pos1 === false || $pos2 === false)
            {
                return false;
            }

            //寻找与最靠左端的左括号，成对的括号的位置
            $count1 = 0;
            $count2 = 0;
            for ($i = $pos1; $i < $len; $i++)
            {
                $char = $str[$i];
                $char == "(" && $count1 ++;
                $char == ")" && $count2 ++;
                if($count1 !=0 && $count1 == $count2)
                {
                    $pos2 = $i;
                    break;
                }
            }

            //括号在两端，除掉，继续下一轮查找
            if ($pos1 == 0 && $pos2 == $len -1)
            {
                $this->_str = substr($str, 1, -1);
            }
            else
            {
                break;
            }
        }

        //计算类型
        if ($pos1 == 0)
        {
            $kindStr = substr($str, $pos2 + 1, 1);
        }
        else
        {
            $kindStr = substr($str, $pos1 -1 ,1);
        }
        $this->_type = ($kindStr == "|") ? "or" : "and";

        //分割成两端不含括号和逻辑运算符号的三段
        if($pos1 == 0)
        {
            $pre = "";
        }
        else
        {
            $firstLen = $pos1 - 1;   //除去位运算符和右括号
            $pre = substr($str, 0, $firstLen);
        }

        $minPartLen = ($pos2 + 1) - $pos1;
        $mid = substr($str, $pos1, $minPartLen);
        $mid = substr($mid, 1, -1);

        $lastLen = $len - ($pos2 + 1);    //最后部分的长度
        if ($lastLen == 0)
        {
            $last = '';
        }
        else
        {
            $lastStart = $pos2 + 1 + 1;  //除去位运算符和右括号
            $last = substr($str, $pos2 + 1 + 1);
        }

        $pre && $this->_subConditions[] = MCore_Cli_Condition::parse($pre);
        $mid && $this->_subConditions[] = MCore_Cli_Condition::parse($mid);
        $last && $this->_subConditions[] = MCore_Cli_Condition::parse($last);

        return true;
    }

    public function isOk()
    {
        if ($this->_type == "or")
        {
            foreach ($this->_subConditions as $subCondition)
            {
                if($subCondition->isOk())
                {
                    return true;
                }
            }
            return false;
        }
        else
        {
            foreach ($this->_subConditions as $subCondition)
            {
                if ($subCondition->isOk())
                {
                    return false;
                }
            }
            return true;
        }
    }

    public function getType()
    {
        return $this->_type;
    }

    public function getContent()
    {
        return $this->_str;
    }

    public function isCell()
    {
        return count($this->_subConditions) == 0;
    }

    public function getSubConditions()
    {
        return $this->_subConditions;
    }
}
?>
