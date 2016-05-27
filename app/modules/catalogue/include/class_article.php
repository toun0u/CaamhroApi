<?php
include_once DIMS_APP_PATH."modules/catalogue/include/class_marque.php";
include_once DIMS_APP_PATH."modules/catalogue/include/class_article_thumb.php";
include_once DIMS_APP_PATH."modules/catalogue/include/class_cata_prix_nets.php";
include_once DIMS_APP_PATH."modules/catalogue/include/class_champ.php";
include_once DIMS_APP_PATH."modules/catalogue/include/class_article_link.php";
include_once DIMS_APP_PATH."modules/catalogue/include/class_tva.php";
include_once DIMS_APP_PATH."modules/catalogue/include/class_article_kit.php";
include_once DIMS_APP_PATH."modules/catalogue/include/class_param.php";
include_once DIMS_APP_PATH."modules/catalogue/include/class_article_reference.php";

class article extends pagination {
	const TYPE_ART = 1;
	const TYPE_KIT = 2;
	const TABLE_NAME = 'dims_mod_cata_article';
	const MY_GLOBALOBJECT_CODE = 334;

	const STATUS_DELETED = 'DELETED';
	const STATUS_OK = 'OK';

	const ARTICLE_PUBLISHED = 1;
	const ARTICLE_UNPUBLISHED = 0;

	const SHIPPING_COST_AUTO 					= 0;
	const SHIPPING_COST_DEVIS 					= 1;
	const SHIPPING_COST_DELIVERY 				= 2;
	const SHIPPING_COST_DELIVERY_AND_UNLOADING 	= 3;

	/*
	* Constantes liées au champs fields_scope --> pour stocker la préférence de l'utilisateur sur la vue des
	* champs libres dans la fiche article
	*/
	const FIELDS_SCOPE_FULL = 0;
	const FIELDS_SCOPE_FAMILY = 1;

	private $activePag = true;

	// Filtres sur une liste d'articles via build_index
	private $filters;
	private $filter_values;
	private $additional_filters;
	private $filters_view;

	// Détail du stock de toutes les sociétés
	private $stock_detail;


	public function article() {
		parent::dims_data_object(self::TABLE_NAME, 'id', 'id_lang');
	}

	public function openFromResultSet($fields, $unset_db = false, $go_object_value = null){
		parent::openFromResultSet($fields, $unset_db, $go_object_value);
	}

	public function setid_object(){
		$this->id_globalobject = self::MY_GLOBALOBJECT_CODE;
	}

	public function settitle(){
		$this->title = 'ARTICLE id n° '.$this->get('id');
	}

	public function open(){
		$id = func_get_arg(0);
		if(func_num_args() > 1)
			$id_lang = func_get_arg(1);
		else $id_lang = cata_param::getDefaultLang();
		return parent::open($id, $id_lang);
	}

	public function prepareindexbeforechanges($full_index = false) {
		if( ! $full_index ) $this->controlChangesBeforeIndex(true);
		$this->to_index(array('reference', 'label', 'description', 'longdescription', 'gencode', 'code_barre', 'fields1','fields2','fields3','fields4','fields5','fields6','fields7','fields8','fields9','fields10','fields11','fields12','fields13','fields14','fields15','fields16','fields17','fields18','fields19','fields20','fields21','fields22','fields23','fields24','fields25','fields26','fields27','fields28','fields29','fields30','fields31','fields32','fields33','fields34','fields35','fields36','fields37','fields38','fields39','fields40','fields41','fields42','fields43','fields44','fields45','fields46','fields47','fields48','fields49','fields50','fields51','fields52','fields53','fields54','fields55','fields56','fields57','fields58','fields59','fields60','fields61','fields62','fields63','fields64','fields65','fields66','fields67','fields68','fields69','fields70','fields71','fields72','fields73','fields74','fields75','fields76','fields77','fields78','fields79','fields80','fields81','fields82','fields83','fields84','fields85','fields86','fields87','fields88','fields89','fields90','fields91','fields92','fields93','fields94','fields95','fields96','fields97','fields98','fields99','fields100','fields101','fields102','fields103','fields104','fields105','fields106','fields107','fields108','fields109','fields110','fields111','fields112','fields113','fields114','fields115','fields116','fields117','fields118','fields119','fields120','fields121','fields122','fields123','fields124','fields125','fields126','fields127','fields128','fields129','fields130','fields131','fields132','fields133','fields134','fields135','fields136','fields137','fields138','fields139','fields140','fields141','fields142','fields143','fields144','fields145','fields146','fields147','fields148','fields149','fields150'));
		$this->belongs_to('cata_marque', cata_marque::TABLE_NAME, 'marque', 'id', array('mode' => mb_object_relation::MB_RELATION_ID_TYPE, 'ext_index' => mb_object_relation::MB_RELATION_ON_ME_INDEX));
	}

	public function save() {
		//Nettoyage de la ref pour eviter les prob quand on la passe dans l'url
		$caract = array('\'','"','&','$','=','?');
		$this->fields['reference'] = str_replace($caract,'-',$this->fields['reference']);

		//Mysql n'aime pas les , dans les float
		$this->fields['poids'] = isset($this->fields['poids']) ?  str_replace(',','.',$this->fields['poids']) : '';
		$this->fields['putarif_0'] = isset($this->fields['putarif_0']) ?  str_replace(',','.',$this->fields['putarif_0']) : '';
		$this->fields['puttc'] = isset($this->fields['puttc']) ?  str_replace(',','.',$this->fields['puttc']) : '';

		// Unité de vente par défaut
		if (empty($this->fields['uvente'])) {
			$this->fields['uvente'] = 1;
		}

		return parent::save(self::MY_GLOBALOBJECT_CODE);#on peut sauvegarder
	}

