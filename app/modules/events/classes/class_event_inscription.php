<?
/**
* @author 	Flo @netlor
* @version  	1.0
* @package  	LFB
* @access  	public
*/

class event_insc extends DIMS_DATA_OBJECT {
	/**
	* Class constructor
	*
	* @param int $connection_id
	* @access public
	**/
	function __construct() {
		parent::dims_data_object('dims_mod_business_event_inscription','id');
	}

	public function verifStep($id_evt,$id_contact) {

		$sql =	'SELECT			eect.*
					FROM		dims_mod_business_event_etap ee
					INNER JOIN	dims_mod_business_event_etap_user eect
					ON			ee.id_action = :idaction
					AND			ee.id = eect.id_etape
					AND			eect.id_ee_contact = :idcontact';


		$res=$this->db->query($sql, array(':idaction' => $id_evt, ':idcontact' => $id_contact) );

		if ($this->db->numrows($res)==0) {
			// on doit créer les étapes user
			$sql ="SELECT		ee.*
					FROM		dims_mod_business_event_etap ee
					WHERE			ee.id_action= :idaction
					ORDER BY	position";

			$res=$this->db->query($sql, array(':idaction' => $id_evt));
			if ($this->db->numrows($res)>0) {
				while ($value=$this->db->fetchrow($res)) {

					$etap_ct = new action_etap_ct();
					$etap_ct->init_description();
					$etap_ct->fields['id_etape'] = $value['id'];
					$etap_ct->fields['id_ee_contact'] = $id_contact;
					$id_eect = $etap_ct->save();

					$file_ct = new etap_file_ct();
					$file_ct->init_description();
					$file_ct->fields['id_etape'] = $value['id'];
					$file_ct->fields['id_contact'] = $id_contact;
					$file_ct->fields['id_action'] = $id_evt;
					$file_ct->fields['id_doc'] = 0;
					$id_newfile = $file_ct->save();

				}
			}
		}
	}

	public function statusInscription($id_evt,$id_contact) {
		$result =array();
		$result['total']=0;
		$result['valide']=0;
		$result['status']=-2;

		$checkuserdoc=array();

		$sql =	'SELECT
				ee.*,
				eect.id AS id_ee_ct,
				eect.id_etape,
				eect.valide_etape,
				eect.date_validation_etape,
				eect.condition as condition_user,
				euser.id AS id_file_ct,
				euser.valide
			FROM
				dims_mod_business_event_etap ee
			LEFT JOIN
				dims_mod_business_event_etap_file efile
				ON
					ee.id = efile.id_etape
			LEFT JOIN
				dims_mod_business_event_etap_file_user euser
				ON
					ee.id = euser.id_etape
				AND
					euser.id_doc = efile.id
				AND
					euser.id_contact = :idcontact1
			LEFT JOIN
				dims_mod_business_event_etap_user eect
				ON
					ee.id = eect.id_etape
				AND
					eect.id_ee_contact = :idcontact2
			WHERE
				ee.id_action = :idaction and type_etape!=1
			ORDER BY
				ee.position,
				euser.date_reception DESC';

		//echo $sql."<br>";
		$res=$this->db->query($sql, array(':idcontact1' => $id_contact,':idcontact2' => $id_contact,':idaction' => $id_evt) );
		$nb_res = $this->db->numrows($res);

		if ($nb_res>0) {
			while ($f=$this->db->fetchrow($res)) {
				if (!isset($checkuserdoc[$f['id_etape']])) {
					$result['total']++;
					if ($f['valide_etape'] == 2) {
						$result['valide']++;
					}

					if(is_null($f['valide'])) {
						//Si null : aucun document -> input = 0
					}
					elseif($f['valide'] == -1) {
						//-1 -> a été refusé
						$result['status']=-1;
					}
					elseif($f['valide'] == -2) {
						//Si 0 -> document en attente de validation; etat input = 1
						//Si 1 -> document validé; etat input = 2
						if ($result['status']!=-1) {
							$result['status']=0;
						}
					}
					elseif($f['valide'] == 1 && $result['status']==-2) {
						$result['status']=1;
					}
					// on a eu le retour
					$checkuserdoc[$f['id_etape']]=1;
				}
			}
		}
		return $result;
	}
}
?>