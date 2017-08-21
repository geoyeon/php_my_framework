<?
class FB
{
	private static $redis = null;
	private $_conn = null;

	public static function getInstance()
	{
		if(self::$redis != null)
		{
			return self::$redis;
		}

		self::$redis = new FB();

		return self::$redis;
	}

	public function connect($db_name)
	{
		if($this->_conn != null)
		{
			$this->close();
		}

		global $db_conn;

		if(empty($db_conn[$db_name]))
		{
			return false;
		}

		$this->db_info = $db_conn[$db_name];

		$this->_conn = new Redis();

		if(!$this->_conn->connect($this->db_info["DB_HOST"],$this->db_info["DB_PORT"]))
		{
			$this->writeLog("error","(errorCode : Connection Error)",$this->db_info["DB_NAME"],$this->sql);
			die();
		}
		
		//$this->_conn->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_PHP);
	}

	public function setArray($isArray=true)
	{
		if($isArray)
		{
			$this->_conn->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_PHP);
		}
		else
		{
			$this->_conn->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_NONE);
		}
	}

	public function set($key,$value)
	{
		if(!$this->_conn->set($key,$value))
		{
			$this->writeLog("error","(errorCode : set Error)",$this->db_info["DB_NAME"],$key." : ".$value);
			die();
		}
	}

	public function del($key)
	{
		return $this->_conn->delete($key);
	}

	public function setTimeout($key,$sec)
	{
		$this->_conn->setTimeout($key,$sec);
	}

	public function get($key)
	{
		$ret = $this->_conn->get($key);

		return $ret;
	}

	public function getKeys($key_pattern)
	{
		$ret = $this->_conn->keys($key_pattern);

		return $ret;
	}

	public function zAdd($key,$sub_key,$value)
	{
		//$this->_conn->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_NONE);
		if($this->_conn->zAdd($key,$value,$sub_key) === false)
		{
			$this->writeLog("error","(errorCode : zAdd Error)",$this->db_info["DB_NAME"],$key." : ".$value." : ".$sub_key);
			return false;
		}

		//$this->_conn->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_PHP);

		return true;
	}

	public function zDelete($key,$sub_key)
	{
		$this->_conn->zDelete($key,$sub_key);
	}

	public function zRevRank($key,$sub_key)
	{
		//$this->_conn->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_NONE);
		$rank = $this->_conn->zRevRank($key,$sub_key);
		//$this->_conn->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_PHP);
		return $rank;
	}

	public function zRevRange($key,$start,$end,$is_score=false)
	{
		return $this->_conn->zRevRange($key,$start,$end,$is_score);
	}

	public function zSize($key)
	{
		return $this->_conn->zSize($key);
	}

	public function zScore($key,$sub_key)
	{
		return $this->_conn->zScore($key,$sub_key);
	}

	public function hSet($main_key,$sub_key,$value)
	{
		if($this->_conn->hSet($main_key,$sub_key,$value) === false)
		{
			$this->writeLog("error","(errorCode : hSet Error)",$this->db_info["DB_NAME"],$main_key." : ".$sub_key." : ".$value);
			return false;
		}
	}

	public function hMSet($main_key,$arr_value)
	{
		if($this->_conn->hMSet($main_key,$arr_value) === false)
		{
			$this->writeLog("error","(errorCode : hMSet Error)",$this->db_info["DB_NAME"],$main_key." : ".$sub_key." : ".json_encode($arr_value));
			return false;
		}
	}

	public function hGet($main_key,$sub_key)
	{
		return $this->_conn->hGet($main_key,$sub_key);
	}

	public function hGetAll($main_key)
	{
		return $this->_conn->hGetAll($main_key);
	}

	public function hDel($main_key,$sub_key)
	{
		return $this->_conn->hDel($main_key,$sub_key);
	}

	public function hIncrBy($main_key,$sub_key,$value)
	{
		return $this->_conn->hIncrBy($main_key,$sub_key,$value);
	}

	public function lPush($key,$value)
	{
		return $this->_conn->lPush($key,$value);
	}

	public function lRange($key,$start=0,$len=30)
	{
		return $this->_conn->lRange($key, $start, $len);
	}

	public function rPop($key)
	{
		return $this->_conn->rPop($key);
	}

	public function lSize($key)
	{
		return $this->_conn->lSize($key);
	}

	public function lRem($key,$value,$size=0)
	{
		return $this->_conn->lRem($key,$value,$size);
	}

	public function close()
	{
		$this->_conn->close();
		unset($this->_conn);
	}

	public function writeLog($errType,$errMsg,$dbname,$sql)
	{
		$logText = date("Y-m-d H:i:s")."\t[$dbname]\t$errType\t$errMsg\t$sql\n";
		//echo $logText;
		//return;
		$fp = fopen(LOG_DIR."dblog_".date("Ymd").".txt","a");
		//$fp = fopen("dblog_".date("Ymd").".txt","a");
		fwrite($fp,$logText);
		fclose($fp);
	}

	public function __destruct()
	{
		$this->close();
	}
}
?>