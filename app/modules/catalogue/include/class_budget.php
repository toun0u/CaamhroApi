<?
class budget extends dims_data_object
{
	function budget()
	{
		parent::dims_data_object('dims_mod_vpc_budget');
	}

	function save()
	{
		if(!isset($this->fields['valeur']) || $this->fields['valeur'] == '') $this->fields['valeur'] = 0;

		parent::save();
	}

	function renouveler()
	{
		global $db;

		$this->fields['en_cours'] = 0;
		$this->save();
		$this->new = true;
		$this->fields['id'] = -1;
		$this->fields['en_cours'] = 1;
		$this->save();

		catalogue_budgetlog($this->fields['id'],$_SESSION['catalogue']['root_group'],5,$this->fields['code'],$this->fields['valeur']);

		// On renouvelle le budget de tous les sous-groupes
		$group = new group();
		$group->open($_SESSION['catalogue']['root_group']);
		$children = $group->getgroupchildrenlite();
		if(is_array($children) && count($children))
		{
			foreach($children as $key)
			{
				$ensbudgets = array();
				$sql = "
					SELECT id
					FROM dims_mod_vpc_budget
					WHERE id_group = $key
					AND id_client = '{$_SESSION['catalogue']['code_client']}'
					AND en_cours = 1
				";
				$db->query($sql);
				while ($row = $db->fetchrow())
				{
					$ensbudgets[] = $row['id'];
				}

				foreach ($ensbudgets as $id_budget)
				{
					$sbudget = new budget();
					$sbudget->open($id_budget);
					$sbudget->fields['en_cours'] = 0;
					$sbudget->save();
					$sbudget->new = true;
					$sbudget->fields['id'] = -1;
					$sbudget->fields['en_cours'] = 1;
					$sbudget->save();

					catalogue_budgetlog($sbudget->fields['id'],$key,5,$sbudget->fields['code'],$sbudget->fields['valeur']);
				}
			}
		}

		include_once DIMS_APP_PATH.'/modules/catalogue/class_client.php';
		$client = new client();
		$client->open($_SESSION['catalogue']['code_client']);
		$client->fields['budget_date_reconduction'] = dims_createtimestamp();
		$client->save();
	}
}
?>
