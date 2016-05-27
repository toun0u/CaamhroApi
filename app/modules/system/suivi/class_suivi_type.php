<?
class suivi_type extends dims_data_object
{
	const TYPE_ACTIF = 1;
	const TYPE_INACTIF = 0;

	const TYPE_PUBLIC = 1;
	const TYPE_PRIVATE = 0;

	const TABLE_NAME = 'dims_mod_business_suivi_type';

	function __construct(){
		parent::dims_data_object(self::TABLE_NAME,'id');
	}

	function create($label, $public = self::TYPE_PRIVATE, $status = self::TYPE_ACTIF){
		$this->init_description(true);
		$this->setugm();
		$this->setLabel($label);
		$this->setPublic($public);
		$this->setStatus($status);
		return $this->save();
	}

	//SETTERS
	public function setLabel($val){
		$this->fields['label'] = $val;
	}
	public function setPublic($val){
		$this->fields['public'] = $val;
	}
	public function setStatus($val){
		$this->fields['status'] = $val;
	}

	//GETTERS
	public function getLabel(){
		return $this->fields['label'];
	}
	public function getPublic(){
		return $this->fields['public'];
	}
	public function isPublic(){
		return $this->fields['public'] == self::TYPE_PUBLIC;
	}
	public function getStatus(){
		return $this->fields['status'];
	}
	public function isActif(){
		return $this->fields['status'] == self::TYPE_ACTIF;
	}

	//surcharge de la fonction all - On récupère les types publics + les types qu'on aurait éventuellement créés manuellement
	public static function all($conditions = '', $params = array()){
		$db = dims::getInstance()->getDb();
		$res = $db->query('SELECT * FROM '.self::TABLE_NAME.' WHERE public='.self::TYPE_PUBLIC.' OR id_workspace=:idw',array(':idw'=>$_SESSION['dims']['workspaceid']));
		$lst = array();
		while($fields = $db->fetchrow($res)){
			$type = new suivi_type();
			$type->openFromResultSet($fields);
			$lst[] = $type;
		}
		return $lst;
	}
}
