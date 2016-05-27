<?php
class slidart_element extends dims_data_object {
    const TABLE_NAME = 'dims_mod_cata_wce_slidart_element';
    /**
    * Class constructor
    *
    * @access public
    **/
    public function __construct() {
        parent::dims_data_object(self::TABLE_NAME, 'id');
    }

	function save() {
		if ($this->isNew() && empty($this->fields['position'])) {
            $db = dims::getInstance()->getDb();
            $sql = 'SELECT  MAX(position) AS higher
                    FROM    '.self::TABLE_NAME.'
                    WHERE   id_slidart = '.$this->fields['id_slidart'];
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
        $db = dims::getInstance()->getDb();
        $sql = 'SELECT  *
                FROM    '.self::TABLE_NAME.'
                WHERE   id_slidart = '.$this->fields['id_slidart']."
                AND     position > ".$this->fields['position'];
        $res = $db->query($sql);
        while ($r = $db->fetchrow($res)){
            $elem = new slidart_element();
            $elem->openFromResultSet($r);
            $elem->fields['position'] --;
            $elem->save();
        }
        return parent::delete();
    }

    public function getArticle(){
        include_once DIMS_APP_PATH.'modules/catalogue/include/class_article.php';
        $elem = new article();
        $elem->open($this->fields['id_article']);
        return $elem;
    }

    public function downElem(){
        $db = dims::getInstance()->getDb();
        $sql = "SELECT  *
                FROM    ".self::TABLE_NAME."
                WHERE   position = ".($this->fields['position']+1)."
                AND     id_slidart = ".$this->fields['id_slidart'];
        $res = $db->query($sql);
        if($r = $db->fetchrow($res)){
            $elem = new slidart_element();
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
                AND     id_slidart = ".$this->fields['id_slidart'];
        $res = $db->query($sql);
        if($r = $db->fetchrow($res)){
            $elem = new slidart_element();
            $elem->openFromResultSet($r);
            $elem->fields['position'] ++;
            $elem->save();
            $this->fields['position'] --;
            $this->save();
        }
    }
}
