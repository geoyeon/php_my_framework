<?php
	// global function

	function __autoload( $class_name )
	{
		$isExist = false;

		foreach(explode(":",ini_get("include_path")) as $dir)
		{
			if(file_exists($dir."class.".$class_name.".php"))
			{
				$isExist = true;
				break;
			}
		}

		if($isExist)
		{
			/*
			if($_SESSION["user_seq"] == 256)
			{
				echo $class_name;
			}
			*/
			require_once "class.".$class_name.".php";
		}
		else
		{
			echo "잘못된 접근 - Class Not Found - $class_name";
			exit;
		}
	}
    
    
	session_start();
	date_default_timezone_set("Asia/Seoul");
	error_reporting(E_ALL & ~E_NOTICE);
	header("Content-Type: text/html; charset=UTF-8");

	//$def_inc_path = "/usr/local/apache/htdocs/lib";
	$def_inc_path = "/usr/share/nginx/html/conf";

    require_once $def_inc_path."/define.php";

	ini_set("include_path",BASIC_DIR."/lib/class/".DELIMETER.BASIC_DIR."/lib/class/common/".DELIMETER);


	if(is_file(BASIC_DIR."/conf/model.php"))
	{
		require_once BASIC_DIR."/conf/model.php";
	}
	else
	{
		Util::sendError("CAN_NOT_FIND_MODEL_DATA");
		exit;
	}

	if(is_file(BASIC_DIR."/conf/construct.php"))
	{
		require_once BASIC_DIR."/conf/construct.php";
	}

	 //require_once $def_inc_path."/class/define/class.define_rate.php";

	//$inc_path = ini_get("include_path");
    
    $path = $_SERVER["REQUEST_URI"];
	$arr = Util::parse_request( $path );
    
    $control = $arr[0];
   	$action = $arr[1]."_action";

	
	if($control == "Index" && $action == "index_action")
	{
		echo "<script>location.href=\"/index.html\";</script>";
		exit;
	}

	$c = new $control($arr);
	
	if(!method_exists($c,$action))
	{
		Util::sendError("INVALID_ACTION");
	}
	else
	{
		call_user_func(array($c,$action));
	}
?>