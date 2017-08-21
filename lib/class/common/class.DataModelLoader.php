<?
class DataModelLoader
{
	private static $loader = null;
	/*
	private static $m_card = null;
	private static $m_enhance = null;
	private static $m_skill_enhance = null;
	private static $m_quest = null;
	private static $m_level = null;
	private static $m_raid = null;
	private static $m_raid_phase = null;
	private static $m_treasure = null;
	private static $m_guild_level = null;
	private static $m_guild_enemy = null;
	private static $m_mission = null;
	private static $m_world = null;
	private static $m_dungeon = null;
	private static $m_material = null;
	private static $m_recipe = null;
	private static $m_story = null;
	private static $m_daily_quest = null;
	private static $m_version = null;
	private static $m_check_in = null;
	private static $m_tip = null;
	private static $m_raremon = null;
	private static $m_dg_coordinate = null;
	private static $m_summon = null;
	private static $m_raid_multicard = null;
	private static $m_item_salelist = null;
	private static $m_dg_bonustime = null;
	private static $m_enhancer_drop = null;
	private static $m_event_schedule = null;
	private static $m_battle_reward = null;
	private static $m_cash_shop = null;
	private static $m_coupon = null;
	private static $m_invitefriend = null;
	private static $m_ghost_world = null;
	private static $m_ghost_world_reward = null;
	*/

	/*
	public static function getInstance()
	{
		if(self::$loader != null)
		{
			return self::$loader;
		}

		self::$loader = new MurimModelLoader;

		return self::$loader;
	}
	*/

	public static function getModel($m,$index,$level=0)
	{
		/*
		$model = self::$$m;

		if(empty($model))
		{
			$model = self::getModelAll($m);
		}
		*/
		$model = self::getModelAll($m);

		if($level > 0)
		{
			return $model[$index][$level];
		}
		else
		{
			return $model[$index];
		}
	}

	public static function getModelAll($m)
	{
		/*
		$model = self::$$m;

		if($model != null)
		{
			return $model;
		}
		*/
		/*
		$fb = FB::getInstance();
		$fb->connect("DB_MURIM_REDIS");
		$model = $fb->get($m);
		*/
		/*
		$model = apc_fetch($m);

		if(empty($model))
		{
			$db = DBR::getInstance();
			$db->connect("DB_MURIM_RELAY");
			$db->execute("SELECT * from $m");

			$model = $db->fetchAll();
			$model = self::arrangeModel($model);

			//$fb->set($m,$model);

			apc_store($m,$model);
		}
		*/

		global $$m;

		$model = $$m;

		return $model;
	}

	public static function arrangeModel($data,$subkey=true)
	{
		$ar_data = array();
		
		foreach($data as $dt)
		{
			if($subkey)
			{
				if($dt["sub_grade"] > 0)
				{
					$ar_data[$dt["index"]][$dt["sub_grade"]] = $dt;
				}
				else if($dt["level"] > 0)
				{
					$ar_data[$dt["index"]][$dt["level"]] = $dt;
				}
				else if($dt["time"] > 0)
				{
					$ar_data[$dt["index"]][$dt["time"]] = $dt;
				}
				else if(is_array($dt) && array_key_exists("grade_diff",$dt))
				{
					$ar_data[$dt["index"]][$dt["grade_diff"]] = $dt;
				}
				else
				{
					$ar_data[$dt["index"]] = $dt;
				}
			}
			else
			{
				$ar_data[$dt["index"]] = $dt;
			}
		}

		return $ar_data;
	}

	public static function makeXML()
	{
		/*
		$hs = HS::getInstance();
		$hs->connect("DB_MURIM_HS_R");

		$hs->select(100,"ref_version",">",array(0),"name,version","PRIMARY",100,0);

		$ret = $hs->getResult();

		foreach($ret as $item)
		{
			$db = DBR::getInstance();
			$db->connect("DB_MURIM_RELAY");

			$sql = "SELECT * FROM {$item['name']}";
			$db->execute($sql);

			$rslt = $db->fetchAll();

			$model = self::arrangeModel($rslt);

			apc_store($item["name"],$model);

			$xml = simplexml_load_string("<?xml version='1.0' encoding='UTF-8'?><{$item['name']}></{$item['name']}>");
			$xml->addChild("version",$item["version"]);

			$listXML = $xml->addChild("list");

			foreach($rslt as $dt)
			{
				$listItem = $listXML->addChild("item");

				foreach($dt as $key=>$a)
				{
					$listItem->addChild($key,$a);
				}
			}

			$xmlStr = $xml->asXML();
			$fs = fopen("xml/".$item["name"]."_kr.xml","w");
			fwrite($fs,$xmlStr);
			fclose($fs);
		}

		$xml = simplexml_load_string("<?xml version='1.0' encoding='UTF-8'?><version_list></version_list>");

		foreach($ret as $item)
		{
			$xml->addChild($item["name"],$item["version"]);
		}

		$xmlStr = $xml->asXML();

		$fs = fopen("xml/version_kr.xml","w");
		fwrite($fs,$xmlStr);
		fclose($fs);

		echo system("zip -P murim xml/murimDataPass.zip xml/*.xml");
		*/

		$db = DBR::getInstance();
		$db->connect("DB_MURIM_RELAY");

		$sql = "SELECT * FROM m_error";
		$db->execute($sql);

		$rslt = $db->fetchAll();

		$model = self::arrangeModel($rslt);

		$xml = simplexml_load_string("<?xml version='1.0' encoding='UTF-8'?><m_error></m_error>");
		$xml->addChild("version",$item["version"]);

		$listXML = $xml->addChild("list");

		foreach($rslt as $dt)
		{
			$listItem = $listXML->addChild("item");

			foreach($dt as $key=>$a)
			{
				$listItem->addChild($key,$a);
			}
		}

		$xmlStr = $xml->asXML();
		$fs = fopen("xml/m_error_kr.xml","w");
		fwrite($fs,$xmlStr);
		fclose($fs);
	}

