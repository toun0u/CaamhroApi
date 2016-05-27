<?
/**
* @author	NETLOR CONCEPT
* @version	1.0
* @package	log
* @access	public
*/

class lang extends dims_data_object {
    const TABLE_NAME = 'dims_lang';
	/**
	* Class constructor
	*
	* @param int $connection_id
	* @access public
	**/

	function __construct() {
		parent::dims_data_object(self::TABLE_NAME,'id');
	}

	function getFlag(){
		if ($this->fields['ref'] != ''){
			$url = './common/img/flag/flag_'.$this->fields['ref'].'.png';
			if (file_exists(DIMS_ROOT_PATH."www/common/img/flag/flag_".$this->fields['ref'].'.png'))
				return $url;
		}
		return false;
	}

	/**
	 *
	 * @return lang
	 */
	public static function getAllLanguageActiv (){
	    $list_lang = array();
	    $db = dims::getInstance()->getDb();

	    $sql = "SELECT * FROM dims_lang WHERE isactive = 1";

	    $res = $db->query($sql);
	    while ($row = $db->fetchrow($res)) {
		$lang = new lang();
		$lang->openWithFields($row, true);

		$list_lang[$lang->getId()] = $lang ;
	    }

	    return $list_lang;
	}

	public function getLabel() {
	    return $this->getAttribut("label", self::TYPE_ATTRIBUT_STRING);
	}

	public static function getByRef($ref) {
        if ($ref != '') {
        	$lang = lang::find_by(array('ref'=>$ref),null,1);
            if (!empty($lang)) {
                return $lang;
            }else {
                return null;
            }
        }
    }
}
