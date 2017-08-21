<?php
class Request
{
	/** $_POST, $_GET 두개를 합쳐놓은 array */
	private $data_arr = NULL;

	/** 사용자 request의 control */
	private $control_name = "";

	/** 사용자 request의 action */
	private $action_name = "";

	/**
	*	Constructor for Request Class
	*
	*	@param string $control_name 사용자 request의 control
	*	@param string $action_name 사용자 request의 action
	*/
	function __construct( $control_name, $action_name )
	{
		$this->data_arr = $_POST + $_GET;
		$this->set_control_name( $control_name );
		$this->set_action_name( $action_name );
	}

	/**
	*	control name setter
	*
	*	@param string $control_name 사용자 request의 control
	*/
	public function set_control_name( $control_name )
	{
		$this->control_name = $control_name;
	}

	/**
	*	action name setter
	*
	*	@param string $action_name 사용자 request의 action
	*/
	public function set_action_name( $action_name )
	{
		$this->action_name = $action_name;
	}

	public function set_request_type( $request_type )
	{
		$this->request_type = $request_type;
	}

	/**
	*	control name getter
	*
	*	@return string 
	*/
	public function get_control()
	{
		return $this->control_name;
	}

	/**
	*	action name getter
	*
	*	@return string
	*/
	public function get_action()
	{
		return $this->action_name;
	}

	public function get_request_type()
	{
		return $this->request_type;
	}

	/**
	*	host name getter
	*
	*	@return string
	*/
	public function get_host()
	{
		return "http://".$_SERVER["HTTP_HOST"];
	}

	/**
	*	url getter ( includes control and action )
	*
	*	@return string
	*/
	public function get_url()
	{
		return $this->get_host()."/".$this->get_control()."/".$this->get_action();
	}

	/**
	*	query string getter
	*
	*	@return string
	*/
	public function get_query_string()
	{
		return $_SERVER["QUERY_STRING"];
	}

	/**
	*	request url getter ( includes url and query string )
	*
	*	@return string
	*/
	public function get_request_url()
	{
		return "/".$this->get_control()."/".$this->get_action()."/?".$this->get_query_string();
	}

	public function get_referer()
	{
		return Util::av( $_SERVER, "HTTP_REFERER" );
	}

	public function get_referer_url()
	{
		$refer = $this->get_referer();
		$pos = strpos( $refer, "?" );
		if ( $pos )
		{
			$refer = substr( $refer, 0, $pos );
		}
		
		if ( substr( $refer, -1, 1 ) != "/" )
		{
			$refer .= "/";
		}

		return $refer;
	}

	public function check_referer( $key_arr )
	{
		return true;
		$refer = $this->get_referer_url();
		$refer_arr = Util::parse_request( $refer );

		for( $i=0;$i<count($key_arr);$i++ )
		{
			$c_key_arr = Util::parse_request( $key_arr[$i] );
			if ( $c_key_arr[0] == $refer_arr[0] && $c_key_arr[1] == $refer_arr[1] )
			{
				return true;
			}
		}
		
		trigger_error( $refer."에서 ".$this->get_url()."에 비정상적으로 접근했습니다" );
		Util::debug_array( $_SERVER );
		die();
	}

	public function set_value( $key, $value )
	{
		if ( $this->is_exist( $key ) )
		{
			$this->data_arr[$key] = $value;
		}
		else
		{
			$this->data_arr += array( $key => $value );
		}
	}

	/**
	*	$_POST, $_GET value getter
	*   @param datatype str = string type, int = number type
	*	@return variant
	*/
	public function get_value( $key, $data_type = "str" )
	{
		$default = NULL;

		if ( "str" == $data_type || "array" == $data_type )
		{
		}
		else if ( "int" == $data_type )
		{
			$default = intval( $default );
		}
		else
		{
			trigger_error( "Wrong datatype error" );
		}

		if ( "" == $key )
		{
			return $default;
		}
	
		if ( !array_key_exists( $key, $this->data_arr ) )
		{
			return $default;
		}

		if ( "str" == $data_type )
		{
			$default = strval( trim($this->data_arr[$key]) );
			$default = Util::html_chars( $default );

		}
		else if ( "int" == $data_type )
		{
			$default = intval(trim($this->data_arr[$key]));
		}
		else if ( "array" == $data_type )
		{
			$default = $this->data_arr[$key];
			if ( !is_array( $default ) )
			{
				trigger_error( "Wrong datatype error:this type is not array" );
			}

			for( $i=0; $i<count($default); $i++ )
			{	
				$default[$i] = trim($default[$i]);
				$default[$i] = Util::html_chars( $default[$i] );
			}
		}
		else
		{
			trigger_error( "Wrong datatype error" );
		}

		return $default;
	}

	/**
	*	$_POST, $_GET value getter
	*   @param datatype str = string type, int = number type
	*	@return variant
	*/
	public function get_nc_value( $key, $data_type = "str" )
	{
		$default = NULL;

		if ( "str" == $data_type || "array" == $data_type )
		{
		}
		else if ( "int" == $data_type )
		{
			$default = intval( $default );
		}
		else
		{
			trigger_error( "Wrong datatype error" );
		}

		if ( "" == $key )
		{
			return $default;
		}
	
		if ( !array_key_exists( $key, $this->data_arr ) )
		{
			return $default;
		}

		if ( "str" == $data_type )
		{
			$default = strval( trim($this->data_arr[$key]) );
		}
		else if ( "int" == $data_type )
		{
			$default = intval( trim($this->data_arr[$key]) );
		}
		else if ( "array" == $data_type )
		{
			$default = $this->data_arr[$key];
			if ( !is_array( $default ) )
			{
				trigger_error( "Wrong datatype error:this type is not array" );
			}
		}
		else
		{
			trigger_error( "Wrong datatype error" );
		}

		return $default;
	}

	public function is_exist( $key )
	{
		if ( array_key_exists( $key, $this->data_arr ) )
		{
			return true;
		}

		false;
	}

	/**
	*	$_POST, $_GET 의 내용을 query string 형태로 만들어 리턴한다.
	*
	*	@return string
	*/
	public function convert_req_string( $skip_arr = false )
	{	
		$query_string = "";

		foreach( $this->data_arr as $key => $value )
		{
			if ( !$skip_arr || array_key_exists( $key, $skip_arr ) )
			{
				continue;	
			}

			if ( "" != $query_string )
			{
				$query_string .= "&";
			}
			
			$query_string .= $key."=".Util::html_chars($value);
		}

		return $query_string;
	}

	/**
	*	$_POST, $_GET 의 내용을 hidden field 형태로 만들어 리턴한다.
	*
	*	@return string
	*/
	public function convert_hidden_field( $skip_arr = false )
	{
		$result = "";

		foreach( $this->data_arr as $key => $value )
		{	
			if ( is_array($skip_arr) && array_key_exists( $key, $skip_arr ) )
			{
				continue;	
			}

			$str = "<input type='hidden' id='$key' name='$key' value='$value'>";
			$result .= $str;
		}

		return $result;
	}

	public function to_string()
	{
		ob_start();
		
		print "POST\n";
		print_r( $_POST );
		print "GET\n";
		print_r( $_GET );
		$msg = ob_get_clean();

		return $msg;
	}
}

?>
