<?
/**
* Generic parameters loader class
*
* @version 2.09
* @since 0.1
*
* @access public
* @abstract
*
* @package includes
* @subpackage param
*
* @author <a href="http://www.netlorconcept.com/">Netlor Concept</a>
* @copyright © 2003 Netlor Concept
* @license http://www.netlorconcept.com
*/


class param
{
	var $moduleid;
	var $idgroup;
	var $iduser;
	var $idtypeparam;
	var $tabparam;
	var $tabparamdet;


	function param()
	{
		$this->moduleid = -1;
	}

	/*******************************************************************************************************
	charge les parametres d'un module à partir de la base de données
	*******************************************************************************************************/

	function open($moduleid, $idgroup=0, $iduser=0, $modify=0) {
		$db = dims::getInstance()->getDb();

		$this->moduleid = $moduleid;
		$this->idgroup = $idgroup;
		$this->iduser = $iduser;

		// select default parameters
		$select =	"
					SELECT		pd.id_module,
								pt.id_module_type,
								pt.name,
								pt.label,
								pd.value

					FROM		dims_param_default pd

					INNER JOIN	dims_param_type pt
					ON			pt.name = pd.name
					AND			pt.id_module_type = pd.id_module_type

					WHERE		pd.id_module = :idmodule
					";

		if ($modify) $select .= " AND pt.public = 1";
		$select .= " ORDER BY pt.name";


		$answer = $db->query($select, array(
			':idmodule' => array('type' => PDO::PARAM_INT, 'value' => $moduleid),
		));
		while ($fields = $db->fetchrow($answer)) {
			$this->tabparam[$fields['name']] = $fields;
		}

		// select group parameters (overide default parameters)
		if ($this->idgroup!=0) {
			$select =	"	SELECT		pg.id_module,
								pt.id_module_type,
								pt.name,
								pt.label,
								pg.value

						FROM		dims_param_group pg

						INNER JOIN	dims_param_type pt
						ON		pt.name = pg.name
						AND		pt.id_module_type = pg.id_module_type

						WHERE		pg.id_module = :idmodule
						AND		pg.id_group = :idgroup
						AND		pt.public = 1
						ORDER BY	pt.label
						";

			$answer = $db->query($select, array(
				':idmodule' => array('type' => PDO::PARAM_INT, 'value' => $moduleid),
				':idgroup' => array('type' => PDO::PARAM_INT, 'value' => $this->idgroup),
			));
			while ($fields = $db->fetchrow($answer)) {
				$this->tabparam[$fields['name']] = $fields;
			}

		}

		// select user parameters (overide user parameters)
		if ($this->iduser!=0) {
			$select =	"	SELECT		pu.id_module,
								pt.id_module_type,
								pt.name,
								pt.label,
								pu.value

						FROM		dims_param_user pu

						INNER JOIN	dims_param_type pt
						ON		pt.name = pu.name
						AND		pt.id_module_type = pu.id_module_type

						WHERE		pu.id_module = :idmodule
						AND		pu.id_user = :iduser ";

			$answer = $db->query($select, array(
				':idmodule' => array('type' => PDO::PARAM_INT, 'value' => $moduleid),
				':iduser' => array('type' => PDO::PARAM_INT, 'value' => $this->iduser),
			));
			while ($fields = $db->fetchrow($answer)) {
				if (!is_null($fields['value'])) {
					$this->tabparam[$fields['name']] = $fields;
				}
			}
		}

		$select =	"	SELECT		pc.*

					FROM		dims_param_choice pc

					INNER JOIN	dims_module
					ON			pc.id_module_type = dims_module.id_module_type
					AND			dims_module.id = :idmodule ";

		$answer = $db->query($select, array(':idmodule' => array('type' => PDO::PARAM_INT, 'type' => $moduleid)));
		while ($fields = $db->fetchrow($answer)) {
			$this->tabparam[$fields['name']]['choices'][$fields['value']] = $fields['displayed_value'];
		}
	}

	/*******************************************************************************************************
	affecte des nouvelles values aux parametres
	en fonction d'un tableau associatif de values
	*******************************************************************************************************/

	function setvalues($values) {
		foreach($values as $name => $value) {
			if (isset($this->tabparam[$name])) $this->tabparam[$name]['value'] = $value;
		}
	}


