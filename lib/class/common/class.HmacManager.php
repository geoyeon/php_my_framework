<?php
class HmacManager {
		
	const MSGPAD = "msgpad=";
	const MD = "&md=";
	const QUESTION = "?";
	const AMPERCENT = "&";
	const MAX_MESSAGESIZE = 255;
	const MAX_KEY_BYTE = 1000;
	
	private static function getMessage($url, $cur_time) {
		$msg_url = substr($url, 0, self::MAX_MESSAGESIZE);
		
		return $msg_url.$cur_time; 
	}
	
	private static function hmac_sha1($data, $key='') {		
		if (extension_loaded('mhash')) {
			if ($key === '')
				$mhash = mhash(MHASH_SHA1, $data);
			else 
				$mhash = mhash(MHASH_SHA1, $data, $key);
			
		  return $mhash;
		}

		if ($key === '')
			return pack('H*',sha1($data));
		
		$key = str_pad($key,64,chr(0x00));
		
		if (strlen($key) > 64)
			$key = pack("H*",sha1($key));	

		$k_ipad =  $key ^ str_repeat(chr(0x36), 64) ;
		$k_opad =  $key ^ str_repeat(chr(0x5c), 64) ;
		
		$hmac = self::hmac_sha1($k_opad . pack("H*",sha1($k_ipad . $data)) );
		return $hmac;
	}

	private static function getMessageDigest($data, $key) {
		return urlencode(base64_encode(self::hmac_sha1($data, $key)));
	}
	
	private static function getTimeStamp() {
		$today  = mktime (date("H"),date("i"),date("s"),date("m") , date("d"), date("Y"))."000";

		return $today; 
	}
	
	private static function makeEncrytUrl($url, $key) {
		$cur_time = self::getTimeStamp();
		$message = self::getMessage($url, $cur_time);
		$md = self::getMessageDigest($message, $key);

		return self::MSGPAD.$cur_time.self::MD.$md;
	}
	

	public static function getEncryptUrl($url, $apikey) {
		
		$pos = strpos($url, self::QUESTION);
		
		if ($pos == false) {
			return $url.self::QUESTION.self::makeEncrytUrl($url, $apikey);
		} else {
			return $url.self::AMPERCENT.self::makeEncrytUrl($url, $apikey);
		}
		
	}
}
?>