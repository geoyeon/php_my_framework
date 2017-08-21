<?php
	define("LANG","kr");

    define("DELIMETER",":");

	define("LOG_DIR","/usr/share/nginx/html/log/");
	define("BASIC_DIR","/usr/share/nginx/html");
	define("DATA_ENCRYPT_KEY","LEVIATHAN");
	
	define("MODE","TEST");
	define("ACCESS_ADMIN",false);

	// 寃뚯엫?ㅼ젙
	define("GOOGLE_GAME_INDEX",2554);
	define("TSTORE_GAME_INDEX",2521);
	define("OLLEH_GAME_INDEX",2592);
	define("UPLUS_GAME_INDEX",2593);
	define("IOS_GAME_INDEX",2553);

	// 移대뱶 ?뚰솚
	//define("FREE_CARD_TOTAL_PCT",9960);
	//define("MONEY_CARD_TOTAL_PCT",9955);

	//?덈꺼???ъ씤??
	define("LEVELUP_POINT",4);

	//理쒕? ?덈꺼
	define("MAX_LEVEL",60);

	//define("MONEY_CARD_TOTAL_PCT",118233);
	define("MAX_SKILL_LEVEL",10);

	//由ы봽?덉떆 ?쒓컙
	define("AP_DELAY",120);
	define("BP_DELAY",60);
	define("FIGHT_DELAY",3600);

	//?깆옣 ?뺤쑉
	define("BIGMON_ENC_PCT",15);
	define("RAREMON_ENC_PCT",0);
	define("FRIEND_ENC_PCT",35);

	//???쒕엻??
	define("GEM_DROP_PCT",10);

	//?寃?湲곌컙
	define("BATTLE_TERM",7);
	define("PVP_RESTRICT",10);
	define("MAX_FIGHT_CNT",3);


	define("NORMAL_MON_RATE",25);
	
	// DB_LEVIATHAN_HS_W :  ?몃뱾???뚯폆
	 $db_conn = array(
                "DB_LEVIATHAN"=>array("DB_HOST"=>"10.70.0.95","DB_USER"=>"leviathan","DB_PASS"=>"qkgkanxm)(*&","DB_NAME"=>"SOULSTONE","DB_CHAR"=>"SET NAMES utf8","DB_PORT"=>"3306"),
                "DB_LEVIATHAN_BATCH"=>array("DB_HOST"=>"10.70.0.95","DB_USER"=>"leviathan","DB_PASS"=>"qkgkanxm)(*&","DB_NAME"=>"SOULSTONE","DB_CHAR"=>"SET NAMES utf8","DB_PORT"=>"3306"),
				"DB_LEVIATHAN_REDIS"=>array("DB_HOST"=>"10.70.0.96","DB_PORT"=>6379,"DB_NAME"=>"DB_LEVIATHAN_REDIS"),
				"DB_LEVIATHAN_SESSION"=>array("DB_HOST"=>"10.70.0.96","DB_PORT"=>6378,"DB_NAME"=>"DB_LEVIATHAN_SESSION"),
                "DB_LEVIATHAN_LOG"=>array("DB_HOST"=>"10.70.0.95","DB_USER"=>"leviathan","DB_PASS"=>"qkgkanxm)(*&","DB_NAME"=>"SOULSTONE_LOG","DB_CHAR"=>"SET NAMES utf8","DB_PORT"=>"3306"),
				"DB_LEVIATHAN2"=>array("DB_HOST"=>"10.70.0.96","DB_USER"=>"leviathan","DB_PASS"=>"qkgkanxm)(*&","DB_NAME"=>"SOULSTONE","DB_CHAR"=>"SET NAMES utf8","DB_PORT"=>"3306"),
				"DB_LEVIATHAN2_LOG"=>array("DB_HOST"=>"10.70.0.96","DB_USER"=>"leviathan","DB_PASS"=>"qkgkanxm)(*&","DB_NAME"=>"SOULSTONE_LOG","DB_CHAR"=>"SET NAMES utf8","DB_PORT"=>"3306"),
                "test"=>array("DB_HOST"=>"10.70.0.95","DB_USER"=>"leviathan","DB_PASS"=>"qkgkanxm)(*&","DB_NAME"=>"test","DB_CHAR"=>"SET NAMES utf8","DB_PORT"=>"3306")
        );
	
	define("CHAT_SERVER","183.110.255.129");
	
		
	// 移대뱶 ?ㅽ궗 諛쒕룞 ?뺣쪧
	$skill_percent = array(
		70,50,40,20,20
	);

	// 怨듯썕?먯닔
	$battle_merit_m = array(
		"-4"=>"0.1","-3"=>"0.2","-2"=>"0.5","-1"=>"0.8","0"=>"1.0","1"=>"1.2","2"=>"1.5","3"=>"2.0"
	);


	// ?ㅽ궗 媛뺥솕 以??④낵
	$skill_effect = array(
		"1"=>"2","2"=>"5","3"=>"8","4"=>"10","5"=>"13","6"=>"15","7"=>"18","8"=>"20","9"=>"25"
	);


	// PVP 由ш렇 
	$battle_inning = 1;



	// ?섏쟾 諛섎났 蹂댁긽
	$dg_clear_reward = array(
		"1011"=>"1", "1021"=>"4","1031"=>"7"
	);

	//?붾뱶 蹂대꼫??
	$world_bonus = array(0,10,10,10);
	define("WORLD_BONUS_HOUR",24);

	//DW?뺤쓽
	define("DW_CASH_LOG","cash_var_log");
	define("DW_NONCASH_LOG","noncash_var_log");
	define("DW_LOGIN_LOG","login_log");
	define("DW_NEW_USER_LOG","new_user_log");

	//URL?뺤쓽
	define("OCCUPATION_URL","http://api.index.com2us.net/modules/gameuser/currentuser");
	define("HUB_URL","http://api.com2us.com/gameserver/");
	define("BAND_REQUEST_URL","http://gapi.band.us/3rd/v1/profile");
	define("SELVAS_VERIFY_URL","https://api.selvas.com/account/iap/v1/verify");
	define("SELVAS_VERIFY_URL_EX","https://api.selvas.com/account/iap/v1/verifyEx");

	//PUSH
	define("PUSH_ACCESS_TOKEN","2E6571BD-C323-46ED-AFDD-015D982EF54A");

	//BAND_PURCHASE_LOG
	define("BAND_PURCHASE_LOG","http://api.campmobile.com/bandGame_selvas/band_gapi/regBillingLog.json");
	define("CLIENT_ID","100212870");
	define("CLIENT_SECRET","IjqHy1jwmg2jrUzqfPw7TTrUF38KKhVr");
	define("BAND_PROPERTY_KEY","lyLBMnyNV70Y5JgYTldPs6axVpY8jaVUb0KfqvFeoYL2TWPJr6KlODDBOfxHiuDL");

	//GUILD_SETTING
	define("GUILD_BATTLE_START_DELAY",600);
	define("GUILD_BATTLE_TERM",3600);
	define("GUILD_BATTLE_END_TERM",300);
	define("GUILD_BATTLE_SHIELD_TERM",3600);
	define("ROUND_TERM",7);
	define("GUILD_MAX_LEVEL",10);
	define("GUILD_CREATE_FEE",100000);


	// 紐ъ뒪??
	/*
	$mon_level = array(
		"big"=>array(10,26)
	);
	
	$mon_material = array(
		"big"=>array(2)
	);
	*/


	/*
	// ?ш?紐?
	$rare_monster = array(
		"raremon_hp"=>"300000+(lv+1)*80000","raremon_character"=>"?곕퉴硫붿씠而?,"raremon_reward"=>"10302,10304,20303,20304,30304,30408"
	);
	*/

	//?쒕쾭二쇱냼
	$server = array("10.70.0.86","10.70.0.87","10.70.0.91","10.70.0.93","10.70.0.94");

	//?덉쇅IP
	$except_ip = array("106.245.244.250");

	//HTTPS?ъ슜
	define("USE_HTTPS",false);

	//愿묒궛由ъ썙??
	$mind_reward = array(
		0=>array("pct"=>45,"type"=>"GOLD","amount"=>3000,"item_index"=>40002),
		1=>array("pct"=>25,"type"=>"GOLD","amount"=>5000,"item_index"=>40002),
		2=>array("pct"=>15,"type"=>"ITEM","amount"=>1,"item_index"=>40003),
		3=>array("pct"=>15,"type"=>"ITEM","amount"=>1,"item_index"=>40004)
	);
?>
