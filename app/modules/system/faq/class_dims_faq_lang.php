<?php

/**
 * Description of dims_faq
 *
 * @author Thomas Metois
 * @copyright Wave Software / Netlor 2011
 */
class dims_faq_lang extends dims_data_object{
	const TABLE_NAME = "dims_faq_lang";

	public function __construct() {
        parent::dims_data_object(self::TABLE_NAME, 'id_faq', 'id_lang');
    }

	public function getTitle(){
        return $this->getAttribut("title", self::TYPE_ATTRIBUT_STRING);
    }

    public function getContent() {
        return $this->getAttribut("content", self::TYPE_ATTRIBUT_STRING);
    }
}
?>