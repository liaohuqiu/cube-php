<?php
/**
 *  MCore_Mid_MCache
 *
 * @author 
 */
interface IMCache
{
    function set($key, $value, $expire,$nozip);
    
    function replace($key, $value, $expire,$nozip);
    
    function add($key, $value, $expire,$nozip);
    
    function get($key);
    
    function getMulti($keys);
    
    function delete($key);
    
    function increment($key, $value);
    
    function decrement($key, $value);
}
class MCore_Mid_MCache
{
    private $mcache;   //IMCache
    private $prefix = '';
    
    public function __construct()
    {
        $this->prefix = MCACHE_KEY_PRE;
        $this->mcache = new MCore_Kxi_MCache(MCACHE_GROUP);
    }
    
    function set($key, $value, $expire = 0, $nozip = false)
    {
        $key = $this->_addPrefix($key);
        return $this->mcache->set($key,$value,$expire);
    }
    
    function setObj($key, $value, $expire = 0, $nozip = false)
    {
        $this->set($key,MCore_Tool_Vbs::encode($value),$expire);
    }
    
    function add($key, $value, $expire = 0, $nozip = false)
    {
        $key = $this->_addPrefix($key);
        return $this->mcache->add($key,$value,$expire);
    }
    
    function replace($key, $value, $expire = 0, $nozip = false)
    {
        $key = $this->_addPrefix($key);
        return $this->mcache->replace($key,$value,$expire);
    }
    
    function get($key)
    {
        $key = $this->_addPrefix($key);
        return $this->mcache->get($key);
    }
    
    function getObj($key)
    {
        $value = $this->get($key);
        return MCore_Tool_Vbs::decode($value);
    }
    
    function getMulti($keys)
    {
        if (empty($keys))
        {
            return array();
        }
        
        $tmpKeys = array_map("strval", array_unique($keys));
        $keys = array();
        foreach($tmpKeys as $key)
        {
            $keys[] = $this->_addPrefix($key);
        }
        $len = count($keys);
        $tmpRet = array();
        for ($i = 0; $i < $len; $i+=500)
        {
            $subkey = array_slice($keys, $i, 500);
            $ret = $this->mcache->getMulti($subkey);
            $tmpRet = array_merge($tmpRet, $ret);
        }
        
        $ret = array();
        foreach ($tmpRet as $k => $v)
        {
            $v = strval($v);
            if (strlen($v))
            {
                $k = $this->_removePrefix($k);
                $ret[$k] = $v;
            }
        }
        return $ret;
    }
    
    function getMultiObj($keys)
    {
        $values = $this->getMulti($keys);
        foreach($values as $key=>$value)
        {
            $values[$key] = MCore_Tool_Vbs::decode($value);
        }
        return $values;
    }
    
    function delete($key)
    {
        $key = $this->_addPrefix($key);
        return $this->mcache->delete($key);
    }
    
    /**
     * if the key is not exsit return false
     */
    function increment($key, $value)
    {
        $key = $this->_addPrefix($key);
        return $this->mcache->increment($key,$value);
    }
    
    /**
     * create if not exist
     */
    function incrementEx($key, $value = 1, $expire = 0)
    {
        $ret = $this->increment($key, $value);
        if ($ret !== false)
        {
            return $ret;
        }
        $ret = $this->add($key, $value, $expire);
        if ($ret !== false)
        {
            return $value;
        }
        return $this->increment($key, $value);
    }
    
    function decrement($key , $value)
    {
        $key = $this->_addPrefix($key);
        return $this->mcache->decrement($key,$value);
    }
    
    private	function _addPrefix($key)
    {
        if(strpos($key,$this->prefix) === false)
        {
            return $this->prefix . $key;
        }
        return $key;
    }
    
    private function _removePrefix($key)
    {
        if(strpos($key,$this->prefix) === 0)
        {
            return str_replace($this->prefix,"",$key);
        }
        return $key;
    }
}
?>