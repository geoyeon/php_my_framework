<?php
class CMSControl
{
	protected $tpl = null;
	protected $except = array("user_login"=>1,"user_join"=>1,"user_login_complete"=>1,"user_join_complete"=>1,"hub_login_complete"=>1,"batchRankReward"=>1,"makeDataModel"=>1,"doPurchaseEnd"=>1,"createCoupon"=>1,"getCurrentTime"=>1,"modifyCardData"=>1);
	protected $ret = array();

	protected $action = array();
	protected $time = null;

	function __construct($action)
	{
		$this->action = $action;

		$this->time = array_sum(explode(' ',microtime()));

		require_once("Template_/Template_.class.php");	// 외부 파일 포함
		$this->tpl = new Template_();
		$this->tpl->template_dir_arr = array("_template","_template/common");
		$this->view_define("TPL_MAIN","tpl_main.tpl");

		
		// define.php MODE값 비교
		if(MODE == "SERVICE")
		{
			// blowfish 암호화
			foreach($_POST as $key=>$value)
			{
				$_POST[$key] = Util::input_decrypt($value);
			}
		}
	}

	function __destruct()
	{
		if(Util::getError() != "")
		{
			//$ret = array("retv"=>false,"msg"=>Util::getError());
			$this->setResult(array(),false,Util::getError());

			//echo json_encode($ret);
			Util::setSession("error","");
		}


		//
		if(!empty($this->ret["data"]) && array_key_exists("user_info",$this->ret["data"]))
		{
			$level_model = DataModelLoader::getModel("m_level",$this->ret["data"]["user_info"]["user_level"]);

			$this->ret["level_info"] = $level_model;
			$this->ret["current_time"] = Util::getDate();
		}

		if(!empty($this->ret))
		{
			echo json_encode($this->ret);
		}

		$length = array_sum(explode(' ',microtime())) - $this->time;

		if($length > 2)
		{
			$this->lazyLog($this->action[0],$this->action[1],$_SESSION["user_seq"],$_REQUEST,$length);
		}
	}

	function isConstruct()
	{
		global $construct;

		if(!empty($construct) && $this->action[0] != "CMS" && $this->action[0] != "BatchJob")
		{
			$isExp = false;

			$start_time = strtotime($construct["start"]);
			$end_time = strtotime($construct["end"]);
			$cur_time = time();

			if($construct["stat"] != "construct")
			{
				global $except_ip;

				foreach($except_ip as $ip)
				{
					if($_SERVER["REMOTE_ADDR"] == $ip)
					{
						$isExp = true;
						break;
					}
				}
			}
			
			if($cur_time >= $start_time && $cur_time <= $end_time && $isExp == false)
			{
				$this->setResult(array("start_time"=>$construct["start"],"end_time"=>$construct["end"]),false,"UNDER_CONSTRUCT");
				exit;
			}
		}
	}

	function lazyLog($action1,$action2,$user_seq,$param=array(),$length)
	{
		$logText = date("Y-m-d H:i:s")."\t$length\t[$action1/$action2 - $user_seq]\t".json_encode($param)."\n";
		//echo $logText;
		//return;
		$fp = fopen(LOG_DIR."lazy_action_".date("Ymd").".txt","a");
		//$fp = fopen("dblog_".date("Ymd").".txt","a");
		fwrite($fp,$logText);
		fclose($fp);
	}

	function getParameter()
	{
		$pr = trim($_REQUEST["param"]);

		$this->param = json_decode($pr,true);
	}

	function getParam($key="",$default=null)
	{
		if($key == "")
		{
			return "";
		}

		$value = Util::av($this->param,$key);

		if(trim($value) == "" && $default != null)
		{
			return $default;
		}
		else
		{
			return trim($value);
		}
	}

	function chkError()
	{
		if(Util::getSession("error"))
		{
			$m_error = MurimModelLoader::getModel("m_error",Util::getSession("error"));
			
			$this->result = array("retv"=>false,"errorCD"=>$m_error["index"],"errorMsg"=>$m_error[LANG]);
		}

		Util::setSession("error",null);
	}


	/**
	 *  @ 나의 게임 정보 가져오기
	 *  @ _setPlayer( 유저키 )
	 */
	function _setPlayer( $uSeq ) {
		$this->user_seq = $uSeq;
	} // end of _setPlayer

	/**
	 *  @ 나의 게임 정보 가져오기
	 *  @ _setPlayerName( 유저명 )
	 */
	function _setDeviceKey( $uName ) {
		$this->device_key = $uName;
	} // end of _setPlayerName

	/**
	 *  @ 나의 게임 정보 가져오기
	 *  @ _getPlayer() return 유저키
	 */
	function _getPlayer() {
		return $this->user_seq;
	} // end of _getPlayer

	/**
	 *  @ 나의 게임 정보 가져오기
	 *  @ _getPlayerName() return 유저이름
	 */
	function _getDeviceKey() {
		return $this->device_key;
	} // end of _getPlayerName
    
    /**
	*	Template_용 template 파일 디렉토리 setter
	*
	*	@param string template 파일 디렉토리
	*/
	public function set_template_dir( $template_dir )
	{
		$this->tpl->template_dir_arr = array($template_dir);
	}

	/**
	*	Template_용 template compile 디렉토리 setter
	*
	*	@param string template compile 디렉토리
	*/
	public function set_compile_dir( $compile_dir )
	{
		$this->tpl->compile_dir = $compile_dir;
	}

	/**
	*	template에 사용될 html 파일을 지정하는 method
	*
	*	@param string $id template 아이디
	*	@param string $file template에 사용된 html 파일이름
	*/
	public function view_define( $id, $file )
	{
		$this->tpl->define( array( $id => $file ) );
	}

	/**
	*	template에 교체될 문자열을 지정하는 method
	*
	*	@param string $key 문자열 아이디
	*	@param string $value 교체될 문자열 값
	*/
	public function view_assign( $key, $value )
	{
		$this->tpl->assign( array( $key => $value ) );
	}

	/**
	*	template을 적용하여 화면에 표시될 html로 만들고 print
	*
	*	@param string $fid 메인 template 아이디
	*/
	public function view_print( $fid )
	{
		$this->tpl->print_( $fid );
	}

	public function setResult( $data,$retv = true,$errMsg="")
	{
		$this->ret = array("retv"=>$retv,"data"=>$data,"errMsg"=>$errMsg);
	}
}
?>