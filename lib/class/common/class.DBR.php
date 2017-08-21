<?php
class DBR
{
	//require_once "../../inc/class/common/define.php";

	private $_h = "";
	private $_s = "";
	private $_u = "";
	private $_p = "";
	private $_c = "SET NAMES utf8";
	private static $conn = null;
	private $db = null;
	private $stmt = null;
	private $sql = "";
	private $bindParam = array();
	private $curMode = "";
	private $fetchMode = PDO::FETCH_ASSOC;
	private $lazyTime = 1000;
	private $rslt = null;
	private $cur = null;

	/*
	function __construct()
	{
		if($this->db == null)
		{
			try	{
				$this->db = new PDO( "mysql:host={$this->_h};port=3306;dbname={$this->_s}", "{$this->_u}", "{$this->_p}", 
				array(PDO::MYSQL_ATTR_INIT_COMMAND => "{$this->_c}", PDO::ATTR_PERSISTENT => true));
				$this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$this->db->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY,true);

			} 
			catch (PDOException $e) {
				print "Error!: " . $e->getMessage() . "<br/>";
				die();
			}
		}
	}
	*/

	public static function getInstance()
	{
		/*
		if(self::$conn != null)
		{
			self::$conn = null;
		}
		*/

		if(self::$conn == null)
		{
			self::$conn = new DBR();
		}

		return self::$conn;
	}

	public function connect($db_name)
	{
		/*
		if($this->db != null && $this->_s != $db_name)
		{
			$this->close();
		}
		else if($this->db != null && $this->_s == $db_name)
		{
			return false;
		}
		*/
		if($this->db != null)
		{
			$this->close();
		}

		global $db_conn;

		$this->_s = $db_name;

		$db_info = $db_conn[$db_name];

		if(!$this->db = sqlrcon_alloc($db_info["DB_HOST"],$db_info["DB_PORT"],"",$db_info["DB_USER"],$db_info["DB_PASS"],0,1))
		{
			$this->writeLog("error",sqlrcon_errorMessage($this->db)."(errorCode : Connection Error)",$this->_s,$this->sql);
			echo sqlrcon_errorMessage($this->db);
			die();
		}
		$this->cur = sqlrcur_alloc($this->db);

		sqlrcur_sendQuery($this->cur,$this->_c);
		//sqlrcon_endSession($this->db);
	}

	public function setFetchMode($fetchMode = "")
	{
		if($fetchMode != "")
		{
			$this->fetchMode = $fetchMode;
		}
	}

	public function setBind($value,$type = "",$order = "")
	{
		if($order == "")
		{
			$this->bindParam[] = array("value"=>$value,"type"=>$type);
		}
		else
		{
			$this->bindParam[$order-1] = array("value"=>$value,"type"=>$type);
		}
	}

	public function bindAll()
	{
		$arr_cnt = count($this->bindParam);
		$bindParam = $this->bindParam;

		for($i=0;$i<$arr_cnt;$i++)
		{
			if($bindParam[$i]["type"] == "float")
			{
				sqlrcur_inputBind($this->cur,strval(($i+1)),$bindParam[$i]["value"]);
			}
			else
			{
				sqlrcur_inputBind($this->cur,strval(($i+1)),strval($bindParam[$i]["value"]));
			}
		}
	}

	public function unsetBind()
	{
		$this->bindParam = null;
	}

	public function prepare($sql)
	{
		$this->sql = $sql;

		sqlrcur_prepareQuery($this->cur,$sql);

		$this->curMode = "prepare";
	}

	public function execute($sql = "")
	{
		if($this->db == null)
		{
			$this->connect($this->_s);
		}

		if($this->curMode == "prepare")
		{
			$this->bindAll();
			
			$time1 = microtime(true);
			if(!sqlrcur_executeQuery($this->cur))
			{
				$this->writeLog("error",sqlrcur_errorMessage($this->cur)."(errorCode : "."Prepare Execute Error".")",$this->_s,$this->sql);
				return false;	
			}
			$time2 = microtime(true);

			sqlrcur_clearBinds($this->cur);

			$this->curMode = "";
		}
		else
		{
			$this->sql = $sql;

			$time1 = microtime(true);
			if(!sqlrcur_sendQuery($this->cur,$this->sql))
			{
				$this->writeLog("error",sqlrcur_errorMessage($this->cur)."(errorCode : "."Execute Error".")",$this->_s,$this->sql);
				return false;	
			}
			$time2 = microtime(true);
		}


		$this->unsetBind();
		sqlrcon_endSession($this->db);

		if(($time2-$time1) > ($this->lazyTime/1000))
		{
			$this->writeLog("lazy",($time2-$time1)." ms",$this->_s,$this->sql);
		}

		return true;
	}

	public function fetchAll($row="")
	{
		if($row === "")
		{
			for($i=0;$i<sqlrcur_rowCount($this->cur);$i++)
			{
				$this->rslt[] = sqlrcur_getRowAssoc($this->cur,$i);
			}
		}
		else
		{
			$this->rslt = sqlrcur_getRowAssoc($this->cur,$row);
		}

		return $this->rslt;
	}

	public function close()
	{
		//sqlrcon_endSession($this->db);
		sqlrcur_free($this->cur);
		sqlrcon_free($this->db);
		$this->rslt = null;
		//unset($this->stmt);
		//self::$conn = null;
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

	public function getConstantType($value)
	{
		if( is_int( $value ) )
		{
			return PDO::PARAM_INT;
		}
		else if( is_bool( $value ) )
		{
			return PDO::PARAM_BOOL;
		}
		else if( is_null( $value ) )
		{
			return PDO::PARAM_NULL;
		}
		else
		{
			return PDO::PARAM_STR;
		}
	}

	function __destruct()
	{
		$this->close();
	}
}
?>