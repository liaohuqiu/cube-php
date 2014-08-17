<?php
/**
 * key-value
 *
 * @author huqiu
 */
class MCore_Tool_KV
{
    private $table;

    public function __construct($table)
    {
        $this->table = $table;
    }

    public function set($key, $value)
    {
        $data = array(
            'k'     =>      $key,
            'v'     =>      cube_encode($value),
            'ctime' =>      'now()',
        );

        MCore_Dao_DB::create()->insert($this->table, $data, array('v'), array(), array('ctime'));
        MCore_Tool_Cache::delete(self::keyForKV($this->table, $key));
        return true;
    }

    public function get($key)
    {
        $info = $this->getRaw($key);
        if($info === false)
        {
            return false;
        }
        return $info['v'];
    }

    public function getRaw($key)
    {
        $table = $this->table;
        $getFn = function() use ($table, $key) {
            $select = array('v', 'ctime', 'mtime');
            $where = array('k' => $key);
            $ret = MCore_Dao_DB::create()->select($table, $select, $where)->first();
            if (!empty($ret))
            {
                $ret['ctime'] = strtotime($ret['ctime']);
                $ret['mtime'] = strtotime($ret['mtime']);
            }
            return $ret;
        };
        $onToLocalFn = function($item) {
            if (!empty($item))
            {
                $item['v'] = cube_decode($item['v']);
            }
            return $item;
        };
        return MCore_Tool_Cache::fetch(self::keyForKV($table, $key), $getFn, $onToLocalFn);
    }

    public function delete($key)
    {
        $where = array('k' => $key);
        MCore_Dao_DB::create()->delete($this->table, $where);
        MCore_Tool_Cache::delete(self::keyForKV($this->table, $key));
        return true;
    }

    private static function keyForKV($table, $key)
    {
        return 'kv_' . $table . '_' . $key;
    }
}
