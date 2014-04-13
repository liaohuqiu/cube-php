<?php
interface IMCache
{
	function set($key, $value, $expire);
	
	function replace($key, $value, $expire);
	
	function add($key, $value, $expire);
	
	function get($key);
	
	function getMulti($keys);
	
	function delete($key);
	
	function increment($key, $value);
	
	function decrement($key, $value);
}

class Mid_MCache
{
	private $mcache;                //IMCache
	private $prefix = "kxm_";
	
	public function __construct()
	{
		if(0)
		{
			$this->mcache = DKXI_MCache::factory("kxm");
		}
		else
		{
			$this->mcache = new Kxi_MCache("kxm");
		}
	}
	
	function set($key, $value, $expire = 0)
	{
		$key = $this->_addPrefix($key);
		return $this->mcache->set($key,$value,$expire);
	}
	
	function setObj($key, $value, $expire = 0)
	{
		$this->set($key,Tool_Vbs::encode($value),$expire);
	}
	
	function add($key, $value, $expire = 0)
	{
		$key = $this->_addPrefix($key);
		return $this->mcache->add($key,$value,$expire);
	}
	
	function replace($key, $value, $expire = 0)
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
		return Tool_Vbs::decode($value);
	}
	
	/**
	 * 每批500，分次获取
	 */
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
			$values[$key] = Tool_Vbs::decode($value);
		}
		return $values;
	}
	
	/**
	 * 删除
	 */
	function delete($key)
	{
		$key = $this->_addPrefix($key);
		return $this->mcache->delete($key);
	}
	
	/**
	 * 键不存在，返回false，否则返回增加后的值
	 */
	function increment($key, $value)
	{
		$key = $this->_addPrefix($key);
		return $this->mcache->increment($key,$value);
	}
	
	/**
	 * 不存在则创建
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
	
	/**
	 * 减少
	 */
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
