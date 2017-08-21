<?php
class DB
{
	//require_once "../../inc/class/common/define.php";

	private $_h = "180.210.58.34";
	private $_s = "test";
	private $_u = "fever";
	private $_p = "vlqj34!@#";
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
		if(self::$conn != null)
		{
			self::$conn = null;
		}

		if(self::$conn == null)
		{
			self::$conn = new DB();
		}

		return self::$conn;
	}

	public function connect($db_name)
	{
		if($db_name != $this->_s)
		{
			$this->close();
		}

		global $db_conn;

		$this->_s = $db_name;

		$db_info = $db_conn[$db_name];

		try	{

			$this->db = new PDO( "mysql:host={$db_info['DB_HOST']};port={$db_info['DB_PORT']};dbname={$db_info['DB_NAME']}", "{$db_info['DB_USER']}", "{$db_info['DB_PASS']}", 
			array(PDO::MYSQL_ATTR_INIT_COMMAND => "{$db_info['DB_CHAR']}", PDO::ATTR_PERSISTENT => false));
			$this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->db->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY,true);
			$this->db->setAttribute(PDO::ATTR_AUTOCOMMIT,true);
		} 
		catch (PDOException $e) {
			$this->writeLog("error",$e->getMessage()."(errorCode : ".$e->getCode().")",$this->_s,$this->sql);
			print "Error!: " . $e->getMessage() . "<br/>";
			die();
		}
	}

	public function beginTransaction()
	{
		$this->db->beginTransaction();
	}

	public function inTransaction()
	{
		return $this->db->inTransaction();
	}

	public function commit()
	{
		$this->db->commit();
	}

	public function rollBack()
	{
		$this->db->rollBack();
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
			if(!empty($bindParam[$i]["type"]))
			{
				if($bindParam[$i]["type"] == "null")
				{
					$this->stmt->bindParam($i+1,$bindParam[$i]["value"],PDO::PARAM_NULL);
				}
				else if($bindParam[$i]["type"] == "str")
				{
					$this->stmt->bindParam($i+1,$bindParam[$i]["value"],PDO::PARAM_STR);
				}
				else if($bindParam[$i]["type"] == "bool")
				{
					$this->stmt->bindParam($i+1,$bindParam[$i]["value"],PDO::PARAM_BOOL);
				}
				else
				{
					$this->stmt->bindParam($i+1,$bindParam[$i]["value"],PDO::PARAM_INT);
				}
			}
			else
			{
				$this->stmt->bindParam($i+1,$bindParam[$i]["value"],$this->getConstantType($bindParam[$i]["value"]));
			}
		}
	}

	public function unsetBind()
	{
		$this->bindParam = null;
	}

	public function prepare($sql)
	{
		$this->unsetBind();

		$this->sql = $sql;

		$this->stmt = $this->db->prepare($sql);

		$this->curMode = "prepare";
	}

	public function execute($sql = "")
	{
		if($this->curMode == "prepare")
		{
			$this->bindAll();

			try
			{
				$this->curMode = "";
				$time1 = microtime(true);
				$this->stmt->execute();
				$time2 = microtime(true);
			}
			catch(PDOException $e)
			{
				//$this->writeLog("error",$e->getMessage()."(errorCode : ".$e->getCode().")",$this->_s,$this->stmt->debugDumpParams());
				$this->writeLog("error",$e->getMessage()."(errorCode : ".$e->getCode().")",$this->_s,$this->sql,$this->bindParam);
				return false;
			}
		}
		else
		{
			$this->sql = $sql;

			if(isset($this->stmt))
			{
				unset($this->stmt);
			}

			try
			{
				$time1 = microtime(true);
				$this->stmt = $this->db->query($this->sql);
				$time2 = microtime(true);
			}
			catch(PDOException $e)
			{
				$this->writeLog("error",$e->getMessage()."(errorCode : ".$e->getCode().")",$this->_s,$this->sql);
				return false;
			}
		}

		$this->unsetBind();

		if(($time2-$time1) > ($this->lazyTime/1000))
		{
			$this->writeLog("lazy",($time2-$time1)." ms",$this->_s,$this->sql);
		}

		return true;
	}

	public function getRowCount()
	{
		if(!empty($this->stmt))
		{
			return $this->stmt->rowCount();
		}
		else
		{
			return 0;
		}
	}

	public function fetchAll($row="")
	{
		if(!empty($this->stmt))
		{
			$this->stmt->setFetchMode($this->fetchMode);
		}

		$this->rslt = $this->stmt->fetchAll();

		$this->stmt = null;

		if($row === "")
		{
			return $this->rslt;
		}
		else
		{
			return $this->rslt[$row];
		}
	}

	public function lastInsertID()
	{
		return $this->db->lastInsertId();
	}

	public function close()
	{
		$this->db = null;
		unset($this->db);
		unset($this->stmt);
		//self::$conn = null;
	}

	public function writeLog($errType,$errMsg,$dbname,$sql,$param=array())
	{
		$logText = date("Y-m-d H:i:s")."\t[$dbname]\t$errType\t$errMsg\t$sql\t".json_encode($param)."\n";
		//echo $logText;
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