	public static function loadXML()
	{
		$f = opendir("xml/");

		$a = array();

		while($file = readdir($f))
		{
			if(substr($file,-4) == ".xml" && substr($file,0,2) == "m_")
			{
				$a[] = $file;
			}
		}

		closedir($f);

		//print_r($a);
		//exit;

		$f = fopen("conf/model.php","w+");
		fwrite($f,"<?php\n");

		for($i=0;$i<count($a);$i++)
		{
			$str = simplexml_load_file("xml/".$a[$i]);
			$json = json_encode($str);
			$array = json_decode($json,true);

			if($a[$i] == "m_card.xml")
			{
				$array = self::arrangeModel($array["list"]["item"],false);

				$free_pct = 0;
				$money_pct = 0;
				$race1_pct = 0;
				$race2_pct = 0;
				$race3_pct = 0;
				$buff_money_pct = 0;
				$cnt = 0;

				foreach($array as $arr)
				{
					if($arr["freepack_weight"] > 0)
					{
						$free_pct += $arr["freepack_weight"];
					}
					
					if($arr["moneypack_weight"] > 0)
					{
						$money_pct += $arr["moneypack_weight"];
					}

					if($arr["buff_moneypack_weight"] > 0)
					{
						$buff_money_pct += $arr["buff_moneypack_weight"];
					}

					if($arr["race1_weight"] > 0)
					{
						$race1_pct += $arr["race1_weight"];
					}

					if($arr["race2_weight"] > 0)
					{
						$race2_pct += $arr["race2_weight"];
					}

					if($arr["race3_weight"] > 0)
					{
						$race3_pct += $arr["race3_weight"];
					}
				}
			}
			else if($a[$i] == "m_item_list.xml")
			{
				$array = self::arrangeModel($array["list"]["item"],false);

				$equip_pct = 0;

				foreach($array as $arr)
				{
					if($arr["equip_weight"] > 0)
					{
						$equip_pct += $arr["equip_weight"];
					}
				}
			}
			else
			{
				$array = self::arrangeModel($array["list"]["item"]);
			}

			$name_arr = explode(".",$a[$i]);
			//$str2 = "$".str_replace("_kr","",$name_arr[0])."=";
			$str2 = "$".$name_arr[0]."=";

			fwrite($f,$str2);
			$str2 = var_export($array,true);
			fwrite($f,$str2.";\n");

			if($a[$i] == "m_card.xml")
			{
				$str3 = "define(\"FREE_CARD_TOTAL_PCT\",{$free_pct});\n";
				$str3 .= "define(\"MONEY_CARD_TOTAL_PCT\",{$money_pct});\n";
				$str3 .= "define(\"BUFF_MONEY_CARD_TOTAL_PCT\",{$buff_money_pct});\n";
				$str3 .= "define(\"RACE1_CARD_TOTAL_PCT\",{$race1_pct});\n";
				$str3 .= "define(\"RACE2_CARD_TOTAL_PCT\",{$race2_pct});\n";
				$str3 .= "define(\"RACE3_CARD_TOTAL_PCT\",{$race3_pct});\n";

				fwrite($f,$str3."\n");
			}
			else if($a[$i] == "m_item_list.xml")
			{
				$str3 = "define(\"EQUIP_TOTAL_PCT\",{$equip_pct});\n";

				fwrite($f,$str3."\n");
			}
		}

			fwrite($f,"?>");
			fclose($f);

			echo "완료";
	}

	public static function makeShadowWorldModel()
	{
		$str = simplexml_load_file("xml/shadow_world_time.xml");
		$json = json_encode($str);
		$array = json_decode($json,true);

		$array = DataModelLoader::arrangeModel($array["list"]["item"]);

		$f = fopen("conf/shadow_world_time.php","w+");
		fwrite($f,"<?php\n");

		$str2 = "$"."shadow_world_time"."=";
		fwrite($f,$str2);
		$str2 = var_export($array,true);
		fwrite($f,$str2.";\n");

		fwrite($f,"?>");
		fclose($f);

		echo "완료";
	}
}
?>