<?php
/**
 * make sql command
 *
 * @author      huqiu
 */
class MCore_Tool_Sql
{
    /**
     *  on duplicate key update
     */
    public static function insert($table, $insertKV, $keysChangeOnDup = array(), $kvAddOnDup = array(), $keysNoEscape = array())
    {
        $table = '`' . $table . '`';
        $keys = array();
        foreach ($insertKV as $k => $v)
        {
            if (!$k)
            {
                throw new Exception('empty key in $insertKV');
            }
            $keys[] = $k;
        }
        $keys = implode(',', $keys);

        $values = array();
        foreach ($insertKV as $k => $v)
        {
            if (is_string($v) && (empty($keysNoEscape) || !in_array($k, $keysNoEscape)))
            {
                $v = "'" . self::escape_string($v) . "'";
            }
            $values[] = $v;
        }
        $values = implode(',', $values);

        $sql = 'insert into ' . $table . '(' . $keys . ') values (' . $values . ')';

        if (!empty($keysChangeOnDup) || !empty($kvAddOnDup))
        {
            $sql .= " ON DUPLICATE KEY UPDATE ";

            $flag = 0;

            foreach ($keysChangeOnDup as $k)
            {
                if (!isset($insertKV[$k]))
                {
                    continue;
                }
                $v = $insertKV[$k];
                if (is_string($v) && (empty($keysNoEscape) || !in_array($k, $keysNoEscape)))
                {
                    $v = "'" . self::escape_string($v) . "'";
                }
                $sql .= ($flag == 0 ? '': ', ') . $k . " = " . $v;
                $flag = 1;
            }

            foreach ($kvAddOnDup as $k => $v)
            {
                if (is_numeric($v))
                {
                    $sql .= ($flag == 0 ? '' : ", ") . $k . "=" . $k . ($v >= 0 ? "+" : '') . ($v);
                }
                else
                {
                    $sql .= ($flag == 0 ? '' : ", ") . $k . "=CONCAT(" . $k . ",'" . self::escape_string($v) . "')";
                }
                $flag = 1;
            }
        }
        return $sql;
    }

