<?
require_once(DIMS_APP_PATH . "/modules/system/class_mb_field.php");

/**
* @author	NETLOR - Pat
* @version		1.0
* @package		system
* @access	public
*/
class meta_field extends dims_data_object {
	private $tablename_dyn; // nom de la table de correspondance
	private $isIndexed = false;
	private $newPosition = 0;

	/**
	* Class constructor
	*
	* @param int $connection_id
	* @access public
	**/
	function __construct() {
		parent::dims_data_object('dims_meta_field');
	}

	/*
	 * Attribution de la valeur de l'objet
	 */
	function setIdObject($idobject) {
		$this->fields['id_object']=$idobject;
	}

	/*
	 * Attribution de la valeur du module type
	 */
	function setIdModuleType($idmoduletype) {
		$this->fields['id_module_type']=$idmoduletype;
	}

	/*
	 * Attribution de la valeur de la table, devrait etre remplacer par la table dims_mb_object avec id_object + id_module_type
	 */
	function setTableName($tablename) {
		$this->tablename_dyn=$tablename;
	}

	function setIsIndexed($isIndexed=false) {
		$this->isIndexed = $isIndexed;
	}

	/*
	* Save de l'objet
	*/
	function save() {

		if($this->isNew()) {
			/*** Nouveau champ ***/
			// Définition de la position
			$sql = "SELECT MAX(position) AS maxpos FROM dims_meta_field WHERE id_object = :idobject AND id_module_type= :idmoduletype ";
			$res = $this->db->query($sql, array(
				':idobject'		=> $this->fields['id_object'],
				':idmoduletype'	=> $this->fields['id_module_type']
			));
			if($this->db->numrows($res)) {
				$info = $this->db->fetchrow($res);
				$this->fields['position'] = $info['maxpos']+1;
			}
			else {
				$this->fields['position'] = 1;
			}

			$this->fields['used'] = 1;

			// on cr�� la r�f�rence dans la table mb_fields
			$mbf = new mb_field();
			$mbf->fields['tablename']=$this->tablename_dyn;
			$mbf->fields['name']="field".$this->fields['fieldname'];
			$mbf->fields['label']=$this->fields['name'];
			$mbf->fields['type']="varchar(255)";
			$mbf->fields['visible']="1";
			$mbf->fields['id_module_type']=$this->fields['id_module_type'];
			$mbf->fields['id_object'] = $this->fields['id_object'];

			// verification de l'indexation
			$mbf->fields['indexed'] = (int)$this->isIndexed;

			$mbf->fields['protected']=0;
			$mbf->save();

			$this->fields['id_mbfield']=$mbf->fields['id'];
		}
		else {
			if ($this->getNewPosition()<1) $fieldnew_position=1;
			else {
				$select = "SELECT max(position) as maxpos from dims_meta_field where id_object = :idobject ";
				$res=$this->db->query($select, array(
					':idobject' => $this->fields['id_object']
				));
				$fields = $this->db->fetchrow($res);
				if ($this->getNewPosition() > $fields['maxpos']) $fieldnew_position = $fields['maxpos'];
			}

			$this->db->query("UPDATE dims_meta_field set position=0 where position= :position and id_object = :idobject ", array(
				':position'	=> $this->fields['position'],
				':idobject'	=> $this->fields['id_object']
			));
			if ($this->getNewPosition() > $this->fields['position']) {
				$this->db->query(	"UPDATE dims_meta_field
									SET position=position-1
									WHERE position BETWEEN :position1 AND :position2
									AND id_object = :idobject ", array(
						':position1'	=> ($this->fields['position']-1),
						':position2'	=> $this->getNewPosition(),
						':idobject'		=> $this->fields['id_object']
				));

			} else {
				$this->db->query(	"UPDATE dims_meta_field
									SET position=position+1
									WHERE position BETWEEN :position2 AND :position1
									AND id_object = :idobject ", array(
						':position1'	=> ($this->fields['position']-1),
						':position2'	=> $this->getNewPosition(),
						':idobject'		=> $this->fields['id_object']
				));
			}
			$this->db->query("UPDATE dims_meta_field set position= :position where position=0 and id_object = :idobject ", array(
					':position1'	=> $this->getNewPosition(),
					':idobject'		=> $this->fields['id_object']
			));
			$this->fields['position'] = $this->getNewPosition();
		}

		$mbf = new mb_field();
		$mbf->open($this->fields['id_mbfield']);

		$mbf->fields['label']=$this->fields['name'];

		if (empty($this->fields['used']))
			$mbf->fields['indexed']=0; // on desindex

		$mbf->fields['indexed'] = (int)$this->isIndexed;

		$mbf->save();

		return(parent::save());
	}

	public function setNewPosition($position) {
		$this->newPosition = $position;
	}

	public function getNewPosition() {
		return $this->newPosition;
	}

	function delete() {
		//update position
		$res=$this->db->query(	"UPDATE dims_meta_field
								SET position=position-1
								WHERE position > :position
								AND id_object = :idobject
								AND id_module_type= :idmoduletype ", array(
				':position'		=> ($this->fields['position']),
				':idobject'		=> $this->fields['id_object'],
				':idmoduletype'	=> $this->fields['id_module_type']
		));

		// on regarde maintenant le mbfield attache
		$mbf = new mb_field();
		$mbf->open($this->fields['id_mbfield']);
		if ($mbf->fields['protected']==0) {
			// on a une valeur metier, on peut supprimer le mbfield
			$mbf->delete();
		}
		parent::delete();
	}
}
?>
