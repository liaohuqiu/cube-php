<?php
/**
 *
 * @package     core
 * @subpackage  dao
 * @author      huqiu
 */
class MCore_Dao_Result extends MCore_Util_ArrayLike
{
    protected $row_num;
    protected $insert_id;
    protected $affected_rows;
    protected $total;
    protected $error;

    private static $_preservedKeys = array(
        'data',
        'row_num',
        'insert_id',
        'affected_rows',
        'total',
        'error',
    );

    function __construct($data, $rowNum, $insertId, $affectedRows)
    {
        $this->row_num = $rowNum;
        $this->insert_id = $insertId;
        $this->affected_rows = $affectedRows;
        parent::__construct($data, self::$_preservedKeys);
    }

    public static function foundRowsData($dbRawData, $foundRowsAsWhat = "FOUND_ROWS()")
    {
        if (!isset($dbRawData[0]) || !isset($dbRawData[1]))
        {
            throw new Exception('Can not build result for not enough data in $dbRawData.');
        }

        $totalData = MCore_Tool_Array::firstOrDefault($dbRawData[1]['data']);
        if (!$totalData || !isset($totalData[$foundRowsAsWhat]))
        {
            throw new Exception('Did not find total in the second db result.');
        }
        $total = $totalData[$foundRowsAsWhat];

        $data0 = $dbRawData[0];
        $data0["total"] = $total;
        return $data0;
    }
}
