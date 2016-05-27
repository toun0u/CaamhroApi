<?
class article_object_corresp extends dims_data_object {
    const TABLE_NAME = 'dims_mod_wce_object_corresp';
	function __construct() {
		parent::dims_data_object(self::TABLE_NAME,'id_object','id_article','id_heading');
	}
}
?>
