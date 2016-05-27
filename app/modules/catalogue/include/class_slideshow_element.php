<?php
include_once DIMS_APP_PATH.'modules/doc/class_docfile.php';
class slideshow_element extends dims_data_object {
    const TABLE_NAME = 'dims_mod_cata_wce_slideshow_element';
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
            $db = dims::getInstance()->getDb();
            $sql = 'SELECT  MAX(position) AS higher
                    FROM    '.self::TABLE_NAME.'
                    WHERE   id_slideshow = '.$this->fields['id_slideshow'];
			$res = $db->query($sql);

            if($db->numrows($res)) {
                $info = $db->fetchrow($res);

                $this->fields['position'] = $info['higher']+1;
            }
            else {
                $this->fields['position'] = 0;
            }
		}

		return parent::save();
	}

    public function delete(){
        if($this->fields['miniature'] != '' && $this->fields['miniature'] > 0){
            $img = new docfile();
            $img->open($this->fields['miniature']);
            $img->delete();
        }
        if($this->fields['image'] != '' && $this->fields['image'] > 0){
            $img = new docfile();
            $img->open($this->fields['image']);
            $img->delete();
        }
        $db = dims::getInstance()->getDb();
        $sql = 'SELECT  *
                FROM    '.self::TABLE_NAME.'
                WHERE   id_slideshow = '.$this->fields['id_slideshow']."
                AND     position > ".$this->fields['position'];
        $res = $db->query($sql);
        while ($r = $db->fetchrow($res)){
            $elem = new slideshow_element();
            $elem->openFromResultSet($r);
            $elem->fields['position'] --;
            $elem->save();
        }
        return parent::delete();
    }

    public function downElem(){
        $db = dims::getInstance()->getDb();
        $sql = "SELECT  *
                FROM    ".self::TABLE_NAME."
                WHERE   position = ".($this->fields['position']+1)."
                AND     id_slideshow = ".$this->fields['id_slideshow'];
        $res = $db->query($sql);
        if($r = $db->fetchrow($res)){
            $elem = new slideshow_element();
            $elem->openFromResultSet($r);
            $elem->fields['position'] --;
            $elem->save();
            $this->fields['position'] ++;
            $this->save();
        }
    }

    public function upElem(){
        $db = dims::getInstance()->getDb();
        $sql = "SELECT  *
                FROM    ".self::TABLE_NAME."
                WHERE   position = ".($this->fields['position']-1)."
                AND     id_slideshow = ".$this->fields['id_slideshow'];
        $res = $db->query($sql);
        if($r = $db->fetchrow($res)){
            $elem = new slideshow_element();
            $elem->openFromResultSet($r);
            $elem->fields['position'] ++;
            $elem->save();
            $this->fields['position'] --;
            $this->save();
        }
    }
}
