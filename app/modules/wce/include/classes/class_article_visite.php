<?
class article_visite extends dims_data_object {
	const TABLE_NAME = 'dims_mod_wce_article_visite';
	function __construct() {
		parent::dims_data_object(self::TABLE_NAME,'sid','timestp','id_module');
	}

	public static function updateVisite(){
		$tmstp = date('Ymd000000');
		$sid = session_id();

		if ($sid != '') {
			$visite = new article_visite();
			$visite->open($sid,$tmstp,$_SESSION['dims']['moduleid']);
			if(isset($visite->fields['meter']) && $visite->fields['meter'] > 0){
				$visite->fields['meter'] ++;
			}else{
				$visite->init_description();
				$visite->setugm();
				$visite->fields['sid'] = $sid;
				$visite->fields['timestp'] = $tmstp;
				$visite->fields['id_module'] = $_SESSION['dims']['moduleid'];
				$visite->fields['meter'] = 1;
			}
			$visite->save();
 		}
	}

	public static function updateSidVisiste($old){
		$tmstp = date('Ymd000000');
		$sid = session_id();
		if ($sid != '') {
			$db = dims::getInstance()->db;
			$sel = "SELECT	*
					FROM	".self::TABLE_NAME."
					WHERE	sid = :sid
					AND		timestp = :tmstp";

			$params=array();
			$params[':sid']=$old;
			$params[':tmstp']=$tmstp;
			$res = $db->query($sel,$params);

			while($r = $db->fetchrow($res)){
				$visite = new article_visite();
				$visite->openFromResultSet($r);
				$visite2 = new article_visite();
				$visite2->fields = $visite->fields;
				$visite2->fields['sid'] = $sid;
				$visite2->save();
				$visite->delete();
			}
		}
		/*$visite2 = new article_visite();
		$visite2->open($old,$tmstp);
		if(isset($visite2->fields['meter']) && $visite2->fields['meter'] > 0){
			$visite = new article_visite();
			$visite->open($sid,$tmstp);
			if(isset($visite->fields['meter']) && $visite->fields['meter'] > 0){
				$visite->fields['meter'] += $visite2->fields['meter'];
				$visite->save();
				$visite2->delete();
			}else{
				$visite->init_description();
				$visite->fields['sid'] = $sid;
				$visite->fields['timestp'] = $tmstp;
				$visite->fields['meter'] = $visite2->fields['meter'];
				$visite->save();
				$visite2->delete();
			}
		}*/
	}
}
?>
