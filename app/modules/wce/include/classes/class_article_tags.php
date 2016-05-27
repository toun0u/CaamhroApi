<?
class article_tags extends dims_data_object {
	const TABLE_NAME = 'dims_mod_wce_article_tags';
	function __construct() {
		parent::dims_data_object(self::TABLE_NAME,'id_tag','id_module','id_workspace');
	}

	public static function updateTags($id_tag, $id_module, $id_workspace){
		$visite = new article_tags();
		$visite->open($id_tag, $id_module, $id_workspace);
		if (!isset($visite->fields['count'])){
			$visite->init_description();
			$visite->fields['id_tag'] = $id_tag;
			$visite->fields['id_module'] = $id_module;
			$visite->fields['id_workspace'] = $id_workspace;
			$visite->fields['count'] = 1;
		}else{
			$visite->fields['count'] ++;
		}
		$visite->save();
	}

	public static function getTags($limit = 20){
		require_once DIMS_APP_PATH."modules/system/class_tag.php";

		// compteur du nombre total
		$sel = "SELECT		sum(at.count) as cpte
				FROM		".tag::TABLE_NAME." t
				INNER JOIN	".self::TABLE_NAME." at
				ON			at.id_tag = t.id
				WHERE		at.id_workspace = :id_workspace
				ORDER BY	at.count DESC
				LIMIT		:limit";

		$params=array();
		$params[':limit']['value']=$limit;
		$params[':limit']['type']=PDO::PARAM_INT;
		$params[':id_workspace']=$_SESSION['dims']['workspaceid'];
		$db = dims::getInstance()->db;

		$total=0;
		$nbelem=$limit;

		$res = $db->query($sel,$params);

		if($r = $db->fetchrow($res)){
			$total=$r['cpte'];
		}

		$seuil1=($total/$nbelem)*2/3;
		$seuil2=($total/$nbelem)/2;
		$seuil3=($total/$nbelem)/4;

		$sel = "SELECT		t.*,at.count
		FROM		".tag::TABLE_NAME." t
		INNER JOIN	".self::TABLE_NAME." at
		ON			at.id_tag = t.id
		WHERE		at.id_workspace = :id_workspace
		ORDER BY	at.count DESC
		LIMIT		:limit";

		$params=array();
		$params[':limit']['value']=$limit;
		$params[':limit']['type']=PDO::PARAM_INT;
		$params[':id_workspace']=$_SESSION['dims']['workspaceid'];
		$db = dims::getInstance()->db;

		$res = $db->query($sel,$params);
		$lst = array();

		while($r = $db->fetchrow($res)){
			$elem = new tag();
			$elem->openFromResultSet($r);

			if ($elem->fields['count']>=$seuil1) $elem->fields['indice']=4;
			elseif($elem->fields['count']>=$seuil2) $elem->fields['indice']=3;
			elseif($elem->fields['count']>=$seuil3) $elem->fields['indice']=2;
			else $elem->fields['indice']=1;
			$lst[$elem->fields['tag']] = $elem;
			$total+=$r['count'];
		}
				/*
				$repartition=($total+1)/4;
				// on fait la répartition
				foreach($lst as $id=>$elem) {
					$lst[$id]['indice']=$elem['count']%$repartition;
				}*/
		return $lst;
	}
}
?>
