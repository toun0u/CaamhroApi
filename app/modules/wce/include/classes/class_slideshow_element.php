<?php
require_once DIMS_APP_PATH."modules/doc/class_docfile.php";
class wce_slideshow_element extends dims_data_object {
	const TABLE_NAME = 'dims_mod_wce_slideshow_element';
    /**
    * Class constructor
    *
    * @access public
    **/
    public function __construct() {
        parent::dims_data_object(self::TABLE_NAME, 'id');
    }

	function save() {
		if ($this->new && empty($this->fields['position'])) {
			$params = array();
			$params[':id_slideshow'] = array('value'=>$this->fields['id_slideshow'],'type'=>PDO::PARAM_INT);
			$res = $this->db->query('SELECT 	MAX(position) AS higher
									 FROM 		'.self::TABLE_NAME.'
									 WHERE 		id_slideshow = :id_slideshow', $params);

            if($this->db->numrows($res)) {
                $info = $this->db->fetchrow($res);

                $this->fields['position'] = $info['higher']+1;
            }
            else {
                $this->fields['position'] = 0;
            }
		}

		return parent::save();
	}

	function delete(){
		require_once DIMS_APP_PATH."modules/doc/class_docfile.php";
		if ($this->fields['image'] != '' && $this->fields['image'] > 0){
			$doc = new docfile();
			$doc->open($this->fields['image']);
			if (isset($doc->fields['id_globalobject'])){
				$infos = $doc->getbasepath();
				$name = pathinfo($doc->getwebpath());
				$name = $name['filename'];
				unlink($infos."/_preview_".$name.".jpg");
				unlink($infos."/_preview_".$name.".mp4");
				unlink($infos."/_preview_".$name.".ogv");
				unlink($infos."/_preview_".$name.".webm");
				$doc->delete();
			}
		}
		if ($this->fields['miniature'] != '' && $this->fields['miniature'] > 0){
			$doc = new docfile();
			$doc->open($this->fields['miniature']);
			if (isset($doc->fields['id_globalobject'])){
				$doc->delete();
			}
		}
		$sel = "SELECT	*
				FROM	".self::TABLE_NAME."
				WHERE	position > :position
				AND		id_slideshow = :id_slideshow";
		$params = array();
		$params[':id_slideshow'] = array('value'=>$this->fields['id_slideshow'],'type'=>PDO::PARAM_INT);
		$params[':position'] = array('value'=>$this->fields['position'],'type'=>PDO::PARAM_INT);
		$db = dims::getInstance()->getDb();
		$res = $db->query($sel, $params);
		while($r = $db->fetchrow($res)){
			$elem = new wce_slideshow_element();
			$elem->openFromResultSet($r);
			$elem->fields['position'] --;
			$elem->save();
		}
		parent::delete();
    }

	public function getPreview(){
		if (isset($this->fields['image']) && $this->fields['image'] != '' && $this->fields['image'] > 0){
			require_once DIMS_APP_PATH."modules/doc/class_docfile.php";
			$doc = new docfile();
			$doc->open($this->fields['image']);
			if(in_array(strtolower($doc->fields['extension']),array('mp4','ogv','webm'))){
				$infos = $doc->getbasepath();
				$name2 = pathinfo($doc->getwebpath());
				$name = $name2['filename'];
				$path = $infos."/_preview_".$name.".jpg";
				if(file_exists($path)){
					return $name2['dirname']."/_preview_".$name.".jpg";
				}else
					return false;
			}else
				return $doc->getwebpath();
		}else
			return false;
	}

	public function positionUp(){
		$sel = "SELECT	*
				FROM	".self::TABLE_NAME."
				WHERE	position = :position
				AND		id_slideshow = :id_slideshow";
		$params = array();
		$params[':id_slideshow'] = array('value'=>$this->fields['id_slideshow'],'type'=>PDO::PARAM_INT);
		$params[':position'] = array('value'=>($this->fields['position']-1),'type'=>PDO::PARAM_INT);
		$db = dims::getInstance()->getDb();
		$res = $db->query($sel,$params);
		if($r = $db->fetchrow($res)){
			$elem = new wce_slideshow_element();
			$elem->openFromResultSet($r);
			$elem->fields['position'] ++;
			$elem->save();
			$this->fields['position'] --;
			$this->save();
		}
	}

	public function positionDown(){
		$sel = "SELECT	*
				FROM	".self::TABLE_NAME."
				WHERE	position = :position
				AND		id_slideshow = :id_slideshow";
		$params = array();
		$params[':id_slideshow'] = array('value'=>$this->fields['id_slideshow'],'type'=>PDO::PARAM_INT);
		$params[':position'] = array('value'=>($this->fields['position']+1),'type'=>PDO::PARAM_INT);
		$db = dims::getInstance()->db;
		$res = $db->query($sel);
		if($r = $db->fetchrow($res)){
			$elem = new wce_slideshow_element();
			$elem->openFromResultSet($r);
			$elem->fields['position'] --;
			$elem->save();
			$this->fields['position'] ++;
			$this->save();
		}
	}
}
