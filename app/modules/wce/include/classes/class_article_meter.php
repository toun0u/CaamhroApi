<?
class article_meter extends dims_data_object {
	function __construct() {
		parent::dims_data_object('dims_mod_wce_article_meter','id_article','timestp','email');
	}

	function updatecount() {
		$this->fields['meter']++;
		$this->save();
	}
}
?>