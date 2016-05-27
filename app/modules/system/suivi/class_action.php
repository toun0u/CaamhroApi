<?
/**
* @author 	NETLOR CONCEPT
* @version  	1.0
* @package  	promotech
* @access  	public
*/
include_once DIMS_APP_PATH.'modules/system/suivi/class_action_detail.php';
include_once DIMS_APP_PATH.'modules/system/suivi/class_action_utilisateur.php';

class action extends dims_data_object
{
	const TABLE_NAME = 'dims_mod_business_action';
	/**
	* Class constructor
	*
	* @param int $connection_id
	* @access public
	**/

	function action()
	{
		parent::dims_data_object(self::TABLE_NAME);
		$this->fields['temps_duplique'] = 'non';
		$this->dossiers = array();
		$this->tiers = array();
	}

	function open($id)
	{
		global $db;

		parent::open($id);

		$res=$db->query("SELECT * FROM dims_mod_business_action_detail WHERE action_id = :id",array(':id'=>$id));
		if ($this->fields['dossier_id'] != 0) // action Dossier
		{
			$this->tiers = array();
			while ($row = $db->fetchrow($res)) {
				$this->tiers[] = $row['tiers_id'];
			}
		}
		if ($this->fields['tiers_id'] != 0) // action Tiers
		{
			$this->dossiers = array();
			while ($row = $db->fetchrow($res)) {
				$this->dossiers[] = $row['dossier_id'];
			}
		}

		$res=$db->query("SELECT * FROM dims_mod_business_action_utilisateur WHERE action_id = :id",array(':id'=>$id));
		$this->utilisateurs = array();
		while ($row = $db->fetchrow($res))
		{
			$this->utilisateurs[] = $row['user_id'];
		}
	}

	function save()
	{
		global $db;

		if (!$this->new)
		{
			$res=$db->query("DELETE FROM dims_mod_business_action_detail WHERE action_id = :id",array(':id'=>$this->fields['id']));
			$res=$db->query("DELETE FROM dims_mod_business_action_utilisateur WHERE action_id = :id",array(':id'=>$this->fields['id']));
		}

		if (!isset($this->fields['temps_passe']) || !$this->fields['temps_passe'])
		{
			$hdeb = split(':',$this->fields['heuredeb']);
			$hfin = split(':',$this->fields['heurefin']);
			$this->fields['temps_passe'] = ($hfin[0]-$hdeb[0])*60+$hfin[1]-$hdeb[1];
			$this->fields['temps_prevu'] = $this->fields['temps_passe'];
		}

		parent::save();

		if ($this->fields['tiers_id']) // Action Tiers
		{
			echo "tiers";
			if (!isset($this->dossiers)) $this->dossiers[0] = 0;

			foreach($this->dossiers as $dossier_id)
			{
				$action_detail = new action_detail();
				$action_detail->fields['action_id'] = $this->fields['id'];
				$action_detail->fields['tiers_id'] = $this->fields['tiers_id'];
				if(empty($action_detail->fields['interlocuteur_id'])) $action_detail->fields['interlocuteur_id'] = 0;
				$action_detail->fields['dossier_id'] = $dossier_id;
				if ($this->fields['temps_duplique'] == 'oui') $action_detail->fields['duree'] = $this->fields['temps_passe'];
				else $action_detail->fields['duree'] = ($this->fields['temps_passe']/sizeof($this->dossiers));
				$action_detail->fields['id_module'] = $this->fields['id_module'];
				$action_detail->fields['id_user'] = $this->fields['id_user'];
				$action_detail->fields['id_workspace'] = $this->fields['id_workspace'];
				$action_detail->save();
			}
		}
		elseif ($this->fields['dossier_id']) // Action Dossier
		{
			echo "dossier";
			if (!isset($this->tiers)) $this->tiers[0] = 0;

			foreach($this->tiers as $tiers_id)
			{
				$action_detail = new action_detail();
				$action_detail->fields['action_id'] = $this->fields['id'];
				$action_detail->fields['dossier_id'] = $this->fields['dossier_id'];
				if(empty($action_detail->fields['interlocuteur_id'])) $action_detail->fields['interlocuteur_id'] = 0;
				$action_detail->fields['tiers_id'] = $tiers_id;
				if ($this->fields['temps_duplique'] == 'oui') $action_detail->fields['duree'] = $this->fields['temps_passe'];
				else $action_detail->fields['duree'] = ($this->fields['temps_passe']/sizeof($this->tiers));
				$action_detail->fields['id_module'] = $this->fields['id_module'];
				$action_detail->fields['id_user'] = $this->fields['id_user'];
				$action_detail->fields['id_workspace'] = $this->fields['id_workspace'];
				$action_detail->save();
			}
		}
		else // ni dossier ni tiers
		{
			$action_detail = new action_detail();
			$action_detail->fields['action_id'] = $this->fields['id'];
			$action_detail->fields['dossier_id'] = 0;
			if(empty($action_detail->fields['interlocuteur_id'])) $action_detail->fields['interlocuteur_id'] = 0;
			$action_detail->fields['tiers_id'] = 0;
			$action_detail->fields['duree'] = $this->fields['temps_passe'];
			$action_detail->fields['id_module'] = $this->fields['id_module'];
			$action_detail->fields['id_user'] = $this->fields['id_user'];
			$action_detail->fields['id_workspace'] = $this->fields['id_workspace'];
			$action_detail->save();
		}

		if (isset($this->utilisateurs))
		{
			foreach($this->utilisateurs as $user_id)
			{
				$action_utilisateur = new action_utilisateur();
				$action_utilisateur->fields['action_id'] = $this->fields['id'];
				$action_utilisateur->fields['user_id'] = $user_id;
				$action_utilisateur->save();
			}
		}
				/*
		$tiers = array();
		if (!$this->fields['tiers_id']) // tous
		{
			$select = "SELECT distinct(tiers_id) FROM dims_mod_business_tiers_dossier WHERE dossier_id = {$this->fields['dossier_id']}";
			$res=$db->query($select);
			while($row = $db->fetchrow($res)) $tiers[] = $row['tiers_id'];
		}
		else
		{
			$tiers[] = $this->fields['tiers_id'];
		}

		foreach($tiers as $tiers_id)
		{
			$action_detail = new action_detail();
			$action_detail->fields['action_id'] = $this->fields['id'];
			$action_detail->fields['tiers_id'] = $tiers_id;
			if ($this->fields['temps_duplique'] == 'oui') $action_detail->fields['duree'] = $this->fields['temps_passe'];
			else $action_detail->fields['duree'] = ($this->fields['temps_passe']/sizeof($tiers));
			$action_detail->fields['id_module'] = $this->fields['id_module'];
			$action_detail->fields['id_user'] = $this->fields['id_user'];
			$action_detail->fields['id_workspace'] = $this->fields['id_workspace'];
			$action_detail->save();
		}
		*/
		return($this->fields['id']);
	}

	/* Fait par nico donc à vérifier */
	function delete() {
		$action_detail = new action_detail();
		$action_utilisateur = new action_utilisateur();

		$action_detail->open($this->fields['id']);
		$action_utilisateur->open($this->fields['id']);

		$action_detail->delete();
		$action_utilisateur->delete();
		return(parent::delete());

		/*
		dims_print_r($this->fields);
		dims_print_r($action_detail->fields);
		dims_print_r($action_utilisateur->fields);
		*/
	}
}