	public function setUrlrewrite() {
		require_once DIMS_APP_PATH.'modules/catalogue/include/functions.php';
		$this->fields['urlrewrite'] = cata_genRewrite($this->fields['label']);
	}

	#Cyril - 03/01/2013 - Méthode qui retourne toutes les traductions de l'article
	public function getTranslations(){
		$translations = array();
		$translations[$this->fields['id_lang']] = $this;
		$res = $this->db->query('SELECT * FROM '.self::TABLE_NAME.' WHERE id = '.$this->fields['id'].' AND id_lang != '.$this->fields['id_lang']);
		while($fields = $this->db->fetchrow($res)){
			$art = new article();
			$art->openFromResultSet($fields);
			$translations[$art->fields['id_lang']] = $art;
		}
		return $translations;
	}

	#Cyril - 03/01/2013 - Méthode qui retourne toutes les instances d'article qui ont le même GO
	public static function findByGO($id_go){
		$translations = array();
		$db = dims::getInstance()->getDb();
		$res = $db->query('SELECT * FROM '.self::TABLE_NAME.' WHERE id_globalobject='.$id_go);
		while($fields = $db->fetchrow($res)){
			$art = new article();
			$art->prepareindexbeforechanges();
			$art->openFromResultSet($fields);
			$translations[$art->fields['id_lang']] = $art;
		}
		return $translations;
	}

	public function delete()
	{
		include_once DIMS_APP_PATH.'/modules/catalogue/class_article_photo.php';

		$obj_art_photo = new cata_article_photo();

		//suppression des grilles de tarifs
		$this->db->query("DELETE FROM dims_mod_cata_article_tarif_degr WHERE id_article=".$this->fields['id']);

		//suppression des jointure vers les photos (et des photos via le obj_art_photo->delete)
		$requete = "SELECT	id_photo
					FROM	dims_mod_cata_article_photo
					WHERE	dims_mod_cata_article_photo.id_article=".$this->fields['id']."
					AND		id_module = {$_SESSION['dims']['moduleid']}
					AND		id_group IN (". dims_viewgroups($_SESSION['dims']['moduleid']) .")";
		$result = $this->db->query($requete);
		while ($fields = $db->fetchrow($result))
		{
			$obj_art_photo->open($fields['id_photo']);
			$obj_art_photo->delete();
		}

		//Suppression des jointures avec d'autres articles
		$this->db->query("DELETE FROM dims_mod_cata_article_rattache WHERE id_article = ".$this->fields['id']." OR id_option = ".$this->fields['id']);

		//Suppression des sous_ref
		$this->db->query("DELETE FROM dims_mod_cata_article_sous_ref WHERE id_article_2 = ".$this->fields['id']);

		//Suppression des rattachements aux familles
		$this->db->query("DELETE FROM dims_mod_cata_article_famille WHERE id_article = ".$this->fields['id']);

		//suppression de l'article
		parent::delete();
	}

	public static function delete_all_lng($id) {
		require_once DIMS_APP_PATH.'/modules/catalogue/include/class_article_famille.php';
		$db = dims::getInstance()->getDb();
		$db->query('DELETE FROM `'.cata_article_famille::TABLE_NAME.'` WHERE `id_article` = '.$id);
		$db->query('UPDATE `'.self::TABLE_NAME.'` SET `status` = "'.self::STATUS_DELETED.'" WHERE `id` = '.$id);
	}

	// renvoie une colonne de tarif
	public function getprix($tarif = '0') {
		if (isset($this->fields["putarif_$tarif"])) return($this->fields["putarif_$tarif"]);
		else return($this->fields["putarif_1"]);
	}

	// renvoie les tarifs degressifs de l'article
	public function getDegressif() {
		if (!isset($this->degressifs)) {
			$this->degressifs = array();
			$rs = $this->db->query('SELECT * FROM `dims_mod_cata_tarqte` WHERE `id_article` = \''.$this->fields['id'].'\' AND ccat = '.$_SESSION['catalogue']['ccat']);
			while ($row = $this->db->fetchrow($rs)) {
				$this->degressifs[] = $row;
			}
		}
	}

