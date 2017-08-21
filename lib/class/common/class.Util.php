<?php
	class Util
	{
		public static function JsonReturn( $retv, $err = null )
		{
			if (!DEVMODE)
			{
				$json= array( 'retv'=>$retv );
			}
			else
			{
				if (!is_null( $err ))
				{
					$json = array( 'retv'=>$retv, 'err'=>$err );
				}
				else
				{
					$json= array( 'retv'=>$retv );
				}
			}
			
			echo json_encode( $json );
		}

		public static function cutString( $string, $num )
		{
			$cut_string = $string;
			$cut = $num;
			if(strlen($string) > $cut AND $cut != "0"){
			   for($j=0; $j<$cut-1; $j++){
				   if(ord(substr($cut_string, $j, 1))>127) $j++;
			   }
			   $cut_string  = sprintf("%s",substr($cut_string, 0, $j)."..");
			}
			else $cut_string = $string;
			
			return $cut_string;
		}
		
		 /********** 파라미터값 규칙 설정 함수 *****************/
		public static function addParamRule( $str, $rule )
		{ 
			$chk = 1; 
			$str = trim( $str );
			$rule = trim($rule);
			if( strlen( $str ) > 0 )
			{ 
				switch( $rule )
				{
					case 'kr' :
						if(preg_match("/[\xA1-\xFE\xA1-\xFE]/",$str)) $chk = 0; 
						//echo "str : $str rule : $rule  chk : $chk";
						break;
					case 'en' :
						if(preg_match("/[a-zA-Z]/",$str)) $chk = 0; 
						//echo "str : $str rule : $rule  chk : $chk";
						break;
					case 'int' :
						if(preg_match("/[0-9]/",$str)) $chk = 0; 
						//echo "str : $str rule : $rule  chk : $chk";
						break;
					case 'cd' :
						if(preg_match("/[A-Z0-9]/",$str)) $chk = 0; 
						//echo "str : $str rule : $rule  chk : $chk";
						break;
					case 'sp' :
						if(preg_match("/[!#$%^&*()?+=\/]/",$str)) $chk = 0; 
						//echo "str : $str rule : $rule  chk : $chk";
						break;
				}

				if($chk == 0) { 
					//echo $chk ."true";
					return true;
				}
				else
				{
					//echo $chk ."false";
					return false;
				}
			} 
			else
			{
				return false;
			}
		}

		//배열상의 특정필도 절대값을 반환
		public static function absArrVal($arrData,$field)
		{
			if(is_null($arrData))
			{
				return $arrData;
			}

			for($i=0;$i<count($arrData);$i++)
			{
				$arrData[$i][$field] = abs($arrData[$i][$field]);
			}

			return $arrData;
		}

		// request -> control and action
		public static function parse_request( $path )
		{
			$path_arr = parse_url( $path );
			$path = $path_arr["path"];
			
			if (strstr($path, '?')) 
			{
				$path = substr($path, 0, strpos($path, '?'));
			}

			$path = explode('/', trim($path, '/'));
			
			$control = $path[0];
			$action = "";
		
			if ( isset($path[1]) )
			{
				$action = $path[1];
			}
			
			if ( "" == $control )
			{
				$control = "Index";
			}

			if ( "" == $action )
			{
				$action = "index";
			}

			return array( $control, $action );
		}
        
        public function get_referer()
		{
			return Util::av( $_SERVER, "HTTP_REFERER" );
		}

		public static function av( $arr, $key )
		{
			if ( !is_array( $arr ) )
			{
				return "";
			}

			if ( !array_key_exists( $key, $arr ) )
			{
				return "";
			}

			return $arr[$key];
		}
        
        public static function acc_referer()
		{
			if(!strpos(Util::get_referer(),"feverapps.co.kr"))
			{
				Util::JsonReturn(false, '비정상적 접근');
				exit;
			}
		}

		// html용
		public static function go_back( $msg = "", $url = "" )
		{
			echo "<script>";
			
			if( $msg ) 
			{
				$msg = str_replace( "\"", "\\\"", $msg );
				echo 'alert("'.$msg.'");';
			}
			if( $url ) 
			{
				echo 'location.replace("'.$url.'");';
			}
			else 
			{
				echo 'history.go(-1);';
			}
			
			echo"</script>";
			die();
		}

		public static function alert( $msg )
		{
			echo "<script>";
			echo "alert('".$msg."');";
			echo "</script>";
		}

		public static function sendError($msg)
		{
			self::setSession("error",$msg);
			//exit();
		}

		public static function getError()
		{
			return self::getSession("error");
		}

		public static function getDate($time="",$type="Y-m-d H:i:s")
		{
			if($time != "")
			{
				return date($type,$time);
			}
			else
			{
				return date($type);
			}
		}

		public static function chk_email($email)
		{
			if( !preg_match("/^[_\.0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i", $email) )
			{
				return false;
			}

			return true;
		}

		public function getCharacterCount($char)
		{
			$byte = 0;

			for($i=0;$i<iconv_strlen($char,"utf-8");$i++)
			{
				$str = iconv_substr($char,$i,1,"utf-8");

				if(preg_match("/[\xA1-\xFE\xA1-\xFE]/",$str))
				{
					$byte += 3;
				}
				else if(preg_match("/[a-zA-Z0-9]/",$str))
				{
					$byte++;
				}
			}

			return $byte;
		}

		public static function setSession($key,$value)
		{
			$_SESSION[$key] = $value;
		}

		public static function getSession($key)
		{
			return Util::av($_SESSION,$key);
		}
		
		
		//-----암호화-----

		/*
		// blowfish
		public static function output_encrypt($str)
		{
			$str_c = mcrypt_encrypt(MCRYPT_BLOWFISH,DATA_ENCRYPT_KEY,$str,MCRYPT_MODE_ECB);

			return base64_encode($str_c);
		}

		public static function input_decrypt($str)
		{
			$str_d =  mcrypt_decrypt(MCRYPT_BLOWFISH,DATA_ENCRYPT_KEY,base64_decode($str),MCRYPT_MODE_ECB);

			return $str_d;
		}
		*/
		
		// XOR
		public static function output_encrypt($str,$enc_key=DATA_ENCRYPT_KEY)
		{
			$base64_str = base64_encode($str);

			$str_cnt = strlen($base64_str);
			$key_cnt = strlen($enc_key);

			for($i=0;$i<$str_cnt;$i++)
			{
				$val = substr($base64_str,$i,1);
				$key = $enc_key[$i%$key_cnt];

				$enc_val .= chr(ord($val) ^ ord($key));
			}

			//$str_c = mcrypt_encrypt(MCRYPT_BLOWFISH,DATA_ENCRYPT_KEY,$str,MCRYPT_MODE_ECB);

			//echo $enc_val;

			return $enc_val;
		}

		public static function input_decrypt($str,$enc_key=DATA_ENCRYPT_KEY)
		{
			$str_cnt = strlen($str);
			$key_cnt = strlen($enc_key);

			for($i=0;$i<$str_cnt;$i++)
			{
				$val = substr($str,$i,1);
				$key = $enc_key[$i%$key_cnt];

				$dec_val .= chr(ord($val) ^ ord($key));
			}

			//$str_c = mcrypt_encrypt(MCRYPT_BLOWFISH,DATA_ENCRYPT_KEY,$str,MCRYPT_MODE_ECB);

			$ret_val = base64_decode($dec_val);

			//echo $ret_val;

			return $ret_val;
		}

		public static function sendMessageGCM($message)
		{
			$apiKey = "AIzaSyBQsOX1GRpW5ML0JPosdty1ijZOO6oy9K0";
			//$apiKey = "air.GameHubTest";

			$cut = 1000;
			$cnt = count($message["device_key"]);

			$loop = ceil($cnt / $cut);

			for($i=0;$i<$loop;$i++)
			{
				$start = $i * $cut;
				$less = $cnt - $start;

				$send_cnt = $cut;

				if($less < $cut)
				{
					$send_cnt = $less;
				}

				if($send_cnt < 0)
				{
					break;
				}

				$d_key = array_slice($message["device_key"],$start,$send_cnt);
				$ch = curl_init();  

				$resultJson = array(
				"collapse_key" => "score_update" ,
				"time_to_live" => 1 ,
				"delay_while_idle" => true,
				"data" => array(
					"type" => "popup",
					"header" => $message["title"] ,
					"ticker" => $message["msg"] ,
					"title" => $message["msg"],
					"url"=>"air.com.com2us.soulstone.normal.freefull2.google.kr.android.common.test"
				   ),
				//"registration_ids" => array("APA91bH1V2X-DePwBMNkmh0ItS54Q69VXbXkLRj3xTNcdyOFSZlSgwN-Es3TKFjjetRf8sOmPEPQChMnyQfIIfmsBimSH1w-Bk1bcHDFYWS1c7DGj9PS9DIFoSdw0Ohaae0cMoYyZoFu")
				   "registration_ids" => $message["device_key"]
				) ;

				$data = json_encode($resultJson) ;


				//return $data;

				$headers = array(
				"Content-Type: application/json", 
				"Content-Length: ". strlen($data), 
				"Authorization: key=" . $apiKey  
				);


				curl_setopt($ch, CURLOPT_URL, "https://android.googleapis.com/gcm/send");
				curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
				$result = curl_exec($ch);
				curl_close($ch);

				$returnCode = 1;
			}

			//echo $result;
		}

		public static function sendPushEx($message)
		{
			$cut = 1000;
			$cnt = count($message["device_key"]);

			$loop = ceil($cnt / $cut);

			if(empty($message["title"]))
			{
				$message["title"] = "엘리시온 사가";
			}

			for($i=0;$i<$loop;$i++)
			{
				$start = $i * $cut;
				$less = $cnt - $start;

				$send_cnt = $cut;

				if($less < $cut)
				{
					$send_cnt = $less;
				}

				if($send_cnt < 0)
				{
					break;
				}

				$d_key = array_slice($message["device_key"],$start,$send_cnt);
				$ch = curl_init();  

				if($message["category"] > 0)
				{
					$resultJson = array(
					"userid_list" => $d_key ,
					"msg_alert" => $message["msg"],
					"msg_payload" => array(
						array("android_ticker" => $message["msg"]) ,
						array("android_title" => $message["title"]),
						array("push_popup_yn" => "1"),
						array("push_category"=>$message["category"])
					   )
					);
				}
				else
				{
					$resultJson = array(
					"userid_list" => $d_key ,
					"msg_alert" => $message["msg"],
					"msg_payload" => array(
						array("android_ticker" => $message["msg"]) ,
						array("android_title" => $message["title"]),
						array("push_popup_yn" => "1")
					   )
					);
				}

				if(!empty($message["push_image_url"]))
				{
					$resultJson["msg_payload"][] = array("push_image_url"=>"http://cdn4elsaga.selvas.com/push_img/".$message["push_image_url"]."_330.png");
					$resultJson["msg_payload"][] = array("push_popuptitle_image_url"=>"http://cdn4elsaga.selvas.com/push_img/".$message["push_image_url"]."_246.png");
				}

				$data = json_encode($resultJson) ;

				//return $data;

				$headers = array(
				"Content-Type: application/json", 
				"Content-Length: ". strlen($data), 
				"X-AccessToken:" . PUSH_ACCESS_TOKEN
				);


				curl_setopt($ch, CURLOPT_URL, "https://hub.selvas.com/api/sendPushEx");
				curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
				$result = curl_exec($ch);
				curl_close($ch);

				$returnCode = 1;
			}

			return $result;
		}

		public static function getCUrlData($url,$input=array())
		{
			$ch = curl_init();

			$data = json_encode($input);

			$headers = array(
				"Content-Type: application/json", 
				"Content-Length: ". strlen($data), 
				"Accept-Language:en-US"
			);

			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			//curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			$result = curl_exec($ch);
			curl_close($ch);
			//echo $result;
			return json_decode($result,true);
		}

		public static function getHubData($url,$input=array())
		{
			$ch = curl_init();

			$data = json_encode($input);

			$headers = array(
				"Content-Type: application/json", 
				"Content-Length: ". strlen($data), 
				"Accept-Language:en-US"
			);

			curl_setopt($ch, CURLOPT_URL, HUB_URL.$url);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			//curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
			$result = curl_exec($ch);
			curl_close($ch);
			//echo $result;
			return json_decode($result,true);
		}

		public static function createCode($length=7,$rest=0,$code="")
		{
			$str = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';

			if($rest == 0)
			{
				$rest = mt_rand(pow(36,($length-1))+1,pow(36,$length));
				//$code = $str[mt_rand(0,35)];
			}

			$code = $str[$rest%36].$code;
			$rest = floor($rest/36);

			if($rest > 0)
			{
				return self::createCode($length,$rest,$code);
			}
			else
			{
				return $code;
			}
		}

		public static function checkBillingSelvas($receipt,$is_ex="")
		{
			$send = json_encode($receipt);

			$ch = curl_init();  

			$headers = array(
			"Content-Type: application/json", 
			"Content-Length: ". strlen($send), 
			);

			if($is_ex == "ex")
			{
				$url = SELVAS_VERIFY_URL_EX;
			}
			else
			{
				$url = SELVAS_VERIFY_URL;
			}

			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $send);
			$result = curl_exec($ch);
			curl_close($ch);

			return $result;
		}

		public static function checkBillingTstore($receipt)
		{
			$data = json_decode($receipt,true);

			$result = $data["result"];

			$send_data = array("txid"=>$result["txid"],
								"appid"=>TSTORE_APP_ID,
								"signdata"=>$result["receipt"]
			);

			$send = json_encode($send_data);

			$ch = curl_init();  

			$headers = array(
			"Content-Type: application/json", 
			"Content-Length: ". strlen($send), 
			);


			curl_setopt($ch, CURLOPT_URL, TSTORE_VERIFY_URL);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $send);
			$result = curl_exec($ch);
			curl_close($ch);

			return $result;
		}

		public static function checkBillingIOS($receipt)
		{
			$send_data = array("receipt-data"=>base64_encode($receipt));

			$send = json_encode($send_data);

			$ch = curl_init();  

			$headers = array(
			"Content-Type: application/json", 
			"Content-Length: ". strlen($send), 
			);

			curl_setopt($ch, CURLOPT_URL, IOS_VERIFY_URL);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $send);
			$result = curl_exec($ch);
			curl_close($ch);

			return $result;
		}

		public static function sendMessageChatRoom($msg,$room_name,$user="admin",$server=CHAT_SERVER)
		{
			//return;
			require_once 'XMPPHP/XMPP.php';

			$conn = new XMPPHP_XMPP($server, 5222, 'system_msg', 'fpqldkxks', 'xmpphp', '', $printlog=false, $loglevel=XMPPHP_Log::LEVEL_VERBOSE);

			try {
				$conn->useEncryption(false);
				$conn->connect();
				$conn->processUntil('session_start');
				//$conn->presence();
				$conn->presence(NULL, "bot", $room_name."@conference.".$server."/".$user, "available");
				$conn->message($room_name."@conference.".$server, $msg,"groupchat");
				$conn->presence(NULL, "bot", $room_name."@conference.".$server."/".$user, "unavailable");
				$conn->disconnect();
			} catch(XMPPHP_Exception $e) {
				//die($e->getMessage());
			}
		}

		public static function sendPurchaseLogToBand($url)
		{
			$enc_url = HmacManager::getEncryptUrl($url,BAND_PROPERTY_KEY);
			$ret = file_get_contents($enc_url);

			return $ret;
		}

	}
?>