    public static function escape_string($str)
    {
        return str_replace(array('\\', "\0", "\n", "\r", "'", '"', "\x1a"), array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'), $str);
    }

    /**
     * insert multi
     */
    public static function insertList($table, $insertFieldList, $keysNoEscape=array())
    {
        $table = '`' . $table . '`';
        $tmpArr = $insertFieldList[0];

        $sql = "insert into $table (";
        $flag = 0;
        foreach ($tmpArr as $k => $v)
        {
            $sql .= ($flag == 0 ? '': ', ') . $k;
            $flag = 1;
        }
        $sql .= ") values";
        foreach ($insertFieldList as $varr)
        {
            $flag = 0;
            $sql .= "(";
            foreach ($varr as $k => $v)
            {
                if (is_string($v) && (empty($keysNoEscape) || !in_array($k, $keysNoEscape)))
                {
                    $v = "'" . self::escape_string($v) . "'";
                }
                $sql .= ($flag == 0 ? '' : ", ").$v;
                $flag = 1;
            }
            $sql .= "),";
        }
        $sql = trim($sql,' ,');

        return $sql;
    }

    /**
     * delete
     */
    public static function delete($table, $whereField)
    {
        $where = self::where($whereField);
        $sql = self::deleteRawWhere($table, $where);
        return $sql;
    }

    public static function deleteRawWhere($table,$where)
    {
        $table = '`' . $table . '`';
        if(0 == strlen($where))
        {
            throw new Exception("try to excute sql text without a where condition");
        }

        $sql = "delete from " . $table . " where " . $where;
        return $sql;
    }

    public static function replace($table, $replaceField)
    {
        $table = '`' . $table . '`';
        $sql = "replace into $table (";
        $flag = 0;
        foreach ($replaceField as $k => $v)
        {
            $sql .= ($flag == 0 ? '': ', ') . $k;
            $flag = 1;
        }
        $sql .= ") values(";
        $flag = 0;
        foreach ($replaceField as $k => $v)
        {
            $sql .= ($flag == 0 ? "'" : ", '") . self::escape_string($v) . "'";
            $flag = 1;
        }
        $sql .= ")";

        return $sql;
    }

    public static function replaceList($table, $replaceFieldList)
    {
        $table = '`' . $table . '`';
        $tmpArr = $replaceFieldList[0];
        $sql = "replace into $table (";
        $flag = 0;
        foreach ($tmpArr as $k => $v)
        {
            $sql .= ($flag == 0 ? '': ', ') . $k;
            $flag = 1;
        }
        $sql .= ") values";
        foreach ($replaceFieldList as $varr)
        {
            $flag = 0;
            $sql.="(";
            foreach ($varr as $k => $v)
            {
                $sql .= ($flag == 0 ? "'" : ", '") . self::escape_string($v) . "'";
                $flag = 1;
            }
            $sql .= "),";
        }
        $sql = trim($sql,' ,');

        return $sql;
    }

    public static function update($table, $kvSet, $kvChange = array(), $whereField, $keysNoEscape=array())
    {
        $table = '`' . $table . '`';
        $set = array();
        foreach ($kvSet as $k => $v)
        {
            if (is_string($v) && (empty($keysNoEscape) || !in_array($k, $keysNoEscape)))
            {
                $v = "'" . self::escape_string($v) . "'";
            }
            $set[] = $k . ' = ' . $v;
        }

        foreach ($kvChange as $k => $v)
        {
            if (is_numeric($v) && (empty($keysNoEscape) || !in_array($k, $keysNoEscape)))
            {
                if ($v >= 0)
                {
                    $set[] = $k . ' = ' . $k . ' + ' . $v;
                }
                else
                {
                    $set[] = $k . ' = ' . $k . ' - ' . $v;
                }
            }
            else
            {
                $v = self::escape_string($v);
                $set[] = $k . '=CONCAT(' . $k . ', ' . $v . ')';
            }
        }
        $sql = 'update ' . $table . ' set ' . implode(', ', $set);

        $where = self::where($whereField);
        if(0 == strlen($where))
        {
            throw new Exception("try to excute sql text without a where condition");
        }
        $sql .= " where " . $where;

        return $sql;
    }

    public static function where($whereField, $where = '')
    {
        if ($whereField)
        {
            $list = array();
            if (!empty($where))
            {
                $list[] = $where;
            }
            foreach ($whereField as $k=>$v)
            {
                if (is_array($v) && empty($v))
                {
                    continue;
                }
                if (is_array($v))
                {
                    $v = array_unique($v);
                    if (count($v) == 1)
                    {
                        $v = current($v);
                    }
                    else
                    {
                        if (!is_string(current($v)))
                        {
                            $str = implode(',', $v);
                            $list[] = $k . " in ($str)";
                        }
                        else
                        {
                            $str = implode("','", array_map('self::escape_string',$v));
                            $list[] = $k . " in ('$str')";
                        }
                        continue;
                    }
                }
                if (!is_string($v))
                {
                    $list[] = $k . ' = ' . $v;
                }
                else
                {
                    $list[] = $k . " = '" . self::escape_string($v) . "'";
                }
            }
            $where = implode(' and ', $list);
        }
        return $where;
    }

    public static function updateRawWhere($table, $kvSet, $kvChange = array(), $where = '', $keysNoEscape=array())
    {
        $table = '`' . $table . '`';
        $sql = "update $table set ";
        $flag = 0;
        foreach ($kvSet as $k => $v)
        {
            $v = (empty($keysNoEscape) || !in_array($k,$keysNoEscape)) ? ("'" . self::escape_string($v) . "'") : $v;
            $sql .= ($flag == 0 ? '': ', ') . $k . " = " . $v;
            $flag = 1;
        }
        foreach ($kvChange as $k => $v)
        {
            if (is_numeric($v))
            {
                $sql .= sprintf("%s %s=%s%s%s", ($flag==0 ? '' : ","), $k, $k, ($v >= 0 ? "+" : " "), $v);
                $flag = 1;
            }
            else
            {
                $sql .= sprintf("%s %s=CONCAT(%s,'%s')", ($flag == 0 ? '' : ","), $k, $k, self::escape_string($v));
                $flag = 1;
            }
        }
        if(0 == strlen($where))
        {
            throw new Exception("try to excute sql text without a where condition");
        }
        $sql .= " where " . $where;

        return $sql;
    }

    public static function select($table, $selectField, $whereField, $order, $start, $num, $foundRows = false)
    {
        $table = '`' . $table . '`';
        $selectField = (array) $selectField;
        $select = implode(",", $selectField);

        $where = self::where($whereField);

        if ($foundRows)
        {
            $sql = "select SQL_CALC_FOUND_ROWS " . $select . " from " . $table;
        }
        else
        {
            $sql = "select " . $select . " from " . $table;
        }

        if(0 != strlen($where))
        {
            $sql .= " where " . $where;
        }
        $sql .= " " . $order;

        if($num != 0)
        {
            $sql .= " limit " . intval($start) . ", " . intval($num);
        }
        return $sql;
    }

    public static function selectRawWhere($table, $selectField, $where, $order, $start, $num, $foundRows = false)
    {
        $table = '`' . $table . '`';
        $selectField = (array) $selectField;
        $select = implode(",", $selectField);

        if($foundRows)
        {
            $sql = "select SQL_CALC_FOUND_ROWS " . $select . " from " . $table;
        }
        else
        {
            $sql = "select " . $select . " from " . $table;
        }

        if(0 != strlen($where))
        {
            $sql .= " where " . $where;
        }
        $sql .= " " . $order;

        if($num != 0)
        {
            $sql .= " limit " . intval($start) . ", " . intval($num);
        }

        return $sql;
    }

    public static function foundRows()
    {
        $sql = "select FOUND_ROWS()";
        return $sql;
    }
}