	public function getFams() {
		if  (!isset($_SESSION['catalogue']['correspart'])) {
			//die('r');
			$a_fams = array();
			$rs = $this->db->query('SELECT	caf.id_famille,ca.reference
					FROM	dims_mod_cata_article ca
					INNER JOIN	dims_mod_cata_article_famille caf
					ON			caf.id_article = ca.id');
			while ($row = $this->db->fetchrow($rs)) {
				$a_fams[$row['reference']][] = $row['id_famille'];
			}
			$_SESSION['catalogue']['correspart']=$a_fams;
		}
		/*
		$rs = $this->db->query('
			SELECT	caf.id_famille
			FROM	dims_mod_cata_article ca

			INNER JOIN	dims_mod_cata_article_famille caf
			ON			caf.id_article = ca.id

			WHERE	ca.reference = \''.$this->fields['reference'].'\'
			');
		while ($row = $this->db->fetchrow($rs)) {
			$a_fams[] = $row['id_famille'];
		}*/

		//return $a_fams;
		if (isset($a_fams[$this->fields['reference']])) return $a_fams[$this->fields['reference']];
		else return array();
	}

	public function getChampsDyn($nb_fields = 150) {
		// chargement des champs dynamiques
		$a_values = array();

		for ($i = 1; $i <= $nb_fields; $i++) {
			if (!empty($this->fields['field'.$i])) {
				$nomchamp = $_SESSION['catalogue']['champslibres']['champs'][$i]['libelle'];
				$typechamp = $_SESSION['catalogue']['champslibres']['champs'][$i]['type'];

				if ($typechamp == 'liste') {
					$a_values[$nomchamp] = $_SESSION['catalogue']['champslibres']['champsvaleur'][$this->fields['field'.$i]];
				}
				else {
					$a_values[$nomchamp] = $this->fields['field'.$i];
				}
			}
		}
		return $a_values;
	}

	// renvoie la liste des articles de remplacement qui ont du stock
	#Cyril - 07/08/2013 -> adaptation à la nouvelle mécanique de rattachement des articles
	public function getArticlesRempl() {
		$a_refs = array();
		$lstArts = $this->getLinkedArticles(link_type::TYPE_SUBSTITUTION, ' AND a.qte > 0');
		foreach($lstArts as $id_article => $art){
			$a_refs[] = $art->fields['reference'];
		}
		return $a_refs;
	}

	/**
	* @access public
	*/
	public function createclone($escape_save = false) {
		//copie de l'enregistrement courant
		$clone = new article();
		$clone->fields = $this->fields;
		$clone->fields['id'] = '';
		if( ! $escape_save ) $clone->save();
		return $clone;
	}

	public function tarif($qte=1) {
		//Traitement des autres articles
		$result = $db->query("
			SELECT	*
			FROM	dims_mod_cata_article_tarif_degr
			WHERE	dims_mod_cata_article_tarif_degr.id_article = {$this->fields['id']}
			AND		dims_mod_cata_article_tarif_degr.qte <= {$qte}
			AND		id_module = {$_SESSION['dims']['moduleid']}
			AND		id_group IN (". dims_viewgroups($_SESSION['dims']['moduleid']) .")
			ORDER BY qte DESC
			LIMIT 1");
		if ($this->db->numrows($result)>0) {
			$fields = $this->db->fetchrow($result);
		}
		else {
			$fields = $this->fields;
		}
		return $fields;
	}

	public function chg_posit($new_posit) {
		$this->fields['position'] = $new_posit;

		//Traitement des autres articles
		$result = $this->db->query("
			SELECT	id_article
			FROM	dims_mod_cata_article_famille
			WHERE	id_famille = {$_SESSION['catalogue']['familyId']}
			AND		id_article <> {$this->fields['id']}
			AND		id_module = {$_SESSION['dims']['moduleid']}
			ORDER BY position, id_article");
		$i=1;
		while ($fields = $this->db->fetchrow($result)) {
			if($i==$new_posit) $i++;
			$this->db->query("UPDATE dims_mod_cata_article_famille
						SET position = {$i}
						WHERE id_article={$fields['id']}");
			$i++;
		}
	}

	public function findByRef($ref) {
		$rs = $this->db->query('SELECT * FROM '.self::TABLE_NAME.' WHERE reference = \''.$ref.'\' LIMIT 0,1');
		$this->openFromResultSet($this->db->fetchrow($rs));
		return $this->db->numrows($rs);
	}

	public function drop_photo() {
		// TODO : Check if picture already used by others articles and delete if not.
		$this->fields['image'] = '';
		$this->save();
	}

	public function set_photo($fileName) {
		if ($fileName != '') {
			$this->fields['image'] = $fileName;
			$this->save();
		}
	}

	// famille du produit
	public function getfam() {
		return($this->fields['fam']);
	}

	public function getLabel(){
		return isset($this->fields['label']) ? $this->fields['label'] : '';
	}

	public function getPhoto($size = 100){
		$res = null;
		$path = realpath(".")."/photos/${size}x${size}/".$this->fields['image'];
		if($this->fields['image'] != '' && file_exists($path)) {
			return $path;
		} else {
			return $res;
		}
	}

	public function getWebPhoto($size=100){
		$res = null;

		$basepath =  realpath(".")."/photos/${size}x${size}/";
		$path = $basepath . $this->fields['image'];
		if(file_exists($path)){
			if($this->fields['image'] != '' && file_exists($path)) return "/photos/${size}x${size}/".$this->fields['image'];
			else return $res;
		}
		else
		{
			$orig_path = realpath(".")."/photos/orig/". $this->fields['image'];
			if( file_exists($orig_path) ){
				#création du dossier s'il n'existe pas déjà
				if( ! file_exists($basepath)){
					mkdir($basepath);
				}
				dims_resizeimage($orig_path, 0, $size, $size,'jpeg',0,$path);
				return "/photos/${size}x${size}/".$this->fields['image'];
			}
		}
		return $res;
	}

	public function activePagination($val = true){
		$this->activePag = $val;
	}

	public function setAdditionalFilters($search_filters) {
		$this->additional_filters = $search_filters;
	}

	public function setFiltersView($filters_view) {
		$this->filters_view = $filters_view;
	}

	#Cyril -> 28/11/2012 <- Construction de la liste des articles
	public function build_index($lang, $status = self::STATUS_OK, $publication = 'all', $type = 'all', $famille = 'dims_nan', $unattached = 0, $keywords = '', $in_clipboard = null, $sort_by = 'ref', $sort_way='ASC', $include_photos = false, $pagination=false){
		$db = dims::getInstance()->getDb();

		if ($this->activePag && !$pagination) {
			$this->total_index = $this->build_index($lang, $status, $publication, $type, $famille, $unattached, $keywords, $in_clipboard, $sort_by, $sort_way, $include_photos, true);
			pagination::liste_page($this->total_index);
			$limit = " LIMIT ".$this->sql_debut.", ".$this->limite_key;
		}
		else $limit="";
		$where = "WHERE a.id_lang='".$lang."' ";

		switch($publication){
			case 'published':
				$where .= ' AND a.published = 1';
				break;
			case 'unpublished':
				$where .= ' AND a.published = 0';
				break;
			default: //Tous
				break;
		}

		switch($type){
			case 'kit':
				$where .= ' AND a.kit = 1';
				break;
			case 'classical':
				$where .= ' AND a.kit = 0';
				break;
			default: // Tous
				break;
		}

		switch($status){
			case self::STATUS_OK:
				$where .= ' AND a.status = \''.self::STATUS_OK.'\'';
				break;
			case self::STATUS_DELETED:
				$where .= ' AND a.status = \''.self::STATUS_DELETED.'\'';
				break;
			default: //Tous
				break;
		}
		$inner_plus = ' ';
		if ($pagination) { // quand on va calculer la pagination inutile de ramener le multi-langue
			$select_plus = ' ';
			$order_by = ' ';
		}
		else{
			$select_plus = ', a.label ';
			$order_by = ' ORDER BY ';
			if( $sort_by == 'ref' )
				$order_by .= 'a.reference ';
			else $order_by .= 'a.label ';

			$order_by .= $sort_way;
		}

		if(!empty($famille)){
			if(is_array($famille))
				$inner_plus .= ' INNER JOIN dims_mod_cata_article_famille caf ON caf.id_article = a.id AND caf.id_famille IN ('.implode(',',$famille).")";
			elseif($famille != 'dims_nan')
				$inner_plus .= ' INNER JOIN dims_mod_cata_article_famille caf ON caf.id_article = a.id AND caf.id_famille = '.$famille;
		}

		if( ( empty($famille) || $famille == 'dims_nan' ) && $unattached ){
			$inner_plus .= ' LEFT JOIN dims_mod_cata_article_famille caf ON caf.id_article = a.id';
			$where .= ' AND caf.id_famille IS NULL';
		}

		if( ! empty($keywords)){
			#Récupération des ids d'article correspondant aux keywords
			$go_ids = article::simple_search($keywords);
			if(!empty($go_ids)){
				$where .= " AND a.id_globalobject IN (".implode(',', $go_ids).")";
			}
			else $where .= " AND a.id_globalobject IN (-1) ";
		}

		if( !is_null($in_clipboard) ){
			if(empty($in_clipboard)) $in = '(0)';
			else $in = "(".implode(',', $in_clipboard).")";
			$where .= " AND a.id IN ".$in;
		}

		if( $include_photos){ #C'est pour pouvoir traiter les vignettes en batch plutôt que de les traiter une par une à l'affichage ce qui aurait pour conséquence de lancer une requête par article
			$inner_plus .= ' LEFT JOIN dims_mod_cata_article_thumbnail thumb ON a.id = thumb.id_article AND thumb.position=1
							 LEFT JOIN dims_mod_doc_file doc ON doc.id = thumb.id_doc';
			$select_plus .= ', doc.* ';
		}

		if ($pagination) {
			// On recherche les filtres si on a tapé un mot clé ou sélectionné une famille
			// Vue globale
			if ( $this->filters_view == 'global' && (!empty($keywords) || (!empty($famille) && $famille != 'dims_nan')) ) {
				// Lorsqu'on compte le nombre d'éléments, on va rechercher
				// la liste des filtres en commun pour tous les résultats
				$this->filter_values = array();

				// Requete de recherche des filtres
				$sql = 'SELECT a.* '.$select_plus.'
						FROM '.self::TABLE_NAME.' a '.$inner_plus.' '.$where.' '.$order_by.' '.$limit;

				$res = $this->db->query($sql);
				while ($row = $this->db->fetchrow($res)) {
					$current_filters = array();

					for ($i = 1; $i <= 150; $i++) {
						if (!is_null($row['fields'.$i]) && trim($row['fields'.$i]) != '') {
							$current_filters[] = $i;
							$this->filter_values[$i][$row['fields'.$i]] = 1;
						}
					}

					// Merge avec les filtres des autres articles
					if (isset($this->filters)) {
						$this->filters = array_unique(array_merge($this->filters, $current_filters));
					}
					else {
						$this->filters = $current_filters;
					}
				}
			}
			else {
				$this->filters = null;
			}
		}

		if ( !is_null($this->additional_filters) ){
			foreach ($this->additional_filters as $key => $value) {
				$where .= " AND a.fields".$key." = ".$value;
			}
		}

		// On recherche les filtres si on a tapé un mot clé ou sélectionné une famille
		// Vue globale
		if ( !$pagination && $this->filters_view == 'filtered' && (!empty($keywords) || (!empty($famille) && $famille != 'dims_nan')) ) {
			// Lorsqu'on compte le nombre d'éléments, on va rechercher
			// la liste des filtres en commun pour tous les résultats
			$this->filter_values = array();

			// Requete de recherche des filtres
			$sql = 'SELECT a.* '.$select_plus.'
					FROM '.self::TABLE_NAME.' a '.$inner_plus.' '.$where.' '.$order_by;

			$res = $this->db->query($sql);
			while ($row = $this->db->fetchrow($res)) {
				$current_filters = array();

				for ($i = 1; $i <= 150; $i++) {
					if (!is_null($row['fields'.$i])) {
						$current_filters[] = $i;
						$this->filter_values[$i][$row['fields'.$i]] = 1;
					}
				}

				// Merge avec les filtres des autres articles
				if (isset($this->filters)) {
					$this->filters = array_unique(array_merge($this->filters, $current_filters));
				}
				else {
					$this->filters = $current_filters;
				}
			}
		}

		// Requete standard avec tous les critères de recherche
		$sql = 'SELECT a.* '.$select_plus.'
				FROM '.self::TABLE_NAME.' a '.$inner_plus.' '.$where.' '.$order_by.' '.$limit;

        // optimisation Pat du 11/07/2015
        // ALTER TABLE `dims_mod_cata_article` ADD INDEX (`id_lang`, `status`, `id_globalobject`);

		$res = $this->db->query($sql);

		if ($pagination) {
			// On renvoie le nombre d'élements
			return $this->db->numrows($res);
		}
		else{
			$split = $this->db->split_resultset($res);
            //$split=array();
            $lst = array();
            $artmodele = new article();
			foreach($split as $tab){
                $art = clone $artmodele;
				//$art->openFromResultSet($tab['a']);
                $art->openWithFields($tab['a']);
				$familles = array();
				$art->setLightAttribute('familles', $familles);
				if( ! empty($tab['doc']['id']) ){
					$doc = new docfile();
					$doc->openFromResultSet($tab['doc']);
					$art->setLightAttribute('thumb', $doc);
				}
				$lst[$art->fields['id']] = $art;
			}
			if(count($lst)){
				#Récupération des familles associées en 1 seule requête pour l'optimisation
				$sql2 = "SELECT caf.id_article, f.id, f.label
						 FROM dims_mod_cata_famille f
						 INNER JOIN dims_mod_cata_article_famille caf ON caf.id_famille = f.id
						 WHERE caf.id_article IN (".implode(',' , array_keys($lst)).")
						 AND f.id_lang = ".$lang."
						 ORDER BY caf.id_article, f.label ASC";

				$res2 = $this->db->query($sql2);

				while($fields = $this->db->fetchrow($res2)){
					$familles = $lst[$fields['id_article']]->getLightAttribute('familles');
					$familles[$fields['id']] = $fields['label'];
					$lst[$fields['id_article']]->setLightAttribute('familles', $familles);
				}
			}
			return $lst;
		}
	}

	#Fonction simple de recherche textuelle d'articles utilisée uniquement en backoffice
	public static function simple_search($keywords, $id_module = 0){
		if ($id_module == 0 && !empty($_SESSION['dims']['moduleid'])) {
			$id_module = $_SESSION['dims']['moduleid'];
		}
		if ($id_module > 0) {
			include_once DIMS_APP_PATH.'/modules/system/class_search.php';
			$dims = dims::getInstance();
			$dimsearch = new search($dims);
			$dimsearch->addSearchObject($id_module, self::MY_GLOBALOBJECT_CODE, '');
			$dimsearch->initSearchObject();
			$dimsearch->executeSearch2($keywords, '', $id_module, self::MY_GLOBALOBJECT_CODE, 0, '', 0, null, null, is_numeric($keywords), false);

			return array_keys($dimsearch->tabresultat[$id_module][self::MY_GLOBALOBJECT_CODE]);
		}

		return null;
	}

	public function disable($auto_save = false){
		$translations = $this->getTranslations();
		foreach($translations as $id_lang => $article){
			$article->fields['status'] = self::STATUS_DELETED;
			if($auto_save) $article->save();
		}
	}
	public function enable(){
		$this->fields['status'] = self::STATUS_OK;
	}

	public function isDeleted(){
		return $this->fields['status'] == self::STATUS_DELETED;
	}

	public function isOK(){
		return $this->fields['status'] == self::STATUS_OK;
	}

	public function isPublished(){
		return $this->fields['published'];
	}

	public function getFilters() {
		if (!is_null($this->filters)) {
			sort($this->filters);
		}
		return $this->filters;
	}

	public function getFilterValues($filter_id) {
		if (isset($this->filter_values[$filter_id])) {
			return $this->filter_values[$filter_id];
		}
		else {
			return null;
		}
	}

	public function getThumbnails($notIn = array()){
		$db = dims::getInstance()->getDb();
		$sel = "SELECT 		d.*, lk.*
				FROM 		".docfile::TABLE_NAME." d
				INNER JOIN 	".cata_art_thumb::TABLE_NAME." lk
				ON 			lk.id_doc = d.id
				WHERE 		lk.id_article = ".$this->fields['id']."
				".((count($notIn))?"AND lk.id_doc NOT IN (".implode(',',$notIn).")":"")."
				ORDER BY	lk.position";
		$res = $db->query($sel);
		$lst = array();
		foreach($db->split_resultset($res) as $r){
			$lk = new cata_art_thumb();
			$lk->openFromResultSet($r['lk']);
			$doc = new docfile();
			$doc->openFromResultSet($r['d']);
			$lk->setDocFile($doc);
			$lst[] = $lk;
		}
		return $lst;
	}

	public function getNbThumbnails(){
		$db = dims::getInstance()->getDb();
		$sel = "SELECT 		d.*, lk.*
				FROM 		".docfile::TABLE_NAME." d
				INNER JOIN 	".cata_art_thumb::TABLE_NAME." lk
				ON 			lk.id_doc = d.id
				WHERE 		lk.id_article = ".$this->fields['id']."
				ORDER BY 	lk.position";
		$res = $db->query($sel);
		return $db->numrows($res);
	}

	public static function reverse_publication($lst){
		if(!empty($lst)){
			foreach($lst as $id){
				$art = new article();
				$art->open($id);
				$art->reversePublication();
			}
		}
	}

	public function reversePublication(){
		$translations = $this->getTranslations();
		foreach($translations as $id_lang => $article){
			$article->fields['published'] = !$article->fields['published'];
			$article->save();
		}
	}

	public function isDegressif(){
		return $this->fields['degressif'];
	}

	public function cleanFamiliesAttachment(){
		$this->db->query("DELETE FROM dims_mod_cata_article_famille WHERE id_article = ".$this->fields['id']);
	}

	public function clearDegressiveTable(){
		$this->db->query("DELETE FROM `dims_mod_cata_tarqte` WHERE `id_article` = '".$this->fields['id']."'");
	}

	public function getDegressiveTable(){
		$degress = array();
		$sql = "SELECT *
			FROM `dims_mod_cata_tarqte`
			WHERE `reference` = '".$this->fields['reference']."'
			AND ".dims_createtimestamp()." BETWEEN `datedeb` AND `datefin`
			AND `id_module` = ".$this->fields['id_module']."
			AND `id_workspace` = ".$this->fields['id_workspace'];
		$rs = $this->db->query($sql);
		while ($fields = $this->db->fetchrow($rs)) {
			$degress[$fields['qtedeb']] = $fields['puqte'];
		}
		return $degress;
	}

	#Fonction permettant de gérer l'ajout d'une nouvelle vignette uploadée depuis le web
	public function storeVignette($uploaded_file){
		$doc = new docfile();
		$doc->init_description();
		$doc->setugm();
		$doc->fields['id_folder'] = -1;
		$doc->tmpuploadedfile = $uploaded_file['tmp_name'];
		$doc->fields['name'] = $uploaded_file['name'];
		$doc->fields['size'] = filesize($uploaded_file['tmp_name']);
		$doc->save();

	$image = new Imagick( $doc->getfilepath() );
		$image->resizeImage(600,600,Imagick::FILTER_LANCZOS,1);
		$image->writeImage( $doc->getfilepath() );

		$lk = new cata_art_thumb();
		$lk->init_description();
		$lk->setugm();
		$lk->fields['id_article'] = $this->get('id');
		$lk->setDocFile($doc);
		$lk->save();
	}


	public function getVignette( $size = 100, $position = 1 ){
		$thumb = $this->getLightAttribute('thumb');
		if( ! isset($thumb) ){
			$res = $this->db->query("SELECT *
									 FROM ".docfile::TABLE_NAME." doc
									 INNER JOIN ".cata_art_thumb::TABLE_NAME." thumb ON thumb.id_doc = doc.id
									 WHERE thumb.id_article = ".$this->get('id')." AND thumb.position = ".$position." LIMIT 1");
			if($this->db->numrows($res)){
				$fields = $this->db->fetchrow($res);
				$thumb = new docfile();
				$thumb->openFromResultSet($fields);
				return $thumb->getThumbnail($size);
			}
			else return null;
		}
		else return $thumb->getThumbnail($size);
	}

	public function getOriginal($position = 1){
		$res = $this->db->query("SELECT *
									 FROM ".docfile::TABLE_NAME." doc
									 INNER JOIN ".cata_art_thumb::TABLE_NAME." thumb ON thumb.id_doc = doc.id
									 WHERE thumb.id_article = ".$this->get('id')." AND thumb.position = ".$position." LIMIT 1");
		if($this->db->numrows($res)){
			$fields = $this->db->fetchrow($res);
			$thumb = new docfile();
			$thumb->openFromResultSet($fields);
			return $thumb;
		}
		else return null;

	}

	public function isKit(){
		return $this->fields['kit'];
	}

	public function clearKitComposition(){
		$this->db->query("DELETE FROM `dims_mod_cata_article_kit` WHERE `id_article_kit` = '".$this->fields['id']."'");
	}

	public function getKit($include_article = false) {
		$packagedArticles = array();
		if( $this->isKit() ) {

			$sql = 'SELECT *
					FROM dims_mod_cata_article_kit kit
					INNER JOIN 	dims_mod_cata_article article
					ON 			article.id = kit.id_article_attach
					WHERE 		kit.id_article_kit = '.$this->get('id');

			$res = $this->db->query($sql);

			$split = $this->db->split_resultset($res);
			foreach($split as $tab){
				$component = new article_kit();
				$component->openFromResultSet($tab['kit']);
				if($include_article){
					$art = new article();
					$art->openFromResultSet($tab['article']);
					$component->setLightAttribute('component', $art);
				}
				$packagedArticles[] = $component;
			}
		}
		return $packagedArticles;
	}

	public function getFamilles($id_lang = -1){
		if ($id_lang == -1) {
			$id_lang = $_SESSION['dims']['currentlang'];
		}

		$sql = "SELECT f.*
				FROM dims_mod_cata_article_famille caf
				INNER JOIN dims_mod_cata_famille f
				ON f.id = caf.id_famille
				AND f.id_lang = ".$id_lang."
				WHERE caf.id_article = ".$this->get('id')."
				ORDER BY f.label ASC";

		$res = $this->db->query($sql);
		$tab = array();
		while($fields = $this->db->fetchrow($res)){
			$f = new cata_famille();
			$f->openFromResultSet($fields, false, null, false); //Le false à la fin permet d'éviter de recharger les langues avec l'initLangue
			$tab[$f->get('id')] = $f;
		}
		return $tab;
	}

	public function clearNetPrices(){
		$this->db->query("DELETE FROM ".cata_prix_nets::TABLE_NAME." WHERE reference = '".$this->fields['reference']."'");
	}

	#Le paramètre $include_clients permet d'optimiser la récupération des clients associés aux prix nets de l'article
	public function getPrixNets($include_clients = false){
		include_once DIMS_APP_PATH."modules/system/class_tiers.php";
		include_once DIMS_APP_PATH."modules/catalogue/include/class_client.php";

		$db = dims::getInstance()->getDb();
		$now = dims_createtimestamp();

		$sel_plus = $inner_plus = '';
		if($include_clients){
			$sel_plus	= ', cli.*, t.* ';
			$inner_plus = ' INNER JOIN '.client::TABLE_NAME.' cli ON cli.code_client = pn.code_cm AND pn.type = \'C\'
							LEFT JOIN '.tiers::TABLE_NAME.' t ON t.id = cli.tiers_id ';
		}
		$sel = "SELECT	pn.* ".$sel_plus."
				FROM	".cata_prix_nets::TABLE_NAME." pn ".
				$inner_plus."
				WHERE	reference = '".$this->fields['reference']."'
				AND	(datedeb = '00000000000000' OR datedeb <= '".$now."')
				AND	(datefin = '00000000000000' OR datefin >= '".$now."')";

		$lst = array();
		$res = $db->query($sel);
		$split = $db->split_resultset($res);
		foreach($split as $tab){
			$elem = new cata_prix_nets();
			$elem->openFromResultSet($tab['pn']);
			$tiers = new tiers();
			$tiers->openFromResultSet($tab['t']);
			$client = new client();
			$client->openFromResultSet($tab['cli']);
			$client->assignTiers($tiers);
			$elem->setLightAttribute('client', $client);
			$lst[$client->getCode()] = $elem;
		}
		return $lst;
	}

	public function createPrixNets($code_client, $puht){
		$elem = new cata_prix_nets();
		$elem->open($code_client, $this->fields['reference']);
		if($elem->isNew()){
			$elem = new cata_prix_nets();
			$elem->init_description();
			$elem->fields['datedeb'] = dims_createtimestamp();
			$elem->fields['datefin'] = '00000000000000';
		}
		$elem->fields['code_cm'] = $code_client;
		$elem->fields['reference'] = $this->fields['reference'];
		$elem->fields['puht'] = $puht;
		$elem->fields['type'] = 'C';
		$elem->save();
	}

	public function setFieldsScope($scope){
		$this->fields['fields_scope'] = $scope;
	}
	public function isFullScope(){
		return $this->fields['fields_scope'] == self::FIELDS_SCOPE_FULL;
	}

	public function getLinkedArticles($type = null, $special_conditions = ' ') {
		$where_plus = ' ';
		if( ! is_null($type) ){
			$where_plus .= 'AND al.type = '.$type;
		}

		$res = $this->db->query("SELECT a.*, al.id as link_id, al.type as type_link, al.symetric
			FROM ".article_link::TABLE_NAME." al
			INNER JOIN ".article::TABLE_NAME." a ON al.id_article_to = a.id
			WHERE al.id_article_from = ".$this->get('id').$where_plus.$special_conditions);

		if( is_null($type) ) $lst = array();
		else $lst[$type] = array();

		// Dans le cas d'un article phytosanitaire, on vérifie que le client a bien un certiphyto
		$certiphyto = !(isset($_SESSION['catalogue']['enr_certiphyto']) && $_SESSION['catalogue']['enr_certiphyto'] == 0);

		while($fields = $this->db->fetchrow($res)){
			$article = new article();
			$symetric = $fields['symetric'];
			$type_link = $fields['type_link'];
			$link_id = $fields['link_id'];
			unset($fields['symetric']);
			unset($fields['type_link']);
			unset($fields['link_id']);
			$article->openFromResultSet($fields);
			$article->setLightAttribute('sym_link', $symetric);
			$article->setLightAttribute('link_id', $link_id);

			// On vérifie qu'on peut consulter l'article en fonction des règles de filtrage
			if (!$article->get('certiphyto') || $certiphyto) {
				if (isset($_SESSION['catalogue']['id_company'])) {
					if ( $article->isHeldInStock($_SESSION['catalogue']['id_company']) || $article->getStockTotal($_SESSION['catalogue']['id_company']) > 0 ) {
						$lst[$type_link][$fields['id']] = $article;
					}
				}
				else {
					if ( $article->isHeldInStock() || $article->getStockTotal() > 0 ) {
						$lst[$type_link][$fields['id']] = $article;
					}
				}
			}
		}
		if( is_null($type) ) return $lst;
		else return $lst[$type];
	}

	/* --- Méthode de calcul de tarifs -------------------------------------------------------------------------------------------------------------- */

	public function getPUHT($idx = 0){
		// Ouverture du param cata_base_ttc
		$dims = dims::getInstance();
		$mods = $dims->getModuleByType('catalogue');
		$catalogue_moduleid = $mods[0]['instanceid'];

		$oCatalogue = new catalogue();
		$oCatalogue->open($catalogue_moduleid);
		$oCatalogue->loadParams();

		if ($oCatalogue->getParams('cata_base_ttc')) {
			if (!empty($this->fields['ctva'])) {
				$tva = tva::findByCode($this->fields['ctva']);
				return (isset($this->fields['putarif_'.$idx])) ? $this->fields['putarif_'.$idx] / (1 + $tva[0]->getTaux() / 100) : 0;
			}
			else {
				return (isset($this->fields['putarif_'.$idx])) ? $this->fields['putarif_'.$idx] : 0;
			}
		}
		else {
			return (isset($this->fields['putarif_'.$idx]) ) ? $this->fields['putarif_'.$idx] : 0;
		}
	}

	public function getLocalRemise($idx = 1){
		return (isset($this->fields['rempromo_'.$idx]) ) ? $this->fields['rempromo_'.$idx] : 0;
	}

	public function calculate_PUHTRemise($idx = 0){
		$puht = $this->getPUHT($idx);
		$rem = $this->getLocalRemise($idx+1);#Décalage entre l'index des prix et des remises
		return $puht * ( 1 - ($rem/100));
	}

	public function getTauxTVA(){
		$tva = 0;
		#Récupération du pays de l'entreprise courante
		$work = new workspace();
		$work->open($_SESSION['dims']['workspaceid']);
		$tiers = $work->getTiers();
		if(isset($tiers) && !empty($tiers->fields['id_country']) && !empty($this->fields['ctva'])) {
			#Récupération du code TVA de l'article
			$ctva = new tva();
			$ctva->open($this->fields['ctva'], $tiers->fields['id_country']);
			$tva = $ctva->getTaux();
		}
		return $tva;
	}

	public function calculate_PUTTC($idx = 0){
		// Ouverture du param cata_base_ttc
		$dims = dims::getInstance();
		$mods = $dims->getModuleByType('catalogue');
		$catalogue_moduleid = $mods[0]['instanceid'];

		$oCatalogue = new catalogue();
		$oCatalogue->open($catalogue_moduleid);
		$oCatalogue->loadParams();

		if ($oCatalogue->getParams('cata_base_ttc')) {
			return (isset($this->fields['putarif_'.$idx])) ? $this->fields['putarif_'.$idx] * (1 - $this->getLocalRemise($idx+1) / 100) : 0;
		}
		else {
			if (!empty($this->fields['ctva'])) {
				$tva = tva::findByCode($this->fields['ctva']);
				if (empty($tva)) {
					return (isset($this->fields['putarif_'.$idx])) ? $this->fields['putarif_'.$idx] * (1 - $this->getLocalRemise($idx+1) / 100) : 0;
				}
				else {
					return (isset($this->fields['putarif_'.$idx])) ? $this->fields['putarif_'.$idx] * (1 - $this->getLocalRemise($idx+1) / 100) * (1 + $tva[0]->getTaux() / 100) : 0;
				}
			}
			else {
				return (isset($this->fields['putarif_'.$idx])) ? $this->fields['putarif_'.$idx] * (1 - $this->getLocalRemise($idx+1) / 100) : 0;
			}
		}
	}

	public function getPackagedArticle() {
		$packagedArticles = array();
		if($this->fields['kit'] == self::TYPE_KIT) {
			$sql = 'SELECT * 	FROM dims_mod_cata_articles_kit kit
					INNER JOIN 	dims_mod_cata_article article
					ON 			article.id = kit.id_article_attach
					INNER JOIN 	dims_mod_cata_article_lang al
					ON 			al.id_article_1 = article.id
					WHERE 		kit.id_article_kit = '.$this->get('id');

			$res = $this->db->query($sql);

			while($packagedArtRaw = $this->db->fetchrow($res)) {
				$packagedArt = new article();
				$packagedArt->openFromResultSet($packagedArtRaw);

				$packagedArt->fields['ispack'] = ( $packagedArt->fields['kit'] == self::TYPE_KIT);

				$packagedArticles[] = $packagedArt;
			}
		}
		return $packagedArticles;
	}

	public function getReference() {
		return $this->fields['reference'];
	}

	public function getMarqueLabel() {
		include_once DIMS_APP_PATH.'/modules/catalogue/include/class_marque.php';
		$marqueLabel = '';
		if (!empty($this->fields['marque'])) {
			$marque = new cata_marque();
			if ($marque->open($this->fields['marque'])) {
				$marqueLabel = $marque->getLabel();
			}
		}
		return $marqueLabel;
	}

	public function getConditionnement() {
		return $this->fields['cond'];
	}

	public function getWeight() {
		return $this->fields['poids'];
	}

	public function getStockDetail($id_company = 0) {
		if (is_null($this->stock_detail)) {
			require_once DIMS_APP_PATH.'modules/catalogue/include/class_stock.php';

			if ($id_company > 0) {
				$this->stock_detail = cata_stock::find_by(array('id_article' => $this->get('id'), 'id_company' => $id_company));
			}
			else {
				$this->stock_detail = cata_stock::find_by(array('id_article' => $this->get('id')));
			}
		}

		return $this->stock_detail;
	}

	public function getStockTotal($id_company = 0) {
		if ($id_company > 0) {
			$stock_detail = $this->getStockDetail($id_company);
		}
		else {
			$stock_detail = $this->getStockDetail();
		}
		$stock_total = 0;
		foreach ($stock_detail as $stock) {
			$stock_total += $stock->fields['stock'];
		}
		return $stock_total;
	}

	public function isHeldInStock($id_company = 0) {
		if ($id_company > 0) {
			$stock_detail = $this->getStockDetail($id_company);
		}
		else {
			$stock_detail = $this->getStockDetail();
		}
		$held_in_stock = false;
		foreach ($stock_detail as $stock) {
			if ($stock->get('held_in_stock')) {
				$held_in_stock = true;
			}
		}
		return $held_in_stock;
	}

	public function isEndOfLife($id_company = 0) {
		if ($id_company > 0) {
			$stock_detail = $this->getStockDetail($id_company);
		}
		else {
			$stock_detail = $this->getStockDetail();
		}
		$end_of_life = false;
		foreach ($stock_detail as $stock) {
			if ($stock->get('end_of_life')) {
				$end_of_life = true;
			}
		}
		return $end_of_life;
	}

	public function getReferences()
	{
		$references = array();

		if (!$this->isNew()) {
			$references = article_reference::find_by(array('id_article' => $this->fields['id']), ' ORDER BY position ASC ');
		}

		return $references;
	}
}
