<?php
/**
 *   array operation
 *
 * @author      huqiu
 */
class MCore_Tool_Array
{
    public static function getFields($objs, $key, $preserveKey = false)
    {
        $ids = array();
        if (is_array($objs))
        {
            foreach($objs as $index => $obj)
            {
                if (is_array($obj))
                {
                    $ids[$index] = $obj[$key];
                }
                else if (is_object($obj))
                {
                    $ids[$index] = $obj->$key;
                }
                else
                {
                    $ids[$index] = $obj;
                }
            }
        }
        if(!$preserveKey)
        {
            return array_values($ids);
        }
        return $ids;
    }

    public static function list2Map($list, $key, $value_key = null)
    {
        if(empty($list) || (!is_array($list)))
        {
            return array();
        }

        $map = array();
        foreach($list as $item)
        {
            if(!isset($item[$key]))
            {
                continue;
            }
            if ($value_key !== null)
            {
                $map[$item[$key]] = $item[$value_key];
            }
            else
            {
                $map[$item[$key]] = $item;
            }
        }
        return $map;
    }

    public static function filterEmpty($list)
    {
        if (!empty($list) && is_array($list))
        {
            foreach ($list as $idx => $item)
            {
                if (empty($item))
                {
                    unset($list[$idx]);
                }
            }
        }
        return $list;
    }

    public static function uniqueAndNotEmpty($list)
    {
        if (empty($list) || !is_array($list))
        {
            return $list;
        }
        $list = self::filterEmpty($list);
        $list = array_values(array_unique($list));
        return $list;
    }

    public static function sortByField($arr, $fieldName, $flag = 'desc', $reserveKey = true)
    {
        $indexArr = array();
        foreach ($arr as $idx => $item)
        {
            $indexArr[$idx] = $item[$fieldName];
        }

        if ('desc' == $flag)
        {
            arsort($indexArr);
        }
        else
        {
            asort($indexArr);
        }

        $result = array();
        foreach ($indexArr as $idx => $field)
        {
            if($reserveKey)
            {
                $result[$idx] = $arr[$idx];
            }
            else
            {
                $result[] = $arr[$idx];
            }
        }
        return $result;
    }

    public static function objectToArray($obj)
    {
        if (is_object ( $obj ))
        {
            $obj = get_object_vars ( $obj );
        }
        if (is_array($obj))
        {
            foreach ($obj as $key => $value )
            {
                $obj[$key] = self::objectToArray( $value );
            }
        }
        return $obj;
    }

    public static function sumField($list, $fieldKey = "")
    {
        $total = 0;
        foreach($list as $item)
        {
            if ($fieldKey != "")
            {
                $total += $item[$fieldKey];
            }
            else
            {
                $total += $item;
            }
        }
        return $total;
    }

    public static function rand($source, $count = 1)
    {
        if(!is_array($source) || $count <= 0)
        {
            return array();
        }
        if($count == 1)
        {
            $keys = array(array_rand($source, $count));
        }
        else
        {
            $keys = array_rand($source, $count);
        }

        $list = array();
        foreach($keys as $key)
        {
            $list[$key] = $source[$key];
        }
        if($count == 1 && !empty($list))
        {
            return array_shift($list);
        }
        return $list;
    }

    public static function randKeyByKey($arr, $probabilityKey)
    {
        $total = 0;
        $randData = array();
        foreach ($arr as $key => $info)
        {
            $weight = $info[$probabilityKey];
            $randData[$key] = $weight;
            $total += $weight;
        }

        $total2 = 0;
        $randKey = "";
        $rand = mt_rand(0, $total);
        foreach($randData as $key => $weight)
        {
            $total2 += $weight;
            if($total2>=$rand)
            {
                return $key;
            }
        }
        return "";
    }

    public static function randByKey($arr, $probabilityKey)
    {
        $randKey = self::randKeyByKey($arr, $probabilityKey);
        return $arr[$randKey];
    }

    public static function where($collection, $where)
    {
        if(!is_array($where) || empty($where))
        {
            return array();
        }
        $list = array();
        foreach($collection as $idx => $item)
        {
            $hit = true;
            foreach($where as $key => $val)
            {
                if (!isset($item[$key]) || $item[$key] != $val)
                {
                    $hit = false;
                    break;
                }
            }
            if($hit)
            {
                $list[$idx] = $item;
            }
        }
        return $list;
    }

    public static function firstOrDefault($arr)
    {
        if(!$arr)
        {
            return false;
        }
        foreach($arr as $item)
        {
            return $item;
        }
    }

    public static function fetch($arr, $keys, $setNull = false)
    {
        $ret = array();
        foreach($keys as $key)
        {
            if ($setNull)
            {
                $ret[$key] = $arr[$key];
            }
            else
            {
                isset($arr[$key]) && $ret[$key] = $arr[$key];
            }
        }
        return $ret;
    }

    public static function fetchOne($arr, $key, $default)
    {
        return isset($arr[$key]) ? $arr[$key] : $default;
    }

    public static function remove($arrs, $keys)
    {
        foreach ($keys as $key)
        {
            unset($arrs[$key]);
        }
        return $arrs;
    }

    public static function mapValue($arr, $fieldMap, $throw = false)
    {
        if (!is_array($arr))
        {
            return false;
        }
        $ret = array();
        if ($throw)
        {
            $keys = array_keys($arr);
            $keys = array_flip($keys);
        }
        foreach ($fieldMap as $key => $mapedKey)
        {
            if ($throw && !isset($keys[$mapedKey]))
            {
                throw new Exception('mapValue, this key is not existent: ' . $mapedKey);
            }
            $ret[$key] = $arr[$mapedKey];
        }
        return $ret;
    }

    public static function arrayDiffKey($arr1, $arr2)
    {
        $ret = array();
        foreach ($arr1 as $key => $item)
        {
            if (!isset($arr2[$key]))
            {
                $ret[$key] = $item;
            }
        }
        return $ret;
    }

    public static function flatten($array)
    {
        $return = array();
        foreach ($array as $key => $value)
        {
            if (is_array($value))
            {
                $return = array_merge($return, self::flatten($value));
            }
            else
            {
                $return[$key] = $value;
            }
        }
        return $return;
    }
}
