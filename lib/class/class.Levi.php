<?
class Levi extends Control
{
	public function Index_action()
	{
		echo "Levi Complete";
	}

	
	public function lavi1_action()
	{
		$this->view_define("TPL_CONTENT","lavi1.tpl");
		$this->view_print("TPL_MAIN1");
	}
}
?>