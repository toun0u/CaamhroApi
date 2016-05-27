<?php
require_once DIMS_APP_PATH.'modules/wce/include/classes/class_slideshow_element.php';

class wce_slideshow extends dims_data_object {
	const TABLE_NAME = 'dims_mod_wce_slideshow';
    /**
    * Class constructor
    *
    * @access public
    **/
    public function __construct() {
        parent::dims_data_object(self::TABLE_NAME, 'id');
    }

    public function getElements() {
		require_once DIMS_APP_PATH."modules/wce/include/classes/class_slideshow.php";
        $elemList = array();

        $sql = 'SELECT 		*
				FROM 		dims_mod_wce_slideshow_element
				WHERE 		id_slideshow = :id_slideshow
				ORDER BY 	position ASC';

		$params=array();
		$params[':id_slideshow']['value']=$this->fields['id'];
		$params[':id_slideshow']['type']=PDO::PARAM_INT;
        $res = $this->db->query($sql,$params);

        while ($fields = $this->db->fetchrow($res)) {
            $element = new wce_slideshow_element();
			$element->openFromResultSet($fields);
            $elemList[] = $element;
        }

        return $elemList;
    }

    public function delete() {
        foreach($this->getElements() as $element) {
            $element->delete();
        }

        return parent::delete();
    }

    /*
     * liste des templates disponibles dans l'arborescence
     */
    public static function getTemplates() {
		$tplList = array();

		$dir = DIMS_APP_PATH.'templates/objects/slideshows';

		$handler_dir = opendir($dir);

		while (false !== ($file = readdir($handler_dir))) {
			if(is_file($dir.'/'.$file)) {
				$tplName = $file;

				if ( false !== ($extPos = strrpos($file, '.')) && substr($file, -4) == '.tpl'  ) {
					$tplName = substr($file,0,$extPos);
					$tplList[$tplName] = $tplName;
				}
			}
		}

		sort($tplList);

		return $tplList;
	}

}
