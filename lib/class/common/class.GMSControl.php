<?php
class GMSControl
{
	protected $tpl = null;
	protected $except = array("user_login"=>1,"user_join"=>1,"user_login_complete"=>1,"user_join_complete"=>1,"hub_login_complete"=>1,"batchRankReward"=>1,"makeDataModel"=>1);
	protected $ret = array();

	protected $action = array();
	protected $request = null;

	function __construct($action)
	{
		if($action[1] != "test" && $action[1] != "getOccupation" && $action[1] != "doRequestGoods")
		{
			//header("Content-type:application/json");
			$this->request = json_decode(file_get_contents("php://input"),true);
			//echo(file_get_contents("php://input"));

			if(empty($this->request["admin"]))
			{
				$this->setResult(array(),"201","Require Admin ID");
				exit;
			}
		}
	}

	function __destruct()
	{
		if(Util::getError() != "")
		{
			//$ret = array("retv"=>false,"msg"=>Util::getError());
			$this->setResult(array(),"200",Util::getError());

			//echo json_encode($ret);

			Util::setSession("error","");
		}


		//
		/*
		if(!empty($this->ret["data"]) && array_key_exists("user_info",$this->ret["data"]))
		{
			$level_model = DataModelLoader::getModel("m_level",$this->ret["data"]["user_info"]["user_level"]);

			$this->ret["level_info"] = $level_model;
			$this->ret["current_time"] = Util::getDate();
		}
		*/
//echo $this->ret;
		if(!empty($this->ret) && $action[1] != "doRequestGoods")
		{
			echo json_encode($this->ret);
		}
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
	 *  @ ���� ���� ���� ��������
	 *  @ _setPlayer( ����Ű )
	 */
	function _setPlayer( $uSeq ) {
		$this->user_seq = $uSeq;
	} // end of _setPlayer

	/**
	 *  @ ���� ���� ���� ��������
	 *  @ _setPlayerName( ������ )
	 */
	function _setDeviceKey( $uName ) {
		$this->device_key = $uName;
	} // end of _setPlayerName

	/**
	 *  @ ���� ���� ���� ��������
	 *  @ _getPlayer() return ����Ű
	 */
	function _getPlayer() {
		return $this->user_seq;
	} // end of _getPlayer

	/**
	 *  @ ���� ���� ���� ��������
	 *  @ _getPlayerName() return �����̸�
	 */
	function _getDeviceKey() {
		return $this->device_key;
	} // end of _getPlayerName
    
    /**
	*	Template_�� template ���� ���丮 setter
	*
	*	@param string template ���� ���丮
	*/
	public function set_template_dir( $template_dir )
	{
		$this->tpl->template_dir_arr = array($template_dir);
	}

	/**
	*	Template_�� template compile ���丮 setter
	*
	*	@param string template compile ���丮
	*/
	public function set_compile_dir( $compile_dir )
	{
		$this->tpl->compile_dir = $compile_dir;
	}

	/**
	*	template�� ���� html ������ �����ϴ� method
	*
	*	@param string $id template ���̵�
	*	@param string $file template�� ���� html �����̸�
	*/
	public function view_define( $id, $file )
	{
		$this->tpl->define( array( $id => $file ) );
	}

	/**
	*	template�� ��ü�� ���ڿ��� �����ϴ� method
	*
	*	@param string $key ���ڿ� ���̵�
	*	@param string $value ��ü�� ���ڿ� ��
	*/
	public function view_assign( $key, $value )
	{
		$this->tpl->assign( array( $key => $value ) );
	}

	/**
	*	template�� �����Ͽ� ȭ�鿡 ǥ�õ� html�� ����� print
	*
	*	@param string $fid ���� template ���̵�
	*/
	public function view_print( $fid )
	{
		$this->tpl->print_( $fid );
	}

	public function setResult( $data,$retv = "100",$errMsg="SUCCESS")
	{
		$this->ret = array("result"=>array("code"=>$retv,"msg"=>$errMsg),"res"=>$data);
	}
}
?>