<?php
/**
* @author	NETLOR - Flo
* @version	1.0
* @package	system
* @access	public
*/
class ctlink extends dims_data_object {
	/**
	* Class constructor
	*
	* @param int $connection_id
	* @access public
	**/
	function __construct() {
		parent::dims_data_object('dims_mod_business_ct_link');
	}

	function save() {

		// verification si new
		if ($this->new) {

			// on ajoute une surcouche pour la creation d'une action entre la personne et l'entreprise
			require_once(DIMS_APP_PATH . '/modules/system/class_contact.php');
			$ct1= new contact();
			$ct2= new contact();

			$id_user1=0;
			$id_user2=0;

			if (isset($this->fields['id_contact1']) && $this->fields['id_contact1'] != '' && $this->fields['id_contact1'] > 0 &&
				isset($this->fields['id_contact2']) && $this->fields['id_contact2'] != '' && $this->fields['id_contact2'] > 0){
				$ct1->open($this->fields['id_contact1']);
				$ct2->open($this->fields['id_contact2']);

				$res=$this->db->query('select id from dims_user where id_contact= :idcontact', array(
					':idcontact' => array('type' => PDO::PARAM_INT, 'value' => $this->fields['id_contact1']),
				));
				if ($this->db->numrows($res)>0) {
					while ($u=$this->db->fetchrow($res)) {
						$id_user1=$u['id'];
					}
				}

				$res=$this->db->query('select id from dims_user where id_contact= :idcontact', array(
					':idcontact' => array('type' => PDO::PARAM_INT, 'value' => $this->fields['id_contact2']),
				));
				if ($this->db->numrows($res)>0) {
					while ($u=$this->db->fetchrow($res)) {
						$id_user2=$u['id'];
					}
				}

				require_once(DIMS_APP_PATH . '/include/class_dims_action.php');
				$action = new dims_action(/*$this->db*/);
				$action->fields['id_parent']=0;
				$action->setUser($_SESSION['dims']['userid']);
				$action->setWorkspace($_SESSION['dims']['workspaceid']);
				$action->setModule($_SESSION['dims']['moduleid']);
				$action->fields['timestp_modify']= dims_createtimestamp();

				// verification si contact1 est soit meme ou contact2!=soi meme mais on a qq en user1
				if ($id_user1==$_SESSION['dims']['userid'] || ($id_user1>0 && $id_user2!=$_SESSION['dims']['userid'])) {
					// on a un lien avec un dims_user, on cree le message dims_user est en lien avec l'entreprise X
					$action->fields['comment']= '_DIMS_LABEL_IS_LINKING';
					$action->fields['type'] = dims_const::_ACTION_LINK; // link
					$action->addObject(0, $_SESSION['dims']['moduleid'], dims_const::_SYSTEM_OBJECT_CONTACT, $this->fields['id_contact2'],$ct2->fields['firstname']." ".$ct2->fields['lastname']);
					$action->addObject(0, $_SESSION['dims']['moduleid'], dims_const::_SYSTEM_OBJECT_CONTACT, $this->fields['id_contact1'],$ct1->fields['firstname']." ".$ct1->fields['lastname']);
					$action->setUser($id_user1);
				}
				elseif($id_user2==$_SESSION['dims']['userid']) {
					// on met simplement un message Fiche modifiée, + en additional => ajout d'un lien avec l'entreprise X
					$action->fields['comment']= '_DIMS_LABEL_IS_LINKING';
					$action->fields['type'] = dims_const::_ACTION_LINK; // link
					$action->addObject(0, $_SESSION['dims']['moduleid'], dims_const::_SYSTEM_OBJECT_CONTACT, $this->fields['id_contact2'],$ct2->fields['firstname']." ".$ct2->fields['lastname']);
					$action->addObject(0, $_SESSION['dims']['moduleid'], dims_const::_SYSTEM_OBJECT_CONTACT, $this->fields['id_contact1'],$ct1->fields['firstname']." ".$ct1->fields['lastname']);
					$action->setUser($id_user2);
				}
				else {
					// on a deux contacts sans id_user
					$action->fields['comment']= '_DIMS_LABEL_IS_LINKING';
					$action->fields['type'] = dims_const::_ACTION_LINK; // link
					$action->addObject(0, $_SESSION['dims']['moduleid'], dims_const::_SYSTEM_OBJECT_CONTACT, $this->fields['id_contact2'],$ct2->fields['firstname']." ".$ct2->fields['lastname']);
					$action->addObject(0, $_SESSION['dims']['moduleid'], dims_const::_SYSTEM_OBJECT_CONTACT, $this->fields['id_contact1'],$ct1->fields['firstname']." ".$ct1->fields['lastname']);
					$action->setUser($_SESSION['dims']['userid']);
				}

							// save object action
				$action->save();
			}
		}
		return(parent::save());
	}

	function delete() {
		$db = dims::getInstance()->getDb();
		parent::delete();
	}

	public static function isLinked($id, $type) {
		if ($id != null && $type != null) {
			$db = dims::getInstance()->getDb();
			$rs = $db->query('SELECT id FROM dims_mod_business_ct_link WHERE id_contact1 = :idcontact1 AND id_contact2 = :idcontact2 AND id_object = :idobject', array(
				':idcontact1' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['user']['id_contact']),
				':idcontact2' => array('type' => PDO::PARAM_INT, 'value' => $id),
				':idobject' => array('type' => PDO::PARAM_STR, 'value' => $type),
			));
			return $db->numrows($rs);
		}
		else return false;
	}
}
