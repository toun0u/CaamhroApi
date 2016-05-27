<?
require_once DIMS_APP_PATH.'include/class_dims_data_object.php';

class favorite extends dims_data_object {
    const NotFavorite = 0;
    const Interressed = 1;
    const Favorite = 2;
	/**
	* Class constructor
	*
	* @param int $idconnexion
	* @access public
	**/
	function __construct() {
		parent::dims_data_object('dims_favorite','id_user','id_globalobject');
	}

    public function isNew(){
	return $this->new;
    }

    public function changeStatus($stat = favorite::NotFavorite,$note = null){
		switch($stat){
			default :
			case favorite::NotFavorite :
				$this->fields['status'] = favorite::NotFavorite;
				$this->fields['note'] = null;
				break;
			case favorite::Interressed :
				$this->fields['status'] = $stat;
				$this->fields['note'] = $note;
				break;
			case favorite::Favorite :
				$this->fields['status'] = $stat;
				$this->fields['note'] = $note;
				break;
		}
    }
}
?>
