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
    public static function insert($table, $dataToBeInserted, $fieldsWillBeChangedOnDuplicate = array(), $dataToBeAddedOnDuplucated = array(), $notEscapeFields=array())
    {
        $sql = "insert into $table (";

        $flag = 0;
        foreach ($dataToBeInserted as $k => $v)
        {
            if (!$k)
            {
                throw new Exception('empty key in $dataToBeInserted');
            }
            $sql .= ($flag == 0 ? '': ', ') . $k;
            $flag = 1;
        }

        $sql .= ") values(";
        $flag = 0;
        foreach ($dataToBeInserted as $k => $v)
        {
            $v = (empty($notEscapeFields) || !in_array($k,$notEscapeFields)) ? ("'" . self::escape_string($v) . "'") : $v;
            $sql .= ($flag == 0 ? '' : ", ") . $v;
            $flag = 1;
        }
        $sql .= ")";

        if (!empty($fieldsWillBeChangedOnDuplicate) || !empty($dataToBeAddedOnDuplucated))
        {
            $sql .= " ON DUPLICATE KEY UPDATE ";
            $flag = 0;
            foreach ($fieldsWillBeChangedOnDuplicate as $k)
            {
                if (!isset($dataToBeInserted[$k]))
                {
                    continue;
                }
                $v = (empty($notEscapeFields) || !in_array($k,$notEscapeFields)) ? ("'" . self::escape_string($dataToBeInserted[$k]) . "'") : $dataToBeInserted[$k];
                $sql .= ($flag == 0 ? '': ', ') . $k . " = " . $v;
                $flag = 1;
            }

            foreach ($dataToBeAddedOnDuplucated as $k => $v)
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
    public static function insertList($table, $insertFieldList, $notEscapeFields=array())
    {
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
                $v = (empty($notEscapeFields) || !in_array($k,$notEscapeFields)) ? ("'" . self::escape_string($v) . "'") : $v;
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
        $where = '';
        foreach ($whereField as $k=>$v)
        {
            if(0 != strlen($where))
            {
                $where .= " and ";
            }
            $where .= $k . " = '" . self::escape_string($v) . "'";
        }
        $sql = self::deleteRawWhere($table,$where);
        return $sql;
    }

    public static function deleteRawWhere($table,$where)
    {
        if(0 == strlen($where))
        {
            throw new Exception("try to excute sql text without a where condition");
        }

        $sql = "delete from " . $table . " where " . $where;
        return $sql;
    }

    public static function replace($table, $replaceField)
    {
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

    public static function update($table, $dataToBeSet, $dataToBeAdded = array(), $whereField, $notEscapeFields=array())
    {
        $sql = "update $table set ";
        $flag = 0;
        foreach ($dataToBeSet as $k => $v)
        {
            $v = (empty($notEscapeFields) || !in_array($k,$notEscapeFields)) ? ("'" . self::escape_string($v) . "'") : $v;
            $sql .= ($flag == 0 ? '': ', ') . $k . " = " . $v;
            $flag = 1;
        }
        foreach ($dataToBeAdded as $k => $v)
        {
            if (is_numeric($v))
            {
                $sql .= sprintf("%s %s=%s%s%s", ($flag==0 ? '' : ","),
                    $k, $k, ($v >= 0 ? "+" : " "), $v);
                $flag = 1;
            }
            else
            {
                $sql .= sprintf("%s %s=CONCAT(%s,'%s')", ($flag==0 ? '' : ","),
                    $k, $k, self::escape_string($v));
                $flag = 1;
            }
        }

        $where = self::where($whereField);
        if(0 == strlen($where))
        {
            throw new Exception("try to excute sql text without a where condition");
        }
        $sql .= " where " . $where;

        return $sql;
    }

    public static function where($whereField)
    {
        $where = '';
        if($whereField)
        {
            foreach ($whereField as $k => $v)
            {
                if(is_array($v) && empty($v))
                {
                    continue;
                }
                if(0 != strlen($where))
                {
                    $where .= " and ";
                }
                if(is_array($v))
                {
                    $v = array_unique($v);
                    if(count($v) == 1)
                    {
                        $where .= $k . " = '" . self::escape_string(current($v)) . "'";
                    }
                    else
                    {
                        $str = implode("','",array_map('self::escape_string',$v));
                        $where .= $k . " in ('$str')";
                    }
                }
                else
                {
                    $where .= $k . " = '" . self::escape_string($v) . "'";
                }
            }
        }
        return $where;
    }

    public static function updateRawWhere($table, $dataToBeSet, $dataToBeAdded = array(), $where = '', $notEscapeFields=array())
    {
        $sql = "update $table set ";
        $flag = 0;
        foreach ($dataToBeSet as $k => $v)
        {
            $v = (empty($notEscapeFields) || !in_array($k,$notEscapeFields)) ? ("'" . self::escape_string($v) . "'") : $v;
            $sql .= ($flag == 0 ? '': ', ') . $k . " = " . $v;
            $flag = 1;
        }
        foreach ($dataToBeAdded as $k => $v)
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
        $selectField = (array) $selectField;
        $select = implode(",",$selectField);

        $where = self::where($whereField);

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

    public static function selectRawWhere($table, $selectField, $where, $order, $start, $num, $foundRows = false)
    {
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