	/*******************************************************************************************************
	sauvegarde les parametres du module ouvert
	*******************************************************************************************************/

	function save($id_module_type = 0) {
		$db = dims::getInstance()->getDb();

		foreach($this->tabparam as $name => $param) {
			if ($this->idgroup == 0 && $this->iduser == 0) { // parametres par défaut
				$res=$db->query("UPDATE dims_param_default SET value = :value WHERE name = :name AND id_module = :idmodule", array(
					':value' => array('type' => PDO::PARAM_STR, 'value' => $param['value']),
					':name' => array('type' => PDO::PARAM_INT, 'value' => $name),
					':idmodule' => array('type' => PDO::PARAM_INT, 'value' => $this->moduleid),
				));
			} else if ($this->idgroup != 0) { // parametres du groupe
				$res=$db->query("SELECT * FROM dims_param_group WHERE name = :name AND id_module = :idmodule AND id_group = :idgroup", array(
					':name' => array('type' => PDO::PARAM_STR, 'value' => $name),
					':idmodule' => array('type' => PDO::PARAM_INT, 'value' => $this->moduleid),
					':idgroup' => array('type' => PDO::PARAM_INT, 'value' => $this->idgroup),
				));
				if ($db->numrows()) {
					$res=$db->query("UPDATE dims_param_group SET value = :value WHERE name = :name AND id_module = :idmodule AND id_group = :idgroup", array(
						':value' => array('type' => PDO::PARAM_STR, 'value' => $param['value']),
						':name' => array('type' => PDO::PARAM_STR, 'value' => $name),
						':idmodule' => array('type' => PDO::PARAM_INT, 'value' => $this->moduleid),
						':idgroup' => array('type' => PDO::PARAM_INT, 'value' => $this->idgroup),
					));
				} else {
					$res=$db->query("INSERT INTO dims_param_group SET value = :value, name = :name, id_module = :idmodule, id_group = :idgroup, id_module_type = :idmoduletype", array(
						':value' => array('type' => PDO::PARAM_STR, 'value' => $param['value']),
						':name' => array('type' => PDO::PARAM_STR, 'value' => $name),
						':idmodule' => array('type' => PDO::PARAM_INT, 'value' => $this->moduleid),
						':idgroup' => array('type' => PDO::PARAM_INT, 'value' => $this->idgroup),
						':idmoduletype' => array('type' => PDO::PARAM_INT, 'value' => $id_module_type),
					));
				}
			} else if ($this->iduser != 0) { // parametres de l'utilisateur

				$res=$db->query("SELECT * FROM dims_param_user WHERE name = :name AND id_module = :idmodule AND id_user = :iduser", array(
					':name' => array('type' => PDO::PARAM_STR, 'value' => $name),
					':idmodule' => array('type' => PDO::PARAM_INT, 'value' => $this->moduleid),
					':iduser' => array('type' => PDO::PARAM_INT, 'value' => $this->iduser),
				));
				if ($db->numrows()) {
					$res=$db->query("UPDATE dims_param_user SET value = :value WHERE name = :name AND id_module = :idmodule AND id_user = :iduser", array(
						':value' => array('type' => PDO::PARAM_STR, 'value' => $param['value']),
						':name' => array('type' => PDO::PARAM_STR, 'value' => $name),
						':idmodule' => array('type' => PDO::PARAM_INT, 'value' => $this->moduleid),
						':iduser' => array('type' => PDO::PARAM_INT, 'value' => $this->iduser),
					));
				} else {
					$res=$db->query("INSERT INTO dims_param_user SET value = :value, name = :name, id_module = :idmodule, id_user = :iduser, id_module_type = :idmoduletype", array(
						':value' => array('type' => PDO::PARAM_STR, 'value' => $param['value']),
						':name' => array('type' => PDO::PARAM_STR, 'value' => $name),
						':idmodule' => array('type' => PDO::PARAM_INT, 'value' => $this->moduleid),
						':iduser' => array('type' => PDO::PARAM_INT, 'value' => $this->iduser),
						':idmoduletype' => array('type' => PDO::PARAM_INT, 'value' => $id_module_type),
					));
				}
			}
		}
	}
}
