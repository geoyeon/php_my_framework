<?
class HS
{
	private static $hs = null;
	private $_conn = null;
	private $db_info = array();
	private $_s = "";
	private $iNum = 1;
	private $result = null;
	private $field = "";

	public static function getInstance()
	{
		if(self::$hs != null)
		{
			return self::$hs;
		}

		self::$hs = new HS;

		return self::$hs;
	}

	public function connect($db_name="")
	{
		if($db_name == "")
		{
			return false;
		}

		if($this->_conn != null && $this->_s != $db_name)
		{
			$this->close();
		}
		else if($this->_conn != null && $this->_s == $db_name)
		{
			return $this->_conn;
		}
		else
		{
			$this->close();
		}

		global $db_conn;

		if(empty($db_conn[$db_name]))
		{
			return false;
		}

		$this->_s = $db_name;

		$this->db_info = $db_conn[$db_name];

		if(!$this->_conn = new HandlerSocket($this->db_info["DB_HOST"],$this->db_info["DB_PORT"]))
		{
			$this->writeLog("error",$this->_conn->getError()."(errorCode : Connection Error)",$this->_s,$this->sql);
			echo $this->_conn->getError();
			die();
		}
	}

	public function openIndex($iNum,$table,$iName="",$field="",$filter=array())
	{
		if($this->_conn == null)
		{
			return false;
		}

		$this->iNum = $iNum;

		if(empty($filter))
		{
			if(!$this->_conn->openIndex($this->iNum,$this->db_info["DB_NAME"],$table,$iName,$field))
			{
				$this->writeLog("error",$this->_conn->getError()."(errorCode : openIndex1 Error)",$this->_s,$table." : "." : ".$iName." : ".$field);
				//echo $this->_conn->getError();
				return false;
			}
		}
		else
		{
			$filter_text = implode(",",array_keys($filter));

			if(!$this->_conn->openIndex($this->iNum,$this->db_info["DB_NAME"],$table,$iName,$field,$filter_text))
			{
				$this->writeLog("error",$this->_conn->getError()."(errorCode : openIndex2 Error)",$this->_s,$table." : "." : ".$iName." : ".$field);
				//echo $this->_conn->getError();
				return false;
			}
		}
	}

	public function insert($iNum,$table,$iData=array())
	{
		$field = "";
		$data = array();

		if(empty($iData))
		{
			return false;
		}

		$field = implode(",",array_keys($iData));

		foreach($iData as $dt)
		{
			$data[] = $dt;
		}

		$this->openIndex($iNum,$table,"",$field);

		if(!$this->_conn->executeInsert($this->iNum,$data))
		{
			$this->writeLog("error",$this->_conn->getError()."(errorCode : Insert Error)",$this->_s,$table." : "." : ".$iName." : ".json_encode($iData));
			echo $this->_conn->getError();
			die();
		}

		return true;
	}
	
	public function update($iNum,$table,$cond,$cond_arr,$data_arr,$limit=1,$offset=0,$filters=array(),$in_key=-1,$in_values=array())
	{
		$field = "";
		$data = array();
		$cond_data = array();

		if(empty($data_arr))
		{
			return false;
		}

		$field = implode(",",array_keys($data_arr));

		foreach($data_arr as $dt)
		{
			$data[] = $dt;
		}

		$filter = array();

		foreach($filters as $ft)
		{
			$filter[] = $ft;
		}

		$this->openIndex($iNum,$table,"",$field,$filters);

		$this->_conn->executeSingle($this->iNum,$cond,$cond_arr,$limit,$offset,"U",$data,$filter,$in_key,$in_values);

		if($this->_conn->getError() != "")
		{
			$this->writeLog("error",$this->_conn->getError()."(errorCode : UPDATE Error)",$this->_s,$table." : "." : ".json_encode($cond_arr)." : ".json_encode($data_arr));
			echo $this->_conn->getError();
			die();
		}

		return true;
	}

	public function select($iNum,$table,$cond,$cond_arr,$field,$index="PRIMARY",$limit=1,$offset=0,$filters=array(),$in_key=-1,$in_values=array())
	{
		if(empty($cond_arr))
		{
			return false;
		}

		$this->openIndex($iNum,$table,$index,$field,$filters);

		$filter = array();

		foreach($filters as $ft)
		{
			$filter[] = $ft;
		}

		$this->result = $this->_conn->executeSingle($this->iNum,$cond,$cond_arr,$limit,$offset,null,null,$filter,$in_key,$in_values);

		if($this->_conn->getError() != "")
		{
			$this->writeLog("error",$this->_conn->getError()."(errorCode : SELECT Error)",$this->_s,$table." : "." : ".json_encode($cond_arr)." : ".$field." : ".$index);
			echo $this->_conn->getError();
			return false;
		}

		$this->field = $field;

		return true;
	}

	public function delete($iNum,$table,$cond,$iData=array())
	{
		$field = "";
		$data = array();

		if(empty($iData))
		{
			return false;
		}

		$field = implode(",",array_keys($iData));

		foreach($iData as $dt)
		{
			$data[] = $dt;
		}

		$this->openIndex($iNum,$table,"",$field);

		if(!$this->_conn->executeDelete($this->iNum,$cond,$data))
		{
			$this->writeLog("error",$this->_conn->getError()."(errorCode : Delete Error)",$this->_s,$table." : "." : ".$iName." : ".json_encode($iData));
			echo $this->_conn->getError();
			die();
		}

		return true;
	}

	public function getResult($row="")
	{
		$result = array();
		$field = $this->field;

		if(empty($this->result))
		{
			return $result;
		}

		if($field == "")
		{
			return $this->result;
		}

		$field_arr = explode(",",$field);

		$count = count($this->result);

		for($i=0;$i<$count;$i++)
		{
			for($j=0;$j<count($this->result[$i]);$j++)
			{
				$result[$i][$field_arr[$j]] = $this->result[$i][$j];
			}
		}

		if($row === "")
		{
			return $result;
		}
		else
		{
			return $result[$row];
		}
	}

	public function close()
	{
		$this->_conn = null;
		unset($this->_conn);
	}

	public function writeLog($errType,$errMsg,$dbname,$sql)
	{
		$logText = date("Y-m-d H:i:s")."\t[$dbname]\t$errType\t$errMsg\t$sql\n";
		echo $logText;
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