<?php
class Control
{
	protected $tpl = null;
	protected $except = array("user_login"=>1,"user_join"=>1,"user_login_complete"=>1,"user_join_complete"=>1,"band_login_complete"=>1,"batchRankReward"=>1,"makeDataModel"=>1,"doPurchaseEnd"=>1,"createCoupon"=>1,"getCurrentTime"=>1,"attendCheckInEvent"=>1,"getCheckInEventCount"=>1,"setBattleRank"=>1,"makeShadowWorldModel"=>1,"Index"=>1,"batchDrawBox"=>1,"batchGuildBattle"=>1,"checkLoginType"=>1,"test2"=>1,"setAccountClear"=>1,"completeStory"=>1,"batchGuildTax"=>1);
	protected $ret = array();

	protected $action = array();
	protected $time = null;
	protected $param = null;

	function __construct($action)
	{
		Util::sendError("서비스가 종료되었습니다.");
		exit;

		$this->action = $action;

		$this->time = array_sum(explode(' ',microtime()));

		$this->isConstruct();

		if(!array_key_exists($action[1],$this->except))
		{
			// 테스트
			if($action[0] == "Test")
			{
				print_r($action);
			}
			else
			{
				if($action[0] != "Admin" && $action[0] != "GMS" && $action[0] != "BatchJob")
				{
					/*
					if(!empty($_SESSION["last_action"]) && (time() - $_SESSION["last_action_time"]) < 3)
					{
						Util::sendError("아직 기존 요청 처리가 완료되지 않았습니다.\n잠시후 다시 시도해 주세요");
						exit;
					}

					$_SESSION["is_action"] = $action[1];
					$_SESSION["last_action_time"] = time();
					*/
					$this->chkAction();

					$userLogic = UserLogic::getInstance();
					$userLogic->isLogin();
				}
				else
				{
					$addr = $_SERVER["REMOTE_ADDR"];
					
					//$addr_arr = explode(".",$addr);
					if($addr != "106.245.244.250" && substr($addr,0,6) != "10.70." && substr($addr,0,6) != "127.0.")
					{
						echo $addr;
						exit;
					}
				}
			}
		}

		//$this->req = $req;
		$this->getParameter();

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

	function chkAction()
	{
		if(strpos($this->action[1],"get") === false)
		{
			$fb = FB::getInstance();
			$fb->connect("DB_LEVIATHAN_REDIS");

			$user_action = $fb->get("user_action_".session_id());

			if(!empty($user_action))
			{
				if((time() - $user_action) < 3)
				{
					Util::sendError("아직 기존 요청이 완료되지 않았습니다.\n잠시후 다시 시도해 주세요");
					exit;
				}
			}

			$fb->set("user_action_".session_id(),time());
			$fb->setTimeout("user_action_".session_id(),3);
		}
	}

	function endAction()
	{
		$fb = FB::getInstance();
		$fb->connect("DB_LEVIATHAN_REDIS");

		$fb->del("user_action_".session_id());
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
		}

		if($this->action[0] == "Leviathan")
		{
			$this->ret["current_time"] = Util::getDate();
		}

		if(!empty($this->ret))
		{
			echo json_encode($this->ret);
		}

		$length = array_sum(explode(' ',microtime())) - $this->time;

		if($length > 3)
		{
			$this->lazyLog($this->action[0],$this->action[1],$_SESSION["user_seq"],$_REQUEST,$length);
		}

		$this->endAction();
		//$_SESSION["last_action"] = "";
		//$_SESSION["last_action_time"] = time();
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
		/*
		$pr = trim($_REQUEST["param"]);

		$this->param = json_decode($pr,true);
		*/
		//$this->param = json_decode(base64_decode(file_get_contents('php://input')),true);
		$this->param = json_decode(file_get_contents('php://input'),true);

		/*
		if(is_null($this->param))
		{
			Util::sendError("파라미터 오류");
			exit;
		}
		*/

		/*
		$logText = date("Y-m-d H:i:s")."\taction : ".$this->action[1]."\tparam : ".file_get_contents('php://input')."\n";
		//return;
		$fp = fopen(LOG_DIR."request_log_".date("Ymd").".txt","a");
		//$fp = fopen("dblog_".date("Ymd").".txt","a");
		fwrite($fp,$logText);
		fclose($fp);
		*/
	}

	function getParam($key="",$default=null)
	{
		if($key == "")
		{
			return "";
		}

		$value = Util::av($this->param,$key);

		if(is_array($value))
		{
			return $value;
		}
		else if(trim($value) == "" && $default != null)
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