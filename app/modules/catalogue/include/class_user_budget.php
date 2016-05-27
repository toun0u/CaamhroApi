<?
class user_budget extends dims_data_object
{
	function user_budget()
	{
		parent::dims_data_object('dims_mod_vpc_user_budget');
	}

	function getUserBudgetByUserId($user_id)
	{
		global $db;
		$res = $db->query("SELECT id FROM dims_mod_vpc_user_budget WHERE id_user=".$user_id.' AND en_cours=1');
		$tab = $db->fetchrow($res);
		$this->open($tab['id']);
	}

	function save()
	{
		if($this->fields['valeur'] == '') $this->fields['valeur'] = 0;
		parent::save();
	}
}
?>
