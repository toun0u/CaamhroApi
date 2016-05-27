<?php
require_once DIMS_APP_PATH."/modules/system/desktopV2/include/global.php";
require_once DIMS_APP_PATH."/include/class_skin_common.php";
$desktop = new desktopv2();

$force_desktop = dims_load_securvalue('force_desktop',dims_const::_DIMS_CHAR_INPUT,true,true,true);
if(!empty($force_desktop)){
	unset($_SESSION['desktopv2']['concepts']['filters']);
}

$action=dims_load_securvalue('action',dims_const::_DIMS_CHAR_INPUT,true,true);
if ($action!="") {
	switch($action) {
		case 'null':
		default:
			ob_clean();
			die();
			break;

		case 'inet_add_sector':
			ob_clean();
			$id_popup = dims_load_securvalue('id_popup',dims_const::_DIMS_CHAR_INPUT,true,true);
			?>
			<div>
				<div class="actions">
					<a href="Javascript: void(0);" onclick="Javascript: dims_closeOverlayedPopup('<?php echo $id_popup; ?>');">
						<img src="modules/courrier/templates/backoffice/img/icon_close.gif" />
					</a>
				</div>
				<h2>New sector</h2>

				<p class="popup_add_value">
					<span><label for="new_sector_label">Sector :</label></span>
					<input type="text" id="new_sector_label" name="new_sector_label" />
					<input type="button" value="Create" onclick="javascript:popupAddSectorValue('<?php echo $id_popup; ?>');" />
				</p>
			</div>
			<script type="text/javascript">
				$("#new_sector_label").focus();
			</script>
			<?php
			die();
			break;

		case 'inet_add_sector_value':
			ob_clean();
			$label = dims_load_securvalue('label', dims_const::_DIMS_CHAR_INPUT, false, true);
			if ($label != '') {
				require_once DIMS_APP_PATH.'modules/system/opportunity/class_sector.php';
				$sector = new opportunity_sector();
				$sector->fields['label'] = $label;
				$sector->save();
				echo json_encode(array('id' => $sector->fields['id'], 'label' => $sector->fields['label']));
			}
			die();
			break;

		case 'inet_add_type':
			ob_clean();
			$id_popup = dims_load_securvalue('id_popup',dims_const::_DIMS_CHAR_INPUT,true,true);
			?>
			<div>
				<div class="actions">
					<a href="Javascript: void(0);" onclick="Javascript: dims_closeOverlayedPopup('<?php echo $id_popup; ?>');">
						<img src="modules/courrier/templates/backoffice/img/icon_close.gif" />
					</a>
				</div>
				<h2>New type</h2>

				<p class="popup_add_value">
					<span><label for="new_type_label">Type :</label></span>
					<input type="text" id="new_type_label" name="new_type_label" />
					<input type="button" value="Create" onclick="javascript:popupAddTypeValue('<?php echo $id_popup; ?>');" />
				</p>
			</div>
			<script type="text/javascript">
				$("#new_type_label").focus();
			</script>
			<?php
			die();
			break;

		case 'inet_add_type_value':
			ob_clean();
			$label = dims_load_securvalue('label', dims_const::_DIMS_CHAR_INPUT, false, true);
			if ($label != '') {
				require_once DIMS_APP_PATH.'modules/system/opportunity/class_type.php';
				$type = new opportunity_type();
				$type->fields['label'] = $label;
				$type->save();
				echo json_encode(array('id' => $type->fields['id'], 'label' => $type->fields['label']));
			}
			die();
			break;

		case 'inet_add_function':
			ob_clean();
			$id_popup = dims_load_securvalue('id_popup',dims_const::_DIMS_CHAR_INPUT,true,true);
			echo "balboa";
			?>
			<div class="actions">
				<a onclick="Javascript: dims_closeOverlayedPopup('<? echo $id_popup; ?>');" href="Javascript: void(0);">
					<img src="modules/assurance/templates/backoffice/img/icon_close.gif">
				</a>
			</div>
			<?
			die();
			break;

// --------------------- RECHERCHE SIMPLE --------------------------------------------------------------------------------------------
		case 'search2':
			ob_end_clean();
			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
				//suppression des éléments de filtrage de l'advanced search
				unset($_SESSION['dims']['advanced_search']['filters']);
				$_SESSION['dims']['advanced_search']['keep_opened'] = true;
				$_SESSION['dims']['search']['search_starting_by_tag'] = false;

				$expression = dims_load_securvalue('desktop_editbox_search', dims_const::_DIMS_CHAR_INPUT, true, true);
				$expression=str_replace("[et]","&",$expression);
				//die($expression);
				$position = -1;
				$position = dims_load_securvalue('replace_position', dims_const::_DIMS_NUM_INPUT, true, true);
				$replace_by = dims_load_securvalue('replace_by', dims_const::_DIMS_CHAR_INPUT, true, true);

				$kword					= dims_load_securvalue('kword', dims_const::_DIMS_CHAR_INPUT, true, true);
				$idmodule				= dims_load_securvalue('idmodule', dims_const::_DIMS_CHAR_INPUT, true, true);
				$idobj					= dims_load_securvalue('idobj', dims_const::_DIMS_CHAR_INPUT, true, true);
				$idmetafield			= dims_load_securvalue('idmetafield', dims_const::_DIMS_CHAR_INPUT, true, true);
				$sens					= dims_load_securvalue('sens', dims_const::_DIMS_CHAR_INPUT, true, true);
				$tag					= dims_load_securvalue('tag', dims_const::_DIMS_NUM_INPUT, true, true);
				require_once(DIMS_APP_PATH . "/modules/system/class_search.php");
				$dimsearch = new search($dims);
				$dimsearch->addallObject();
				$dimsearch->initSearchObject();

				if ($expression != "") {
					unset($_SESSION['desktop']['search']['tags']);//on vide les tags utilisés dans la précédente recherche

					if($tag && isset($_SESSION['dims']['tag_search']) && count($_SESSION['dims']['tag_search'])){
						$_SESSION['desktop']['search']['tags'] = $_SESSION['dims']['tag_search'];
					}else{
						unset($_SESSION['dims']['tag_search']);
					}

					if (!isset($_SESSION['dims']['modsearch']['expression_brut'])) $_SESSION['dims']['modsearch']['expression_brut']="";

					//$expression			= dims_load_securvalue('word', dims_const::_DIMS_CHAR_INPUT, true, true,false);
					if ($expression!='' && $expression!=$_SESSION['dims']['modsearch']['expression_brut']) {
						$_SESSION['dims']['modsearch']['expression_brut']=$expression;
						unset($_SESSION['dims']['selectedfilternews']);
					}


					if ($expression!='') {
						$_SESSION['dims']['modsearch']['expression_brut']=$expression;
					}



					$replace = array();
					if(isset($position) && isset($replace_by) && $position > -1 && !empty($replace_by)){
						$replace[$position] = urldecode($replace_by);
					}

					$dimsearch->executeSearch2($expression, $kword,$_SESSION['dims']['moduleid'], $idobj, $idmetafield, $sens,0,null,$_SESSION['dims']['workspaceid']);

					//$_SESSION['dims']['search']['result']=$dimsearch->tabresultat;
					//dims_print_r($dimsearch->tabresultat);

					$_SESSION['dims']['search']['current_search'] = $dimsearch->insertResultsForUser($dims->getUserId(), $dimsearch->tabresultat);
					$_SESSION['dims']['modsearch']['my_real_expression'] = stripslashes($expression);
					dims_redirect($dims->getScriptEnv().'?submenu='._DESKTOP_V2_DESKTOP.'&search=1&page=0&cat=0&mode=default');
				}
				else if(isset($position) && isset($replace_by) && $position > -1 && !empty($replace_by)){
					$replace[$position] = urldecode($replace_by);
					$dimsearch->executeSearch2($_SESSION['dims']['modsearch']['my_real_expression'], $kword,$_SESSION['dims']['moduleid'], $idobj, $idmetafield, $sens,0, $replace,$_SESSION['dims']['workspaceid']);
					$_SESSION['dims']['modsearch']['my_real_expression'] = $_SESSION['dims']['modsearch']['expression_brut'];
					$_SESSION['dims']['search']['current_search'] = $dimsearch->insertResultsForUser($dims->getUserId(), $dimsearch->tabresultat);
					dims_redirect($dims->getScriptEnv().'?submenu='._DESKTOP_V2_DESKTOP.'&search=1&page=0&cat=0&mode=default');
				}
				else{
					if($tag && isset($_SESSION['dims']['tag_search']) && count($_SESSION['dims']['tag_search'])){
						$_SESSION['desktop']['search']['tags'] = $_SESSION['dims']['tag_search'];
						unset($_SESSION['dims']['tag_search']);
						$_SESSION['dims']['advanced_search']['keep_opened'] = true;

						//lance la recherche
						require_once(DIMS_APP_PATH . "/modules/system/class_search.php");
						$search_tag = new search();
						$_SESSION['dims']['search']['current_search'] = $search_tag->startsWithTags($_SESSION['dims']['userid'], $_SESSION['desktop']['search']['tags']);
						$_SESSION['dims']['search']['search_starting_by_tag'] = true;
						dims_redirect($dims->getScriptEnv().'?submenu='._DESKTOP_V2_DESKTOP.'&search=1&page=0&cat=0&mode=default');
					}else{
						unset($_SESSION['dims']['tag_search']);
						dims_redirect($dims->getScriptEnv().'?submenu='._DESKTOP_V2_DESKTOP.'&mode=default&force_desktop=1');
					}
				}
			}
			die();
			break;
// --------------------- RECHERCHE AVANCEE --------------------------------------------------------------------------------------------
		case 'as_managefilter':
			ob_clean();
			$todo = false;
			$val = dims_load_securvalue('val', dims_const::_DIMS_CHAR_INPUT, true, true);
			$faction = dims_load_securvalue('faction', dims_const::_DIMS_CHAR_INPUT, true, true);
			if(!empty($val)){
				$type = dims_load_securvalue('type', dims_const::_DIMS_CHAR_INPUT, true, true);
				switch($type){
					case 'country':
						if($faction == 'add'){
							if(!isset($_SESSION['dims']['advanced_search']['filters']['countries']) || !in_array($val,$_SESSION['dims']['advanced_search']['filters']['countries'])){//on s'assure qu'il n'y est pas déjà
								$_SESSION['dims']['advanced_search']['filters']['countries'][$val] = $val;
								$todo = true;
							}
						}
						else if($faction == 'del'){
							if(isset($_SESSION['dims']['advanced_search']['filters']['countries']) && in_array($val,$_SESSION['dims']['advanced_search']['filters']['countries'])){
								unset($_SESSION['dims']['advanced_search']['filters']['countries'][$val]);
								$todo = true;
							}
						}
						break;
					case 'year':
						if($faction == 'add'){
							if(!isset($_SESSION['dims']['advanced_search']['filters']['years']) || !in_array($val,$_SESSION['dims']['advanced_search']['filters']['years'])){//on s'assure qu'il n'y est pas déjà
								$_SESSION['dims']['advanced_search']['filters']['years'][$val] = $val;
								$todo = true;
							}
						}
						else if($faction == 'del'){
							if(isset($_SESSION['dims']['advanced_search']['filters']['years']) && in_array($val,$_SESSION['dims']['advanced_search']['filters']['years'])){
								unset($_SESSION['dims']['advanced_search']['filters']['years'][$val]);
								$todo = true;
							}
						}
						break;
					case 'document':
						if($faction == 'add'){
							if(!isset($_SESSION['dims']['advanced_search']['filters']['documents']) || !in_array($val,$_SESSION['dims']['advanced_search']['filters']['documents'])){//on s'assure qu'il n'y est pas déjà
								$_SESSION['dims']['advanced_search']['filters']['documents'][$val] = $val;
								$todo = true;
							}
						}
						else if($faction == 'del'){
							if(isset($_SESSION['dims']['advanced_search']['filters']['documents']) && in_array($val,$_SESSION['dims']['advanced_search']['filters']['documents'])){
								unset($_SESSION['dims']['advanced_search']['filters']['documents'][$val]);
								$todo = true;
							}
						}
						break;

					case 'project':
						if($faction == 'add'){
							if(!isset($_SESSION['dims']['advanced_search']['filters']['projects']) || !in_array($val,$_SESSION['dims']['advanced_search']['filters']['projects'])){//on s'assure qu'il n'y est pas déjà
								$_SESSION['dims']['advanced_search']['filters']['projects'][$val] = $val;
								$todo = true;
							}
						}
						else if($faction == 'del'){
							if(isset($_SESSION['dims']['advanced_search']['filters']['projects']) && in_array($val,$_SESSION['dims']['advanced_search']['filters']['projects'])){
								unset($_SESSION['dims']['advanced_search']['filters']['projects'][$val]);
								$todo = true;
							}
						}
						break;


					case 'activity':
						if($faction == 'add'){
							if(!isset($_SESSION['dims']['advanced_search']['filters']['activities']) || !in_array($val,$_SESSION['dims']['advanced_search']['filters']['activities'])){//on s'assure qu'il n'y est pas déjà
								$_SESSION['dims']['advanced_search']['filters']['activities'][$val] = $val;
								$todo = true;
							}
						}
						else if($faction == 'del'){
							if(isset($_SESSION['dims']['advanced_search']['filters']['activities']) && in_array($val,$_SESSION['dims']['advanced_search']['filters']['activities'])){
								unset($_SESSION['dims']['advanced_search']['filters']['activities'][$val]);
								$todo = true;
							}
						}
						break;
					case 'opportunity':
						if($faction == 'add'){
							if(!isset($_SESSION['dims']['advanced_search']['filters']['opportunities']) || !in_array($val,$_SESSION['dims']['advanced_search']['filters']['opportunities'])){//on s'assure qu'il n'y est pas déjà
								$_SESSION['dims']['advanced_search']['filters']['opportunities'][$val] = $val;
								$todo = true;
							}
						}
						else if($faction == 'del'){
							if(isset($_SESSION['dims']['advanced_search']['filters']['opportunities']) && in_array($val,$_SESSION['dims']['advanced_search']['filters']['opportunities'])){
								unset($_SESSION['dims']['advanced_search']['filters']['opportunities'][$val]);
								$todo = true;
							}
						}
						break;
					case 'event':
						if($faction == 'add'){
							if(!isset($_SESSION['dims']['advanced_search']['filters']['events']) || !in_array($val,$_SESSION['dims']['advanced_search']['filters']['events'])){//on s'assure qu'il n'y est pas déjà
								$_SESSION['dims']['advanced_search']['filters']['events'][$val] = $val;
								$todo = true;
							}
						}
						else if($faction == 'del'){
							if(isset($_SESSION['dims']['advanced_search']['filters']['events']) && in_array($val,$_SESSION['dims']['advanced_search']['filters']['events'])){
								unset($_SESSION['dims']['advanced_search']['filters']['events'][$val]);
								$todo = true;
							}
						}
						break;
					case 'company':
						if($faction == 'add'){
							if(!isset($_SESSION['dims']['advanced_search']['filters']['companies']) || !in_array($val,$_SESSION['dims']['advanced_search']['filters']['companies'])){//on s'assure qu'il n'y est pas déjà
								$_SESSION['dims']['advanced_search']['filters']['companies'][$val] = $val;
								$todo = true;
							}
						}
						else if($faction == 'del'){
							if(isset($_SESSION['dims']['advanced_search']['filters']['companies']) && in_array($val,$_SESSION['dims']['advanced_search']['filters']['companies'])){
								unset($_SESSION['dims']['advanced_search']['filters']['companies'][$val]);
								$todo = true;
							}
						}
						break;
					case 'contact':
						if($faction == 'add'){
							if(!isset($_SESSION['dims']['advanced_search']['filters']['contacts']) || !in_array($val,$_SESSION['dims']['advanced_search']['filters']['contacts'])){//on s'assure qu'il n'y est pas déjà
								$_SESSION['dims']['advanced_search']['filters']['contacts'][$val] = $val;
								$todo = true;
							}
						}
						else if($faction == 'del'){
							if(isset($_SESSION['dims']['advanced_search']['filters']['contacts']) && in_array($val,$_SESSION['dims']['advanced_search']['filters']['contacts'])){
								unset($_SESSION['dims']['advanced_search']['filters']['contacts'][$val]);
								if($_SESSION['dims']['advanced_search']['filters']['only_me']){
									$u = new user();
									$u->open($_SESSION['dims']['userid']);
									$ct = new contact();
									$ct->open($u->fields['id_contact']);
									if(!$ct->isNew() && $val==$ct->fields['id_globalobject']){
										$_SESSION['dims']['advanced_search']['filters']['only_me'] = false;
									}
								}
								$todo = true;
							}
						}
						break;
					case 'dossier':
						if($faction == 'add'){
							if(!isset($_SESSION['dims']['advanced_search']['filters']['dossiers']) || !in_array($val,$_SESSION['dims']['advanced_search']['filters']['dossiers'])){//on s'assure qu'il n'y est pas déjà
								$_SESSION['dims']['advanced_search']['filters']['dossiers'][$val] = $val;
								$todo = true;
							}
						}
						else if($faction == 'del'){
							if(isset($_SESSION['dims']['advanced_search']['filters']['dossiers']) && in_array($val,$_SESSION['dims']['advanced_search']['filters']['dossiers'])){
								unset($_SESSION['dims']['advanced_search']['filters']['dossiers'][$val]);
								$todo = true;
							}
						}
						break;

		   			case 'suivi':
						if($faction == 'add'){
							if(!isset($_SESSION['dims']['advanced_search']['filters']['suivis']) || !in_array($val,$_SESSION['dims']['advanced_search']['filters']['suivis'])){//on s'assure qu'il n'y est pas déjà
								$_SESSION['dims']['advanced_search']['filters']['suivis'][$val] = $val;
								$todo = true;
							}
						}
						else if($faction == 'del'){
							if(isset($_SESSION['dims']['advanced_search']['filters']['suivis']) && in_array($val,$_SESSION['dims']['advanced_search']['filters']['suivis'])){
								unset($_SESSION['dims']['advanced_search']['filters']['suivis'][$val]);
								$todo = true;
							}
						}
						break;
					case 'region':
						if($faction == 'add'){
							$vals = explode(',',$val);
							$_SESSION['dims']['advanced_search']['filters']['region'] = array_combine($vals, $vals);
							$todo = true;
						}elseif($faction == 'del'){
							if(isset($_SESSION['dims']['advanced_search']['filters']['suivis']) && in_array($val,$_SESSION['dims']['advanced_search']['filters']['region'])){
								unset($_SESSION['dims']['advanced_search']['filters']['suivis'][$val]);
								$todo = true;
							}
						}
						break;
					case 'departement':
						if($faction == 'add'){
							$vals = explode(',',$val);
							$_SESSION['dims']['advanced_search']['filters']['departement'] = array_combine($vals, $vals);
							$todo = true;
						}elseif($faction == 'del'){
							if(isset($_SESSION['dims']['advanced_search']['filters']['departement']) && in_array($val,$_SESSION['dims']['advanced_search']['filters']['departement'])){
								unset($_SESSION['dims']['advanced_search']['filters']['departement'][$val]);
								$todo = true;
							}
						}
						break;
					case 'arrondissement':
						if($faction == 'add'){
							$vals = explode(',',$val);
							$_SESSION['dims']['advanced_search']['filters']['arrondissement'] = array_combine($vals, $vals);
							$todo = true;
						}elseif($faction == 'del'){
							if(isset($_SESSION['dims']['advanced_search']['filters']['arrondissement']) && in_array($val,$_SESSION['dims']['advanced_search']['filters']['arrondissement'])){
								unset($_SESSION['dims']['advanced_search']['filters']['arrondissement'][$val]);
								$todo = true;
							}
						}
						break;
					case 'canton':
						if($faction == 'add'){
							$vals = explode(',',$val);
							$_SESSION['dims']['advanced_search']['filters']['canton'] = array_combine($vals, $vals);
							$todo = true;
						}elseif($faction == 'del'){
							if(isset($_SESSION['dims']['advanced_search']['filters']['suivis']) && in_array($val,$_SESSION['dims']['advanced_search']['filters']['canton'])){
								unset($_SESSION['dims']['advanced_search']['filters']['suivis'][$val]);
								$todo = true;
							}
						}
						break;
					case 'dyn_ct':
						//suppression des éléments de filtrage de l'advanced search
						unset($_SESSION['dims']['advanced_search']['filters']);
						$_SESSION['dims']['advanced_search']['keep_opened'] = true;
						$_SESSION['dims']['search']['search_starting_by_tag'] = false;

						$expression=str_replace("[et]","&",$val);
						$position = -1;
						$position = dims_load_securvalue('replace_position', dims_const::_DIMS_NUM_INPUT, true, true);
						$replace_by = dims_load_securvalue('replace_by', dims_const::_DIMS_CHAR_INPUT, true, true);

						$kword					= dims_load_securvalue('val', dims_const::_DIMS_CHAR_INPUT, true, true);
						$idmodule				= dims_load_securvalue('idmodule', dims_const::_DIMS_CHAR_INPUT, true, true);
						$idobj					= dims_const::_SYSTEM_OBJECT_CONTACT;
						$idmetafield			= dims_load_securvalue('field', dims_const::_DIMS_CHAR_INPUT, true, true);
						$sens					= dims_load_securvalue('sens', dims_const::_DIMS_CHAR_INPUT, true, true);
						$tag					= dims_load_securvalue('tag', dims_const::_DIMS_NUM_INPUT, true, true);
						require_once(DIMS_APP_PATH . "/modules/system/class_search.php");
						$dimsearch = new search($dims);
						$dimsearch->addSearchObject(dims_const::_DIMS_MODULE_SYSTEM,dims_const::_SYSTEM_OBJECT_CONTACT,$_SESSION['cste']['_DIMS_LABEL_CONTACTS'],true,$idmetafield);
						//$dimsearch->addallObject();
						$dimsearch->initSearchObject();

						if ($expression != "") {
							unset($_SESSION['desktop']['search']['tags']);//on vide les tags utilisés dans la précédente recherche

							if($tag && isset($_SESSION['dims']['tag_search']) && count($_SESSION['dims']['tag_search'])){
								$_SESSION['desktop']['search']['tags'] = $_SESSION['dims']['tag_search'];
							}else{
								unset($_SESSION['dims']['tag_search']);
							}

							if (!isset($_SESSION['dims']['modsearch']['expression_brut'])) $_SESSION['dims']['modsearch']['expression_brut']="";

							//$expression			= dims_load_securvalue('word', dims_const::_DIMS_CHAR_INPUT, true, true,false);
							if ($expression!='' && $expression!=$_SESSION['dims']['modsearch']['expression_brut']) {
								$_SESSION['dims']['modsearch']['expression_brut']=$expression;
								unset($_SESSION['dims']['selectedfilternews']);
							}
							if ($expression!='') {
								$_SESSION['dims']['modsearch']['expression_brut']=$expression;
							}
							$replace = array();
							if(isset($position) && isset($replace_by) && $position > -1 && !empty($replace_by)){
								$replace[$position] = urldecode($replace_by);
							}
							$dimsearch->executeSearch2($val, $kword,$_SESSION['dims']['moduleid'], $idobj, $idmetafield, $sens,0,null,$_SESSION['dims']['workspaceid']);
							$_SESSION['dims']['search']['current_search'] = $dimsearch->insertResultsForUser($dims->getUserId(), $dimsearch->tabresultat);
							//$_SESSION['dims']['modsearch']['my_real_expression'] = stripslashes($expression);

							unset($_SESSION['dims']['advanced_search']['filters']['keywords']);
							$_SESSION['dims']['advanced_search']['filters']['dyn_ct']['val'] = $val;
							$_SESSION['dims']['advanced_search']['filters']['dyn_ct']['field'] = $idmetafield;

							dims_redirect($dims->getScriptEnv().'?submenu='._DESKTOP_V2_DESKTOP.'&search=1&page=0&cat=0&mode=default');
						}
						break;
					case 'dyn_tiers':
						//suppression des éléments de filtrage de l'advanced search
						unset($_SESSION['dims']['advanced_search']['filters']);
						$_SESSION['dims']['advanced_search']['keep_opened'] = true;
						$_SESSION['dims']['search']['search_starting_by_tag'] = false;

						$expression=str_replace("[et]","&",$val);
						$position = -1;
						$position = dims_load_securvalue('replace_position', dims_const::_DIMS_NUM_INPUT, true, true);
						$replace_by = dims_load_securvalue('replace_by', dims_const::_DIMS_CHAR_INPUT, true, true);

						$kword					= dims_load_securvalue('val', dims_const::_DIMS_CHAR_INPUT, true, true);
						$idmodule				= dims_load_securvalue('idmodule', dims_const::_DIMS_CHAR_INPUT, true, true);
						$idobj					= dims_const::_SYSTEM_OBJECT_TIERS;
						$idmetafield			= dims_load_securvalue('field', dims_const::_DIMS_CHAR_INPUT, true, true);
						$sens					= dims_load_securvalue('sens', dims_const::_DIMS_CHAR_INPUT, true, true);
						$tag					= dims_load_securvalue('tag', dims_const::_DIMS_NUM_INPUT, true, true);
						require_once(DIMS_APP_PATH . "/modules/system/class_search.php");
						$dimsearch = new search($dims);
						$dimsearch->addSearchObject(dims_const::_DIMS_MODULE_SYSTEM,dims_const::_SYSTEM_OBJECT_TIERS,$_SESSION['cste']['_DIMS_LABEL_ENTERPRISES'],true,$idmetafield);
						//$dimsearch->addallObject();
						$dimsearch->initSearchObject();

						if ($expression != "") {
							unset($_SESSION['desktop']['search']['tags']);//on vide les tags utilisés dans la précédente recherche

							if($tag && isset($_SESSION['dims']['tag_search']) && count($_SESSION['dims']['tag_search'])){
								$_SESSION['desktop']['search']['tags'] = $_SESSION['dims']['tag_search'];
							}else{
								unset($_SESSION['dims']['tag_search']);
							}

							if (!isset($_SESSION['dims']['modsearch']['expression_brut'])) $_SESSION['dims']['modsearch']['expression_brut']="";

							//$expression			= dims_load_securvalue('word', dims_const::_DIMS_CHAR_INPUT, true, true,false);
							if ($expression!='' && $expression!=$_SESSION['dims']['modsearch']['expression_brut']) {
								$_SESSION['dims']['modsearch']['expression_brut']=$expression;
								unset($_SESSION['dims']['selectedfilternews']);
							}
							if ($expression!='') {
								$_SESSION['dims']['modsearch']['expression_brut']=$expression;
							}
							$replace = array();
							if(isset($position) && isset($replace_by) && $position > -1 && !empty($replace_by)){
								$replace[$position] = urldecode($replace_by);
							}
							$dimsearch->executeSearch2($val, $kword,$_SESSION['dims']['moduleid'], $idobj, 0, $sens,0,null,$_SESSION['dims']['workspaceid']);
							$_SESSION['dims']['search']['current_search'] = $dimsearch->insertResultsForUser($dims->getUserId(), $dimsearch->tabresultat);
							//$_SESSION['dims']['modsearch']['my_real_expression'] = stripslashes($expression);

							unset($_SESSION['dims']['advanced_search']['filters']['keywords']);
							$_SESSION['dims']['advanced_search']['filters']['dyn_tiers']['val'] = $val;
							$_SESSION['dims']['advanced_search']['filters']['dyn_tiers']['field'] = $idmetafield;

							dims_redirect($dims->getScriptEnv().'?submenu='._DESKTOP_V2_DESKTOP.'&search=1&page=0&cat=0&mode=default');
						}
						break;
				}
				//on vide les keywords pour ne pas être surpris par les résultats obtenus si on a oublié qu'on avait déjà filtré par un keyword
				if(isset($_SESSION['dims']['advanced_search']['filters']['keywords'])) unset($_SESSION['dims']['advanced_search']['filters']['keywords']);
			}
			else if(isset($faction) && !empty($faction) && $faction=='kw_filter'){//filtre sur mots clefs
				$keywords = dims_load_securvalue('as_keywords', dims_const::_DIMS_CHAR_INPUT, true, true);
				//---- keywords peut être vide (ça annule le filtre sur les mots clef précédents
				if(!isset($keywords))$keywords = '';
				$_SESSION['dims']['advanced_search']['filters']['keywords'] = $keywords;
				$todo = true;
				//introduction du filtrage par date range sur le timestp_modify de la matrice
				$from = dims_load_securvalue('date_from', dims_const::_DIMS_CHAR_INPUT, true, true);
				$to = dims_load_securvalue('date_to', dims_const::_DIMS_CHAR_INPUT, true, true);

				$date_from = $date_to = '';
				if(isset($from) && !empty($from)){
					$tab = explode('/', $from);
					if(!empty($tab) && count($tab)==3){
						$date_from = $tab[2].$tab[1].$tab[0].'000000';
						$_SESSION['dims']['advanced_search']['filters']['from']['date_from'] = $from;
						$_SESSION['dims']['advanced_search']['filters']['from']['date_from_calculated'] = $date_from;
					}
				}
				else{
					if(isset($_SESSION['dims']['advanced_search']['filters']['from'])) unset($_SESSION['dims']['advanced_search']['filters']['from']);
				}
				if(isset($to) && !empty($to)){
					$tab = explode('/', $to);
					if(!empty($tab) && count($tab)==3){
						$date_to = $tab[2].$tab[1].$tab[0].'235959';
						$_SESSION['dims']['advanced_search']['filters']['to']['date_to'] = $to;
						$_SESSION['dims']['advanced_search']['filters']['to']['date_to_calculated'] = $date_to;
					}
				}
				else{
					if(isset($_SESSION['dims']['advanced_search']['filters']['to'])) unset($_SESSION['dims']['advanced_search']['filters']['to']);
				}

				$only_me = dims_load_securvalue('only_me', dims_const::_DIMS_CHAR_INPUT, true, true);
				if(isset($only_me) && !empty($only_me)){
						if($only_me=='on'){
							$_SESSION['dims']['advanced_search']['filters']['only_me'] = true;
						}
						else $_SESSION['dims']['advanced_search']['filters']['only_me'] = false;
				}
				else $_SESSION['dims']['advanced_search']['filters']['only_me'] = false;
			}
			else if(isset($faction) && !empty($faction) && $faction=='init_kwfilter'){//réinitialise les keywords
				if(isset($_SESSION['dims']['advanced_search']['filters']['keywords'])) unset($_SESSION['dims']['advanced_search']['filters']['keywords']);
				$todo = true;
			}

			if($todo){
				//on supprime l'éventuelle expression de recherche simple qui aurait pu être tapée précédemment
				if(!empty($_SESSION['dims']['modsearch']['my_real_expression']))
				{
					$_SESSION['dims']['advanced_search']['filters']['before_matrix'] = $_SESSION['dims']['modsearch']['my_real_expression'];
				}
				unset($_SESSION['dims']['modsearch']['my_real_expression']);
				$_SESSION['dims']['advanced_search']['keep_opened'] = true;
				$_SESSION['dims']['search']['search_starting_by_tag'] = false;
				unset($_SESSION['dims']['modsearch']['expression']);
				unset($_SESSION['dims']['modsearch']['tabpossible']);

				require_once(DIMS_APP_PATH . "/modules/system/class_search.php");
				$matrix = new search();

				//------- Pour éviter tout plein de warning ----
				if(!isset($_SESSION['dims']['advanced_search']['filters']['keywords'] ))$_SESSION['dims']['advanced_search']['filters']['keywords']  = '';
				if(!isset($_SESSION['dims']['advanced_search']['filters']['events'] ))$_SESSION['dims']['advanced_search']['filters']['events'] = array();
				if(!isset($_SESSION['dims']['advanced_search']['filters']['activities'] ))$_SESSION['dims']['advanced_search']['filters']['activities'] = array();
				if(!isset($_SESSION['dims']['advanced_search']['filters']['opportunities'] ))$_SESSION['dims']['advanced_search']['filters']['opportunities'] = array();
				if(!isset($_SESSION['dims']['advanced_search']['filters']['companies'] ))$_SESSION['dims']['advanced_search']['filters']['companies'] = array();
				if(!isset($_SESSION['dims']['advanced_search']['filters']['contacts'] ))$_SESSION['dims']['advanced_search']['filters']['contacts'] = array();
				if(!isset($_SESSION['dims']['advanced_search']['filters']['documents'] ))$_SESSION['dims']['advanced_search']['filters']['documents'] = array();
				if(!isset($_SESSION['dims']['advanced_search']['filters']['dossiers'] ))$_SESSION['dims']['advanced_search']['filters']['dossiers'] = array();
				if(!isset($_SESSION['dims']['advanced_search']['filters']['suivis'] ))$_SESSION['dims']['advanced_search']['filters']['suivis'] = array();
				if(!isset($_SESSION['dims']['advanced_search']['filters']['projects'] ))$_SESSION['dims']['advanced_search']['filters']['projects'] = array();

				if(!isset($_SESSION['dims']['advanced_search']['filters']['years'] ))$_SESSION['dims']['advanced_search']['filters']['years'] = array();
				if(!isset($_SESSION['dims']['advanced_search']['filters']['countries'] ))$_SESSION['dims']['advanced_search']['filters']['countries'] = array();
				if(!isset($_SESSION['desktop']['search']['tags']))$_SESSION['desktop']['search']['tags'] = array();

				if(!isset($_SESSION['desktop']['search']['region']))$_SESSION['desktop']['search']['region'] = array();
				if(!isset($_SESSION['desktop']['search']['departement']))$_SESSION['desktop']['search']['departement'] = array();
				if(!isset($_SESSION['desktop']['search']['arrondissement']))$_SESSION['desktop']['search']['arrondissement'] = array();
				if(!isset($_SESSION['desktop']['search']['canton']))$_SESSION['desktop']['search']['canton'] = array();

				if(!isset($_SESSION['desktop']['search']['region']))$_SESSION['desktop']['search']['region'] = array();
				if(!isset($_SESSION['desktop']['search']['departement']))$_SESSION['desktop']['search']['departement'] = array();
				if(!isset($_SESSION['desktop']['search']['arrondissement']))$_SESSION['desktop']['search']['arrondissement'] = array();
				if(!isset($_SESSION['desktop']['search']['canton']))$_SESSION['desktop']['search']['canton'] = array();
				if(!isset($date_from) && empty($_SESSION['dims']['advanced_search']['filters']['from']['date_from'])) $date_from = '';
				else if(!empty($_SESSION['dims']['advanced_search']['filters']['from']['date_from_calculated'])){
					$date_from = $_SESSION['dims']['advanced_search']['filters']['from']['date_from_calculated'];
				}
				if(!isset($date_to) && empty($_SESSION['dims']['advanced_search']['filters']['to']['date_to'])) $date_to = '';
				else if(!empty($_SESSION['dims']['advanced_search']['filters']['to']['date_to_calculated'])){
					$date_to = $_SESSION['dims']['advanced_search']['filters']['to']['date_to_calculated'];
				}

				//GESTION DU CAS ONLY ME - Filtre sur l'activité du dims_user connecté ------------------------------------------------------------------------
				if(isset($_SESSION['dims']['advanced_search']['filters']['only_me'])){
					if(isset($_SESSION['dims']['userid'])){
						$u = new user();
						$u->open($_SESSION['dims']['userid']);
						$ct = new contact();
						$ct->open($u->fields['id_contact']);
						if(!$ct->isNew()){
							$val = $ct->fields['id_globalobject'];
							if( $_SESSION['dims']['advanced_search']['filters']['only_me']){
								if(!isset($_SESSION['dims']['advanced_search']['filters']['contacts']) || !in_array($val,$_SESSION['dims']['advanced_search']['filters']['contacts'])){//on s'assure qu'il n'y est pas déjà
									$_SESSION['dims']['advanced_search']['filters']['contacts'][$val] = $val;
									$faction = 'add'; //hack pour incrémenter le compteur
								}
							}
							else if(isset($_SESSION['dims']['advanced_search']['filters']['contacts'][$val])){
								 unset($_SESSION['dims']['advanced_search']['filters']['contacts'][$val]);
							}
						}
					}
				}

                if (!isset($_SESSION['dims']['advanced_search']['filters']['region'])) $_SESSION['dims']['advanced_search']['filters']['region']='';
                if (!isset($_SESSION['dims']['advanced_search']['filters']['departement'])) $_SESSION['dims']['advanced_search']['filters']['departement']='';
                if (!isset($_SESSION['dims']['advanced_search']['filters']['arrondissement'])) $_SESSION['dims']['advanced_search']['filters']['arrondissement']='';
                if (!isset($_SESSION['dims']['advanced_search']['filters']['canton'])) $_SESSION['dims']['advanced_search']['filters']['canton']='';

				$_SESSION['dims']['search']['current_search'] = $matrix->searchInMatrice($_SESSION['dims']['userid'],
																						$_SESSION['dims']['advanced_search']['filters']['keywords'],
																						$_SESSION['dims']['workspaceid'],
																						$_SESSION['dims']['advanced_search']['filters']['events'],
																						$_SESSION['dims']['advanced_search']['filters']['activities'],
																						$_SESSION['dims']['advanced_search']['filters']['opportunities'],
																						$_SESSION['dims']['advanced_search']['filters']['companies'],
																						$_SESSION['dims']['advanced_search']['filters']['contacts'],
																						$_SESSION['dims']['advanced_search']['filters']['documents'],
																						$_SESSION['dims']['advanced_search']['filters']['dossiers'],
																						$_SESSION['dims']['advanced_search']['filters']['suivis'],
																						$_SESSION['dims']['advanced_search']['filters']['years'],
																						$_SESSION['dims']['advanced_search']['filters']['countries'],
																						$_SESSION['dims']['advanced_search']['filters']['region'],
																						$_SESSION['dims']['advanced_search']['filters']['departement'],
																						$_SESSION['dims']['advanced_search']['filters']['arrondissement'],
																						$_SESSION['dims']['advanced_search']['filters']['canton'],
																						$_SESSION['desktop']['search']['tags'],
																						$date_from, $date_to);

                if($faction == 'add'){
					if(!isset($_SESSION['dims']['advanced_search']['filters']['count'])) $_SESSION['dims']['advanced_search']['filters']['count'] = 1;
					else $_SESSION['dims']['advanced_search']['filters']['count'] ++;
				}
				else if($faction == 'del'){
					if(!isset($_SESSION['dims']['advanced_search']['filters']['count'])) $_SESSION['dims']['advanced_search']['filters']['count'] = 0;
					else $_SESSION['dims']['advanced_search']['filters']['count'] --;
					//si on vient d'une recherche simple au départ, ce code permettra de relancer la recherche
					if($_SESSION['dims']['advanced_search']['filters']['count']==0 && !empty($_SESSION['dims']['advanced_search']['filters']['before_matrix'] )){
						dims_redirect($dims->getScriptEnv().'?dims_op=desktopv2&action=search2&desktop_editbox_search='.$_SESSION['dims']['advanced_search']['filters']['before_matrix']);
					}

				}

				dims_redirect($dims->getScriptEnv().'?submenu='._DESKTOP_V2_DESKTOP.'&search=1&page=0&cat=0&mode=default');
			}

			dims_redirect($dims->getScriptEnv());
			die();
			break;
		case 'expand_to_all_workspaces':
			ob_clean();
			$expand_to_all_workspace = dims_load_securvalue('expand_to_all_workspace', dims_const::_DIMS_CHAR_INPUT, true, true);
			if(isset($expand_to_all_workspace) && !empty($expand_to_all_workspace)){
					if($expand_to_all_workspace=='checked'){
						$_SESSION['dims']['desktopfilters']['expand_to_all_workspace'] = true;
					}
					else $_SESSION['dims']['desktopfilters']['expand_to_all_workspace'] = false;
			}
			else $_SESSION['dims']['desktopfilters']['expand_to_all_workspace'] = false;
			die();
			break;

		case 'as_keep_opened':
			ob_clean();
			$val = dims_load_securvalue('val', dims_const::_DIMS_CHAR_INPUT, true, true);
			if(isset($val) && !empty($val)){
				$_SESSION['dims']['advanced_search']['keep_opened'] = ($val=='yes')?true:false;
			}
			die();
			break;

		case 'as_kw_keyup':
			ob_clean();
			$keywords = dims_load_securvalue('as_keywords', dims_const::_DIMS_CHAR_INPUT, true, true);
			if(isset($keywords) && !empty($keywords) && isset($_SESSION['dims']['search']['current_search'])){

				if(!isset($keywords))$keywords = '';
				$_SESSION['dims']['advanced_search']['filters']['keywords'] = $keywords;
				require_once(DIMS_APP_PATH . "/modules/system/class_search.php");
				$matrix = new search();

				//------- Pour éviter tout plein de warning ----
				if(!isset($_SESSION['dims']['advanced_search']['filters']['keywords'] ))$_SESSION['dims']['advanced_search']['filters']['keywords']  = '';
				if(!isset($_SESSION['dims']['advanced_search']['filters']['events'] ))$_SESSION['dims']['advanced_search']['filters']['events'] = array();
				if(!isset($_SESSION['dims']['advanced_search']['filters']['activities'] ))$_SESSION['dims']['advanced_search']['filters']['activities'] = array();
				if(!isset($_SESSION['dims']['advanced_search']['filters']['opportunities'] ))$_SESSION['dims']['advanced_search']['filters']['opportunities'] = array();
				if(!isset($_SESSION['dims']['advanced_search']['filters']['companies'] ))$_SESSION['dims']['advanced_search']['filters']['companies'] = array();
				if(!isset($_SESSION['dims']['advanced_search']['filters']['contacts'] ))$_SESSION['dims']['advanced_search']['filters']['contacts'] = array();
				if(!isset($_SESSION['dims']['advanced_search']['filters']['documents'] ))$_SESSION['dims']['advanced_search']['filters']['documents'] = array();

				if(!isset($_SESSION['dims']['advanced_search']['filters']['dossiers'] ))$_SESSION['dims']['advanced_search']['filters']['dossiers'] = array();
				if(!isset($_SESSION['dims']['advanced_search']['filters']['suivis'] ))$_SESSION['dims']['advanced_search']['filters']['suivis'] = array();

				if(!isset($_SESSION['dims']['advanced_search']['filters']['years'] ))$_SESSION['dims']['advanced_search']['filters']['years'] = array();
				if(!isset($_SESSION['dims']['advanced_search']['filters']['countries'] ))$_SESSION['dims']['advanced_search']['filters']['countries'] = array();
				if(!isset($_SESSION['desktop']['search']['tags']))$_SESSION['desktop']['search']['tags'] = array();

				if(!isset($_SESSION['desktop']['search']['region']))$_SESSION['desktop']['search']['region'] = array();
				if(!isset($_SESSION['desktop']['search']['departement']))$_SESSION['desktop']['search']['departement'] = array();
				if(!isset($_SESSION['desktop']['search']['arrondissement']))$_SESSION['desktop']['search']['arrondissement'] = array();
				if(!isset($_SESSION['desktop']['search']['canton']))$_SESSION['desktop']['search']['canton'] = array();

				if(!isset($_SESSION['desktop']['search']['region']))$_SESSION['desktop']['search']['region'] = array();
				if(!isset($_SESSION['desktop']['search']['departement']))$_SESSION['desktop']['search']['departement'] = array();
				if(!isset($_SESSION['desktop']['search']['arrondissement']))$_SESSION['desktop']['search']['arrondissement'] = array();
				if(!isset($_SESSION['desktop']['search']['canton']))$_SESSION['desktop']['search']['canton'] = array();
				if(!isset($date_from) && empty($_SESSION['dims']['advanced_search']['filters']['from']['date_from'])) $date_from = '';
				else if(!empty($_SESSION['dims']['advanced_search']['filters']['from']['date_from_calculated'])){
					$date_from = $_SESSION['dims']['advanced_search']['filters']['from']['date_from_calculated'];
				}
				if(!isset($date_to) && empty($_SESSION['dims']['advanced_search']['filters']['to']['date_to'])) $date_to = '';
				else if(!empty($_SESSION['dims']['advanced_search']['filters']['to']['date_to_calculated'])){
					$date_to = $_SESSION['dims']['advanced_search']['filters']['to']['date_to_calculated'];
				}

				$objects = $matrix->searchInMatrice($_SESSION['dims']['userid'],
					$_SESSION['dims']['advanced_search']['filters']['keywords'],
					$_SESSION['dims']['workspaceid'],
					$_SESSION['dims']['advanced_search']['filters']['events'],
					$_SESSION['dims']['advanced_search']['filters']['activities'],
					$_SESSION['dims']['advanced_search']['filters']['opportunities'],
					$_SESSION['dims']['advanced_search']['filters']['companies'],
					$_SESSION['dims']['advanced_search']['filters']['contacts'],
					$_SESSION['dims']['advanced_search']['filters']['documents'],
					$_SESSION['dims']['advanced_search']['filters']['dossiers'],
					$_SESSION['dims']['advanced_search']['filters']['suivis'],
					$_SESSION['dims']['advanced_search']['filters']['years'],
					$_SESSION['dims']['advanced_search']['filters']['countries'],
					$_SESSION['dims']['advanced_search']['filters']['region'],
					$_SESSION['dims']['advanced_search']['filters']['departement'],
					$_SESSION['dims']['advanced_search']['filters']['arrondissement'],
					$_SESSION['dims']['advanced_search']['filters']['canton'],
					$_SESSION['desktop']['search']['tags'],
					$date_from, $date_to, false);//le false à la fin indique que ça retourne un tableau plutôt que d'insérer en base

				echo '<ul>';
				$count = 0;
				foreach(array_keys($objects) as $type){
					switch($type){
						 case search::RESULT_TYPE_COMPANY:
							$table = 'dims_mod_business_tiers t';
							$alias = 't';
							break;
						 case search::RESULT_TYPE_CONTACT:
							$table = 'dims_mod_business_contact c';
							$alias = 'c';
							break;
						 case search::RESULT_TYPE_ACTIVITY:
						 case search::RESULT_TYPE_OPPORTUNITY:
						 case search::RESULT_TYPE_MISSION:
						 case search::RESULT_TYPE_FAIR:
							$table = 'dims_mod_business_action a';
							$alias = 'a';
							break;
						 case search::RESULT_TYPE_DOCUMENT:
						 case search::RESULT_TYPE_PICTURE:
						 case search::RESULT_TYPE_MOVIE:
							$table = 'dims_mod_doc_file d';
							$alias = 'd';
							break;
					}

					//on fait la jointure sur expression_result pour pouvoir bénéficier du tri par ranking - ceci dit l'algo nous impose de présenter les résultats par groupe (pour l'instant, i.e : companies, contact, ...)
					$params = array();
					$res = $matrix->db->query(	'SELECT er.id_globalobject_ref, '.$alias.'.*
												FROM dims_search_expression_result er '.
												' INNER JOIN '.$table. ' ON er.id_globalobject_ref = '.$alias.'.id_globalobject
												WHERE id_search= :idsearch
												AND er.id_globalobject_ref IN ('.$matrix->db->getParamsFromArray($objects[$type], 'idglobalobject', $params).')
												ORDER BY rank DESC');
					$params[':idsearch'] = array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['search']['current_search']);


					$split = $matrix->db->split_resultset($res, dims_db::TABLE_ROW_FIELD_ROTATION);
					if(isset($split[$alias])){
						foreach($split[$alias] as $row => $fields){
							switch($type){
								case search::RESULT_TYPE_COMPANY:
									$obj = new tiers();
									$tpl = 'tiers.tpl.php';
									break;
								case search::RESULT_TYPE_CONTACT:
									$obj = new contact();
									$tpl = 'contact.tpl.php';
									break;
								case search::RESULT_TYPE_ACTIVITY:
								case search::RESULT_TYPE_OPPORTUNITY:
								case search::RESULT_TYPE_MISSION:
								case search::RESULT_TYPE_FAIR:
									$obj = new action();
									$tpl = 'event.tpl.php';
									break;
								case search::RESULT_TYPE_DOCUMENT:
								case search::RESULT_TYPE_PICTURE:
								case search::RESULT_TYPE_MOVIE:
									$obj = new docfile();
									$tpl = 'document.tpl.php';
									break;

							}
							$obj->openFromResultSet($fields);
							echo '<li>';
							$obj->display(_DESKTOP_TPL_LOCAL_PATH.'/advanced_search/keyword_suggestions/'.$tpl);
							echo '</li>';
							$count ++;
						}
					}
				}
				if($count==0){
					echo '<li><pan class="no_result">'.$_SESSION['cste']['NO_RESULT'].'</span></li>';
				}
				echo '</ul>';
			}
			die();
			break;

//----------------------------------------------------------------------------------------------------------------------------------
		case 'toogleTag':
			ob_clean();
			$id = dims_load_securvalue('tag',dims_const::_DIMS_NUM_INPUT,true,true,true);
			if($id != '' && $id > 0){
				if (isset($_SESSION['dims']['tag_search'][$id])){
					unset($_SESSION['dims']['tag_search'][$id]);
				}else{
					$_SESSION['dims']['tag_search'][$id] = $id;
				}
			}
			echo count($_SESSION['dims']['tag_search']);
			die();
			break;
		case 'selectTag':
			ob_clean();
			$id = dims_load_securvalue('tag',dims_const::_DIMS_NUM_INPUT,true,false,true);
			$from_desktop = dims_load_securvalue('from_desktop',dims_const::_DIMS_NUM_INPUT,true, false);
			if ($id != '' && $id > 0){
				if (isset($_SESSION['desktop']['search']['tags']) && in_array($id,$_SESSION['desktop']['search']['tags'])){
					unset($_SESSION['desktop']['search']['tags'][$id]);

					//si on est en mode recherche par tag (en cliquant sur le bureau sur un tag) et que y'a plus de tag dans le filtrage, alors la recherche est finie
					if($_SESSION['dims']['search']['search_starting_by_tag'] && count($_SESSION['desktop']['search']['tags']) == 0){
						$_SESSION['dims']['search']['search_starting_by_tag'] = false;
						dims_redirect($dims->getScriptEnv().'?submenu='._DESKTOP_V2_DESKTOP.'&force_desktop=1&mode=default');//retour à la page d'accueil
					}
				}

				else $_SESSION['desktop']['search']['tags'][$id] = $id;

				//if(isset($from_desktop) && $from_desktop == 1){//on part du bureau en cliquant sur un tag pour lancer une recherche complète
					//on supprime l'éventuelle expression de recherche simple qui aurait pu être tapée précédemment
					/*unset($_SESSION['dims']['modsearch']['my_real_expression']);
					unset($_SESSION['dims']['advanced_search']['filters']);
					$_SESSION['dims']['advanced_search']['keep_opened'] = true;

					//lance la recherche
					require_once(DIMS_APP_PATH . "/modules/system/class_search.php");
					$search_tag = new search();
					$_SESSION['dims']['search']['current_search'] = $search_tag->startsWithTags($_SESSION['dims']['userid'], $_SESSION['desktop']['search']['tags']);
					$_SESSION['dims']['search']['search_starting_by_tag'] = true;
					dims_redirect($dims->getScriptEnv().'?submenu='._DESKTOP_V2_DESKTOP.'&search=1&page=0&cat=0&mode=default');*/

					require_once(DIMS_APP_PATH . "/modules/system/class_search.php");
					$matrix = new search();

					if(!isset($_SESSION['dims']['advanced_search']['filters']['keywords'] ))$_SESSION['dims']['advanced_search']['filters']['keywords']  = '';
					if(!isset($_SESSION['dims']['advanced_search']['filters']['events'] ))$_SESSION['dims']['advanced_search']['filters']['events'] = array();
					if(!isset($_SESSION['dims']['advanced_search']['filters']['activities'] ))$_SESSION['dims']['advanced_search']['filters']['activities'] = array();
					if(!isset($_SESSION['dims']['advanced_search']['filters']['opportunities'] ))$_SESSION['dims']['advanced_search']['filters']['opportunities'] = array();
					if(!isset($_SESSION['dims']['advanced_search']['filters']['companies'] ))$_SESSION['dims']['advanced_search']['filters']['companies'] = array();
					if(!isset($_SESSION['dims']['advanced_search']['filters']['contacts'] ))$_SESSION['dims']['advanced_search']['filters']['contacts'] = array();
					if(!isset($_SESSION['dims']['advanced_search']['filters']['documents'] ))$_SESSION['dims']['advanced_search']['filters']['documents'] = array();
					if(!isset($_SESSION['dims']['advanced_search']['filters']['dossiers'] ))$_SESSION['dims']['advanced_search']['filters']['dossiers'] = array();
					if(!isset($_SESSION['dims']['advanced_search']['filters']['suivis'] ))$_SESSION['dims']['advanced_search']['filters']['suivis'] = array();
					if(!isset($_SESSION['dims']['advanced_search']['filters']['projects'] ))$_SESSION['dims']['advanced_search']['filters']['projects'] = array();

					if(!isset($_SESSION['dims']['advanced_search']['filters']['years'] ))$_SESSION['dims']['advanced_search']['filters']['years'] = array();
					if(!isset($_SESSION['dims']['advanced_search']['filters']['countries'] ))$_SESSION['dims']['advanced_search']['filters']['countries'] = array();
					if(!isset($_SESSION['desktop']['search']['tags']))$_SESSION['desktop']['search']['tags'] = array();

					if(!isset($_SESSION['dims']['advanced_search']['filters']['region']))$_SESSION['dims']['advanced_search']['filters']['region'] = array();
					if(!isset($_SESSION['dims']['advanced_search']['filters']['departement']))$_SESSION['dims']['advanced_search']['filters']['departement'] = array();
					if(!isset($_SESSION['dims']['advanced_search']['filters']['arrondissement']))$_SESSION['dims']['advanced_search']['filters']['arrondissement'] = array();
					if(!isset($_SESSION['dims']['advanced_search']['filters']['canton']))$_SESSION['dims']['advanced_search']['filters']['canton'] = array();
					if(!isset($date_from) && empty($_SESSION['dims']['advanced_search']['filters']['from']['date_from'])) $date_from = '';
					if(!isset($date_to) && empty($_SESSION['dims']['advanced_search']['filters']['to']['date_to'])) $date_to = '';

					$_SESSION['dims']['search']['current_search'] = $matrix->searchInMatrice($_SESSION['dims']['userid'],
																						$_SESSION['dims']['advanced_search']['filters']['keywords'],
																						$_SESSION['dims']['workspaceid'],
																						$_SESSION['dims']['advanced_search']['filters']['events'],
																						$_SESSION['dims']['advanced_search']['filters']['activities'],
																						$_SESSION['dims']['advanced_search']['filters']['opportunities'],
																						$_SESSION['dims']['advanced_search']['filters']['companies'],
																						$_SESSION['dims']['advanced_search']['filters']['contacts'],
																						$_SESSION['dims']['advanced_search']['filters']['documents'],
																						$_SESSION['dims']['advanced_search']['filters']['dossiers'],
																						$_SESSION['dims']['advanced_search']['filters']['suivis'],
																						$_SESSION['dims']['advanced_search']['filters']['years'],
																						$_SESSION['dims']['advanced_search']['filters']['countries'],
																						$_SESSION['dims']['advanced_search']['filters']['region'],
																						$_SESSION['dims']['advanced_search']['filters']['departement'],
																						$_SESSION['dims']['advanced_search']['filters']['arrondissement'],
																						$_SESSION['dims']['advanced_search']['filters']['canton'],
																						$_SESSION['desktop']['search']['tags'],
																						$date_from, $date_to);
					dims_redirect($dims->getScriptEnv().'?submenu='._DESKTOP_V2_DESKTOP.'&search=1&page=0&cat=0&mode=default');
				//}
			}

			dims_redirect($dims->getScriptEnv().'?submenu=1&mode=default');
			die();
			break;
		case 'selectConceptTag':
			ob_clean();
			$id = dims_load_securvalue('tag', dims_const::_DIMS_NUM_INPUT, true, false);
			if ($id > 0) {
				if (isset($_SESSION['desktop']['concept']['tags']) && in_array($id, $_SESSION['desktop']['concept']['tags'])){
					unset($_SESSION['desktop']['concept']['tags'][$id]);
				}
				else {
					$_SESSION['desktop']['concept']['tags'][$id] = $id;
				}
			}
			dims_redirect($dims->getScriptEnv());
			die();
			break;
		/*
		Hack demandé par André Hansen, plus de tags génériques

		case 'displayMoreGenericTags':
			ob_clean();
			$_SESSION['desktop']['display']['tags']['generic'] ++;
			include _DESKTOP_TPL_LOCAL_PATH.'/tag/tag_generic.tpl.php';
			die();
			break;
		/ase 'displayLessGenericTags':
			ob_clean();
			$_SESSION['desktop']['display']['tags']['generic'] --;
			include _DESKTOP_TPL_LOCAL_PATH.'/tag/tag_generic.tpl.php';
			die();
			break;*/
		case 'displayMoreNewsletter':
			ob_clean();
			//$_SESSION['desktop']['display']['newsletters']['search'] ++;
			include _DESKTOP_TPL_LOCAL_PATH.'/newsletters/newsletter_search.tpl.php';
			die();
			break;
		case 'displayMoreRecentlyTags':
			ob_clean();
			$_SESSION['desktop']['display']['tags']['recently'] ++;
			include _DESKTOP_TPL_LOCAL_PATH.'/tag/tag_recently.tpl.php';
			die();
			break;
		case 'displayLessRecentlyTags':
			ob_clean();
			$_SESSION['desktop']['display']['tags']['recently'] --;
			include _DESKTOP_TPL_LOCAL_PATH.'/tag/tag_recently.tpl.php';
			die();
			break;
		case 'displayMoreSearchTags':
			ob_clean();
			$_SESSION['desktop']['display']['tags']['search'] ++;
			include _DESKTOP_TPL_LOCAL_PATH.'/tag/tag_search.tpl.php';
			die();
			break;
		case 'displayLessSearchTags':
			ob_clean();
			$_SESSION['desktop']['display']['tags']['search'] --;
			include _DESKTOP_TPL_LOCAL_PATH.'/tag/tag_search.tpl.php';
			die();
			break;
		case 'displaySearchTags':
			ob_clean();
			$_SESSION['tags']['search'] = trim(dims_load_securvalue('label',dims_const::_DIMS_CHAR_INPUT,true,false,true,$_SESSION['tags']['search'],null,true));
			if ($_SESSION['tags']['search'] == ''){
				?>
				<div id="zone_recently">
					<?php include _DESKTOP_TPL_LOCAL_PATH.'/tag/tag_recently.tpl.php'; ?>
				</div>
				<?php
				/*
				* Hack demandé par André Hansen le 24/01/2012
				<div id="zone_generic">
					<?php include _DESKTOP_TPL_LOCAL_PATH.'/tag/tag_generic.tpl.php'; ?>
				</div>
				<?
				*/
			}else
				include _DESKTOP_TPL_LOCAL_PATH.'/tag/tag_search.tpl.php';
			die();
			break;
		case 'displaySearchTagsConcept':
			ob_clean();
			$_SESSION['tags']['search'] = trim(dims_load_securvalue('label',dims_const::_DIMS_CHAR_INPUT,true,false,true,$_SESSION['tags']['search'],null,true));
			$id_fiche = dims_load_securvalue('id_fiche',dims_const::_DIMS_NUM_INPUT,true,false,true);
			$type_fiche = dims_load_securvalue('type_fiche',dims_const::_DIMS_NUM_INPUT,true,false,true);
			if(!empty($_SESSION['tags']['search'])){
				include _DESKTOP_TPL_LOCAL_PATH.'/tag/tag_search_concept.tpl.php';
			}
			die();
			break;
		case 'attachTag':
			ob_clean();
			$id_fiche = dims_load_securvalue('id_fiche',dims_const::_DIMS_NUM_INPUT,true,false,true);
			$type_fiche = dims_load_securvalue('type_fiche',dims_const::_DIMS_NUM_INPUT,true,false,true);
			$id_tag = dims_load_securvalue('tag',dims_const::_DIMS_NUM_INPUT,true,false,true);

			$tag = new tag();
			$tag->open($id_tag);

			switch($type_fiche) {
				case dims_const::_SYSTEM_OBJECT_CONTACT:
					$fiche_concept = new contact();
					break;
				case dims_const::_SYSTEM_OBJECT_TIERS:
					$fiche_concept = new tiers();
					break;
			}

			$fiche_concept->open($id_fiche);

			$db->query('INSERT INTO dims_tag_globalobject VALUES (:idtag, :idglobalobject, '.dims_createtimestamp().')', array(
				':idtag' => array('type' => PDO::PARAM_INT, 'value' => $tag->getId()),
				':idglobalobject' => array('type' => PDO::PARAM_INT, 'value' => $fiche_concept->fields['id_globalobject']),
			));

			dims_redirect(dims::getInstance()->getScriptEnv().'?mode=edit_tags');
			break;
		case 'detachTag':
			ob_clean();
			$id_fiche = dims_load_securvalue('id_fiche',dims_const::_DIMS_NUM_INPUT,true,false,true);
			$type_fiche = dims_load_securvalue('type_fiche',dims_const::_DIMS_NUM_INPUT,true,false,true);
			$id_tag = dims_load_securvalue('tag',dims_const::_DIMS_NUM_INPUT,true,false,true);

			$tag = new tag();
			$tag->open($id_tag);

			switch($type_fiche) {
				case dims_const::_SYSTEM_OBJECT_CONTACT:
					$fiche_concept = new contact();
					break;
				case dims_const::_SYSTEM_OBJECT_TIERS:
					$fiche_concept = new tiers();
					break;
			}

			$fiche_concept->open($id_fiche);

			$db->query('DELETE FROM dims_tag_globalobject WHERE id_tag = :idtag AND id_globalobject = :idglobalobject', array(
				':idtag' => array('type' => PDO::PARAM_INT, 'value' => $tag->getId()),
				':idglobalobject' => array('type' => PDO::PARAM_INT, 'value' => $fiche_concept->fields['id_globalobject']),
			));

			dims_redirect(dims::getInstance()->getScriptEnv().'?mode=edit_tags');
			break;
		case 'attachNewsletter':
			ob_clean();
			require_once(DIMS_APP_PATH . '/modules/system/class_newsletter.php');
			require_once(DIMS_APP_PATH . '/modules/system/class_news_subscribed.php');

			$id_fiche = dims_load_securvalue('id_fiche',dims_const::_DIMS_NUM_INPUT,true,false,true);
			$type_fiche = dims_load_securvalue('type_fiche',dims_const::_DIMS_NUM_INPUT,true,false,true);
			$id_newsletter = dims_load_securvalue('id_newsletter',dims_const::_DIMS_NUM_INPUT,true,false,true);

			$newsl = new newsletter();
			$newsl->open($id_newsletter);

			$newsub = new news_subscribed();
			$newsub->fields['id_newsletter']=$id_newsletter;
			$newsub->fields['id_contact']=$id_fiche;
			$newsub->fields['date_inscription']=  dims_createtimestamp();
			$newsub->fields['etat']=1;
			$newsub->save();

			dims_redirect(dims::getInstance()->getScriptEnv().'?action=show&id='.$id_fiche.'&modenewsletter=edit_newsletter');
			break;
		case 'detachNewsletter':
			ob_clean();
			require_once(DIMS_APP_PATH . 'modules/system/class_newsletter.php');
			$id_fiche = dims_load_securvalue('id_fiche',dims_const::_DIMS_NUM_INPUT,true,false,true);
			$type_fiche = dims_load_securvalue('type_fiche',dims_const::_DIMS_NUM_INPUT,true,false,true);
			$id_newsletter = dims_load_securvalue('id_newsletter',dims_const::_DIMS_NUM_INPUT,true,false,true);

			$newsl = new newsletter();
			$newsl->open($id_newsletter);

			$db->query('DELETE FROM dims_mod_newsletter_subscribed WHERE id_newsletter = :idnewsletter AND id_contact = :idcontact', array(
				':idnewsletter' => array('type' => PDO::PARAM_INT, 'value' => $newsl->getId()),
				':idcontact' => array('type' => PDO::PARAM_INT, 'value' => $id_fiche),
			));

			dims_redirect(dims::getInstance()->getScriptEnv().'?action=show&id='.$id_fiche.'&modenewsletter=edit_newsletter');
			break;
		case 'save_new_tag_concept':
			ob_clean();

			$id_fiche = dims_load_securvalue('id_fiche',dims_const::_DIMS_NUM_INPUT,true,true,true);
			$type_fiche = dims_load_securvalue('type_fiche',dims_const::_DIMS_NUM_INPUT,true,true,true);

			$tag = new tag();
			$tag->init_description();
			$tag->fields['private'] = 0;
			$tag->setvalues($_POST,'tag_');
			$tag->fields['id_user'] = $_SESSION['dims']['userid'];
			$tag->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
			$tag->fields['type'] = 0;
			$tag->fields['group'] = 0;
			$tag->fields['tag'] = trim($tag->fields['tag']);
			if ($tag->fields['tag'] != '') {
				$tag->save();
			}

			dims_redirect(dims::getInstance()->getScriptEnv().'?dims_op=desktopv2&action=attachTag&tag='.$tag->getId().'&id_fiche='.$id_fiche.'&type_fiche='.$type_fiche);
			break;
		case 'displayAddressBookContact':
			ob_clean();
			$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
			$type = dims_load_securvalue('type',dims_const::_DIMS_NUM_INPUT,true,true,true);
			if ($id != '' && $id > 0){
				$lstConn = $desktop->getFirstRecentConnexions();
				switch($type){
					case dims_const::_SYSTEM_OBJECT_TIERS :
						$ct = new tiers();
						if($ct->open($id)){
							$_SESSION['desktopv2']['adress_book']['sel_type'] = $type;
							$_SESSION['desktopv2']['adress_book']['sel_id'] = $id;
							$ct->display(_DESKTOP_TPL_LOCAL_PATH.'/address_book/fiche_desc_tiers_address_book.tpl.php');
						}
						break;
					case dims_const::_SYSTEM_OBJECT_CONTACT :
						$ct = new contact();
						if($ct->open($id)){
							$_SESSION['desktopv2']['adress_book']['sel_type'] = $type;
							$_SESSION['desktopv2']['adress_book']['sel_id'] = $id;
							$ct->display(_DESKTOP_TPL_LOCAL_PATH.'/address_book/fiche_desc_contact_address_book.tpl.php');
						}
						break;
				}
			}
			die();
			break;
		case 'saveupdatelink':
			include_once(DIMS_APP_PATH .'modules/system/class_tiers_contact.php');

			$idlink = dims_load_securvalue('id_link',dims_const::_DIMS_CHAR_INPUT,true,true);
			$id_ct = dims_load_securvalue('id_ent',dims_const::_DIMS_CHAR_INPUT,true,true);

			$type_link = dims_load_securvalue('type_link',dims_const::_DIMS_CHAR_INPUT,true,true);
			$link_level = dims_load_securvalue('link_level',dims_const::_DIMS_CHAR_INPUT,true,true);
			//$fonction = dims_load_securvalue('fonction',dims_const::_DIMS_CHAR_INPUT,true,true);
			//$departement = dims_load_securvalue('departement',dims_const::_DIMS_CHAR_INPUT,true,true);
			$commentaire = dims_load_securvalue('commentaire',dims_const::_DIMS_CHAR_INPUT,true,true);

			//$date_deb_d = dims_load_securvalue('date_deb_day', dims_const::_DIMS_NUM_INPUT, true, true);
			//$date_deb_m = dims_load_securvalue('date_deb_month', dims_const::_DIMS_NUM_INPUT, true, true);
			//$date_deb_y = dims_load_securvalue('date_deb_year', dims_const::_DIMS_NUM_INPUT, true, true);
			$date_fin_d = dims_load_securvalue('date_fin_day', dims_const::_DIMS_NUM_INPUT, true, true);
			$date_fin_m = dims_load_securvalue('date_fin_month', dims_const::_DIMS_NUM_INPUT, true, true);
			$date_fin_y = dims_load_securvalue('date_fin_year', dims_const::_DIMS_NUM_INPUT, true, true);
			//if($date_deb_d != "jj" && $date_deb_m != "mm" && $date_deb_y != "aaaa") {
			//	$date_deb = $date_deb_y.$date_deb_m.$date_deb_d."000000";
			//}
			//else {
			//	$date_deb = 0;
			//}

			if($date_fin_d != "jj" && $date_fin_m != "mm" && $date_fin_y != "aaaa") {
				$date_fin = $date_fin_y.$date_fin_m.$date_fin_d."000000";
			}
			else {
				$date_fin = 0;
			}

			$ctlk = new tiersct();
			$ctlk->open($idlink);

			//insertion des éléments
			if($type_link != $ctlk->fields['type_lien'])	$ctlk->fields['type_lien'] = $type_link;
			//if($fonction != $ctlk->fields['function'])		$ctlk->fields['function'] = $fonction;
			//if($departement != $ctlk->fields['departement'])		$ctlk->fields['departement'] = $departement;
			if($commentaire != $ctlk->fields['commentaire'])		$ctlk->fields['commentaire'] = $commentaire;
			//if($link_level != $ctlk->fields['link_level'])	$ctlk->fields['link_level'] = $link_level;
			//if($date_deb != $ctlk->fields['date_deb'])		$ctlk->fields['date_deb'] = $date_deb;
			if($date_fin != $ctlk->fields['date_fin'])		$ctlk->fields['date_fin'] = $date_fin;
			$ctlk->fields['id_user'] = $_SESSION['dims']['userid'];
			$ctlk->save();
			//dims_print_r($ctlk->fields);
			//die();
			dims_redirect('/admin.php');
			break;
		case 'displayLinkInfo':
			ob_clean();
			$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
			$type = dims_load_securvalue('type',dims_const::_DIMS_NUM_INPUT,true,true,true);
			if ($id != '' && $id > 0){

				switch($type){
					case dims_const::_SYSTEM_OBJECT_TIERS :
						$ct = new tiers();
						if($ct->open($id)){
							$_SESSION['desktopv2']['adress_book']['sel_type'] = $type;
							$_SESSION['desktopv2']['adress_book']['sel_id'] = $id;
							$ct->display(_DESKTOP_TPL_LOCAL_PATH.'/address_book/fiche_desc_tiers_link.tpl.php');
						}
						break;
					case dims_const::_SYSTEM_OBJECT_CONTACT :
						$ct = new contact();
						if($ct->open($id)){
							$_SESSION['desktopv2']['adress_book']['sel_type'] = $type;
							$_SESSION['desktopv2']['adress_book']['sel_id'] = $id;
							$ct->display(_DESKTOP_TPL_LOCAL_PATH.'/address_book/fiche_desc_contact_link.tpl.php');
						}
						break;
				}
			}
			die();
			break;
		case 'export_vcard':
			ob_clean();
			$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
			$type = dims_load_securvalue('type',dims_const::_DIMS_NUM_INPUT,true,true,true);
			if ($id != '' && $id > 0){
				switch($type){
					case dims_const::_SYSTEM_OBJECT_TIERS :
						$ct = new tiers();
						if($ct->open($id)){
							dims_downloadfile($ct->getVcard(),$ct->fields['intitule'].'.vcf');
						}
						break;
					case dims_const::_SYSTEM_OBJECT_CONTACT :
						$ct = new contact();
						if($ct->open($id)){
							dims_downloadfile($ct->getVcard(),$ct->fields['firstname'].'.vcf');
						}
						break;
				}
			}
			die();
			break;
		case 'send_vcard':
			ob_clean();
			$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
			$type = dims_load_securvalue('type',dims_const::_DIMS_NUM_INPUT,true,true,true);
			if ($id != '' && $id > 0){
				switch($type){
					case dims_const::_SYSTEM_OBJECT_TIERS :
						$ct = new tiers();
						if($ct->open($id)){
							$files = array();
							$file['filename'] = $ct->getVcard();
							$file['name'] = $ct->fields['intitule'].".vcf";
							$file['mime-type'] = 'text/x-vcard; charset=utf-8';
							$files[] = $file;
							dims_send_mail_with_files($_SESSION['dims']['user']['email'],$_SESSION['dims']['user']['email'],"vCard : ".$ct->fields['intitule'],"",$files);
						}
						break;
					case dims_const::_SYSTEM_OBJECT_CONTACT :
						$ct = new contact();
						if($ct->open($id)){
							$files = array();
							$file['filename'] = $ct->getVcard();
							$file['name'] = $ct->fields['firstname']."_".$ct->fields['lastname'].".vcf";
							$file['mime-type'] = 'text/x-vcard; charset=utf-8';
							$files[] = $file;

				dims_send_mail_with_pear($_SESSION['dims']['user']['email'],$_SESSION['dims']['user']['email'],"vCard : ".$ct->fields['firstname']." ".$ct->fields['lastname'],"",$files);
							//dims_send_mail_with_files($_SESSION['dims']['user']['email'],$_SESSION['dims']['user']['email'],"vCard : ".$ct->fields['firstname']." ".$ct->fields['lastname'],"",$files);
						}
						break;
				}
			}
			dims_redirect(dims::getInstance()->getScriptEnv());
			die();
			break;
		case 'sendContactsVcf':
			ob_clean();
			require_once DIMS_APP_PATH . '/modules/system/class_search_expression.php' ;
			require_once DIMS_APP_PATH . '/modules/system/class_search_expression_tag.php' ;
			require_once DIMS_APP_PATH . '/modules/system/class_search.php' ;

			if(isset($_SESSION['dims']['search']['current_search'])){
				$search = new search_expression(true);
				$search->open($_SESSION['dims']['search']['current_search'] );

				if (isset($_SESSION['desktop']['search']['tags']))
					$results = $search->getResults(search::RESULT_TYPE_CONTACT, $_SESSION['desktop']['search']['tags']);//récupération de la liste des résultats
				else
					$results = $search->getResults(search::RESULT_TYPE_CONTACT);//récupération de la liste des résultats

				$courPath = realpath('.');
				$dirExport = DIMS_TMP_PATH . '/vcardexport/';
				if (!file_exists($dirExport))
					mkdir($dirExport);
				$sid = session_id();

				if (!file_exists($dirExport.$sid))
					mkdir($dirExport.$sid);
				$file2 = $dirExport.$sid."/vcards_search.vcf";
				//$file3 = $dirExport.$sid."/vcards_search.csv"; <========
				$vcard = fopen($file2,'w+');
				//$vcard2 = fopen($file3,'w+'); <========
				/*fwrite($vcard2,"\"First Name\",\"Middle Name\",\"Last Name\",\"Title\",\"Suffix\",\"Initials\",\"Web Page\",\"Gender\",\"Birthday\",\"Anniversary\",\"Location\",\"Language\",\"Internet Free Busy\",\"Notes\",\"E-mail Address\",\"E-mail 2 Address\",\"E-mail 3 Address\",\"Primary Phone\",\"Home Phone\",\"Home Phone 2\",\"Mobile Phone\",\"Pager\",\"Home Fax\",\"Home Address\",\"Home Street\",\"Home Street 2\",\"Home Street 3\",\"Home Address PO Box\",\"Home City\",\"Home State\",\"Home Postal Code\",\"Home Country\",\"Spouse\",\"Children\",\"Manager's Name\",\"Assistant's Name\",\"Referred By\",\"Company Main Phone\",\"Business Phone\",\"Business Phone 2\",\"Business Fax\",\"Assistant's Phone\",\"Company\",\"Job Title\",\"Department\",\"Office Location\",\"Organizational ID Number\",\"Profession\",\"Account\",\"Business Address\",\"Business Street\",\"Business Street 2\",\"Business Street 3\",\"Business Address PO Box\",\"Business City\",\"Business State\",\"Business Postal Code\",\"Business Country\",\"Other Phone\",\"Other Fax\",\"Other Address\",\"Other Street\",\"Other Street 2\",\"Other Street 3\",\"Other Address PO Box\",\"Other City\",\"Other State\",\"Other Postal Code\",\"Other Country\",\"Callback\",\"Car Phone\",\"ISDN\",\"Radio Phone\",\"TTY/TDD Phone\",\"Telex\",\"User 1\",\"User 2\",\"User 3\",\"User 4\",\"Keywords\",\"Mileage\",\"Hobby\",\"Billing Information\",\"Directory Server\",\"Sensitivity\",\"Priority\",\"Private\",\"Categories\"
"); <======== */
				foreach($results as $res){
					if($res['type'] == search::RESULT_TYPE_CONTACT){
						$obj = new contact();
						$obj->openWithGB($res['record']);
						$content = file_get_contents($obj->getVcard())."
";
						fwrite($vcard,$content);
						/*$cont = $obj->fields['firstname'].",,".$obj->fields['lastname'].",,,,,,,,,,,,".$obj->fields['email'].",,,,,,,,,,,,,,,,,,,,,,,,".$obj->fields['phone'].",,,,".$obj->fields['field13'].",".$obj->fields['field11'].",,,,,,".$obj->fields['address']." ".$obj->fields['postalcode']." ".$obj->fields['city']." ".$obj->fields['country'].",".$obj->fields['address'].",,,,".$obj->fields['city'].",,".$obj->fields['postalcode'].",".$obj->fields['country'].",,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,Normal,,,
";
						fwrite($vcard2,$cont); <======== */
					}
				}
				//fclose($vcard);
				//fclose($vcard2); <========
				//chdir($courPath);
				$files = array();
				/*$file['filename'] = $dirExport.$sid."/vcards_search.vcf";
				$file['name'] = "vcards_search.vcf";
				$file['mime-type'] = 'text/x-vcard; charset=utf-8';
				$files[] = $file;*/
				/*$file['filename'] = $dirExport.$sid."/vcards_search.csv"; <========
				$file['name'] = "vcards_search.csv";
				$file['mime-type'] = 'text/csv; charset=utf-8';
				$files[] = $file;*/

				if (isset($_SESSION['desktop']['search']['tags']))
					$results = $search->getResults(search::RESULT_TYPE_COMPANY, $_SESSION['desktop']['search']['tags']);//récupération de la liste des résultats
				else
					$results = $search->getResults(search::RESULT_TYPE_COMPANY);//récupération de la liste des résultats

				foreach($results as $res){
					if($res['type'] == search::RESULT_TYPE_COMPANY){
						$obj = new tiers();
						$obj->openWithGB($res['record']);
						$content = file_get_contents($obj->getVcard())."
";
						fwrite($vcard,$content);
					}
				}
				fclose($vcard);
				chdir($courPath);
				//$files = array();
				$file['filename'] = $dirExport.$sid."/vcards_search.vcf";
				$file['name'] = "vcards_search.vcf";
				$file['mime-type'] = 'text/x-vcard; charset=utf-8';
				$files[] = $file;


				dims_send_mail_with_files($_SESSION['dims']['user']['email'],$_SESSION['dims']['user']['email'],"vCard : search","",$files);
			}
			dims_redirect(dims::getInstance()->getScriptEnv());
			die();
			break;
		case 'sendContactsLdif':
			ob_clean();
			require_once DIMS_APP_PATH . '/modules/system/class_search_expression.php' ;
			require_once DIMS_APP_PATH . '/modules/system/class_search_expression_tag.php' ;
			require_once DIMS_APP_PATH . '/modules/system/class_search.php' ;

			if(isset($_SESSION['dims']['search']['current_search'])){
				$search = new search_expression(true);
				$search->open($_SESSION['dims']['search']['current_search'] );

				if (isset($_SESSION['desktop']['search']['tags']))
					$results = $search->getResults(search::RESULT_TYPE_CONTACT, $_SESSION['desktop']['search']['tags']);//récupération de la liste des résultats
				else
					$results = $search->getResults(search::RESULT_TYPE_CONTACT);//récupération de la liste des résultats

				$courPath = realpath('.');
				$dirExport = DIMS_TMP_PATH . '/vcardexport/';
				if (!file_exists($dirExport))
					mkdir($dirExport);
				$sid = session_id();

				if (!file_exists($dirExport.$sid))
					mkdir($dirExport.$sid);
				$file2 = $dirExport.$sid."/vcards_search.ldif";
				$vcard = fopen($file2,'w+');
				foreach($results as $res){
					if($res['type'] == search::RESULT_TYPE_CONTACT){
						$obj = new contact();
						$obj->openWithGB($res['record']);
						$content = file_get_contents($obj->getLdif())."-

";
						fwrite($vcard,$content);
					}
				}
				fclose($vcard);
				chdir($courPath);
				$files = array();
				$file['filename'] = $dirExport.$sid."/vcards_search.ldif";
				$file['name'] = "vcards_search.ldif";
				$file['mime-type'] = 'text/x-ldif; charset=utf-8';
				$files[] = $file;
				dims_send_mail_with_files($_SESSION['dims']['user']['email'],$_SESSION['dims']['user']['email'],"LDIF : search","",$files);
			}
			dims_redirect(dims::getInstance()->getScriptEnv());
			die();
			break;
		case 'add_to_favorite':
			ob_clean();
			$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
			if ($id != '' && $id > 0){
				$isFav = false;
				require_once DIMS_APP_PATH.'modules/system/class_favorite.php';
				$fav = new favorite();
				if ($fav->open($_SESSION['dims']['userid'],$id))
					if($fav->fields['status'] == favorite::Favorite)
						$fav->changeStatus();
					else
						$fav->changeStatus(favorite::Favorite);
				else{
					$fav->fields['id_user'] = $_SESSION['dims']['userid'];
					$fav->fields['id_globalobject'] = $id;
					$fav->fields['status'] = favorite::Favorite;
				}
				$fav->save();
				$refreshLst = 'false';
					if($_SESSION['desktopv2']['adress_book']['group'] == _DESKTOP_V2_ADDRESS_BOOK_FAVORITES)
						$refreshLst = 'true';
				if ($fav->fields['status'] == favorite::Favorite){
				?>
					<img onclick="javascript: addToFavoriteAB(<? echo $id; ?>,<? echo $refreshLst; ?>);" src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/favori_plain.png" border="0" style="float:right">
					<?
				}else{
					?>
					<img onclick="javascript: addToFavoriteAB(<? echo $id; ?>,<? echo $refreshLst; ?>);" src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/favori_empty.png" border="0" style="float:right">
					<?
				}
			}
			die();
			break;
		case 'refresh_address_book_groups':
			ob_clean();
			$lastLinkedContacts = $desktop->getLastLinkedContacts($_SESSION['desktopv2']['adress_book']['type']);
			$favoriteContacts = $desktop->getFavoritesContacts($_SESSION['desktopv2']['adress_book']['type']);
			$monitoredContacts = $desktop->getMonitoredContacts($_SESSION['desktopv2']['adress_book']['type']);
			include _DESKTOP_TPL_LOCAL_PATH."/address_book/address_book_dynamic_group.tpl.php";
			die();
			break;
		case 'refresh_address_book_your_groups':
			$allContacts = $desktop->getAllContacts($_SESSION['desktopv2']['adress_book']['type']);
			$lstGroups = $desktop->getGroupsUser();
			include _DESKTOP_TPL_LOCAL_PATH."/address_book/address_book_your_group.tpl.php";
			die();
			break;
		case 'refresh_address_book_lst':
			ob_clean();
			switch($_SESSION['desktopv2']['adress_book']['group']){
				case _DESKTOP_V2_ADDRESS_BOOK_LAST_LINKED :
					$lastLinkedContacts = $desktop->getLastLinkedContacts($_SESSION['desktopv2']['adress_book']['type']);
					break;
				case _DESKTOP_V2_ADDRESS_BOOK_FAVORITES :
					$favoriteContacts = $desktop->getFavoritesContacts($_SESSION['desktopv2']['adress_book']['type']);
					break;
				case _DESKTOP_V2_ADDRESS_BOOK_MONITORED :
					$monitoredContacts = $desktop->getMonitoredContacts($_SESSION['desktopv2']['adress_book']['type']);
					break;
				default:
					if ($_SESSION['desktopv2']['adress_book']['group'] > 0){
						$gr = new ct_group();
						if ($gr->open($_SESSION['desktopv2']['adress_book']['group']))
							$lstGroups[$_SESSION['desktopv2']['adress_book']['group']] = $gr;
					}
					break;
			}
			include _DESKTOP_TPL_LOCAL_PATH.'/address_book/fiche_address_book.tpl.php';
			die();
			break;
		case 'refresh_address_book_menu_bas':
			ob_clean();
			include _DESKTOP_TPL_LOCAL_PATH."/address_book/address_book_menu_bas_desc.tpl.php";
			die();
			break;
		case 'add_contacts_group':
			ob_clean();
			$gr = new ct_group();
			$gr->init_description();
			$id_popup = dims_load_securvalue('id_popup',dims_const::_DIMS_CHAR_INPUT,true,true);
			$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
			if ($id != '' && $id > 0)
				$gr->open($id);
			?>
			<div>
				<div class="actions">
					<a href="Javascript: void(0);" onclick="Javascript: dims_closeOverlayedPopup('<?php echo $id_popup; ?>');">
						<img src="<? echo _DESKTOP_TPL_PATH; ?>/gfx/common/close.png" />
					</a>
				</div>
				<h2>
				<?
				if ($id != '' && $id > 0)
					echo $_SESSION['cste']['_DIMS_LABEL_GROUP_MODIFY'];
				else
					echo $_SESSION['cste']['_DIMS_LABEL_CREATE_GROUP'];
				?>
				</h2>
				<form method="POST" action="/admin.php?action=save_contacts_gr">
					<?
						// Sécurisation du formulaire par token
						require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
						$token = new FormToken\TokenField;
						$token->field("id_gr",	$gr->fields['id']);
						$token->field("label_gr");
						$tokenHTML = $token->generate();
						echo $tokenHTML;
					?>
					<span>
						<label for="new_sector_label">
							<? echo $_SESSION['cste']['_DIMS_LABEL']; ?> :
						</label>
					</span>
					<input type="hidden" name="id_gr" value="<? echo $gr->fields['id']; ?>" />
					<input type="text" id="label_gr" name="label_gr" value="<? echo $gr->fields['label']; ?>" />
					<input style="float:right;margin:10px;" type="submit" value="<? echo $_SESSION['cste']['_DIMS_SAVE']; ?>" />
				</form>
			</div>
			<script type="text/javascript">
				$("#label_gr").focus();
			</script>
			<?php
			die();
			break;
		case 'display_group_for_contact':
			ob_clean();
			$lstGroups = $desktop->getGroupsUser();
			$id = dims_load_securvalue('id_gb',dims_const::_DIMS_NUM_INPUT,true,true,true);
			$type = dims_load_securvalue('type',dims_const::_DIMS_NUM_INPUT,true,true,true);
			if ($id != '' && $id > 0 && ($type == dims_const::_SYSTEM_OBJECT_CONTACT || $type == dims_const::_SYSTEM_OBJECT_TIERS)){
				?>
				<div>
					<div class="actions">
						<a href="Javascript: void(0);" onclick="Javascript: dims_hidepopup();">
							<img src="<? echo _DESKTOP_TPL_PATH; ?>/gfx/common/close.png" />
						</a>
					</div>
					<h2>
					<?
					echo $_SESSION['cste']['_DIMS_LABEL_GROUP_LIST'];
					?>
					</h2>
					<p class="popup_add_value">
						<?
						if (count($lstGroups) > 0) {
							?>
							<table cellpadding="0" cellspacing="0" style="width:100%;">
								<tr>
									<th>
										<? echo $_SESSION['cste']['_DIMS_LABEL']; ?>
									</th>
									<th>
									</th>
								</tr>
								<?
								foreach($lstGroups as $gr){
									$isSel = 'false';
									if($_SESSION['desktopv2']['adress_book']['group'] == $gr->fields['id'])
										$isSel = 'true';
									?>
									<tr>
										<td>
											<? echo $gr->fields['label']; ?>
										</td>
										<td>
											<input onclick="javascript:checkGroupForContact(<? echo $id; ?>,<? echo $type; ?>,this.value,<? echo $isSel; ?>);" value="<? echo $gr->fields['id']; ?>" type="checkbox" <? if ($gr->isInGroup($id)) echo "checked=true"; ?> />
										</td>
									</tr>
									<?
								}
								?>
							</table>
							<?
						}else{
							echo 'No group defined !';
						}
						?>
					</p>
				</div>
				<?php
			}else{
				?>
				<script type="text/javascript">
					dims_hidepopup();
				</script>
				<?
			}
			die();
			break;
		case 'add_contact_to_list':
			ob_clean();
			$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
			$type = dims_load_securvalue('type',dims_const::_DIMS_NUM_INPUT,true,true,true);
			$id_gr = dims_load_securvalue('id_gr',dims_const::_DIMS_NUM_INPUT,true,true,true);
			if ($id != '' && $id > 0 && ($type == dims_const::_SYSTEM_OBJECT_CONTACT || $type == dims_const::_SYSTEM_OBJECT_TIERS)){
				if ($id_gr != '' && $id_gr > 0){
					$gr = new ct_group();
					$gr->open($id_gr);
					$gr->deOrAttachContact($id,$type);
				}
				?>
				<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/groupe16.png" border="0" style="float:left">
				<?
				$nb_group = ct_group::getNbGroupsForContact($id,$type);
				?>
				<span>(<? echo $nb_group; ?>)</span>
				<?
			}
			die();
			break;

		case 'ab_export_contacts':
			ob_end_clean();

			$lstContacts = array();
			switch($_SESSION['desktopv2']['adress_book']['group']){
				case _DESKTOP_V2_ADDRESS_BOOK_ALL_CONTACT :
					$lstContacts = $desktop->getAllContacts($_SESSION['desktopv2']['adress_book']['type']);
					break;
				case _DESKTOP_V2_ADDRESS_BOOK_LAST_LINKED :
					$lstContacts = $desktop->getLastLinkedContacts($_SESSION['desktopv2']['adress_book']['type']);
					break;
				case _DESKTOP_V2_ADDRESS_BOOK_FAVORITES :
					$lstContacts = $desktop->getFavoritesContacts($_SESSION['desktopv2']['adress_book']['type']);
					break;
				case _DESKTOP_V2_ADDRESS_BOOK_MONITORED :
					$lstContacts = $desktop->getMonitoredContacts($_SESSION['desktopv2']['adress_book']['type']);
					break;
				default:
					$lstGroups = $desktop->getGroupsUser();
					if (isset($lstGroups[$_SESSION['desktopv2']['adress_book']['group']])){
						if (!isset($lstGroups[$_SESSION['desktopv2']['adress_book']['group']]->contacts)){
							switch($_SESSION['desktopv2']['adress_book']['type']){
								case dims_const::_SYSTEM_OBJECT_CONTACT :
									$res = $lstGroups[$_SESSION['desktopv2']['adress_book']['group']]->getContactsGroup(dims_const::_SYSTEM_OBJECT_CONTACT);
									break;
								case dims_const::_SYSTEM_OBJECT_TIERS :
									$res = $lstGroups[$_SESSION['desktopv2']['adress_book']['group']]->getContactsGroup(dims_const::_SYSTEM_OBJECT_TIERS);
									break;
								default:
									$res = array_merge($lstGroups[$_SESSION['desktopv2']['adress_book']['group']]->getContactsGroup(dims_const::_SYSTEM_OBJECT_CONTACT),$lstGroups[$_SESSION['desktopv2']['adress_book']['group']]->getContactsGroup(dims_const::_SYSTEM_OBJECT_TIERS));
									usort($res,'sortCtTiers');
									break;
							}
							$lstGroups[$_SESSION['desktopv2']['adress_book']['group']]->contacts = $res;
						}
						$lstContacts = $lstGroups[$_SESSION['desktopv2']['adress_book']['group']]->contacts;
					}
					break;
			}

			foreach($lstContacts as $key => $contact){
				if (get_class($contact) == 'tiers'){
					unset($lstContacts[$key]);
				}
			}

			require_once DIMS_APP_PATH.'modules/system/desktopV2/templates/address_book/contacts_excel_export.php';
			die();
			break;
		case 'exportContactsFromObject':
		ob_clean();
		foreach ($_SESSION['desktopv2']['concepts']['filters']['stack'] as $nb => $filter) {
				$filter_type = $filter[0];
				$filter_value = $filter[1];

		$lstContacts=array();
		$lsttiers=array();

		if (isset($_SESSION['desktopv2']['concepts']['exporttiers'])) $lsttiers=$_SESSION['desktopv2']['concepts']['exporttiers'];
		if (isset($_SESSION['desktopv2']['concepts']['exportcontacts'])) $lstContacts=$_SESSION['desktopv2']['concepts']['exportcontacts'];

		switch ($filter_type) {
			case 'event':
			case 'opportunity':
			$action = new action();
			$action->openWithGB($filter_value);
			// $action->getLightContactsAndCompanies(&$lstContacts,&$lsttiers);
			break;
		}

		// on appelle la generation des donnees
		if (!empty($lstContacts) || !empty($lsttiers)) {
			require_once DIMS_APP_PATH.'modules/system/desktopV2/templates/excel_export.php';
		}
		}
		die();
		break;
		case 'exportContacts':
			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
				unset($_SESSION['business']['search_ent']);
				if(isset($_SESSION['dims']['search']['current_search'])){
					require_once DIMS_APP_PATH . '/modules/system/class_search_expression.php' ;
					$search = new search_expression(false);//false = pas de pagination
					$search->open($_SESSION['dims']['search']['current_search'] );
					if(isset($_SESSION['desktop']['search']['tags']))
						$exported_contacts = $search->getLightContacts($_SESSION['desktop']['search']['tags']);
					else $exported_contacts = $search->getLightContacts();
				}
				require_once(DIMS_APP_PATH . '/modules/system/desktopV2/templates/export_data/contact.php');

				//dims_print_r($exported_contacts);die();
				//traitement repris de Pat (cf. include/op.php) de l'ancien desktop ----------------------------
				/*$sql =	"
					SELECT		mf.*,mc.label as categlabel, mc.id as id_cat,
							mb.protected,mb.name as namefield,mb.label as titlefield
					FROM		dims_mod_business_meta_field as mf
					INNER JOIN	dims_mb_field as mb
					ON		mb.id=mf.id_mbfield
					RIGHT JOIN	dims_mod_business_meta_categ as mc
					ON		mf.id_metacateg=mc.id
					WHERE		mf.id_object = :idobject
					AND		mf.used=1
					AND		mf.option_exportview=1
					ORDER BY	mc.position, mf.position
					";
				$rs_fields=$db->query($sql, array(
					':idobject' => dims_const::_SYSTEM_OBJECT_CONTACT
				));
				$_SESSION['business']['exportdata']=array();
				while ($fields = $db->fetchrow($rs_fields)) {
						$sql_s .= ",c.".$fields['namefield'];

						if (isset($_DIMS['cste'][$fields['titlefield']])) $namevalue= $_DIMS['cste'][$fields['titlefield']];
						else $namevalue=$fields['name'];
						$elem=array();
						$elem['title']=$namevalue;
						$elem['namefield']=$fields['namefield'];

						$_SESSION['business']['exportdata'][]=$elem;
				}
				require_once(DIMS_APP_PATH . '/modules/system/crm_public_contact_searchexport2.php');*/
			}
			die();
			break;

		case 'exportCompanies':

			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
				unset($_SESSION['business']['ent_search_ct']);
				if(isset($_SESSION['dims']['search']['current_search'])){
					require_once DIMS_APP_PATH . '/modules/system/class_search_expression.php' ;
					$search = new search_expression(false);//false = pas de pagination
					$search->open($_SESSION['dims']['search']['current_search'] );
					if(isset($_SESSION['desktop']['search']['tags']))
						$exported_tiers = $search->getLightCompanies($_SESSION['desktop']['search']['tags']);
					else $exported_tiers = $search->getLightCompanies();
				}
				require_once(DIMS_APP_PATH . '/modules/system/desktopV2/templates/export_data/tiers.php');

				/*$sql =	"
					SELECT		mf.*,mc.label as categlabel, mc.id as id_cat,
							mb.protected,mb.name as namefield,mb.label as titlefield
					FROM		dims_mod_business_meta_field as mf
					INNER JOIN	dims_mb_field as mb
					ON		mb.id=mf.id_mbfield
					RIGHT JOIN	dims_mod_business_meta_categ as mc
					ON		mf.id_metacateg=mc.id
					WHERE		 mf.id_object = :idobject
					AND		mf.used=1
					AND		mf.option_exportview=1
					ORDER BY	mc.position, mf.position
					";

				$rs_fields=$db->query($sql, array(
					':idobject' => dims_const::_SYSTEM_OBJECT_TIERS
				));
				$_SESSION['business']['exportdata']=array();

				while ($fields = $db->fetchrow($rs_fields)) {
					$sql_s .= ",t.".$fields['namefield'];

					if (isset($_DIMS['cste'][$fields['titlefield']])) $namevalue= $_DIMS['cste'][$fields['titlefield']];
					else $namevalue=$fields['name'];
					$elem=array();
					$elem['title']=$namevalue;
					$elem['namefield']=$fields['namefield'];

					$_SESSION['business']['exportdata'][]=$elem;
				}
				require_once(DIMS_APP_PATH . '/modules/system/crm_public_ent_searchexport2.php');*/
			}
			die();
			break;
		case 'exportCompaniesWithContacts':

			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
				unset($_SESSION['business']['ent_search_ct']);
				$exported_tiers = array();
				if(isset($_SESSION['dims']['search']['current_search'])){
					require_once DIMS_APP_PATH . '/modules/system/class_search_expression.php' ;
					$search = new search_expression(false);//false = pas de pagination
					$search->open($_SESSION['dims']['search']['current_search'] );
					if(isset($_SESSION['desktop']['search']['tags']))
						$exported_tiers = $search->getLightCompanies($_SESSION['desktop']['search']['tags']);
					else $exported_tiers = $search->getLightCompanies();
				}
				unset($_SESSION['business']['search_ent']);
				$exported_contacts = array();
				if(isset($_SESSION['dims']['search']['current_search'])){
					require_once DIMS_APP_PATH . '/modules/system/class_search_expression.php' ;
					$search = new search_expression(false);//false = pas de pagination
					$search->open($_SESSION['dims']['search']['current_search'] );
					if(isset($_SESSION['desktop']['search']['tags']))
						$exported_contacts = $search->getLightContacts($_SESSION['desktop']['search']['tags']);
					else $exported_contacts = $search->getLightContacts();
				}
				$exported_datas = array_merge($exported_tiers,$exported_contacts);
				usort($exported_datas,'sortCtTiers');
				require_once(DIMS_APP_PATH . '/modules/system/desktopV2/templates/export_data/contact_and_tiers.php');
			}

			/*if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
				unset($_SESSION['business']['ent_search_ct']);
				$_SESSION['business']['exportdata']=array();

				$sql =	"
					SELECT		mf.*,mc.label as categlabel, mc.id as id_cat,
							mb.protected,mb.name as namefield,mb.label as titlefield
					FROM		dims_mod_business_meta_field as mf
					INNER JOIN	dims_mb_field as mb
					ON		mb.id=mf.id_mbfield
					RIGHT JOIN	dims_mod_business_meta_categ as mc
					ON		mf.id_metacateg=mc.id
					WHERE		 mf.id_object = ".dims_const::_SYSTEM_OBJECT_TIERS."
					AND		mf.used=1
					AND		mf.option_exportview=1
					ORDER BY	mc.position, mf.position
					";

				$rs_fields=$db->query($sql);

				$tiersfields="";

				while ($fields = $db->fetchrow($rs_fields)) {
					if ($tiersfields=="")
						$tiersfields = "t.".$fields['namefield'];
					else
						$tiersfields .= ",t.".$fields['namefield'];

					if (isset($_DIMS['cste'][$fields['titlefield']])) $namevalue= $_DIMS['cste'][$fields['titlefield']];
					else $namevalue=$fields['name'];
					$elem=array();
					$elem['title']=$namevalue;
					$elem['namefield']=$fields['namefield'];

					$_SESSION['business']['exportdata'][]=$elem;
				}

				// on fait les champs pour le contact
				$sql =	"
					SELECT		mf.*,mc.label as categlabel, mc.id as id_cat,
							mb.protected,mb.name as namefield,mb.label as titlefield
					FROM		dims_mod_business_meta_field as mf
					INNER JOIN	dims_mb_field as mb
					ON		mb.id=mf.id_mbfield
					RIGHT JOIN	dims_mod_business_meta_categ as mc
					ON		mf.id_metacateg=mc.id
					WHERE		mf.id_object = ".dims_const::_SYSTEM_OBJECT_CONTACT."
					AND		mf.used=1
					AND		mf.option_exportview=1
					ORDER BY	mc.position, mf.position
					";
				$rs_fields=$db->query($sql);
				$contactfields="";

				while ($fields = $db->fetchrow($rs_fields)) {
					if ($contactfields=="")
						$contactfields= "c.".$fields['namefield'];
					else
						$contactfields.= ",c.".$fields['namefield'];

					if (isset($_DIMS['cste'][$fields['titlefield']])) $namevalue= $_DIMS['cste'][$fields['titlefield']];
					else $namevalue=$fields['name'];
					$elem=array();
					$elem['title']=$namevalue." (Contact)"; //ici avec perso
					$elem['namefield']=$fields['namefield'];

					$_SESSION['business']['exportdata'][]=$elem;
				}

				// on ajoute la fonction du contact
				$elem=array();
				$elem['title']="Function";
				$elem['namefield']="Function";
				$_SESSION['business']['exportdata'][]=$elem;

				if(isset($_SESSION['dims']['search']['current_search'])){
					require_once DIMS_APP_PATH . '/modules/system/class_search_expression.php' ;
					$search = new search_expression(false);//false = pas de pagination
					$search->open($_SESSION['dims']['search']['current_search'] );
					//if(isset($_SESSION['desktop']['search']['tags']))
					$exported_tiers = $search->getLightCompaniesWithContacts($tiersfields,$contactfields);
					//else $exported_tiers = $search->getLightCompaniesWithContacts();
				}


				require_once(DIMS_APP_PATH . '/modules/system/crm_public_ent_searchexportAll.php');
			}*/
			die();
			break;
		case 'choose_type_create_fiche':
			ob_clean();
			$id_popup	= dims_load_securvalue('id_popup',dims_const::_DIMS_CHAR_INPUT,true,true);
			$id_from	= dims_load_securvalue('id_from',dims_const::_DIMS_CHAR_INPUT,true,true);
			$type_from	= dims_load_securvalue('type_from',dims_const::_DIMS_CHAR_INPUT,true,true);
			$submenu	= dims_load_securvalue('submenu', dims_const::_DIMS_NUM_INPUT, true, false);
			if ($submenu > 0) {
				$_SESSION['dims']['desktopv2']['submenu'] = $submenu;
			}

			?>
			<div>
				<div class="actions">
					<a href="Javascript: void(0);" onclick="Javascript: dims_closeOverlayedPopup('<?php echo $id_popup; ?>');">
						<img src="<? echo _DESKTOP_TPL_PATH; ?>/gfx/common/close.png" />
					</a>
				</div>
				<h2><?php echo $_DIMS['cste']['_DIMS_LABEL_ADDLINK']; ?></h2>
				<img title="<? echo $_SESSION['cste']['_ADD_CT']; ?>" alt="<? echo $_SESSION['cste']['_ADD_CT']; ?>" onclick="javascript:linkContact(<?php echo $id_from; ?>, <?php echo $type_from; ?>); dims_closeOverlayedPopup('<?php echo $id_popup; ?>');" class="select_ct" src="<? echo _DESKTOP_TPL_PATH; ?>/gfx/common/contact_default_search.png" />
				<img title="<? echo $_SESSION['cste']['_DIMS_LABEL_ENT_CREATE']; ?>" alt="<? echo $_SESSION['cste']['_DIMS_LABEL_ENT_CREATE']; ?>" onclick="javascript:linkTier(<?php echo $id_from; ?>, <?php echo $type_from; ?>); dims_closeOverlayedPopup('<?php echo $id_popup; ?>');" class="select_ct" src="<? echo _DESKTOP_TPL_PATH; ?>/gfx/common/company_default_search.png" />
			</div>
			<?
			die();
			break;
		case 'link_contact_popup':
			ob_clean();
			$id_popup	= dims_load_securvalue('id_popup',dims_const::_DIMS_CHAR_INPUT,true,true);
			if(!isset($_SESSION['dims']['desktopv2']['matrice']['id_from'])) $_SESSION['dims']['desktopv2']['matrice']['id_from'] = 0;
			if(!isset($_SESSION['dims']['desktopv2']['matrice']['type_from'])) $_SESSION['dims']['desktopv2']['matrice']['type_from'] = 0;
			$id_from	= dims_load_securvalue('id_from',dims_const::_DIMS_NUM_INPUT,true,true,true,$_SESSION['dims']['desktopv2']['matrice']['id_from']);
			$type_from	= dims_load_securvalue('type_from',dims_const::_DIMS_NUM_INPUT,true,true,true,$_SESSION['dims']['desktopv2']['matrice']['type_from']);
			?>
			<div>
				<div class="actions">
					<a href="Javascript: void(0);" onclick="Javascript: dims_closeOverlayedPopup('<?php echo $id_popup; ?>');">
						<img src="<? echo _DESKTOP_TPL_PATH; ?>/gfx/common/close.png" />
					</a>
				</div>
				<h2><?php echo $_DIMS['cste']['_DIMS_LABEL_CT_SEARCH_PERS']; ?></h2>
				<div class="zone_search">
					<form method="post" action="">
						<?
							// Sécurisation du formulaire par token
							require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
							$token = new FormToken\TokenField;
							$token->field("dims_op",	"desktopv2");
							$token->field("action",		"link_contact");
							$token->field("id_contact",	"id_contact");
							$token->field("tiers_type_link");
							$token->field("tiers_link_level");
							$token->field("date_deb_day");
							$token->field("date_deb_month");
							$token->field("date_deb_year");
							$token->field("date_fin_day");
							$token->field("date_fin_month");
							$token->field("date_fin_year");
							$token->field("fonction");
							$token->field("departement");
							$token->field("commentaire");
							$tokenHTML = $token->generate();
							echo $tokenHTML;
						?>
						<input type="hidden" name="dims_op" value="desktopv2" />
						<input type="hidden" name="action" value="link_contact" />
						<input type="hidden" name="id_contact" id="id_contact" value="" />
						<?php
						if(!empty($_SESSION['dims']['desktopv2']['matrice']['id_from']) && !empty($_SESSION['dims']['desktopv2']['matrice']['type_from']) && $_SESSION['dims']['desktopv2']['matrice']['type_from'] == dims_const::_SYSTEM_OBJECT_TIERS) {
							?>
							<table width="100%" cellpadding="0" cellspacing="0">
								<tr>
									<td width="15%" align="right"><?php echo $_DIMS['cste']['_DIMS_LABEL_LINK_TYPE']; ?>&nbsp;</td>
									<td width="35%" align="left">
										<select id="tiers_type_link" name="tiers_type_link" style="background-color:#ebf2ea;">
											<option value="<?php echo $_DIMS['cste']['_DIMS_LABEL_EMPLOYEUR']; ?>"><?php echo $_DIMS['cste']['_DIMS_LABEL_EMPLOYEUR']; ?></option>
											<option value="<?php echo $_DIMS['cste']['_DIMS_LABEL_ASSOCIE']; ?>"><?php echo $_DIMS['cste']['_DIMS_LABEL_ASSOCIE']; ?></option>
											<option value="<?php echo stripslashes($_DIMS['cste']['_DIMS_LABEL_CONSADMIN']); ?>"><?php echo stripslashes($_DIMS['cste']['_DIMS_LABEL_CONSADMIN']); ?></option>
											<option value="<?php echo $_DIMS['cste']['_DIMS_LABEL_OTHER']; ?>"><?php echo $_DIMS['cste']['_DIMS_LABEL_OTHER']; ?></option>
										</select
									</td>
									<td></td>
								</tr>
								<tr>
									<td width="15%" align="right"><?php echo $_DIMS['cste']['_DIMS_LABEL_LEVEL_LINK']; ?>&nbsp;</td>
									<td width="35%" align="left">
										<select id="tiers_link_level" name="tiers_link_level" style="background-color:#ebf2ea;">
											<option value="1"><?php echo $_DIMS['cste']['_DIMS_LABEL_LFB_GEN']; ?></option>
											<option value="2" selected><?php echo $_DIMS['cste']['_DIMS_LABEL_LFB_MET']; ?></option>
										</select>
									</td>
									<td></td>
								</tr>
								<tr>
									<td width="15%" align="right"><?php echo $_DIMS['cste']['_BEGIN']; ?>&nbsp;</td>
									<td width="35%" align="left">
										<table cellpadding="0" cellspacing="0">
											<tr>
												<td>
													<input id="date_deb_day" name="date_deb_day" maxlenght="2" value="<?php echo date("d"); ?>" style="width:30px;background-color:#ebf2ea;"/>&nbsp;/&nbsp;
												</td>
												<td>
													<input id="date_deb_month" name="date_deb_month" maxlenght="2" value="<?php echo date("m"); ?>" style="width:30px;background-color:#ebf2ea;"/>&nbsp;/&nbsp;
												</td>
												<td>
													<input id="date_deb_year" name="date_deb_year" maxlenght="4" value="<?php echo date("Y"); ?>" style="width:30px;background-color:#ebf2ea;"/>
												</td>
											</tr>
										</table>
									</td>
									<td></td>
								</tr>
								<tr>
									<td width="15%" align="right"><?php echo $_DIMS['cste']['_END']; ?>&nbsp;</td>
									<td width="35%" align="left">
										<table cellpadding="0" cellspacing="0">
											<tr>
												<td>
													<input id="date_fin_day" name="date_fin_day" maxlenght="2" value="jj" style="width:30px;background-color:#ebf2ea;"/>&nbsp;/&nbsp;
												</td>
												<td>
													<input id="date_fin_month" name="date_fin_month" maxlenght="2" value="mm" style="width:30px;background-color:#ebf2ea;"/>&nbsp;/&nbsp;
												</td>
												<td>
													<input id="date_fin_year" name="date_fin_year" maxlenght="4" value="aaaa" style="width:30px;background-color:#ebf2ea;"/>
												</td>
											</tr>
										</table>
									</td>
									<td></td>
								</tr>
								<tr>
									<td width="15%" align="right"><?php echo $_DIMS['cste']['_DIMS_LABEL_FUNCTION']; ?>&nbsp;</td>
									<td width="35%" align="left">
										<input type="text" id="fonction" name="fonction" style="background-color:#ebf2ea;" value=""/>
									</td>
									<td></td>
								</tr>
								<tr>
									<td width="15%" align="right"><?php echo ucfirst(strtolower($_DIMS['cste']['_DIMS_LABEL_DEPARTEMENT'])); ?>&nbsp;</td>
									<td width="35%" align="left">
										<input type="text" id="departement" name="departement" style="background-color:#ebf2ea;" value=""/>
									</td>
									<td></td>
								</tr>
								<tr>
									<td width="15%" align="right"><?php echo $_DIMS['cste']['_DIMS_COMMENTS']; ?>&nbsp;</td>
									<td width="35%" align="left">
										<textarea id="commentaire" name="commentaire" style="background-color:#ebf2ea;"></textarea>
									</td>
									<td></td>
								</tr>
							</table>
							<?php
						}
						?>
					</form>
					<div class="searchform">
						<span>
							<input id="button_image_search_ct" type="image" class="button_search" src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/search_gauche.png" style="float:left;" />
							<input autocomplete="off" onkeyup="javascript:searchLinkCt(this.value);" type="text" class="editbox_search" id="editbox_search_contact" maxlength="80" value="<? echo $_SESSION['cste']['_DIMS_LABEL_SEARCH_FOR_CT']; ?>" onfocus="Javascript:if (this.value=='<? echo $_SESSION['cste']['_DIMS_LABEL_SEARCH_FOR_CT']; ?>')this.value=''; $(this).addClass('working');" onblur="Javascript:if (this.value=='') { $(this).removeClass('working'); this.value='<? echo $_SESSION['cste']['_DIMS_LABEL_SEARCH_FOR_CT']; ?>'; }" />
							<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/search_droite.png" style="float:left;" />
						</span>
					</div>
				</div>
				<div id="div_list_search" style="max-height:300px;overflow-x: auto;"></div>
				<div class="otherAction">
					<a href="Javascript: void(0);" onclick="javascript:addContact(<?php echo $id_from; ?>, <?php echo $type_from; ?>); dims_closeOverlayedPopup('<?php echo $id_popup; ?>');">
						<?php echo $_DIMS['cste']['_DIMS_LABEL_NO_CONTACT_CREATE_ONE']; ?>
					</a>
				</div>
			</div>
			<?php
			die();
			break;
		case 'link_search_contact':
			ob_clean();
			$label = trim(dims_load_securvalue('label',dims_const::_DIMS_CHAR_INPUT,true,true,true));
			if ($label != '' && $label != $_SESSION['cste']['_DIMS_LABEL_SEARCH_FOR_CT']){
				$lstCt = array();
				$lstCtId = array();
				$sel = "SELECT		DISTINCT *
					FROM		dims_mod_business_contact
					WHERE		(firstname LIKE :label
					OR		lastname LIKE :label)
					AND		inactif = 0
					ORDER BY	lastname, firstname";
				$res = $db->query($sel, array(
					':label' => array('type' => PDO::PARAM_STR, 'value' => '%'.$label.'%'),
				));

				while($r = $db->fetchrow($res)){
					if((!empty($_SESSION['dims']['desktopv2']['matrice']['id_from']) && $_SESSION['dims']['desktopv2']['matrice']['id_from'] != $r['id']) || (!empty($_SESSION['dims']['desktopv2']['matrice']['type_from']) && $_SESSION['dims']['desktopv2']['matrice']['type_from'] != dims_const::_SYSTEM_OBJECT_CONTACT)) {
						$ct = new contact();
						$ct->openWithFields($r);
						$lstCt[$r['id']] = $ct;
						$lstCtId[$r['id']] = $r['id'];
					}
				}
				$lstTiers = $desktop->constructLstTiersFromCt($lstCt,$lstCtId);
				if (count($lstTiers) > 0)
					foreach($lstTiers as $tiers)
						$tiers->display(_DESKTOP_TPL_LOCAL_PATH.'/concepts/bloc_contact/search_contact_tiers.tpl.php');
				else
					echo "<div class=\"no_result\">No result for \"$label\"</div>";
			}
			die();
			break;
		case 'link_search_contact_activity':
			ob_clean();
			$label = trim(dims_load_securvalue('label',dims_const::_DIMS_CHAR_INPUT,true,true,true));
			if ($label != '' && $label != $_SESSION['cste']['_DIMS_LABEL_SEARCH_FOR_CT']){
				$lstCt = array();
				$lstCtId = array();
				$sel = "SELECT		DISTINCT *
					FROM		dims_mod_business_contact
					WHERE		(firstname LIKE :label)
					OR		lastname LIKE :label)
					AND		inactif = 0
					ORDER BY	lastname, firstname";
				$res = $db->query($sel, array(
					':label' => array('type' => PDO::PARAM_STR, 'value' => '%'.$label.'%'),
				));

				while($r = $db->fetchrow($res)){
					if(!empty($_SESSION['desktopv2']['activity']['tiers_selected'])) {
						$ct = new contact();
						$ct->openWithFields($r);
						$lstCt[$r['id']] = $ct;
						$lstCtId[$r['id']] = $r['id'];
					}
				}
				$lstTiers = $desktop->constructLstTiersFromCt($lstCt,$lstCtId);
				if (count($lstTiers) > 0)
					foreach($lstTiers as $tiers)
						$tiers->display(_DESKTOP_TPL_LOCAL_PATH.'/concepts/bloc_contact/search_contact_tiers_activity.tpl.php');
				else
					echo "<div class=\"no_result\">No result for \"$label\"</div>";
			}
			die();
			break;
		case 'link_search_contact_opportunity':
			ob_clean();
			$label = trim(dims_load_securvalue('label',dims_const::_DIMS_CHAR_INPUT,true,true,true));
			if ($label != '' && $label != $_SESSION['cste']['_DIMS_LABEL_SEARCH_FOR_CT']){
				$lstCt = array();
				$lstCtId = array();
				$sel = "SELECT		DISTINCT *
					FROM		dims_mod_business_contact
					WHERE		(firstname LIKE :label
					OR		lastname LIKE :label)
					AND		inactif = 0
					ORDER BY	lastname, firstname";
				$res = $db->query($sel, array(
					':label' => array('type' => PDO::PARAM_STR, 'value' => '%'.$label.'%'),
				));

				while($r = $db->fetchrow($res)){
					if(!empty($_SESSION['desktopv2']['opportunity']['tiers_selected'])) {
						$ct = new contact();
						$ct->openWithFields($r);
						$lstCt[$r['id']] = $ct;
						$lstCtId[$r['id']] = $r['id'];
					}
				}
				$lstTiers = $desktop->constructLstTiersFromCt($lstCt,$lstCtId);
				if (count($lstTiers) > 0)
					foreach($lstTiers as $tiers)
						$tiers->display(_DESKTOP_TPL_LOCAL_PATH.'/concepts/bloc_contact/search_contact_tiers_opportunity.tpl.php');
				else
					echo "<div class=\"no_result\">No result for \"$label\"</div>";
			}
			die();
			break;
		case 'link_contact':
			$id_contact = dims_load_securvalue('id_contact', dims_const::_DIMS_NUM_INPUT, true, true);

			if(!empty($_SESSION['dims']['desktopv2']['matrice']['id_from']) && !empty($_SESSION['dims']['desktopv2']['matrice']['type_from']) && $_SESSION['dims']['desktopv2']['matrice']['type_from'] == dims_const::_SYSTEM_OBJECT_TIERS) {
				$to_ent = $_SESSION['dims']['desktopv2']['matrice']['id_from'];
				$pers_from_id = $id_contact;

				$type_ent = dims_load_securvalue('tiers_type_link', dims_const::_DIMS_CHAR_INPUT, true, true);
				$ent_link_lvl = dims_load_securvalue('tiers_link_level', dims_const::_DIMS_CHAR_INPUT, true, true);

				$date_deb_d = dims_load_securvalue('date_deb_day', dims_const::_DIMS_NUM_INPUT, true, true);
				$date_deb_m = dims_load_securvalue('date_deb_month', dims_const::_DIMS_NUM_INPUT, true, true);
				$date_deb_y = dims_load_securvalue('date_deb_year', dims_const::_DIMS_NUM_INPUT, true, true);
				$date_fin_d = dims_load_securvalue('date_fin_day', dims_const::_DIMS_NUM_INPUT, true, true);
				$date_fin_m = dims_load_securvalue('date_fin_month', dims_const::_DIMS_NUM_INPUT, true, true);
				$date_fin_y = dims_load_securvalue('date_fin_year', dims_const::_DIMS_NUM_INPUT, true, true);

				$date_deb = $date_deb_y.$date_deb_m.$date_deb_d."000000";
				$date_fin = $date_fin_y.$date_fin_m.$date_fin_d."000000";

				$commentaire = dims_load_securvalue('commentaire', dims_const::_DIMS_CHAR_INPUT, true, true);
				$fonction = dims_load_securvalue('fonction', dims_const::_DIMS_CHAR_INPUT, true, true);
				$departement = dims_load_securvalue('departement', dims_const::_DIMS_CHAR_INPUT, true, true);

				require_once(DIMS_APP_PATH . '/modules/system/class_tiers_contact.php');
				$ct_tiers = new tiersct();
				$ct_tiers->fields['id_tiers']=$to_ent;
				$ct_tiers->fields['id_contact']=$pers_from_id;
				$ct_tiers->fields['type_lien']=$type_ent;
				$ct_tiers->fields['function']=$fonction;
				$ct_tiers->fields['departement']=$departement;
				$ct_tiers->fields['id_workspace']=$_SESSION['dims']['workspaceid'];
				$ct_tiers->fields['id_user']=$_SESSION['dims']['userid'];
				$ct_tiers->fields['date_create']=date("YmdHis");
				$ct_tiers->fields['link_level']=$ent_link_lvl;
				$ct_tiers->fields['id_ct_user_create']=$_SESSION['dims']['user']['id_contact'];
				$ct_tiers->fields['date_deb']=$date_deb;
				$ct_tiers->fields['date_fin']=$date_fin;
				$ct_tiers->fields['commentaire']=$commentaire;
				$ct_tiers->save();
			}
			elseif(!empty($_SESSION['dims']['desktopv2']['matrice']['id_from']) && !empty($_SESSION['dims']['desktopv2']['matrice']['type_from']) && $_SESSION['dims']['desktopv2']['matrice']['type_from'] == dims_const::_SYSTEM_OBJECT_CONTACT) {
				$sql_ins = "INSERT INTO `dims_mod_business_ct_link` (
							`id_contact1` ,
							`id_contact2` ,
							`id_object` ,
							`type_link` ,
							`link_level` ,
							`time_create` ,
							`id_ct_user_create` ,
							`date_deb` ,
							`date_fin` ,
							`id_workspace` ,
							`id_user` ,
							`commentaire`
							)
							VALUES (
							:idcontactfrom,
							:idcontactto,
							:idobject ,
							'',
							'2',
							'".date('YmdHis')."',
							:idcontact,
							'',
							'',
							:idworkspace,
							:iduser,
							''
							);
						";
				$db->query($sql_ins, array(
					':idcontactfrom' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['desktopv2']['matrice']['id_from']),
					':idcontactto' => array('type' => PDO::PARAM_INT, 'value' => $id_contact),
					':idobject' => array('type' => PDO::PARAM_INT, 'value' => dims_const::_SYSTEM_OBJECT_CONTACT),
					':idcontact' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['id_contact']),
					':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['workspaceid']),
					':iduser' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['userid']),
				));
			}
			if(!empty($_SESSION['dims']['desktopv2']['matrice']['id_from']) && !empty($_SESSION['dims']['desktopv2']['matrice']['type_from']) && $_SESSION['dims']['desktopv2']['matrice']['type_from'] == dims_const::_SYSTEM_OBJECT_EVENT) {
				require_once(DIMS_APP_PATH . '/modules/system/class_event_inscription.php');

				$contact = new contact();
				$contact->open($id_contact);
				$evt_insc = new event_insc();

				$evt_insc->fields['id_contact'] = $id_contact;
				$evt_insc->fields['id_action'] = $_SESSION['dims']['desktopv2']['matrice']['id_from'];
				$evt_insc->fields['validate'] = 2;

				$evt_insc->fields['lastname'] = $contact->fields['lastname'];
				$evt_insc->fields['firstname'] = $contact->fields['firstname'];
				$evt_insc->fields['address'] = $contact->fields['address'];
				$evt_insc->fields['email'] = $contact->fields['email'];
				$evt_insc->fields['city'] = $contact->fields['city'];
				$evt_insc->fields['postalcode'] = $contact->fields['postalcode'];
				$evt_insc->fields['country'] = $contact->fields['country'];
				$evt_insc->fields['phone'] = $contact->fields['phone'];
				$evt_insc->fields['email'] = $contact->fields['email'];
				$evt_insc->fields['company'] = '';
				$evt_insc->fields['function'] = '';

				$evt_insc->save();
			}

			dims_redirect('admin.php?dims_op=desktopv2&action=contact_matrice&id_contact='.$id_contact);
			break;
		case 'save_link_entity':
			$id_from = dims_load_securvalue('id_from', dims_const::_DIMS_NUM_INPUT, true, true);
			$goFrom = new dims_globalobject();

			if (!isset($_SESSION['desktopv2']['activity']['ct_added']))
				$_SESSION['desktopv2']['activity']['ct_added'] = array();

			if (!isset($_SESSION['desktopv2']['activity']['tiers_added']))
				$_SESSION['desktopv2']['activity']['tiers_added'] = array();

			if (empty($_SESSION['desktopv2']['opportunity']['ct_added']))
				$_SESSION['desktopv2']['opportunity']['ct_added'] = $_SESSION['desktopv2']['activity']['ct_added'];

			if (empty($_SESSION['desktopv2']['opportunity']['tiers_added']))
				$_SESSION['desktopv2']['opportunity']['tiers_added'] = $_SESSION['desktopv2']['activity']['tiers_added'];


			if(!empty($id_from) && $goFrom->open($id_from)){
				require_once DIMS_APP_PATH."modules/system/desktopV2/include/class_desktopv2.php";

				$lstTiers = $desktop->constructLstTiersFromCt(array(),$_SESSION['desktopv2']['opportunity']['ct_added'],$_SESSION['desktopv2']['opportunity']['tiers_added']);

				foreach($lstTiers as $tiers){
					if (isset($tiers->contacts)){
						foreach($tiers->contacts as $ct){
							if($goFrom->fields['id_object'] == dims_const::_SYSTEM_OBJECT_TIERS) {
								$to_ent = $goFrom->fields['id_record'];
								$pers_from_id = $ct->getId();

								require_once(DIMS_APP_PATH . '/modules/system/class_tiers_contact.php');

								$ct_tiers = new tiersct();
								$ct_tiers->fields['id_tiers']=$to_ent;
								$ct_tiers->fields['id_contact']=$pers_from_id;
								$ct_tiers->fields['type_lien'] = 'business';
								$ct_tiers->fields['link_level']=2;
								$ct_tiers->fields['id_workspace']=$_SESSION['dims']['workspaceid'];
								$ct_tiers->fields['id_user']=$_SESSION['dims']['userid'];
								$ct_tiers->fields['date_create']=date("YmdHis");
								$ct_tiers->fields['date_deb']=date("YmdHis");
								$ct_tiers->fields['id_ct_user_create']=$_SESSION['dims']['user']['id_contact'];
								$ct_tiers->save();
							}
							elseif($goFrom->fields['id_object'] == dims_const::_SYSTEM_OBJECT_CONTACT) {
								$sql_ins = "INSERT INTO `dims_mod_business_ct_link` (
											`id_contact1` ,
											`id_contact2` ,
											`id_object` ,
											`type_link` ,
											`link_level` ,
											`time_create` ,
											`id_ct_user_create` ,
											`date_deb` ,
											`date_fin` ,
											`id_workspace` ,
											`id_user` ,
											`commentaire`
											)
											VALUES (
											:idcontactfrom,
											:idcontactto,
											:idobject ,
											'',
											'2',
											'".date('YmdHis')."',
											:idcontactcreate,
											'',
											'',
											:idworkspace,
											:iduser,
											''
											);
										";
								$db->query($sql_ins, array(
									':idcontactfrom' => array('type' => PDO::PARAM_INT, 'value' => $goFrom->fields['id_record']),
									':idcontactto' => array('type' => PDO::PARAM_INT, 'value' => $ct->getId()),
									':idobject' => array('type' => PDO::PARAM_INT, 'value' => dims_const::_SYSTEM_OBJECT_CONTACT),
									':idcontactcreate' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['user']['id_contact']),
									':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['workspaceid']),
									':iduser' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['userid']),
								));
							}
							elseif($goFrom->fields['id_object'] == dims_const::_SYSTEM_OBJECT_EVENT) {
								require_once(DIMS_APP_PATH . '/modules/system/class_event_inscription.php');

								$evt_insc = new event_insc();

								$evt_insc->fields['id_contact'] = $ct->getId();
								$evt_insc->fields['id_action'] = $goFrom->fields['id_record'];
								$evt_insc->fields['validate'] = 2;

								$evt_insc->fields['lastname'] = $ct->fields['lastname'];
								$evt_insc->fields['firstname'] = $ct->fields['firstname'];
								$evt_insc->fields['address'] = $ct->fields['address'];
								$evt_insc->fields['email'] = $ct->fields['email'];
								$evt_insc->fields['city'] = $ct->fields['city'];
								$evt_insc->fields['postalcode'] = $ct->fields['postalcode'];
								$evt_insc->fields['country'] = $ct->fields['country'];
								$evt_insc->fields['phone'] = $ct->fields['phone'];
								$evt_insc->fields['email'] = $ct->fields['email'];
								$evt_insc->fields['company'] = '';
								$evt_insc->fields['function'] = '';

								$evt_insc->save();
							}

							$querystring='insert into dims_matrix (`id`, `id_action`, `id_opportunity`, `id_activity`, `id_tiers`, `id_tiers2`, `id_contact`, `id_contact2`, `id_doc`, `id_country`, `year`, `month`, `timestp_modify`, `id_workspace`) values ';

							$id_action=0;
							$id_opportunity=0;
							$id_activity=0;
							$id_contact=0;
							$id_contact2=0;
							$id_tiers=0;
							$id_tiers2=0;
							$id_doc=0;
							$id_country=0;
							$id_workspace=$_SESSION['dims']['workspaceid'];
							$year=date('Y');
							$month=date('m');
							$timestp=dims_createtimestamp();
							$id_from = $goFrom->fields['id_record'];


							switch ($goFrom->fields['id_object']) {
								case dims_const::_SYSTEM_OBJECT_CONTACT:
									$contact = new contact();
									$contact->open($id_from);

									$id_country = $ct->updateIdCountry();

									$id_contact = $ct->fields['id_globalobject'];
									$id_contact2 = $contact->fields['id_globalobject'];

									$querystring.= '(null,:2idaction,:2idopportunity,:2idaction,:2idtiers1,:2idtiers2,:2idcontact1,:2idcontact2,:2iddoc,:2idcase,:2idsuivi,:2idcountry,:2year,:2month,:2timestamp,:2idworkspace) ';
									$params = array();
									$params[':2idaction'] = array('type' => PDO::PARAM_INT, 'value' => $id_action);
									$params[':2idopportunity'] = array('type' => PDO::PARAM_INT, 'value' => $id_opportunity);
									$params[':2idactivity'] = array('type' => PDO::PARAM_INT, 'value' => $id_activity);
									$params[':2idtiers1'] = array('type' => PDO::PARAM_INT, 'value' => $id_tiers);
									$params[':2idtiers2'] = array('type' => PDO::PARAM_INT, 'value' => $id_tiers2);
									$params[':2idcontact1'] = array('type' => PDO::PARAM_INT, 'value' => $id_contact);
									$params[':2idcontact2'] = array('type' => PDO::PARAM_INT, 'value' => $id_contact2);
									$params[':2iddoc'] = array('type' => PDO::PARAM_INT, 'value' => $id_doc);
									$params[':2idcase'] = array('type' => PDO::PARAM_INT, 'value' => $id_case);
									$params[':2idsuivi'] = array('type' => PDO::PARAM_INT, 'value' => $id_suivi);
									$params[':2idcountry'] = array('type' => PDO::PARAM_INT, 'value' => $id_country);
									$params[':2year'] = array('type' => PDO::PARAM_INT, 'value' => $year);
									$params[':2month'] = array('type' => PDO::PARAM_INT, 'value' => $month);
									$params[':2timestamp'] = array('type' => PDO::PARAM_INT, 'value' => $timstp);
									$params[':2idworkspace'] = array('type' => PDO::PARAM_INT, 'value' => $id_workspace);


									$querystring.= ', ';

									$id_country = $contact->updateIdCountry();

									$id_contact = $contact->fields['id_globalobject'];
									$id_contact2 = $ct->fields['id_globalobject'];

									dims_globalobject::setLightTimestamp($ct->fields['id_globalobject'], $timestp);
									dims_globalobject::setLightTimestamp($contact->fields['id_globalobject'], $timestp);
									break;
								case dims_const::_SYSTEM_OBJECT_TIERS:
									$tiers = new tiers();
									$tiers->open($id_from);

									$id_tiers = $tiers->fields['id_globalobject'];
									$id_contact = $ct->fields['id_globalobject'];

									$id_country = $tiers->updateIdCountry();

									dims_globalobject::setLightTimestamp($ct->fields['id_globalobject'], $timestp);
									dims_globalobject::setLightTimestamp($tiers->fields['id_globalobject'], $timestp);
									break;
								case dims_const::_SYSTEM_OBJECT_EVENT:
									$event = new action();
									$event->open($id_from);

									$id_contact = $ct->fields['id_globalobject'];

									$id_action = $event->fields['id_globalobject'];
									$id_country = $event->updateIdCountry();
									break;
								case dims_const::_SYSTEM_OBJECT_OPPORTUNITY:
									$event = new action();
									$event->open($id_from);

									$id_contact = $ct->fields['id_globalobject'];

									$id_opportunity = $event->fields['id_globalobject'];
									$id_country = $event->updateIdCountry();
									break;
							}

							$querystring.= '(null,:idaction,:idopportunity,:idaction,:idtiers1,:idtiers2,:idcontact1,:idcontact2,:iddoc,:idcase,:idsuivi,:idcountry,:year,:month,:timestamp,:idworkspace) ';
							$params = array();
							$params[':idaction'] = array('type' => PDO::PARAM_INT, 'value' => $id_action);
							$params[':idopportunity'] = array('type' => PDO::PARAM_INT, 'value' => $id_opportunity);
							$params[':idactivity'] = array('type' => PDO::PARAM_INT, 'value' => $id_activity);
							$params[':idtiers1'] = array('type' => PDO::PARAM_INT, 'value' => $id_tiers);
							$params[':idtiers2'] = array('type' => PDO::PARAM_INT, 'value' => $id_tiers2);
							$params[':idcontact1'] = array('type' => PDO::PARAM_INT, 'value' => $id_contact);
							$params[':idcontact2'] = array('type' => PDO::PARAM_INT, 'value' => $id_contact2);
							$params[':iddoc'] = array('type' => PDO::PARAM_INT, 'value' => $id_doc);
							$params[':idcase'] = array('type' => PDO::PARAM_INT, 'value' => $id_case);
							$params[':idsuivi'] = array('type' => PDO::PARAM_INT, 'value' => $id_suivi);
							$params[':idcountry'] = array('type' => PDO::PARAM_INT, 'value' => $id_country);
							$params[':year'] = array('type' => PDO::PARAM_INT, 'value' => $year);
							$params[':month'] = array('type' => PDO::PARAM_INT, 'value' => $month);
							$params[':timestamp'] = array('type' => PDO::PARAM_INT, 'value' => $timstp);
							$params[':idworkspace'] = array('type' => PDO::PARAM_INT, 'value' => $id_workspace);

							$db->query($querystring, $params);
						}
					}

					if(!$tiers->isNew() && isset($_SESSION['desktopv2']['opportunity']['tiers_tolink'][$tiers->getId()]) && $_SESSION['desktopv2']['opportunity']['tiers_tolink'][$tiers->getId()] == _TIER_LINK) {
						switch($goFrom->fields['id_object']) {
							case dims_const::_SYSTEM_OBJECT_CONTACT:
								$pers_from_id = $goFrom->fields['id_record'];
								$to_ent = $tiers->getId();

								$type_ent = dims_load_securvalue('tiers_type_link', dims_const::_DIMS_CHAR_INPUT, true, true);
								$ent_link_lvl = dims_load_securvalue('tiers_link_level', dims_const::_DIMS_CHAR_INPUT, true, true);

								$fonction = dims_load_securvalue('fonction', dims_const::_DIMS_CHAR_INPUT, true, true);
								$departement = dims_load_securvalue('departement', dims_const::_DIMS_CHAR_INPUT, true, true);

								require_once DIMS_APP_PATH.'modules/system/class_tiers_contact.php';
								$ct_tiers = new tiersct();
								$ct_tiers->fields['id_tiers']=$to_ent;
								$ct_tiers->fields['id_contact']=$pers_from_id;
								$ct_tiers->fields['type_lien'] = 'business';
								$ct_tiers->fields['link_level']=2;
								$ct_tiers->fields['id_workspace']=$_SESSION['dims']['workspaceid'];
								$ct_tiers->fields['id_user']=$_SESSION['dims']['userid'];
								$ct_tiers->fields['date_create']=date("YmdHis");
								$ct_tiers->fields['id_ct_user_create']=$_SESSION['dims']['user']['id_contact'];
								$ct_tiers->fields['date_deb']=date("YmdHis");
								$ct_tiers->save();
								break;
							case dims_const::_SYSTEM_OBJECT_TIERS:
								$ent_from_id = $_SESSION['dims']['desktopv2']['matrice']['id_from'];

								require_once DIMS_APP_PATH.'modules/system/class_ct_link.php';
								$ctlink = new ctlink();
								$ctlink->fields['id_contact1'] = $goFrom->fields['id_record'];
								$ctlink->fields['id_contact2'] = $tiers->getId();
								$ctlink->fields['id_object'] = dims_const::_SYSTEM_OBJECT_TIERS;
								$ctlink->fields['type_link'] = '';
								$ctlink->fields['link_level'] = 1;
								$ctlink->fields['time_create'] = dims_createtimestamp();
								$ctlink->fields['id_ct_user_create'] = $_SESSION['dims']['userid'];
								$ctlink->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
								$ctlink->fields['id_user'] = $_SESSION['dims']['userid'];
								$ctlink->save();
								break;
							case dims_const::_SYSTEM_OBJECT_OPPORTUNITY:
								require_once DIMS_APP_PATH."modules/system/desktopV2/include/class_desktopv2.php";
								require_once DIMS_APP_PATH.'modules/system/class_ct_link.php';
								require_once DIMS_APP_PATH.'modules/system/class_tiers_contact.php';
								require_once DIMS_APP_PATH.'modules/system/opportunity/class_opportunity.php';

								$opp = new dims_opportunity();
								$opp->open($goFrom->fields['id_record']);
								$date = explode('-',$opp->fields['datejour']);
								$u = new contact();
								$u->open($_SESSION['dims']['user']['id_contact']);
								if (count($date) == 3){
									$datestart_year = $date[0];
									$datestart_month = $date[1];
								}else{
									$datestart_year = date('Y');
									$datestart_month = date('m');
								}
								$matrice = new matrix();
								$matrice->fields['id_tiers'] = $tiers->fields['id_globalobject'];
								$matrice->fields['id_contact'] = $u->fields['id_globalobject'];
								$matrice->fields['id_country'] = $opp->fields['id_country'];
								$matrice->fields['year'] = $datestart_year;
								$matrice->fields['month'] = $datestart_month;
								$matrice->fields['id_opportunity'] = $opp->fields['id_globalobject'];
								$matrice->fields['timestp_modify'] = dims_createtimestamp();
								$matrice->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
								$matrice->save();

								$tiersct = new tiersct();
								$tiersct->fields['id_tiers'] = $tiers->fields['id'];
								$tiersct->fields['id_contact'] = $_SESSION['dims']['user']['id_contact'];
								$tiersct->fields['type_lien'] = 'Other';
								$tiersct->fields['link_level'] = 2;
								$tiersct->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
								$tiersct->fields['id_user'] = $_SESSION['dims']['userid'];
								$tiersct->fields['date_create'] = dims_createtimestamp();
								$tiersct->save();
								break;
						}


						$querystring='insert into dims_matrix (`id`, `id_action`, `id_opportunity`, `id_activity`, `id_tiers`, `id_tiers2`, `id_contact`, `id_contact2`, `id_doc`, `id_country`, `year`, `month`, `timestp_modify`, `id_workspace`) values ';

						$id_action=0;
						$id_opportunity=0;
						$id_activity=0;
						$id_contact=0;
						$id_contact2=0;
						$id_tiers=0;
						$id_tiers2=0;
						$id_doc=0;
						$id_country=0;
						$id_workspace=$_SESSION['dims']['workspaceid'];
						$year=date('Y');
						$month=date('m');
						$timestp=dims_createtimestamp();
						$id_from = $goFrom->fields['id_record'];

						switch ($goFrom->fields['id_object']) {
							case dims_const::_SYSTEM_OBJECT_CONTACT:
								$contact = new contact();
								$contact->open($id_from);

								$id_tiers = $tiers->fields['id_globalobject'];
								$id_contact = $contact->fields['id_globalobject'];

								$contact->updateIdCountry();

								$id_country = $tiers->updateIdCountry();

								dims_globalobject::setLightTimestamp($tiers->fields['id_globalobject'], $timestp);
								dims_globalobject::setLightTimestamp($contact->fields['id_globalobject'], $timestp);
								break;
							case dims_const::_SYSTEM_OBJECT_TIERS:
								$currenttiers = new tiers();
								$currenttiers->open($id_from);

								$id_country = $tiers->updateIdCountry();

								$id_tiers = $tiers->fields['id_globalobject'];
								$id_tiers2 = $currenttiers->fields['id_globalobject'];

								$querystring.= '(null,:2idaction,:2idopportunity,:2idaction,:2idtiers1,:2idtiers2,:2idcontact1,:2idcontact2,:2iddoc,:2idcase,:2idsuivi,:2idcountry,:2year,:2month,:2timestamp,:2idworkspace) ';
								$params = array();
								$params[':2idaction'] = array('type' => PDO::PARAM_INT, 'value' => $id_action);
								$params[':2idopportunity'] = array('type' => PDO::PARAM_INT, 'value' => $id_opportunity);
								$params[':2idactivity'] = array('type' => PDO::PARAM_INT, 'value' => $id_activity);
								$params[':2idtiers1'] = array('type' => PDO::PARAM_INT, 'value' => $id_tiers);
								$params[':2idtiers2'] = array('type' => PDO::PARAM_INT, 'value' => $id_tiers2);
								$params[':2idcontact1'] = array('type' => PDO::PARAM_INT, 'value' => $id_contact);
								$params[':2idcontact2'] = array('type' => PDO::PARAM_INT, 'value' => $id_contact2);
								$params[':2iddoc'] = array('type' => PDO::PARAM_INT, 'value' => $id_doc);
								$params[':2idcase'] = array('type' => PDO::PARAM_INT, 'value' => $id_case);
								$params[':2idsuivi'] = array('type' => PDO::PARAM_INT, 'value' => $id_suivi);
								$params[':2idcountry'] = array('type' => PDO::PARAM_INT, 'value' => $id_country);
								$params[':2year'] = array('type' => PDO::PARAM_INT, 'value' => $year);
								$params[':2month'] = array('type' => PDO::PARAM_INT, 'value' => $month);
								$params[':2timestamp'] = array('type' => PDO::PARAM_INT, 'value' => $timstp);
								$params[':2idworkspace'] = array('type' => PDO::PARAM_INT, 'value' => $id_workspace);


								$querystring.= ', ';

								$id_country = $tiers->updateIdCountry();

								$id_tiers = $currenttiers->fields['id_globalobject'];
								$id_tiers2 = $tiers->fields['id_globalobject'];

								dims_globalobject::setLightTimestamp($currenttiers->fields['id_globalobject'], $timestp);
								dims_globalobject::setLightTimestamp($tiers->fields['id_globalobject'], $timestp);
								break;
							case dims_const::_SYSTEM_OBJECT_EVENT:
								$event = new action();
								$event->open($id_from);

								$id_tiers = $tiers->fields['id_globalobject'];

								$id_action = $event->fields['id_globalobject'];
								$id_country = $event->updateIdCountry();
								break;
							case dims_const::_SYSTEM_OBJECT_ACTIVITY:
								$event = new action();
								$event->open($id_from);

								$id_tiers = $tiers->fields['id_globalobject'];

								$id_activity = $event->fields['id_globalobject'];
								$id_country = $event->updateIdCountry();
								break;
							case dims_const::_SYSTEM_OBJECT_OPPORTUNITY:
								$event = new action();
								$event->open($id_from);

								$id_tiers = $tiers->fields['id_globalobject'];

								$id_opportunity = $event->fields['id_globalobject'];
								$id_country = $event->updateIdCountry();
								break;
						}

						$querystring.= '(null,:idaction,:idopportunity,:idaction,:idtiers1,:idtiers2,:idcontact1,:idcontact2,:iddoc,:idcase,:idsuivi,:idcountry,:year,:month,:timestamp,:idworkspace) ';
						$params = array();
						$params[':idaction'] = array('type' => PDO::PARAM_INT, 'value' => $id_action);
						$params[':idopportunity'] = array('type' => PDO::PARAM_INT, 'value' => $id_opportunity);
						$params[':idactivity'] = array('type' => PDO::PARAM_INT, 'value' => $id_activity);
						$params[':idtiers1'] = array('type' => PDO::PARAM_INT, 'value' => $id_tiers);
						$params[':idtiers2'] = array('type' => PDO::PARAM_INT, 'value' => $id_tiers2);
						$params[':idcontact1'] = array('type' => PDO::PARAM_INT, 'value' => $id_contact);
						$params[':idcontact2'] = array('type' => PDO::PARAM_INT, 'value' => $id_contact2);
						$params[':iddoc'] = array('type' => PDO::PARAM_INT, 'value' => $id_doc);
						$params[':idcase'] = array('type' => PDO::PARAM_INT, 'value' => $id_case);
						$params[':idsuivi'] = array('type' => PDO::PARAM_INT, 'value' => $id_suivi);
						$params[':idcountry'] = array('type' => PDO::PARAM_INT, 'value' => $id_country);
						$params[':year'] = array('type' => PDO::PARAM_INT, 'value' => $year);
						$params[':month'] = array('type' => PDO::PARAM_INT, 'value' => $month);
						$params[':timestamp'] = array('type' => PDO::PARAM_INT, 'value' => $timstp);
						$params[':idworkspace'] = array('type' => PDO::PARAM_INT, 'value' => $id_workspace);

						$db->query($querystring, $params);
					}
				}
			}

			unset($_SESSION['desktopv2']['activity']['ct_added']);
			unset($_SESSION['desktopv2']['activity']['tiers_added']);
			unset($_SESSION['desktopv2']['opportunity']['ct_added']);
			unset($_SESSION['desktopv2']['opportunity']['tiers_added']);
			unset($_SESSION['desktopv2']['opportunity']['tiers_tolink']);
			ob_clean();
			die();
			break;
		case 'link_existing_contact':
			ob_clean();

			$id_contact = dims_load_securvalue('id_contact', dims_const::_DIMS_NUM_INPUT, true, true);

			if (!empty($_SESSION['desktopv2']['activity']['tiers_selected'])) {
				require_once(DIMS_APP_PATH . '/modules/system/class_tiers_contact.php');
				$ct_tiers = new tiersct();

				// verification du rattachement existant
				$sel = "SELECT		DISTINCT id
					FROM		dims_mod_business_tiers_contact
					WHERE		id_tiers=:idtiers
					AND		id_contact=:idcontact";

				$res = $db->query($sel, array(
					':idtiers' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['desktopv2']['opportunity']['tiers_selected']),
					':idcontact' => array('type' => PDO::PARAM_INT, 'value' => $id_contact),
				));
				if ($r=$db->fetchrow($res)) {
					$ct_tiers->open($r['id']);
				}

				$ct_tiers->fields['id_tiers']=$_SESSION['desktopv2']['activity']['tiers_selected'];
				$ct_tiers->fields['id_contact']=$id_contact;
				$ct_tiers->fields['link_level']=2;
				$ct_tiers->fields['type_lien']='employer';
				$ct_tiers->fields['id_workspace']=$_SESSION['dims']['workspaceid'];
				$ct_tiers->fields['id_user']=$_SESSION['dims']['userid'];

				if (!isset($ct_tiers->fields['date_create']) || $ct_tiers->fields['date_create']=='')
					$ct_tiers->fields['date_create']=date("YmdHis");

				if (!isset($ct_tiers->fields['date_deb']) || $ct_tiers->fields['date_deb']=='')
					$ct_tiers->fields['date_deb']=date("YmdHis");

				$ct_tiers->fields['date_fin']=0;

				if (!isset($ct_tiers->fields['id_ct_user_create']) || $ct_tiers->fields['id_ct_user_create']=='' || $ct_tiers->fields['id_ct_user_create']==0)
					$ct_tiers->fields['id_ct_user_create']=$_SESSION['dims']['user']['id_contact'];

				$ct_tiers->save();
			}

			if (!empty($_SESSION['desktopv2']['opportunity']['tiers_selected'])) {
				require_once(DIMS_APP_PATH . '/modules/system/class_tiers_contact.php');
				$ct_tiers = new tiersct();

				// verification du rattachement existant
				$sel = "SELECT		DISTINCT id
					FROM		dims_mod_business_tiers_contact
					WHERE		id_tiers=:idtiers
					AND		id_contact=:idcontact";

				$res = $db->query($sel, array(
					':idtiers' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['desktopv2']['opportunity']['tiers_selected']),
					':idcontact' => array('type' => PDO::PARAM_INT, 'value' => $id_contact),
				));
				if ($r=$db->fetchrow($res)) {
					$ct_tiers->open($r['id']);
				}

				$ct_tiers->fields['id_tiers']=$_SESSION['desktopv2']['opportunity']['tiers_selected'];
				$ct_tiers->fields['id_contact']=$id_contact;
				$ct_tiers->fields['link_level']=2;
				$ct_tiers->fields['type_lien']='employer';
				$ct_tiers->fields['id_workspace']=$_SESSION['dims']['workspaceid'];
				$ct_tiers->fields['id_user']=$_SESSION['dims']['userid'];

				if (!isset($ct_tiers->fields['date_create']) || $ct_tiers->fields['date_create']=='')
					$ct_tiers->fields['date_create']=date("YmdHis");

				if (!isset($ct_tiers->fields['date_deb']) || $ct_tiers->fields['date_deb']=='')
					$ct_tiers->fields['date_deb']=date("YmdHis");

				$ct_tiers->fields['date_fin']=0;

				if (!isset($ct_tiers->fields['id_ct_user_create']) || $ct_tiers->fields['id_ct_user_create']=='' || $ct_tiers->fields['id_ct_user_create']==0)
					$ct_tiers->fields['id_ct_user_create']=$_SESSION['dims']['user']['id_contact'];

				$ct_tiers->save();
			}

			$currentContact = new contact();
			$currentContact->open($id_contact);

			if (isset($_SESSION['desktopv2']['activity']['tiers_selected']) && $_SESSION['desktopv2']['activity']['tiers_selected']>0) {
				$id_from = $_SESSION['desktopv2']['activity']['tiers_selected'];

				if(!empty($id_from)) {
					$querystring='insert into dims_matrix  (id,id_action,id_opportunity,id_activity,id_appointment_offer,id_tiers,id_tiers2,id_contact,id_contact2,id_doc,id_case,id_suivi,id_country,year,month,timestp_modify,id_workspace) values ';

					$id_action=0;
					$id_opportunity=0;
					$id_appointment_offer=0;
					$id_activity=0;
					$id_contact=0;
					$id_contact2=0;
					$id_tiers=0;
					$id_tiers2=0;
					$id_doc=0;
					$id_case=0;
					$id_suivi=0;
					$id_country=0;
					$id_workspace=$_SESSION['dims']['workspaceid'];
					$year=date('Y');
					$month=date('m');
					$timestp=dims_createtimestamp();

					$tiers = new tiers();
					$tiers->open($id_from);

					$id_tiers = $tiers->fields['id_globalobject'];
					$id_contact = $currentContact->fields['id_globalobject'];

					$currentContact->updateIdCountry();

					$id_country = $tiers->updateIdCountry();

					$querystring.= '(null,:idaction,:idopportunity,:idactivity,:id_appointment_offer,:idtiers1,:idtiers2,:idcontact1,:idcontact2,:iddoc,:idcase,:idsuivi,:idcountry,:year,:month,:timestamp,:idworkspace) ';
					$params = array();
					$params[':idaction'] = array('type' => PDO::PARAM_INT, 'value' => $id_action);
					$params[':idopportunity'] = array('type' => PDO::PARAM_INT, 'value' => $id_opportunity);
					$params[':idactivity'] = array('type' => PDO::PARAM_INT, 'value' => $id_activity);
					$params[':id_appointment_offer'] = array('type' => PDO::PARAM_INT, 'value' => $id_appointment_offer);
					$params[':idtiers1'] = array('type' => PDO::PARAM_INT, 'value' => $id_tiers);
					$params[':idtiers2'] = array('type' => PDO::PARAM_INT, 'value' => $id_tiers2);
					$params[':idcontact1'] = array('type' => PDO::PARAM_INT, 'value' => $id_contact);
					$params[':idcontact2'] = array('type' => PDO::PARAM_INT, 'value' => $id_contact2);
					$params[':iddoc'] = array('type' => PDO::PARAM_INT, 'value' => $id_doc);
					$params[':idcase'] = array('type' => PDO::PARAM_INT, 'value' => $id_case);
					$params[':idsuivi'] = array('type' => PDO::PARAM_INT, 'value' => $id_suivi);
					$params[':idcountry'] = array('type' => PDO::PARAM_INT, 'value' => $id_country);
					$params[':year'] = array('type' => PDO::PARAM_INT, 'value' => $year);
					$params[':month'] = array('type' => PDO::PARAM_INT, 'value' => $month);
					$params[':timestamp'] = array('type' => PDO::PARAM_INT, 'value' => $timestp);
					$params[':idworkspace'] = array('type' => PDO::PARAM_INT, 'value' => $id_workspace);

					dims_globalobject::setLightTimestamp($currentContact->fields['id_globalobject'], $timestp);
					dims_globalobject::setLightTimestamp($tiers->fields['id_globalobject'], $timestp);

					$db->query($querystring,$params);
				}

				// add contact in activity
				$_SESSION['desktopv2']['activity']['ct_added'][$currentContact->fields['id']]['id'] = $currentContact->fields['id'];
				$_SESSION['desktopv2']['activity']['ct_added'][$currentContact->fields['id']]['src'] = $tiers->fields['id'];
				$lstTiers = $desktop->constructLstTiersFromCt(array(),$_SESSION['desktopv2']['activity']['ct_added']);
				foreach($lstTiers as $tiers)
					$tiers->display(_DESKTOP_TPL_LOCAL_PATH.'/activity/added_contact_tiers.tpl.php');
			}

			if (isset($_SESSION['desktopv2']['opportunity']['tiers_selected']) && $_SESSION['desktopv2']['opportunity']['tiers_selected']>0) {
				$id_from = $_SESSION['desktopv2']['opportunity']['tiers_selected'];

				if(!empty($id_from)) {
					$querystring='insert into dims_matrix  (id,id_action,id_opportunity,id_activity,id_appointment_offer,id_tiers,id_tiers2,id_contact,id_contact2,id_doc,id_case,id_suivi,id_country,year,month,timestp_modify,id_workspace) values ';

					$id_action=0;
					$id_opportunity=0;
					$id_activity=0;
					$id_appointment_offer=0;
					$id_contact=0;
					$id_contact2=0;
					$id_tiers=0;
					$id_tiers2=0;
					$id_doc=0;
					$id_case=0;
					$id_suivi=0;
					$id_country=0;
					$id_workspace=$_SESSION['dims']['workspaceid'];
					$year=date('Y');
					$month=date('m');
					$timestp=dims_createtimestamp();

					$tiers = new tiers();
					$tiers->open($id_from);

					$id_tiers = $tiers->fields['id_globalobject'];
					$id_contact = $currentContact->fields['id_globalobject'];

					$currentContact->updateIdCountry();

					$id_country = $tiers->updateIdCountry();

					$querystring.= '(null,:idaction,:idopportunity,:idactivity,:id_appointment_offer,:idtiers1,:idtiers2,:idcontact1,:idcontact2,:iddoc,:idcase,:idsuivi,:idcountry,:year,:month,:timestamp,:idworkspace) ';
					$params = array();
					$params[':idaction'] = array('type' => PDO::PARAM_INT, 'value' => $id_action);
					$params[':idopportunity'] = array('type' => PDO::PARAM_INT, 'value' => $id_opportunity);
					$params[':idactivity'] = array('type' => PDO::PARAM_INT, 'value' => $id_activity);
					$params[':id_appointment_offer'] = array('type' => PDO::PARAM_INT, 'value' => $id_appointment_offer);
					$params[':idtiers1'] = array('type' => PDO::PARAM_INT, 'value' => $id_tiers);
					$params[':idtiers2'] = array('type' => PDO::PARAM_INT, 'value' => $id_tiers2);
					$params[':idcontact1'] = array('type' => PDO::PARAM_INT, 'value' => $id_contact);
					$params[':idcontact2'] = array('type' => PDO::PARAM_INT, 'value' => $id_contact2);
					$params[':iddoc'] = array('type' => PDO::PARAM_INT, 'value' => $id_doc);
					$params[':idcase'] = array('type' => PDO::PARAM_INT, 'value' => $id_case);
					$params[':idsuivi'] = array('type' => PDO::PARAM_INT, 'value' => $id_suivi);
					$params[':idcountry'] = array('type' => PDO::PARAM_INT, 'value' => $id_country);
					$params[':year'] = array('type' => PDO::PARAM_INT, 'value' => $year);
					$params[':month'] = array('type' => PDO::PARAM_INT, 'value' => $month);
					$params[':timestamp'] = array('type' => PDO::PARAM_INT, 'value' => $timestp);
					$params[':idworkspace'] = array('type' => PDO::PARAM_INT, 'value' => $id_workspace);

					dims_globalobject::setLightTimestamp($currentContact->fields['id_globalobject'], $timestp);
					dims_globalobject::setLightTimestamp($tiers->fields['id_globalobject'], $timestp);

					$db->query("select * from dims_user where 1");
					$db->query($querystring,$params);
				}

				// add contact in opportunity
				$_SESSION['desktopv2']['opportunity']['ct_added'][$currentContact->fields['id']]['id'] = $currentContact->fields['id'];
				$_SESSION['desktopv2']['opportunity']['ct_added'][$currentContact->fields['id']]['src'] = $tiers->fields['id'];
				$lstTiers = $desktop->constructLstTiersFromCt(array(),$_SESSION['desktopv2']['opportunity']['ct_added']);
				foreach($lstTiers as $tiers)
					$tiers->display(_DESKTOP_TPL_LOCAL_PATH.'/opportunity/added_contact_tiers.tpl.php');
			}



			die();
			break;

		/***********************************************/
		case 'link_tier_popup':
			ob_clean();
			$id_popup	= dims_load_securvalue('id_popup',dims_const::_DIMS_CHAR_INPUT,true,true);
			if(!isset($_SESSION['dims']['desktopv2']['matrice']['id_from'])) $_SESSION['dims']['desktopv2']['matrice']['id_from'] = 0;
			if(!isset($_SESSION['dims']['desktopv2']['matrice']['type_from'])) $_SESSION['dims']['desktopv2']['matrice']['type_from'] = 0;
			$id_from	= dims_load_securvalue('id_from',dims_const::_DIMS_NUM_INPUT,true,true,true,$_SESSION['dims']['desktopv2']['matrice']['id_from']);
			$type_from	= dims_load_securvalue('type_from',dims_const::_DIMS_NUM_INPUT,true,true,true,$_SESSION['dims']['desktopv2']['matrice']['type_from']);
			?>
			<div>
				<div class="actions">
					<a href="Javascript: void(0);" onclick="Javascript: dims_closeOverlayedPopup('<?php echo $id_popup; ?>');">
						<img src="<? echo _DESKTOP_TPL_PATH; ?>/gfx/common/close.png" />
					</a>
				</div>
				<h2><?php echo $_DIMS['cste']['_DIMS_LABEL_SEARCH_ENT']; ?></h2>
				<div class="zone_search">
					<form method="post" action="">
						<?
							// Sécurisation du formulaire par token
							require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
							$token = new FormToken\TokenField;
							$token->field("dims_op",	"desktopv2");
							$token->field("action",		"link_tier");
							$token->field("id_tier",	"");
							$token->field("tiers_type_link");
							$token->field("tiers_link_level");
							$token->field("date_deb_day");
							$token->field("date_deb_month");
							$token->field("date_deb_year");
							$token->field("date_fin_day");
							$token->field("date_fin_month");
							$token->field("date_fin_year");
							$token->field("fonction");
							$token->field("departement");
							$token->field("commentaire");
							$tokenHTML = $token->generate();
							echo $tokenHTML;
						?>
						<input type="hidden" name="dims_op" value="desktopv2" />
						<input type="hidden" name="action" value="link_tier" />
						<input type="hidden" name="id_tier" id="id_tier" value="" />
						<?php
						if(!empty($_SESSION['dims']['desktopv2']['matrice']['id_from']) && !empty($_SESSION['dims']['desktopv2']['matrice']['type_from']) && $_SESSION['dims']['desktopv2']['matrice']['type_from'] == dims_const::_SYSTEM_OBJECT_CONTACT) {
							?>
							<table width="100%" cellpadding="0" cellspacing="0">
								<tr>
									<td width="15%" align="right"><?php echo $_DIMS['cste']['_DIMS_LABEL_LINK_TYPE']; ?>&nbsp;</td>
									<td width="35%" align="left">
										<select id="tiers_type_link" name="tiers_type_link" style="background-color:#ebf2ea;">
											<option value="<?php echo $_DIMS['cste']['_DIMS_LABEL_EMPLOYEUR']; ?>"><?php echo $_DIMS['cste']['_DIMS_LABEL_EMPLOYEUR']; ?></option>
											<option value="<?php echo $_DIMS['cste']['_DIMS_LABEL_ASSOCIE']; ?>"><?php echo $_DIMS['cste']['_DIMS_LABEL_ASSOCIE']; ?></option>
											<option value="<?php echo stripslashes($_DIMS['cste']['_DIMS_LABEL_CONSADMIN']); ?>"><?php echo stripslashes($_DIMS['cste']['_DIMS_LABEL_CONSADMIN']); ?></option>
											<option value="<?php echo $_DIMS['cste']['_DIMS_LABEL_OTHER']; ?>"><?php echo $_DIMS['cste']['_DIMS_LABEL_OTHER']; ?></option>
										</select
									</td>
									<td></td>
								</tr>
								<tr>
									<td width="15%" align="right"><?php echo $_DIMS['cste']['_DIMS_LABEL_LEVEL_LINK']; ?>&nbsp;</td>
									<td width="35%" align="left">
										<select id="tiers_link_level" name="tiers_link_level" style="background-color:#ebf2ea;">
											<option value="1"><?php echo $_DIMS['cste']['_DIMS_LABEL_LFB_GEN']; ?></option>
											<option value="2" selected><?php echo $_DIMS['cste']['_DIMS_LABEL_LFB_MET']; ?></option>
										</select>
									</td>
									<td></td>
								</tr>
								<tr>
									<td width="15%" align="right"><?php echo $_DIMS['cste']['_BEGIN']; ?>&nbsp;</td>
									<td width="35%" align="left">
										<table cellpadding="0" cellspacing="0">
											<tr>
												<td>
													<input id="date_deb_day" name="date_deb_day" maxlenght="2" value="<?php echo date("d"); ?>" style="width:30px;background-color:#ebf2ea;"/>&nbsp;/&nbsp;
												</td>
												<td>
													<input id="date_deb_month" name="date_deb_month" maxlenght="2" value="<?php echo date("m"); ?>" style="width:30px;background-color:#ebf2ea;"/>&nbsp;/&nbsp;
												</td>
												<td>
													<input id="date_deb_year" name="date_deb_year" maxlenght="4" value="<?php echo date("Y"); ?>" style="width:30px;background-color:#ebf2ea;"/>
												</td>
											</tr>
										</table>
									</td>
									<td></td>
								</tr>
								<tr>
									<td width="15%" align="right"><?php echo $_DIMS['cste']['_END']; ?>&nbsp;</td>
									<td width="35%" align="left">
										<table cellpadding="0" cellspacing="0">
											<tr>
												<td>
													<input id="date_fin_day" name="date_fin_day" maxlenght="2" value="jj" style="width:30px;background-color:#ebf2ea;"/>&nbsp;/&nbsp;
												</td>
												<td>
													<input id="date_fin_month" name="date_fin_month" maxlenght="2" value="mm" style="width:30px;background-color:#ebf2ea;"/>&nbsp;/&nbsp;
												</td>
												<td>
													<input id="date_fin_year" name="date_fin_year" maxlenght="4" value="aaaa" style="width:30px;background-color:#ebf2ea;"/>
												</td>
											</tr>
										</table>
									</td>
									<td></td>
								</tr>
								<tr>
									<td width="15%" align="right"><?php echo $_DIMS['cste']['_DIMS_LABEL_FUNCTION']; ?>&nbsp;</td>
									<td width="35%" align="left">
										<input type="text" id="fonction" name="fonction" style="background-color:#ebf2ea;" value=""/>
									</td>
									<td></td>
								</tr>
								<tr>
									<td width="15%" align="right"><?php echo ucfirst(strtolower($_DIMS['cste']['_DIMS_LABEL_DEPARTEMENT'])); ?>&nbsp;</td>
									<td width="35%" align="left">
										<input type="text" id="departement" name="departement" style="background-color:#ebf2ea;" value=""/>
									</td>
									<td></td>
								</tr>
								<tr>
									<td width="15%" align="right"><?php echo $_DIMS['cste']['_DIMS_COMMENTS']; ?>&nbsp;</td>
									<td width="35%" align="left">
										<textarea id="commentaire" name="commentaire" style="background-color:#ebf2ea;"></textarea>
									</td>
									<td></td>
								</tr>
							</table>
							<?php
						}
						?>
					</form>
					<div class="searchform">
						<span>
							<input id="button_image_search_ct" type="image" class="button_search" src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/search_gauche.png" style="float:left;" />
							<input autocomplete="off" onkeyup="javascript:searchLinkTier(this.value);" type="text" class="editbox_search" id="editbox_search_contact" maxlength="80" value="<? echo $_SESSION['cste']['_DIMS_LABEL_SEARCH_ENT']; ?>" onfocus="Javascript:if (this.value=='<? echo $_SESSION['cste']['_DIMS_LABEL_SEARCH_ENT']; ?>')this.value=''; $(this).addClass('working');" onblur="Javascript:if (this.value=='') { $(this).removeClass('working'); this.value='<? echo $_SESSION['cste']['_DIMS_LABEL_SEARCH_ENT']; ?>'; }" />
							<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/search_droite.png" style="float:left;" />
						</span>
					</div>
				</div>
				<div id="div_list_search" style="max-height:300px;overflow-x: auto;"></div>
				<div class="otherAction">
					<a href="Javascript: void(0);" onclick="javascript:addTiers(<?php echo $id_from; ?>, <?php echo $type_from; ?>); dims_closeOverlayedPopup('<?php echo $id_popup; ?>');">
						<?php echo $_DIMS['cste']['_DIMS_LABEL_NO_TIER_CREATE_ONE']; ?>
					</a>
				</div>
			</div>
			<?php
			die();
			break;
		case 'link_search_tier':
			ob_clean();
			$label = trim(dims_load_securvalue('label',dims_const::_DIMS_CHAR_INPUT,true,true,true));
			if ($label != '' && $label != $_SESSION['cste']['_DIMS_LABEL_SEARCH_ENT']){
				$sel = "SELECT	*
					FROM	dims_mod_business_tiers
					WHERE	intitule LIKE :intitule
					AND	inactif = 0";
				$res = $db->query($sel, array(
					':intitule' => array('type' => PDO::PARAM_INT, 'value' => '%'.$label.'%'),
				));

				$lstTiers = array();
				if ($db->numrows($res) > 0){
					while ($r = $db->fetchrow($res)){
						if((!empty($_SESSION['dims']['desktopv2']['matrice']['id_from']) && $_SESSION['dims']['desktopv2']['matrice']['id_from'] != $r['id']) || (!empty($_SESSION['dims']['desktopv2']['matrice']['type_from']) && $_SESSION['dims']['desktopv2']['matrice']['type_from'] != dims_const::_SYSTEM_OBJECT_TIERS)) {
							$t = new tiers();
							$t->openWithFields($r);
							$lstTiers[$r['id']] = $t;
						}
					}
				}

				if(!empty($lstTiers)) {
					foreach($lstTiers as $t) {
						$t->display(_DESKTOP_TPL_LOCAL_PATH.'/concepts/bloc_contact/result_search_company.tpl.php');
					}
				}else{
					?>
					<div class="no_result">No result for "<?php echo $label; ?>"</div>
					<?php
				}
			}
			die();
			break;
		case 'link_tier':
			ob_clean();
			$id_tier = dims_load_securvalue('id_tier', dims_const::_DIMS_NUM_INPUT, true, true);

			if(!empty($_SESSION['dims']['desktopv2']['matrice']['id_from']) && !empty($_SESSION['dims']['desktopv2']['matrice']['type_from'])) {
				switch ($_SESSION['dims']['desktopv2']['matrice']['type_from']) {
					case dims_const::_SYSTEM_OBJECT_CONTACT:
						$pers_from_id = $_SESSION['dims']['desktopv2']['matrice']['id_from'];
						$to_ent = $id_tier;

						$type_ent = dims_load_securvalue('tiers_type_link', dims_const::_DIMS_CHAR_INPUT, true, true);
						$ent_link_lvl = dims_load_securvalue('tiers_link_level', dims_const::_DIMS_CHAR_INPUT, true, true);

						$date_deb_d = dims_load_securvalue('date_deb_day', dims_const::_DIMS_NUM_INPUT, true, true);
						$date_deb_m = dims_load_securvalue('date_deb_month', dims_const::_DIMS_NUM_INPUT, true, true);
						$date_deb_y = dims_load_securvalue('date_deb_year', dims_const::_DIMS_NUM_INPUT, true, true);
						$date_fin_d = dims_load_securvalue('date_fin_day', dims_const::_DIMS_NUM_INPUT, true, true);
						$date_fin_m = dims_load_securvalue('date_fin_month', dims_const::_DIMS_NUM_INPUT, true, true);
						$date_fin_y = dims_load_securvalue('date_fin_year', dims_const::_DIMS_NUM_INPUT, true, true);

						$date_deb = $date_deb_y.$date_deb_m.$date_deb_d."000000";
						$date_fin = $date_fin_y.$date_fin_m.$date_fin_d."000000";

						$commentaire = dims_load_securvalue('commentaire', dims_const::_DIMS_CHAR_INPUT, true, true);
						$fonction = dims_load_securvalue('fonction', dims_const::_DIMS_CHAR_INPUT, true, true);
						$departement = dims_load_securvalue('departement', dims_const::_DIMS_CHAR_INPUT, true, true);

						require_once DIMS_APP_PATH.'modules/system/class_tiers_contact.php';
						$ct_tiers = new tiersct();
						$ct_tiers->fields['id_tiers']=$to_ent;
						$ct_tiers->fields['id_contact']=$pers_from_id;
						$ct_tiers->fields['type_lien']=$type_ent;
						$ct_tiers->fields['function']=$fonction;
						$ct_tiers->fields['departement']=$departement;
						$ct_tiers->fields['id_workspace']=$_SESSION['dims']['workspaceid'];
						$ct_tiers->fields['id_user']=$_SESSION['dims']['userid'];
						$ct_tiers->fields['date_create']=date("YmdHis");
						$ct_tiers->fields['link_level']=$ent_link_lvl;
						$ct_tiers->fields['id_ct_user_create']=$_SESSION['dims']['user']['id_contact'];
						$ct_tiers->fields['date_deb']=$date_deb;
						$ct_tiers->fields['date_fin']=$date_fin;
						$ct_tiers->fields['commentaire']=$commentaire;
						$ct_tiers->save();
						break;
					case dims_const::_SYSTEM_OBJECT_TIERS:
						$ent_from_id = $_SESSION['dims']['desktopv2']['matrice']['id_from'];

						require_once DIMS_APP_PATH.'modules/system/class_ct_link.php';
						$ctlink = new ctlink();
						$ctlink->fields['id_contact1'] = $ent_from_id;
						$ctlink->fields['id_contact2'] = $id_tier;
						$ctlink->fields['id_object'] = dims_const::_SYSTEM_OBJECT_TIERS;
						$ctlink->fields['type_link'] = '';
						$ctlink->fields['link_level'] = 1;
						$ctlink->fields['time_create'] = dims_createtimestamp();
						$ctlink->fields['id_ct_user_create'] = $_SESSION['dims']['userid'];
						$ctlink->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
						$ctlink->fields['id_user'] = $_SESSION['dims']['userid'];
						$ctlink->save();
						break;
				}
			}

			dims_redirect('admin.php?dims_op=desktopv2&action=tiers_matrice&id_tiers='.$id_tier);
			break;

		case 'step1AddTiers':
			$tiers = new tiers();
			require_once(DIMS_APP_PATH . "/modules/system/crm_public_ent_save.php");

			break;
		case 'initAddTiers':
			$tiers = new tiers();
			if(!isset($_SESSION['dims']['desktopv2']['matrice']['id_from'])) $_SESSION['dims']['desktopv2']['matrice']['id_from'] = 0;
			if(!isset($_SESSION['dims']['desktopv2']['matrice']['type_from'])) $_SESSION['dims']['desktopv2']['matrice']['type_from'] = 0;
			$id_from = dims_load_securvalue('id_from',dims_const::_DIMS_NUM_INPUT,true,true,true,$_SESSION['dims']['desktopv2']['matrice']['id_from']);
			$type_from = dims_load_securvalue('type_from',dims_const::_DIMS_NUM_INPUT,true,true,true,$_SESSION['dims']['desktopv2']['matrice']['type_from']);
			$id_popup = dims_load_securvalue('id_popup', dims_const::_DIMS_NUM_INPUT, true, true, false);
			$actionform="/admin.php?dims_op=desktopv2&action=step1AddTiers&tab=1";
			$saveform="/admin.php?dims_op=desktopv2&action=tiers_matrice&id_tiers=<ID_TIERS>";
			unset($_SESSION['business']['tiers_id']);
			echo $tiers->buildNewEntStep1($actionform,$saveform,$id_popup);
			die();
			break;

		case 'step1AddContact':
			$contact = new contact();
			require_once(DIMS_APP_PATH . "/modules/system/crm_public_contact_save.php");
			break;
		case 'initAddContact':
			if(!isset($_SESSION['dims']['desktopv2']['matrice']['id_from'])) $_SESSION['dims']['desktopv2']['matrice']['id_from'] = 0;
			if(!isset($_SESSION['dims']['desktopv2']['matrice']['type_from'])) $_SESSION['dims']['desktopv2']['matrice']['type_from'] = 0;
			$id_from	= dims_load_securvalue('id_from',dims_const::_DIMS_NUM_INPUT,true,true,true,$_SESSION['dims']['desktopv2']['matrice']['id_from']);
			$type_from	= dims_load_securvalue('type_from',dims_const::_DIMS_NUM_INPUT,true,true,true,$_SESSION['dims']['desktopv2']['matrice']['type_from']);
			$id_popup = dims_load_securvalue('id_popup', dims_const::_DIMS_NUM_INPUT, true, true, false);
			$contact = new contact();
			$actionform="/admin.php?dims_op=desktopv2&action=step1AddContact";
			$saveform="/admin.php?dims_op=desktopv2&action=contact_matrice&id_contact=<ID_CONTACT>";
			unset($_SESSION['business']['contact_id']);
			echo $contact->buildNewContactStep1($actionform,$saveform,$id_popup);
			die();
			break;


		case 'tiers_matrice':
			$id_tiers = dims_load_securvalue('id_tiers', dims_const::_DIMS_NUM_INPUT, true, true, false);

			$currentTiers = new tiers();
			$currentTiers->open($id_tiers);

			$id_from = $_SESSION['dims']['desktopv2']['matrice']['id_from'];
			$type_from = $_SESSION['dims']['desktopv2']['matrice']['type_from'];

			if(!empty($id_from) && !empty($type_from)) {
				$querystring='insert into dims_matrix values ';

				$id_action=0;
				$id_opportunity=0;
				$id_activity=0;
				$id_contact=0;
				$id_contact2=0;
				$id_tiers=0;
				$id_tiers2=0;
				$id_doc=0;
				$id_case=0;
				$id_suivi=0;
				$id_country=0;
				$id_workspace=$_SESSION['dims']['workspaceid'];
				$year=date('Y');
				$month=date('m');
				$timestp=dims_createtimestamp();


				switch ($type_from) {
					case dims_const::_SYSTEM_OBJECT_CONTACT:
						$contact = new contact();
						$contact->open($id_from);

						$id_tiers = $currentTiers->fields['id_globalobject'];
						$id_contact = $contact->fields['id_globalobject'];

						$contact->updateIdCountry();

						$id_country = $currentTiers->updateIdCountry();

						dims_globalobject::setLightTimestamp($currentTiers->fields['id_globalobject'], $timestp);
						dims_globalobject::setLightTimestamp($contact->fields['id_globalobject'], $timestp);
						break;
					case dims_const::_SYSTEM_OBJECT_TIERS:
						$tiers = new tiers();
						$tiers->open($id_from);

						$id_country = $currentTiers->updateIdCountry();

						$id_tiers = $currentTiers->fields['id_globalobject'];
						$id_tiers2 = $tiers->fields['id_globalobject'];

						$querystring.= '(null,:2idaction,:2idopportunity,:2idaction,:2idtiers1,:2idtiers2,:2idcontact1,:2idcontact2,:2iddoc,:2idcase,:2idsuivi,:2idcountry,:2year,:2month,:2timestamp,:2idworkspace) ';
						$params = array();
						$params[':2idaction'] = array('type' => PDO::PARAM_INT, 'value' => $id_action);
						$params[':2idopportunity'] = array('type' => PDO::PARAM_INT, 'value' => $id_opportunity);
						$params[':2idactivity'] = array('type' => PDO::PARAM_INT, 'value' => $id_activity);
						$params[':2idtiers1'] = array('type' => PDO::PARAM_INT, 'value' => $id_tiers);
						$params[':2idtiers2'] = array('type' => PDO::PARAM_INT, 'value' => $id_tiers2);
						$params[':2idcontact1'] = array('type' => PDO::PARAM_INT, 'value' => $id_contact);
						$params[':2idcontact2'] = array('type' => PDO::PARAM_INT, 'value' => $id_contact2);
						$params[':2iddoc'] = array('type' => PDO::PARAM_INT, 'value' => $id_doc);
						$params[':2idcase'] = array('type' => PDO::PARAM_INT, 'value' => $id_case);
						$params[':2idsuivi'] = array('type' => PDO::PARAM_INT, 'value' => $id_suivi);
						$params[':2idcountry'] = array('type' => PDO::PARAM_INT, 'value' => $id_country);
						$params[':2year'] = array('type' => PDO::PARAM_INT, 'value' => $year);
						$params[':2month'] = array('type' => PDO::PARAM_INT, 'value' => $month);
						$params[':2timestamp'] = array('type' => PDO::PARAM_INT, 'value' => $timstp);
						$params[':2idworkspace'] = array('type' => PDO::PARAM_INT, 'value' => $id_workspace);

						$querystring.= ', ';

						$id_country = $tiers->updateIdCountry();

						$id_tiers = $tiers->fields['id_globalobject'];
						$id_tiers2 = $currentTiers->fields['id_globalobject'];

						dims_globalobject::setLightTimestamp($currentTiers->fields['id_globalobject'], $timestp);
						dims_globalobject::setLightTimestamp($tiers->fields['id_globalobject'], $timestp);
						break;
					case dims_const::_SYSTEM_OBJECT_EVENT:
						$event = new action();
						$event->open($id_from);

						$id_tiers = $currentTiers->fields['id_globalobject'];

						$id_action = $event->fields['id_globalobject'];
						$id_country = $event->updateIdCountry();
						break;
					case dims_const::_SYSTEM_OBJECT_ACTIVITY:
						$event = new action();
						$event->open($id_from);

						$id_tiers = $currentTiers->fields['id_globalobject'];

						$id_activity = $event->fields['id_globalobject'];
						$id_country = $event->updateIdCountry();
						break;
					case dims_const::_SYSTEM_OBJECT_OPPORTUNITY:
						$event = new action();
						$event->open($id_from);

						$id_tiers = $currentTiers->fields['id_globalobject'];

						$id_opportunity = $event->fields['id_globalobject'];
						$id_country = $event->updateIdCountry();
						break;
				}

				$querystring.= '(null,:idaction,:idopportunity,:idaction,:idtiers1,:idtiers2,:idcontact1,:idcontact2,:iddoc,:idcase,:idsuivi,:idcountry,:year,:month,:timestamp,:idworkspace) ';
				$params = array();
				$params[':idaction'] = array('type' => PDO::PARAM_INT, 'value' => $id_action);
				$params[':idopportunity'] = array('type' => PDO::PARAM_INT, 'value' => $id_opportunity);
				$params[':idactivity'] = array('type' => PDO::PARAM_INT, 'value' => $id_activity);
				$params[':idtiers1'] = array('type' => PDO::PARAM_INT, 'value' => $id_tiers);
				$params[':idtiers2'] = array('type' => PDO::PARAM_INT, 'value' => $id_tiers2);
				$params[':idcontact1'] = array('type' => PDO::PARAM_INT, 'value' => $id_contact);
				$params[':idcontact2'] = array('type' => PDO::PARAM_INT, 'value' => $id_contact2);
				$params[':iddoc'] = array('type' => PDO::PARAM_INT, 'value' => $id_doc);
				$params[':idcase'] = array('type' => PDO::PARAM_INT, 'value' => $id_case);
				$params[':idsuivi'] = array('type' => PDO::PARAM_INT, 'value' => $id_suivi);
				$params[':idcountry'] = array('type' => PDO::PARAM_INT, 'value' => $id_country);
				$params[':year'] = array('type' => PDO::PARAM_INT, 'value' => $year);
				$params[':month'] = array('type' => PDO::PARAM_INT, 'value' => $month);
				$params[':timestamp'] = array('type' => PDO::PARAM_INT, 'value' => $timstp);
				$params[':idworkspace'] = array('type' => PDO::PARAM_INT, 'value' => $id_workspace);

				$db->query($querystring, $params);
			}

			unset($_SESSION['dims']['desktopv2']['matrice']['id_from']);
			unset($_SESSION['dims']['desktopv2']['matrice']['type_from']);

			if (isset($_SESSION['dims']['desktopv2']['submenu']) && $_SESSION['dims']['desktopv2']['submenu'] > 0) {
				dims_redirect('/admin.php?submenu='.$_SESSION['dims']['desktopv2']['submenu']);
			}
			else {
				dims_redirect('/admin.php');
			}
			break;

		case 'contact_matrice':
			$id_contact = dims_load_securvalue('id_contact', dims_const::_DIMS_NUM_INPUT, true, true, false);

			$currentContact = new contact();
			$currentContact->open($id_contact);

			$id_from = $_SESSION['dims']['desktopv2']['matrice']['id_from'];
			$type_from = $_SESSION['dims']['desktopv2']['matrice']['type_from'];

			if(!empty($id_from) && !empty($type_from)) {
				$querystring='insert into dims_matrix values ';

				$id_action=0;
				$id_opportunity=0;
				$id_activity=0;
				$id_contact=0;
				$id_contact2=0;
				$id_tiers=0;
				$id_tiers2=0;
				$id_doc=0;
				$id_case=0;
				$id_suivi=0;
				$id_country=0;
				$id_workspace=$_SESSION['dims']['workspaceid'];
				$year=date('Y');
				$month=date('m');
				$timestp=dims_createtimestamp();

				switch ($type_from) {
					case dims_const::_SYSTEM_OBJECT_CONTACT:
						$contact = new contact();
						$contact->open($id_from);
						$id_country = $currentContact->updateIdCountry();
						$id_contact = $currentContact->fields['id_globalobject'];
						$id_contact2 = $contact->fields['id_globalobject'];
						$id_country = $contact->updateIdCountry();
						$id_contact = $contact->fields['id_globalobject'];
						$id_contact2 = $currentContact->fields['id_globalobject'];

						dims_globalobject::setLightTimestamp($currentContact->fields['id_globalobject'], $timestp);
						dims_globalobject::setLightTimestamp($contact->fields['id_globalobject'], $timestp);
						break;
					case dims_const::_SYSTEM_OBJECT_TIERS:
						$tiers = new tiers();
						$tiers->open($id_from);
						$id_tiers = $tiers->fields['id_globalobject'];
						$id_contact = $currentContact->fields['id_globalobject'];
						$currentContact->updateIdCountry();
						$id_country = $tiers->updateIdCountry();

						dims_globalobject::setLightTimestamp($currentContact->fields['id_globalobject'], $timestp);
						dims_globalobject::setLightTimestamp($tiers->fields['id_globalobject'], $timestp);
						break;
					case dims_const::_SYSTEM_OBJECT_EVENT:
						$event = new action();
						$event->open($id_from);

						$id_contact = $currentContact->fields['id_globalobject'];

						$id_action = $event->fields['id_globalobject'];
						$id_country = $event->updateIdCountry();
						break;
					case dims_const::_SYSTEM_OBJECT_ACTIVITY:
						$event = new action();
						$event->open($id_from);

						$id_contact = $currentContact->fields['id_globalobject'];

						$id_activity = $event->fields['id_globalobject'];
						$id_country = $event->updateIdCountry();
						break;
					case dims_const::_SYSTEM_OBJECT_OPPORTUNITY:
						$event = new action();
						$event->open($id_from);

						$id_contact = $currentContact->fields['id_globalobject'];

						$id_opportunity = $event->fields['id_globalobject'];
						$id_country = $event->updateIdCountry();
						break;
				}

				$querystring.= '(null,:idaction,:idopportunity,:idaction,:idtiers1,:idtiers2,:idcontact1,:idcontact2,:iddoc,:idcase,:idsuivi,:idcountry,:year,:month,:timestamp,:idworkspace) ';
				$params = array();
				$params[':idaction'] = array('type' => PDO::PARAM_INT, 'value' => $id_action);
				$params[':idopportunity'] = array('type' => PDO::PARAM_INT, 'value' => $id_opportunity);
				$params[':idactivity'] = array('type' => PDO::PARAM_INT, 'value' => $id_activity);
				$params[':idtiers1'] = array('type' => PDO::PARAM_INT, 'value' => $id_tiers);
				$params[':idtiers2'] = array('type' => PDO::PARAM_INT, 'value' => $id_tiers2);
				$params[':idcontact1'] = array('type' => PDO::PARAM_INT, 'value' => $id_contact);
				$params[':idcontact2'] = array('type' => PDO::PARAM_INT, 'value' => $id_contact2);
				$params[':iddoc'] = array('type' => PDO::PARAM_INT, 'value' => $id_doc);
				$params[':idcase'] = array('type' => PDO::PARAM_INT, 'value' => $id_case);
				$params[':idsuivi'] = array('type' => PDO::PARAM_INT, 'value' => $id_suivi);
				$params[':idcountry'] = array('type' => PDO::PARAM_INT, 'value' => $id_country);
				$params[':year'] = array('type' => PDO::PARAM_INT, 'value' => $year);
				$params[':month'] = array('type' => PDO::PARAM_INT, 'value' => $month);
				$params[':timestamp'] = array('type' => PDO::PARAM_INT, 'value' => $timstp);
				$params[':idworkspace'] = array('type' => PDO::PARAM_INT, 'value' => $id_workspace);

				$db->query($querystring, $params);
			}

			unset($_SESSION['dims']['desktopv2']['matrice']['id_from']);
			unset($_SESSION['dims']['desktopv2']['matrice']['type_from']);

			if (isset($_SESSION['dims']['desktopv2']['submenu']) && $_SESSION['dims']['desktopv2']['submenu'] > 0) {
				dims_redirect('/admin.php?submenu='.$_SESSION['dims']['desktopv2']['submenu']);
			}
			else {
				dims_redirect('/admin.php');
			}
			break;

		case 'add_comment_concepts':
			ob_clean();
			$id_parent = dims_load_securvalue('id_parent',dims_const::_DIMS_NUM_INPUT,true,true,true);
			?>
			<div>
				<div class="actions">
					<a href="Javascript: void(0);" onclick="Javascript: dims_hidepopup();">
						<img src="<? echo _DESKTOP_TPL_PATH; ?>/gfx/common/close.png" />
					</a>
				</div>
				<h2><? echo $_SESSION['cste']['_DIMS_COMMENTS']; ?></h2>
				<form method="POST" action="/admin.php?action=save_comment_concepts">
					<?
						// Sécurisation du formulaire par token
						require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
						$token = new FormToken\TokenField;
						$token->field("comm_id_parent",	$id_parent);
						$token->field("comm_content");
						$tokenHTML = $token->generate();
						echo $tokenHTML;
					?>
					<input type="hidden" name="comm_id_parent" value="<? echo $id_parent; ?>" />
					<textarea style="width:324px;resize:none;" id="comm_content" name="comm_content"></textarea>
					<input style="float:right;margin:10px;" type="submit" value="<? echo $_SESSION['cste']['_DIMS_SAVE']; ?>" />
				</form>
			</div>
			<script type="text/javascript">
				$("#comm_content").focus();
			</script>
			<?PHP
			die();
			break;
		case 'add_comment_address_books':
			ob_clean();
			$id_parent = dims_load_securvalue('id_parent',dims_const::_DIMS_NUM_INPUT,true,true,true);
			?>
			<div>
				<div class="actions">
					<a href="Javascript: void(0);" onclick="Javascript: dims_hidepopup();">
						<img src="<? echo _DESKTOP_TPL_PATH; ?>/gfx/common/close.png" />
					</a>
				</div>
				<h2><? echo $_SESSION['cste']['_DIMS_COMMENTS']; ?></h2>
				<form method="POST" action="/admin.php?action=save_comment_address_book">
					<?
						// Sécurisation du formulaire par token
						require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
						$token = new FormToken\TokenField;
						$token->field("comm_id_parent",	$id_parent);
						$token->field("comm_content");
						$tokenHTML = $token->generate();
						echo $tokenHTML;
					?>
					<input type="hidden" name="comm_id_parent" value="<? echo $id_parent; ?>" />
					<textarea style="width:324px;resize:none;" name="comm_content"></textarea>
					<input style="float:right;margin:10px;" type="submit" value="<? echo $_SESSION['cste']['_DIMS_SAVE']; ?>" />
				</form>
			</div>
			<?
			die();
			break;
		case 'add_document_concepts':
			ob_clean();
			$id_parent = dims_load_securvalue('id_parent',dims_const::_DIMS_NUM_INPUT,true,true,true);
			?>
			<div>
				<div class="actions">
					<a href="Javascript: void(0);" onclick="Javascript: dims_hidepopup();">
						<img src="<? echo _DESKTOP_TPL_PATH; ?>/gfx/common/close.png" />
					</a>
				</div>
				<h2><? echo $_SESSION['cste']['DOCUMENT']; ?></h2>
				<form method="POST" action="/admin.php?action=save_document_concepts" enctype="multipart/form-data">
					<?
						// Sécurisation du formulaire par token
						require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
						$token = new FormToken\TokenField;
						$token->field("comm_id_parent", $id_parent);
						$token->field("comm_content");
						$tokenHTML = $token->generate();
						echo $tokenHTML;
					?>
					<input type="hidden" name="comm_id_parent" value="<? echo $id_parent; ?>" />
					<input type="file" name="concept_document" />
					<input style="float:right;margin:10px;" type="submit" value="<? echo $_SESSION['cste']['_DIMS_SAVE']; ?>" />
				</form>
			</div>
			<?
			die();
			break;
		case 'activity_get_all_events':
			ob_clean();
			$typeaction = '';
			$a_events = array();
			$type_event = dims_load_securvalue('type_event', dims_const::_DIMS_CHAR_INPUT, true, false);

			if ($type_event != '') {
				switch ($type_event) {
					case 'trade_fairs':
						$typeaction = '= "_DIMS_PLANNING_FAIR"';
						break;
					case 'trade_missions':
						$typeaction = ' <> "_DIMS_MISSIONS" ';
						break;
				}
				if ($typeaction != '') {
					// chargement des events
					$rs = $db->query('
						SELECT		id, libelle, datejour
						FROM		dims_mod_business_action
						WHERE		type = :type
						AND			typeaction :typeaction
						AND			id_parent = 0
						AND		libelle != ""
						ORDER BY	libelle', array(
							':type'			=> dims_const::_PLANNING_ACTION_EVT,
							':typeaction'	=> $typeaction
						));
					while ($row = $db->fetchrow($rs)) {
						$deb = explode('-',$row['datejour']);
						$row['datejour'] = '';
						if($deb[2] != 0){
							$row['datejour'] .= $deb[2].'/';
						}
						if($deb[1] != 0){
							$row['datejour'] .= $deb[1].'/';
						}
						if($deb[0] != 0){
							$row['datejour'] .= $deb[0];
						}

						$a_events[] = $row;
					}
				}
			}
			die(json_encode($a_events));
			break;
		case 'activity_search_event':
			ob_clean();
			$typeaction = '';
			$a_events = array();
			$a_events[0]=0;
			$label = trim(dims_load_securvalue('label',dims_const::_DIMS_CHAR_INPUT,true,true,true));
			$type_event = dims_load_securvalue('type_event', dims_const::_DIMS_CHAR_INPUT, true, false);

			switch ($type_event) {
				case 'trade_fairs':
					$typeaction = '_DIMS_PLANNING_FAIR';
					break;
				case 'trade_missions':
					$typeaction = '_DIMS_MISSIONS';
					break;
			}

			$sql='
				SELECT		id
				FROM		dims_mod_business_action
				WHERE		type = :type ';
			if ($typeaction != '') {
				if (_DIMS_MISSIONS=='_DIMS_MISSIONS') {
					$sql .= ' AND typeaction <> "_DIMS_PLANNING_FAIR"';
				} else {
					$sql .= ' AND typeaction = :typeaction';
					$params[':typeaction'] = array('type' => PDO::PARAM_STR, 'value' => $typeaction);
				}
			}
			$sql .= '
				AND			id_parent = 0
				AND		UCASE(libelle) like :libelle
				ORDER BY	libelle';
			$params[':libelle'] = array('type' => PDO::PARAM_STR, 'value' => '%'.strtoupper($label).'%');
			$params[':type'] = array('type' => PDO::PARAM_STR, 'value' => dims_const::_PLANNING_ACTION_EVT);
			$rs = $db->query($sql, $params);
			while ($row = $db->fetchrow($rs)) {
					$a_events[$row['id']] = $row['id'];
			}
			$lstEvents = $desktop->getEvents($a_events);
			if (count($lstEvents) > 0)
				foreach($lstEvents as $event)
					$event->display(_DESKTOP_TPL_LOCAL_PATH.'/activity/search_event.tpl.php');
			else
				echo "<div class=\"no_result\">No result for \"$label\"</div>";
			die();
			break;
		case 'activity_select_event':
			ob_clean();
			$id_event = dims_load_securvalue('id_event', dims_const::_DIMS_NUM_INPUT, true, false);
			$rs = $db->query('SELECT id, datejour, id_country FROM dims_mod_business_action WHERE id = :idevent LIMIT 0,1', array(
				':idevent' => array('type' => PDO::PARAM_INT, 'value' => $id_event),
			));
			$row = $db->fetchrow($rs);
			die(json_encode(array( 'id' => $row['id'], 'date' => explode('-', $row['datejour']), 'id_country' => $row['id_country'] )));
			break;
		case 'add_group_in_activity' :
			ob_clean();
			$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
			if ($id != '' && $id > 0){
				require_once DIMS_APP_PATH."modules/system/class_ct_group_link.php";
				$db = dims::getInstance()->getDb();
				$sel = "SELECT 		DISTINCT ct.*
						FROM 		".contact::TABLE_NAME." ct
						INNER JOIN 	".ct_group_link::TABLE_NAME." lk
						ON 			lk.id_globalobject = ct.id_globalobject
						WHERE 		lk.type_contact = :tct
						AND 		lk.id_group_ct = :gr
						AND 		ct.id_workspace = :idw
						ORDER BY 	ct.firstname, ct.lastname";
				$params = array(
					':tct'=>array('value'=>contact::MY_GLOBALOBJECT_CODE, 'type'=>PDO::PARAM_INT),
					':gr'=>array('value'=>$id, 'type'=>PDO::PARAM_INT),
					':idw'=>array('value'=>$_SESSION['dims']['workspaceid'], 'type'=>PDO::PARAM_INT),
				);
				$res = $db->query($sel,$params);
				while($r = $db->fetchrow($res)){
					$ct = new contact();
					$ct->openFromResultSet($r);
					$company = current($ct->getCompaniesLinkedByType('_DIMS_LABEL_EMPLOYEUR'));
					if(isset($company) && !empty($company))
						$idtiers = $company['id'];
					else
						$idtiers = 0;
					$_SESSION['desktopv2']['activity']['ct_added'][$ct->get('id')]['id'] = $ct->get('id');
					$_SESSION['desktopv2']['activity']['ct_added'][$ct->get('id')]['src'] = $idtiers;
				}
			}
			$lstTiers = $desktop->constructLstTiersFromCt(array(),$_SESSION['desktopv2']['activity']['ct_added']);
			foreach($lstTiers as $tiers)
				$tiers->display(_DESKTOP_TPL_LOCAL_PATH.'/activity/added_contact_tiers.tpl.php');
			die();
			break;
		case 'add_contact_in_activity' :
			ob_clean();
			$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
			$idtiers = dims_load_securvalue('idtiers',dims_const::_DIMS_NUM_INPUT,true,true,true);
			if ($id != '' && $id > 0){
				$_SESSION['desktopv2']['activity']['ct_added'][$id]['id'] = $id;
				$_SESSION['desktopv2']['activity']['ct_added'][$id]['src'] = $idtiers;
			}
			$lstTiers = $desktop->constructLstTiersFromCt(array(),$_SESSION['desktopv2']['activity']['ct_added']);
			foreach($lstTiers as $tiers)
				$tiers->display(_DESKTOP_TPL_LOCAL_PATH.'/activity/added_contact_tiers.tpl.php');
			die();
			break;
		case 'del_contact_in_activity' :
			ob_clean();
			$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
			if ($id != '' && $id > 0)
				unset($_SESSION['desktopv2']['activity']['ct_added'][$id]);
			$lstTiers = $desktop->constructLstTiersFromCt(array(),$_SESSION['desktopv2']['activity']['ct_added']);
			foreach($lstTiers as $tiers)
				$tiers->display(_DESKTOP_TPL_LOCAL_PATH.'/activity/added_contact_tiers.tpl.php');
			die();
			break;
		case 'activity_search_companycontact':
			ob_clean();

			$label = trim(dims_load_securvalue('label',dims_const::_DIMS_CHAR_INPUT,true,true,true));
			$type = trim(dims_load_securvalue('type',dims_const::_DIMS_CHAR_INPUT,true,true,true));

			echo '<p class="activity_links"">';
			if ($type == 'cts') {
				echo '<a class="activity_new_contact" href="javascript:void(0);" onclick="javascript:show_new_contact_form(true);" title="'.$_SESSION['cste']['CREATE_A_CONTACT_WITHOUT_COMPANY'].'"><span>
					'.$_SESSION['cste']['CREATE_A_CONTACT_WITHOUT_COMPANY'].'</span><img src="'._DESKTOP_TPL_PATH.'/gfx/common/add.png" alt="'.$_SESSION['cste']['CREATE_A_CONTACT_WITHOUT_COMPANY'].'" /></a>';
			}
			echo '<a class="activity_new_contact" href="javascript:void(0);" onclick="javascript:show_new_company_form();" title="'.$_SESSION['cste']['CREATE_THE_COMPANY'].'"><span>
				'.$_SESSION['cste']['CREATE_THE_COMPANY'].'</span><img src="'._DESKTOP_TPL_PATH.'/gfx/common/add.png" alt="'.$_SESSION['cste']['CREATE_THE_COMPANY'].'" /></a>
				</p>';

			if ($label != ''){
				// initialisation du module de recherche sur
				require_once(DIMS_APP_PATH . "/modules/system/class_search.php");
				$dimsearch = new search($dims);

				// ajout des objects sur lequel la recherche va se baser
				if($type=='cts') $dimsearch->addSearchObject(dims_const::_DIMS_MODULE_SYSTEM, dims_const::_SYSTEM_OBJECT_CONTACT,$_DIMS['cste']['_DIMS_LABEL_CONTACTS']);
				else $dimsearch->addSearchObject(dims_const::_DIMS_MODULE_SYSTEM, dims_const::_SYSTEM_OBJECT_TIERS,$_DIMS['cste']['_DIMS_LABEL_GROUP_LIST']);
				// reinitialise la recherche sur ce module courant, n'efface pas le cache result
				$dimsearch->initSearchObject();

				$kword					= dims_load_securvalue('kword', dims_const::_DIMS_CHAR_INPUT, true, true);
				$idmodule				= dims_load_securvalue('idmodule', dims_const::_DIMS_CHAR_INPUT, true, true);
				$idobj					= dims_load_securvalue('idobj', dims_const::_DIMS_CHAR_INPUT, true, true);
				$idmetafield			= dims_load_securvalue('idmetafield', dims_const::_DIMS_CHAR_INPUT, true, true);
				$sens					= dims_load_securvalue('sens', dims_const::_DIMS_CHAR_INPUT, true, true);

				$dimsearch->executeSearch($label, $kword,$idmodule, $idobj, $idmetafield, $sens);

				$ids = array();
				foreach($dimsearch->tabresultat as $idmodule => $tab_objects){
					foreach($tab_objects as $idobjet => $tab_ids){
						foreach($tab_ids as $kid => $id){
							$ids[$id] = $id;
						}
					}
				}
				if(count($ids)){
					if($type=='cts'){
						$params = array();
						$sel = "SELECT	*
							FROM	dims_mod_business_contact
							WHERE	id IN (".$db->getParamsFromArray($ids, 'idcontact', $params).")
							AND		inactif = 0
							ORDER BY firstname, lastname";
						$res = $db->query($sel, $params);
						$total = $db->numrows($res);
						if ($total > 0){
							?>
							<div class="op_search_results">
							<table style="width: 100%;border-bottom: 1px solid #DDDDDD;">
								<tbody>
							<?php
							$i = 0;
							while ($r = $db->fetchrow($res)){
								$c = new contact();
								$c->openWithFields($r);
								if($i==$total-1)$c->setLightAttribute('last', true);
								$c->display(_DESKTOP_TPL_LOCAL_PATH.'/new_company/result_search_contact.tpl.php');
								$i++;
							}
							?>
								</tbody>
							</table>
							</div>
							<script type="text/javascript">
								$(document).ready(function(){
									$("div#new_company_result table:last").css("border-bottom","none");
								});
							</script>
							<?
						}
					}
					else{
						$params = array();
						 $sel = "SELECT	*
							FROM	dims_mod_business_tiers
							WHERE	id IN (".$db->getParamsFromArray($ids, 'idtiers', $params).")
							AND		inactif = 0
							ORDER BY intitule ASC";
						$res = $db->query($sel, $params);
						$total = $db->numrows($res);
						if ($total > 0){
							?>
							<div class="op_search_results">
							<table style="width: 100%;border-bottom: 1px solid #DDDDDD;">
								<tbody>
							<?php
							$i = 0;
							while ($r = $db->fetchrow($res)){
								$t = new tiers();
								$t->openWithFields($r);
								if($i==$total-1)$t->setLightAttribute('last', true);
								$t->display(_DESKTOP_TPL_LOCAL_PATH.'/new_company/result_search_company.tpl.php');
								$i++;
							}
							?>
									</tbody>
							</table>
							</div>
							<script type="text/javascript">
								$(document).ready(function(){
									$("div#new_company_result table:last").css("border-bottom","none");
								});
							</script>
							<?
						}
					}
				}
				else{
					?>
					<span class="no_result"><?php echo $_SESSION['cste']['NO_RESULT']; ?></span>
					<?php
				}

			}
			die();
			break;
		case 'activity_create_company':
			ob_clean();
			$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
			$tiers = new tiers();
			$tiers->init_description();
			if ($id != '' && $id > 0)
				$tiers->open($id);
			$tiers->display(_DESKTOP_TPL_LOCAL_PATH.'/new_company/new_company_form.tpl.php');
			die();
			break;
		case 'activity_save_company':
			ob_clean();
			$id_tiers = base64_decode(trim(dims_load_securvalue('id_tiers',dims_const::_DIMS_CHAR_INPUT,true,true,true)));
			$name = base64_decode(trim(dims_load_securvalue('company_intitule',dims_const::_DIMS_CHAR_INPUT,true,true,true)));
			$id_country = dims_load_securvalue('country',dims_const::_DIMS_NUM_INPUT,true,true,true);
			$city = dims_load_securvalue('city',dims_const::_DIMS_CHAR_INPUT,true,true,true);
			$address = base64_decode(trim(dims_load_securvalue('company_adresse',dims_const::_DIMS_CHAR_INPUT,true,true,true)));
			$cp = base64_decode(trim(dims_load_securvalue('company_codepostal',dims_const::_DIMS_CHAR_INPUT,true,true,true)));
			$tel = base64_decode(trim(dims_load_securvalue('company_telephone',dims_const::_DIMS_CHAR_INPUT,true,true,true)));
			$fax = base64_decode(trim(dims_load_securvalue('company_telecopie',dims_const::_DIMS_CHAR_INPUT,true,true,true)));
			$email = base64_decode(trim(dims_load_securvalue('company_mel',dims_const::_DIMS_CHAR_INPUT,true,true,true)));
			$site_web = base64_decode(trim(dims_load_securvalue('company_site_web',dims_const::_DIMS_CHAR_INPUT,true,true,true)));
			$tags_company = trim(dims_load_securvalue('tags_company',dims_const::_DIMS_CHAR_INPUT,true,true,true));
			if ($tags_company != 'null') $tags_company = base64_decode($tags_company);
			$photo = base64_decode(dims_load_securvalue('photo_path_company',dims_const::_DIMS_CHAR_INPUT,true,true,true));

			if ($name != ''){
				$tiers = new tiers();
			if ($id_tiers>0) {
				$tiers->open($id_tiers);
			}
			else {
				$tiers->init_description();
				$tiers->setugm();
			}

			$tiers->fields['intitule'] = $name;
			$tiers->fields['id_country'] = $id_country;

			if ($id_country > 0) {
				require_once DIMS_APP_PATH.'modules/system/class_country.php';
				$country = new country();
				$country->open($id_country);
				$tiers->fields['pays'] = $country->fields['printable_name'];
			}

			if ($city > 0) {
				require_once DIMS_APP_PATH.'modules/system/class_city.php';
				$cit = new city();
				$cit->open($city);
				$tiers->fields['ville'] = $cit->fields['label'];
			}

			$tiers->fields['adresse'] = $address;
			$tiers->fields['codepostal'] = $cp;
			$tiers->fields['telephone'] = $tel;
			$tiers->fields['telecopie'] = $fax;
			$tiers->fields['mel'] = $email;
			$tiers->fields['site_web'] = $site_web;


			$tiers->fields['date_creation'] = dims_createtimestamp();
			$tiers->fields['timestp_modify'] = $tiers->fields['date_creation'];
			$tiers->fields['id_user_create'] = $tiers->fields['id_user'];

			if($tiers->save()){
				if ($photo != '' && file_exists(realpath('.').$photo)){
					$time = time();
					$tab_ratio = array(0 => array(100,50), 1 => array(300,150));

					$ext = explode('.', $photo);
					$ext = strtolower($ext[count($ext)-1]);
					$path = DIMS_WEB_PATH.'data/photo_ent/ent_'.$tiers->fields['id'];
					if (!file_exists($path)) mkdir($path, 0770,true);

					$path .= '/tmp_'.$tiers->fields['id'].'.'.$ext;
					//on cree une image temporaire pour la redimensionner
					rename(realpath('.').$photo,$path);
					foreach($tab_ratio as $key => $ratio)
						dims_resizeimage($path, 0, $ratio[0], $ratio[1],'',0,dirname($path)."/photo".$ratio[0]."_".$time.".png");
					unlink($path);
					$tiers->fields['photo'] = "_".$time;
					$tiers->save();
				}

				if ($tags_company != 'null') {
					$lstTags = explode(',',$tags_company);
					$tmspt = dims_createtimestamp();
					foreach($lstTags as $tag){
						$db->query("INSERT INTO dims_tag_globalobject VALUES (:tag, :idtiers, :timestamp)", array(
							':tag' => array('type' => PDO::PARAM_INT, 'value' => $tag),
							':idtiers' => array('type' => PDO::PARAM_INT, 'value' => $tiers->fields['id_globalobject']),
							':timestamp' => array('type' => PDO::PARAM_INT, 'value' => $tmspt),
						));
					}
				}

				if ($tiers->fields['id'] > 0) {
					$_SESSION['desktopv2']['activity']['tiers_selected'] = $tiers->fields['id'];
				}
				$employees = $tiers->getContactsLinkedByType('_DIMS_LABEL_EMPLOYEUR');
				$tiers->setLightAttribute('employees', $employees);
				$tiers->display(_DESKTOP_TPL_LOCAL_PATH.'/new_company/selected_company.tpl.php');
				?>
				<script type="text/javascript">
					$(document).ready(function(){
						dims_xmlhttprequest_todiv("/admin.php","dims_op=desktopv2&action=activity_select_company&id=<?php echo $tiers->fields['id']; ?>","",'zone_form_selected_company');
						<?php /* dims_xmlhttprequest_todiv("/admin.php","dims_op=desktopv2&action=activity_switch_to_form","",'zone_form_selected_company'); */ ?>
					});
				</script>
				<?
			}
		}
		die();
		break;
	case 'activity_edit_existingcontact':
			ob_clean();
			$idct = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
			$idtiers = dims_load_securvalue('idtiers',dims_const::_DIMS_NUM_INPUT,true,true,true);
			$ct = new contact();
			$ct->init_description();
			$fonction='';
			$type_lien='';

			if ($idct>0) {
				$ct->open($idct);
				$ct->setLightAttribute('id_tiers',$idtiers);
				if(isset($idtiers) && $idtiers > 0){
					require_once DIMS_APP_PATH.'modules/system/class_tiers_contact.php';
					$restc=$db->query('SELECT * from dims_mod_business_tiers_contact where id_tiers= :idtiers and id_contact= :idcontact', array(
						':idcontact' => array('type' => PDO::PARAM_INT, 'value' => $idct),
						':idtiers' => array('type' => PDO::PARAM_INT, 'value' => $idtiers),
					));
					if ($db->numrows($restc)>0) {
					   $cttiers=$db->fetchrow($restc);
					   $type_lien=$cttiers['type_lien'];
					   $ct->setLightAttribute('type_lien',$type_lien);
					   $fonction=$cttiers['function'];
					   $ct->setLightAttribute('fonction',$fonction);
					}
				}
				$ct->display(_DESKTOP_TPL_LOCAL_PATH.'/opportunity/edit_contact.tpl.php');
			}
			die();
			break;
		case 'activity_edit_existingcontact2':
			ob_clean();
			$idct = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
			$idtiers = dims_load_securvalue('idtiers',dims_const::_DIMS_NUM_INPUT,true,true,true);
			$ct = new contact();
			$ct->init_description();
			$fonction='';
			$type_lien='';

			if ($idct>0) {
				$ct->open($idct);
				$ct->setLightAttribute('id_tiers',$idtiers);
				$ct->setLightAttribute('id_tiers',$idtiers);
				if(isset($idtiers) && $idtiers > 0){
				require_once DIMS_APP_PATH.'modules/system/class_tiers_contact.php';
					$restc=$db->query('SELECT * from dims_mod_business_tiers_contact where id_tiers= :idtiers and id_contact= :idcontact', array(
						':idcontact' => array('type' => PDO::PARAM_INT, 'value' => $idct),
						':idtiers' => array('type' => PDO::PARAM_INT, 'value' => $idtiers),
					));
					if ($db->numrows($restc)>0) {
					   $cttiers=$db->fetchrow($restc);
					   $type_lien=$cttiers['type_lien'];
					   $ct->setLightAttribute('type_lien',$type_lien);
					   $fonction=$cttiers['function'];
					   $ct->setLightAttribute('fonction',$fonction);
					}
				}

				$ct->display(_DESKTOP_TPL_LOCAL_PATH.'/opportunity/edit_contact2.tpl.php');
			}
			die();
			break;

		case 'activity_save_contact':
			ob_clean();
			$firstname = base64_decode(trim(dims_load_securvalue('firstname',dims_const::_DIMS_CHAR_INPUT,true,true,true)));
			$lastname = base64_decode(trim(dims_load_securvalue('lastname',dims_const::_DIMS_CHAR_INPUT,true,true,true)));
			$nickname = base64_decode(trim(dims_load_securvalue('nickname',dims_const::_DIMS_CHAR_INPUT,true,true,true)));
			$title = base64_decode(dims_load_securvalue('title',dims_const::_DIMS_CHAR_INPUT,true,true,true));
			$type_lien = base64_decode(dims_load_securvalue('type_lien',dims_const::_DIMS_CHAR_INPUT,true,true,true));
			if(!isset($type_lien) || empty($type_lien)){
				$type_lien = $_SESSION['cste']['_DIMS_LABEL_EMPLOYEUR'];
			}
			$fonction = base64_decode(dims_load_securvalue('function',dims_const::_DIMS_CHAR_INPUT,true,true,true));
			$email = base64_decode(dims_load_securvalue('email',dims_const::_DIMS_CHAR_INPUT,true,true,true));
			$comm = base64_decode(dims_load_securvalue('comment',dims_const::_DIMS_CHAR_INPUT,true,true,true));
			$tags = base64_decode(dims_load_securvalue('tags',dims_const::_DIMS_CHAR_INPUT,true,true,true));
			$photo = base64_decode(dims_load_securvalue('photo_path_contact',dims_const::_DIMS_CHAR_INPUT,true,true,true));
			$tiers_selected = base64_decode(dims_load_securvalue('tiers_selected',dims_const::_DIMS_CHAR_INPUT,true,true));

			if (!isset($_SESSION['desktopv2']['activity']['tiers_selected']) && $tiers_selected > 0) {
				$_SESSION['desktopv2']['activity']['tiers_selected'] = $tiers_selected;
			}

			if ($firstname != '' && $lastname != ''){
				$ct = new contact();
				$ct->init_description();
				$ct->setugm();
				$ct->fields['civilite'] = $title;
				$ct->fields['firstname'] = $firstname;
				$ct->fields['lastname'] = $lastname;
				$ct->fields['nickname'] = $nickname;
				$ct->fields['email'] = $email;
				$ct->fields['comments'] = $comm;
				$ct->fields['date_create'] = dims_createtimestamp();
				$ct->fields['timestp_modify'] = $ct->fields['date_create'];
				$ct->fields['id_user_create'] = $ct->fields['id_user'];

				if($ct->save()){
					if ($photo != '' && file_exists(realpath('.').$photo)){
						$time = time();
						$tab_ratio = array(0 => 60, 1 => 300);

						$ext = explode('.', $photo);
						$ext = strtolower($ext[count($ext)-1]);
						$path = DIMS_WEB_PATH.'data/photo_cts/contact_'.$ct->fields['id'];
						if (!file_exists($path)) mkdir($path, 0777,true);

						$path .= '/tmp_'.$ct->fields['id'].'.'.$ext;
						//on cree une image temporaire pour la redimensionner
						rename(realpath('.').$photo,$path);
						switch($ext) {
							case "png" :
								$img_src = imagecreatefrompng($path);
							break;
							case "jpg" :
							case "jpeg" :
								$img_src = imagecreatefromjpeg($path);
							break;
							case "gif" :
								$img_src = imagecreatefromgif($path);
							break;
						}
						$size = getimagesize($path);
						foreach($tab_ratio as $key => $ratio) {
							//modification de la taille du ratio si l'image est plus petite que ratio
							if($ratio > $size[0] && $ratio > $size[1]) {
								if($size[0] > $size[1])  $ratio = $size[0];
								else $ratio = $size[1];
							}
							//largeur > hauteur
							if($size[0] > $size[1]) {

								$hauteur = $max_y = $ratio;
								$largeur = round((($ratio*$size[0])/$size[1]),0);

								//decalage
								$x = round((($ratio-$largeur)/2),0);
								$y = 0;

								$max_x = $ratio;
							}

							//image carrée
							if($size[0] == $size[1]) {
								$hauteur = $ratio;
								$largeur = $ratio;
								$x = 0;
								$y = 0;
								$max_x = $max_y = $ratio;
							}

							//hauteur > largeur
							//La hauteur max sera de 130% du ratio
							if($size[0] < $size[1]) {
								$largeur = $max_x = $ratio;
								$hauteur = round((($ratio/$size[0])*$size[1]),0);

								//decalage
								$x = 0;
								$y = 0;

								//taille image
								if($size[1] > (1.3 * $size[0])){
									$max_y = $ratio * 1.3;
								}else{
									$max_y = round((($ratio/$size[0])*$size[1]),0);
								}
							}

							//On cree l'image
							$image = imagecreatetruecolor($max_x,$max_y);
							imagecopyresized($image,$img_src,$x,$y,0,0,$largeur,$hauteur,$size[0],$size[1]);
							imagepng($image,dirname($path)."/photo".$tab_ratio[$key]."_".$time.".png");

							imagedestroy($image);
						}
						unlink($path);
						$ct->fields['photo'] = "_".$time;
						$ct->save();
					}
					$lstTags = explode(',',$tags);
					$tmspt = dims_createtimestamp();
					foreach($lstTags as $tag){
						if ($tag != '' && $tag > 0)
							$db->query("INSERT INTO dims_tag_globalobject VALUES (:tag, :idcontact, :timestamp)", array(
								':tag' => array('type' => PDO::PARAM_INT, 'value' => $id_contact),
								':idcontact' => array('type' => PDO::PARAM_INT, 'value' => $ct->fields['id_globalobject']),
								':timestamp' => array('type' => PDO::PARAM_INT, 'value' => $tmspt),
							));
					}

					if (isset($_SESSION['desktopv2']['activity']['tiers_selected']) && $_SESSION['desktopv2']['activity']['tiers_selected'] > 0){
						require_once DIMS_APP_PATH.'modules/system/class_tiers_contact.php';
						$tiersCt = new tiersct();
						$tiersCt->init_description();
						$tiersCt->fields['id_tiers'] = $_SESSION['desktopv2']['activity']['tiers_selected'];
						$tiersCt->fields['id_contact'] = $ct->fields['id'];
						$tiersCt->fields['type_lien'] = $type_lien;
						$tiersCt->fields['function'] = $fonction;
						$tiersCt->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
						$tiersCt->fields['id_user'] = $_SESSION['dims']['userid'];
						$tiersCt->fields['date_create'] = dims_createtimestamp();
						$tiersCt->fields['date_deb'] = dims_createtimestamp();
						$tiersCt->fields['link_since'] = 0;
						$tiersCt->fields['link_level'] = 2;
						$tiersCt->fields['id_ct_user_create'] = $_SESSION['dims']['userid'];
						$tiersCt->save();
						// unset($_SESSION['desktopv2']['activity']['tiers_selected']);
					}

					$_SESSION['desktopv2']['activity']['ct_added'][$ct->fields['id']]['id'] = $ct->fields['id'];
					if (isset($_SESSION['desktopv2']['activity']['tiers_selected'])) {
						$_SESSION['desktopv2']['activity']['ct_added'][$ct->fields['id']]['src'] = $_SESSION['desktopv2']['activity']['tiers_selected'];
					}
					else {
						$_SESSION['desktopv2']['activity']['ct_added'][$ct->fields['id']]['src'] = -1;
					}
				}
			}
			$lstTiers = $desktop->constructLstTiersFromCt(array(),$_SESSION['desktopv2']['activity']['ct_added']);
			foreach($lstTiers as $tiers)
				$tiers->display(_DESKTOP_TPL_LOCAL_PATH.'/activity/added_contact_tiers.tpl.php');
			die();
			break;
		case 'activity_save_contact_existing':
			ob_clean();
			$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
			$idtiers = dims_load_securvalue('id_tiers',dims_const::_DIMS_NUM_INPUT,true,true,true);
			if ($id != '' && $id > 0){
				$ct = new contact();
				$ct->open($id);
				foreach ($_GET as $key => $val){
					if ($key != 'dims_op' && $key != 'action' && $key != 'type_lien' && $key != 'function' && $key != 'id' && $key != 'id_tiers'){
						$elem = base64_decode(trim(dims_load_securvalue($key,dims_const::_DIMS_CHAR_INPUT,true,true,true)));
						$ct->fields[$key] = $elem;
					}
				}
				$ct->save();
				if(isset($idtiers) && $idtiers>0){
					$restc=$db->query('	SELECT	*
								FROM	dims_mod_business_tiers_contact
								WHERE	id_tiers=:idtiers
								AND	id_contact=:idcontact', array(
						':idtiers' => array('type' => PDO::PARAM_INT, 'value' => $idtiers),
						':idcontact' => array('type' => PDO::PARAM_INT, 'value' => $id),
					));
					require_once DIMS_APP_PATH.'modules/system/class_tiers_contact.php';
					$tiersCt = new tiersct();
					if ($cttiers = $db->fetchrow($restc)) {
						$tiersCt->openWithFields($cttiers);
					}else{
						$tiersCt->init_description();
						$tiersCt->fields['date_create'] = dims_createtimestamp();
						$tiersCt->fields['id_tiers'] = $idtiers;
						$tiersCt->fields['id_contact'] = $id;
						$tiersCt->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
						$tiersCt->fields['id_user'] = $_SESSION['dims']['userid'];
						$tiersCt->fields['link_level'] = 2;
						$tiersCt->fields['link_since'] = 0;
						$tiersCt->fields['id_user_create'] = $_SESSION['dims']['userid'];
					}
					$ty_link = base64_decode(trim(dims_load_securvalue('type_lien',dims_const::_DIMS_CHAR_INPUT,true,true,true)));
					if(!isset($ty_link) || empty($ty_link)){
						$ty_link = $_SESSION['cste']['_DIMS_LABEL_EMPLOYEUR'];
					}
					$tiersCt->fields['type_lien'] = $ty_link;
					$tiersCt->fields['function'] = base64_decode(trim(dims_load_securvalue('function',dims_const::_DIMS_CHAR_INPUT,true,true,true)));
					$tiersCt->save();
				}
				$_SESSION['desktopv2']['activity']['ct_added'][$id]['id'] = $id;
				$_SESSION['desktopv2']['activity']['ct_added'][$id]['src'] = $idtiers;
			}
			?>
			<script type="text/javascript">
				$(document).ready(function(){
					addContactInActivity(<? echo "'$id','$idtiers'"; ?>);
				});
			</script>
			<?
			die();
			break;
		case 'activity_company_default_form':
			ob_clean();
			$id_tiers = dims_load_securvalue('id_tiers',dims_const::_DIMS_NUM_INPUT,true,true,true);
			$tiers = new tiers();
			$tiers->init_description();

			if ($id_tiers>0) {
				$tiers->open($id_tiers);
			}

			$tiers->display(_DESKTOP_TPL_LOCAL_PATH.'/new_company/new_company_form.tpl.php');
			die();
			break;
		case 'activity_select_company':
			ob_clean();
			$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
			if ($id != '' && $id > 0){
				$tiers = new tiers();
				if ($tiers->open($id)){
					$_SESSION['desktopv2']['opportunity']['tiers_selected'] = $tiers->fields['id'];

					$employees = $tiers->getContactsLinkedByType('_DIMS_LABEL_EMPLOYEUR');
					$tiers->setLightAttribute('employees', $employees);
					$tiers->display(_DESKTOP_TPL_LOCAL_PATH.'/new_company/selected_company.tpl.php');
					?>
					<script type="text/javascript">
						$(document).ready(function(){
							dims_xmlhttprequest_todiv("/admin.php","dims_op=desktopv2&action=activity_switch_to_form","",'zone_form_selected_company');
							hide_new_company_form();

							<?php
							/*
							if (!sizeof($employees)) {
								?>
								show_new_company_form();
								<?php
							}
							else {
								?>
								hide_new_company_form();
								<?php
							}
							*/
							?>
						});
					</script>
					<?
				}
			}
			die();
			break;
		case 'activity_unselect_company' :
			ob_clean();
			$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
			if ($id != '' && $id > 0 && $_SESSION['desktopv2']['activity']['tiers_selected'] == $id)
				unset($_SESSION['desktopv2']['activity']['tiers_selected']);
			die();
			break;
		case 'opportunity_get_all_events':
			ob_clean();
			$typeaction = '';
			$a_events = array();
			$type_event = dims_load_securvalue('type_event', dims_const::_DIMS_CHAR_INPUT, true, false);

			if ($type_event != '') {
				switch ($type_event) {
					case 'trade_fairs':
						$typeaction = '= "_DIMS_PLANNING_FAIR"';
						break;
					case 'trade_missions':
						$typeaction = ' <> "_DIMS_MISSIONS" ';
						break;
				}
				if ($typeaction != '') {
					// chargement des events
					$rs = $db->query('
						SELECT		id, libelle, datejour
						FROM		dims_mod_business_action
						WHERE		type = :type
						AND			typeaction :typeaction
						AND			id_parent = 0
						AND		libelle != ""
						ORDER BY	libelle', array(
							':type'			=> dims_const::_PLANNING_ACTION_EVT,
							':typeaction'	=> $typeaction
						));
					while ($row = $db->fetchrow($rs)) {
						$deb = explode('-',$row['datejour']);
						$row['datejour'] = '';
						if($deb[2] != 0){
							$row['datejour'] .= $deb[2].'/';
						}
						if($deb[1] != 0){
							$row['datejour'] .= $deb[1].'/';
						}
						if($deb[0] != 0){
							$row['datejour'] .= $deb[0];
						}

						$a_events[] = $row;
					}
				}
			}
			die(json_encode($a_events));
			break;
		case 'opportunity_search_event':
			ob_clean();
			$typeaction = '';
			$a_events = array();
			$a_events[0]=0;
			$label = trim(dims_load_securvalue('label',dims_const::_DIMS_CHAR_INPUT,true,true,true));
			$type_event = dims_load_securvalue('type_event', dims_const::_DIMS_CHAR_INPUT, true, true);

			switch ($type_event) {
				case 'trade_fairs':
					$typeaction = '_DIMS_PLANNING_FAIR';
					break;
				case 'trade_missions':
					$typeaction = '_DIMS_MISSIONS';
					break;
			}

			$sql='
				SELECT		id
				FROM		dims_mod_business_action
				WHERE		type = '.dims_const::_PLANNING_ACTION_EVT;
			if ($typeaction != '') {
				if ($typeaction=='_DIMS_MISSIONS') {
					$sql .= ' AND typeaction <> "_DIMS_PLANNING_FAIR"';
				} else {
					$sql .= ' AND typeaction = :typeaction';
					$params[':typeaction'] = array('type' => PDO::PARAM_STR, 'value' => $typeaction);
				}
			}
			$sql .= '
				AND			id_parent = 0
				AND		UCASE(libelle) like :libelle
				ORDER BY	libelle';
			$params[':libelle'] = array('type' => PDO::PARAM_STR, 'value' => '%'.strtoupper($label).'%');
			$rs = $db->query($sql, $params);
			while ($row = $db->fetchrow($rs)) {
					$a_events[$row['id']] = $row['id'];
			}
			$lstEvents = $desktop->getEvents($a_events);
			if (count($lstEvents) > 0)
				foreach($lstEvents as $event)
					$event->display(_DESKTOP_TPL_LOCAL_PATH.'/opportunity/search_event.tpl.php');
			else
				echo "<div class=\"no_result\">No result for \"$label\"</div>";
			die();
			break;
		case 'opportunity_select_event':
			ob_clean();
			$id_event = dims_load_securvalue('id_event', dims_const::_DIMS_NUM_INPUT, true, false);
			$rs = $db->query('SELECT id, datejour, id_country FROM dims_mod_business_action WHERE id = :idevent LIMIT 0,1', array(
				':idevent' => array('type' => PDO::PARAM_INT, 'value' => $id_event),
			));
			$row = $db->fetchrow($rs);
			die(json_encode(array( 'id' => $row['id'], 'date' => explode('-', $row['datejour']), 'id_country' => $row['id_country'] )));
			break;
		case 'opportunity_search_contact':
			ob_clean();
			$label = trim(dims_load_securvalue('label',dims_const::_DIMS_CHAR_INPUT,true,true,true));
			if ($label != '' && $label != $_SESSION['cste']['_DIMS_LABEL_SEARCH_FOR_CT']){
				if (!isset($_SESSION['desktopv2']['opportunity']['ct_added'])) $_SESSION['desktopv2']['opportunity']['ct_added'] = array();
				$lstCt = array();
				$lstCtId = array();
				$params = array();
				$sel = "SELECT		DISTINCT *
						FROM		dims_mod_business_contact
						WHERE		(firstname LIKE :label
						OR			lastname LIKE :label)
						AND			id NOT IN (".$db->getParamsFromArray($_SESSION['desktopv2']['opportunity']['ct_added'], 'idcontact', $params).")
						AND			inactif = 0
						ORDER BY	lastname, firstname";
				$params[':label'] = array('type' => PDO::PARAM_STR, 'value' => '%'.$label.'%');
				$res = $db->query($sel, $params);

				while($r = $db->fetchrow($res)){
					$ct = new contact();
					$ct->openWithFields($r);
					$lstCt[$r['id']] = $ct;
					$lstCtId[$r['id']] = $r['id'];
				}
				$lstTiers = $desktop->constructLstTiersFromCt($lstCt,$lstCtId);
				if (count($lstTiers) > 0)
					foreach($lstTiers as $tiers)
						$tiers->display(_DESKTOP_TPL_LOCAL_PATH.'/opportunity/search_contact_tiers.tpl.php');
				else
					echo "<div class=\"no_result\">No result for \"$label\"</div>";
			}
			die();
			break;
		case 'add_contact_in_opportunity' :
			ob_clean();
			$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
			$idtiers = dims_load_securvalue('idtiers',dims_const::_DIMS_NUM_INPUT,true,true,true);
			if ($id != '' && $id > 0){
				$_SESSION['desktopv2']['opportunity']['ct_added'][$id]['id'] = $id;
				$_SESSION['desktopv2']['opportunity']['ct_added'][$id]['src'] = $idtiers;
			}
			if (!isset($_SESSION['desktopv2']['opportunity']['tiers_added']))
				$_SESSION['desktopv2']['opportunity']['tiers_added'] = array();

			if ($idtiers != '' && $idtiers > 0 && !in_array($idtiers,$_SESSION['desktopv2']['opportunity']['tiers_added'])) {
				$_SESSION['desktopv2']['opportunity']['tiers_added'][$idtiers] = $idtiers;

				if(!isset($_SESSION['desktopv2']['opportunity']['tiers_tolink'][$idtiers])) {
					$_SESSION['desktopv2']['opportunity']['tiers_tolink'][$idtiers] = _TIER_LINK;
				}
			}
			if(!isset($_SESSION['desktopv2']['opportunity']['ct_added'])) $_SESSION['desktopv2']['opportunity']['ct_added'] = array();

			$lstTiers = $desktop->constructLstTiersFromCt(array(),$_SESSION['desktopv2']['opportunity']['ct_added'],$_SESSION['desktopv2']['opportunity']['tiers_added']);
			foreach($lstTiers as $tiers)
				$tiers->display(_DESKTOP_TPL_LOCAL_PATH.'/opportunity/added_contact_tiers.tpl.php');
			die();
			break;
		case 'del_contact_in_opportunity' :
			ob_clean();
			$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
			if ($id != '' && $id > 0)
				unset($_SESSION['desktopv2']['opportunity']['ct_added'][$id]);
			$lstTiers = $desktop->constructLstTiersFromCt(array(),$_SESSION['desktopv2']['opportunity']['ct_added']);
			foreach($lstTiers as $tiers)
				$tiers->display(_DESKTOP_TPL_LOCAL_PATH.'/opportunity/added_contact_tiers.tpl.php');
			die();
			break;
		case 'opportunity_ununselect_company':
			ob_clean();
			$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
			if ($id != '' && $id > 0 && isset($_SESSION['desktopv2']['opportunity']['tiers_added'][$id])) {
				unset($_SESSION['desktopv2']['opportunity']['tiers_added'][$id]);
				unset($_SESSION['desktopv2']['opportunity']['tiers_tolink'][$id]);
			}
			if (isset($_SESSION['desktopv2']['opportunity']['ct_added']))
				foreach($_SESSION['desktopv2']['opportunity']['ct_added'] as $key => $val)
					if ($val['src'] == $id)
						unset($_SESSION['desktopv2']['opportunity']['ct_added'][$key]);
			$lstTiers = $desktop->constructLstTiersFromCt(array(),$_SESSION['desktopv2']['opportunity']['ct_added'],$_SESSION['desktopv2']['opportunity']['tiers_added']);
			foreach($lstTiers as $tiers)
				$tiers->display(_DESKTOP_TPL_LOCAL_PATH.'/opportunity/added_contact_tiers.tpl.php');
			die();
			break;
		case 'keepCompany':
			ob_clean();
			$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);

			$_SESSION['desktopv2']['opportunity']['tiers_tolink'][$id] = _TIER_LINK;

			$lstTiers = $desktop->constructLstTiersFromCt(array(),$_SESSION['desktopv2']['opportunity']['ct_added'],$_SESSION['desktopv2']['opportunity']['tiers_added']);
			foreach($lstTiers as $tiers)
				$tiers->display(_DESKTOP_TPL_LOCAL_PATH.'/opportunity/added_contact_tiers.tpl.php');
			die();
			break;
		case 'DontkeepCompany':
			ob_clean();
			$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);

			$_SESSION['desktopv2']['opportunity']['tiers_tolink'][$id] = _TIER_DONT_LINK;

			$lstTiers = $desktop->constructLstTiersFromCt(array(),$_SESSION['desktopv2']['opportunity']['ct_added'],$_SESSION['desktopv2']['opportunity']['tiers_added']);
			foreach($lstTiers as $tiers)
				$tiers->display(_DESKTOP_TPL_LOCAL_PATH.'/opportunity/added_contact_tiers.tpl.php');
			die();
			break;
		case 'opportunity_search_companycontact':
			ob_clean();

			$label = trim(dims_load_securvalue('label',dims_const::_DIMS_CHAR_INPUT,true,true,true));
			$type = trim(dims_load_securvalue('type',dims_const::_DIMS_CHAR_INPUT,true,true,true));

			if($type == 'grs'){
				if($label!=''){
					require_once DIMS_APP_PATH."modules/system/class_ct_group.php";
					$groups = ct_group::conditions(array('label' => array('op' => 'LIKE', 'value' => "%".$label."%"), 'id_workspace'=>array('op'=>'=','value'=>$_SESSION['dims']['workspaceid'])))->order("label")->run();
					$nbGr = count($groups);
					if($nbGr){
						?>
						<div class="op_search_results">
						<table style="width: 100%;border-bottom: 1px solid #DDDDDD;">
							<tbody>
							<?php
							$i = 0;
							foreach($groups as $g){
								$g->setLightAttribute('last', ($i==$nbGr-1));
								$g->display(_DESKTOP_TPL_LOCAL_PATH.'/new_company/result_search_group.tpl.php');
								$i++;
							}
							?>
							</tbody>
						</table>
						</div>
						<script type="text/javascript">
							$(document).ready(function(){
								$("div#new_company_result table:last").css("border-bottom","none");
							});
						</script>
						<?
					}else{
						?>
						<span class="no_result"><?php echo $_SESSION['cste']['NO_RESULT']; ?></span>
						<?php
					}
				}
			}else{
				echo '<p class="opportunity_links"">';
				if ($type == 'cts') {
					echo '<a class="opportunity_new_contact" href="javascript:void(0);" onclick="javascript:show_new_contact_form(true);" title="'.$_SESSION['cste']['CREATE_A_CONTACT_WITHOUT_COMPANY'].'"><span>
						'.$_SESSION['cste']['CREATE_A_CONTACT_WITHOUT_COMPANY'].'</span><img src="'._DESKTOP_TPL_PATH.'/gfx/common/add.png" alt="'.$_SESSION['cste']['CREATE_A_CONTACT_WITHOUT_COMPANY'].'" /></a>';
				}
				echo '<a class="opportunity_new_contact" href="javascript:void(0);" onclick="javascript:show_new_company_form();" title="'.$_SESSION['cste']['CREATE_THE_COMPANY'].'"><span>
					'.$_SESSION['cste']['CREATE_THE_COMPANY'].'</span><img src="'._DESKTOP_TPL_PATH.'/gfx/common/add.png" alt="'.$_SESSION['cste']['CREATE_THE_COMPANY'].'" /></a>
					</p>';

				if ($label != ''){
					// initialisation du module de recherche sur
					require_once(DIMS_APP_PATH . "/modules/system/class_search.php");
					$dimsearch = new search($dims);

					// ajout des objects sur lequel la recherche va se baser
					if($type=='cts') $dimsearch->addSearchObject(dims_const::_DIMS_MODULE_SYSTEM, contact::MY_GLOBALOBJECT_CODE,$_SESSION['cste']['_DIMS_LABEL_CONTACTS']);
					else $dimsearch->addSearchObject(dims_const::_DIMS_MODULE_SYSTEM, tiers::MY_GLOBALOBJECT_CODE,$_SESSION['cste']['_DIMS_LABEL_GROUP_LIST']);
					// reinitialise la recherche sur ce module courant, n'efface pas le cache result
					$dimsearch->initSearchObject();

					$kword					= dims_load_securvalue('kword', dims_const::_DIMS_CHAR_INPUT, true, true);
					$idmodule				= dims_load_securvalue('idmodule', dims_const::_DIMS_CHAR_INPUT, true, true);
					$idobj					= dims_load_securvalue('idobj', dims_const::_DIMS_CHAR_INPUT, true, true);
					$idmetafield			= dims_load_securvalue('idmetafield', dims_const::_DIMS_CHAR_INPUT, true, true);
					$sens					= dims_load_securvalue('sens', dims_const::_DIMS_CHAR_INPUT, true, true);

					$dimsearch->executeSearch2($label, $kword,$_SESSION['dims']['moduleid'], $idobj, $idmetafield, $sens,0,null,$_SESSION['dims']['workspaceid']);

					$ids = array();
					foreach($dimsearch->tabresultat as $idmodule => $tab_objects){
						foreach($tab_objects as $idobjet => $tab_ids){
							foreach($tab_ids as $kid => $id){
								$ids[$kid] = $kid;
							}
						}
					}
					if(count($ids)){
						if($type=='cts'){
							$params = array();
							$sel = "SELECT	*
								FROM	".contact::TABLE_NAME."
								WHERE	id_globalobject IN (".$db->getParamsFromArray($ids, 'idcontact', $params).")
								AND		inactif = 0
								ORDER BY firstname, lastname";
							$res = $db->query($sel, $params);
							$total = $db->numrows($res);
							if ($total > 0){
								?>
								<div class="op_search_results">
								<table style="width: 100%;border-bottom: 1px solid #DDDDDD;">
									<tbody>
								<?php
								$i = 0;
								while ($r = $db->fetchrow($res)){
									$c = new contact();
									$c->openWithFields($r);
									if($i==$total-1)$c->setLightAttribute('last', true);
									$c->display(_DESKTOP_TPL_LOCAL_PATH.'/new_company/result_search_contact.tpl.php');
									$i++;
								}
								?>
									</tbody>
								</table>
								</div>
								<script type="text/javascript">
									$(document).ready(function(){
										$("div#new_company_result table:last").css("border-bottom","none");
									});
								</script>
								<?
							}
						}
						else{
							$params = array();
							 $sel = "SELECT	*
								FROM	".tiers::TABLE_NAME."
								WHERE	id_globalobject IN (".$db->getParamsFromArray($ids, 'idtiers', $params).")
								AND		inactif = 0
								ORDER BY intitule ASC";
							$res = $db->query($sel, $params);
							$total = $db->numrows($res);
							if ($total > 0){
								?>
								<div class="op_search_results">
								<table style="width: 100%;border-bottom: 1px solid #DDDDDD;">
									<tbody>
								<?php
								$i = 0;
								while ($r = $db->fetchrow($res)){
									$t = new tiers();
									$t->openWithFields($r);
									if($i==$total-1)$t->setLightAttribute('last', true);
									$t->display(_DESKTOP_TPL_LOCAL_PATH.'/new_company/result_search_company.tpl.php');
									$i++;
								}
								?>
										</tbody>
								</table>
								</div>
								<script type="text/javascript">
									$(document).ready(function(){
										$("div#new_company_result table:last").css("border-bottom","none");
									});
								</script>
								<?
							}
						}
					}
					else{
						?>
						<span class="no_result"><?php echo $_SESSION['cste']['NO_RESULT']; ?></span>
						<?php
					}
				}
			}
			die();
			break;
		case 'opportunity_create_company':
			ob_clean();
			$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
			$tiers = new tiers();
			$tiers->init_description();
			if ($id != '' && $id > 0)
				$tiers->open($id);
			$tiers->display(_DESKTOP_TPL_LOCAL_PATH.'/new_company/new_company_form.tpl.php');
			die();
			break;
		case 'opportunity_save_company':
			ob_clean();
			$id_tiers = base64_decode(trim(dims_load_securvalue('id_tiers',dims_const::_DIMS_CHAR_INPUT,true,true,true)));
			$name = base64_decode(trim(dims_load_securvalue('company_intitule',dims_const::_DIMS_CHAR_INPUT,true,true,true)));
			$id_country = dims_load_securvalue('country',dims_const::_DIMS_NUM_INPUT,true,true,true);
			$city = dims_load_securvalue('city',dims_const::_DIMS_CHAR_INPUT,true,true,true);
			$address = base64_decode(trim(dims_load_securvalue('company_adresse',dims_const::_DIMS_CHAR_INPUT,true,true,true)));
			$cp = base64_decode(trim(dims_load_securvalue('company_codepostal',dims_const::_DIMS_CHAR_INPUT,true,true,true)));
			$tel = base64_decode(trim(dims_load_securvalue('company_telephone',dims_const::_DIMS_CHAR_INPUT,true,true,true)));
			$fax = base64_decode(trim(dims_load_securvalue('company_telecopie',dims_const::_DIMS_CHAR_INPUT,true,true,true)));
			$email = base64_decode(trim(dims_load_securvalue('company_mel',dims_const::_DIMS_CHAR_INPUT,true,true,true)));
			$site_web = base64_decode(trim(dims_load_securvalue('company_site_web',dims_const::_DIMS_CHAR_INPUT,true,true,true)));
			$tags_company = trim(dims_load_securvalue('tags_company',dims_const::_DIMS_CHAR_INPUT,true,true,true));
			if ($tags_company != 'null') $tags_company = base64_decode($tags_company);
			$photo = base64_decode(dims_load_securvalue('photo_path_company',dims_const::_DIMS_CHAR_INPUT,true,true,true));

			if ($name != ''){
				$tiers = new tiers();
			if ($id_tiers>0) {
				$tiers->open($id_tiers);
			}
			else {
				$tiers->init_description();
				$tiers->setugm();
			}

			$tiers->fields['intitule'] = $name;
			$tiers->fields['id_country'] = $id_country;

			if ($id_country > 0) {
				require_once DIMS_APP_PATH.'modules/system/class_country.php';
				$country = new country();
				$country->open($id_country);
				$tiers->fields['pays'] = $country->fields['printable_name'];
			}

			if ($city > 0) {
				require_once DIMS_APP_PATH.'modules/system/class_city.php';
				$cit = new city();
				$cit->open($city);
				$tiers->fields['ville'] = $cit->fields['label'];
			}

			$tiers->fields['adresse'] = $address;
			$tiers->fields['codepostal'] = $cp;
			$tiers->fields['telephone'] = $tel;
			$tiers->fields['telecopie'] = $fax;
			$tiers->fields['mel'] = $email;
			$tiers->fields['site_web'] = $site_web;


			$tiers->fields['date_creation'] = dims_createtimestamp();
			$tiers->fields['timestp_modify'] = $tiers->fields['date_creation'];
			$tiers->fields['id_user_create'] = $tiers->fields['id_user'];

			if($tiers->save()){
				if ($photo != '' && file_exists(realpath('.').$photo)){
					$time = time();
					$tab_ratio = array(0 => array(100,50), 1 => array(300,150));

					$ext = explode('.', $photo);
					$ext = strtolower($ext[count($ext)-1]);
					$path = DIMS_WEB_PATH.'data/photo_ent/ent_'.$tiers->fields['id'];
					if (!file_exists($path)) mkdir($path, 0770,true);

					$path .= '/tmp_'.$tiers->fields['id'].'.'.$ext;
					//on cree une image temporaire pour la redimensionner
					rename(realpath('.').$photo,$path);
					foreach($tab_ratio as $key => $ratio)
						dims_resizeimage($path, 0, $ratio[0], $ratio[1],'',0,dirname($path)."/photo".$ratio[0]."_".$time.".png");
					unlink($path);
					$tiers->fields['photo'] = "_".$time;
					$tiers->save();
				}

				if ($tags_company != 'null') {
					$lstTags = explode(',',$tags_company);
					$tmspt = dims_createtimestamp();
					foreach($lstTags as $tag){
						$db->query("INSERT INTO dims_tag_globalobject VALUES (:tag, :idtiers, :timestamp)", array(
							':tag' => array('type' => PDO::PARAM_INT, 'value' => $tag),
							':idtiers' => array('type' => PDO::PARAM_INT, 'value' => $tiers->fields['id_globalobject']),
							':timestamp' => array('type' => PDO::PARAM_INT, 'value' => $tmspt),
						));
					}
				}

				if ($tiers->fields['id'] > 0) {
					$_SESSION['desktopv2']['opportunity']['tiers_selected'] = $tiers->fields['id'];
				}
				$employees = $tiers->getContactsLinkedByType('_DIMS_LABEL_EMPLOYEUR');
				$tiers->setLightAttribute('employees', $employees);
				$tiers->display(_DESKTOP_TPL_LOCAL_PATH.'/new_company/selected_company.tpl.php');

				$u = new contact();
				$u->open($_SESSION['dims']['user']['id_contact']);
				require_once DIMS_APP_PATH."modules/system/class_matrix.php";
				require_once DIMS_APP_PATH.'modules/system/class_tiers_contact.php';
				// lien matrice
				$matrice = new matrix();
				$matrice->fields['id_tiers'] = $tiers->fields['id_globalobject'];
				$matrice->fields['id_contact'] = $u->fields['id_globalobject'];
				$matrice->fields['id_country'] = $id_country;
				$matrice->fields['year'] = date('Y');
				$matrice->fields['month'] = date('m');
				$matrice->fields['timestp_modify'] = dims_createtimestamp();
				$matrice->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
				$matrice->save();

				$tiersct = new tiersct();
				$tiersct->fields['id_tiers'] = $tiers->fields['id'];
				$tiersct->fields['id_contact'] = $_SESSION['dims']['user']['id_contact'];
				$tiersct->fields['type_lien'] = 'Other';
				$tiersct->fields['link_level'] = 2;
				$tiersct->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
				$tiersct->fields['id_user'] = $_SESSION['dims']['userid'];
				$tiersct->fields['date_create'] = dims_createtimestamp();
				$tiersct->save();
				?>
				<script type="text/javascript">
					$(document).ready(function(){
						dims_xmlhttprequest_todiv("/admin.php","dims_op=desktopv2&action=opportunity_select_company&id=<?php echo $tiers->fields['id']; ?>","",'zone_form_selected_company');
						<?php /* dims_xmlhttprequest_todiv("/admin.php","dims_op=desktopv2&action=opportunity_switch_to_form","",'zone_form_selected_company'); */ ?>
					});
				</script>
				<?
			}
		}
		die();
		break;
	case 'opportunity_edit_existingcontact':
			ob_clean();
			$idct = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
			$idtiers = dims_load_securvalue('idtiers',dims_const::_DIMS_NUM_INPUT,true,true,true);
			$ct = new contact();
			$ct->init_description();
			$fonction='';
			$type_lien='';

			if ($idct>0) {
				$ct->open($idct);
				$ct->setLightAttribute('id_tiers',$idtiers);
				if(isset($idtiers) && $idtiers > 0){
					require_once DIMS_APP_PATH.'modules/system/class_tiers_contact.php';
					$restc=$db->query('SELECT * from dims_mod_business_tiers_contact where id_tiers= :idcontact and id_contact= :idcontact', array(
						':idcontact' => array('type' => PDO::PARAM_INT, 'value' => $id_contact),
						':idtiers' => array('type' => PDO::PARAM_INT, 'value' => $idct),
					));
					if ($db->numrows($restc)>0) {
					   $cttiers=$db->fetchrow($restc);
					   $type_lien=$cttiers['type_lien'];
					   $ct->setLightAttribute('type_lien',$type_lien);
					   $fonction=$cttiers['function'];
					   $ct->setLightAttribute('fonction',$fonction);
					}
				}
				$ct->display(_DESKTOP_TPL_LOCAL_PATH.'/opportunity/edit_contact.tpl.php');
			}
			die();
			break;
		case 'opportunity_edit_existingcontact2':
			ob_clean();
			$idct = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
			$idtiers = dims_load_securvalue('idtiers',dims_const::_DIMS_NUM_INPUT,true,true,true);
			$ct = new contact();
			$ct->init_description();
			$fonction='';
			$type_lien='';

			if ($idct>0) {
				$ct->open($idct);
				$ct->setLightAttribute('id_tiers',$idtiers);
				$ct->setLightAttribute('id_tiers',$idtiers);
				if(isset($idtiers) && $idtiers > 0){
				require_once DIMS_APP_PATH.'modules/system/class_tiers_contact.php';
					$restc=$db->query('SELECT * from dims_mod_business_tiers_contact where id_tiers= :idtiers and id_contact= :idcontact', array(
						':idtiers' => array('type' => PDO::PARAM_INT, 'value' => $idtiers),
						':idcontact' => array('type' => PDO::PARAM_INT, 'value' => $idct),
					));
					if ($db->numrows($restc)>0) {
					   $cttiers=$db->fetchrow($restc);
					   $type_lien=$cttiers['type_lien'];
					   $ct->setLightAttribute('type_lien',$type_lien);
					   $fonction=$cttiers['function'];
					   $ct->setLightAttribute('fonction',$fonction);
					}
				}

				$ct->display(_DESKTOP_TPL_LOCAL_PATH.'/opportunity/edit_contact2.tpl.php');
			}
			die();
			break;
		case 'leadSaveNewContact':
		case 'activitySaveNewContact':
		case 'appointmentOfferSaveNewContact':
			ob_clean();
			$firstname = base64_decode(trim(dims_load_securvalue('firstname',dims_const::_DIMS_CHAR_INPUT,true,true,true)));
			$lastname = base64_decode(trim(dims_load_securvalue('lastname',dims_const::_DIMS_CHAR_INPUT,true,true,true)));
			$nickname = base64_decode(trim(dims_load_securvalue('nickname',dims_const::_DIMS_CHAR_INPUT,true,true,true)));
			$title = base64_decode(dims_load_securvalue('title',dims_const::_DIMS_CHAR_INPUT,true,true,true));
			$type_lien = base64_decode(dims_load_securvalue('type_lien',dims_const::_DIMS_CHAR_INPUT,true,true,true));
			if(!isset($type_lien) || empty($type_lien)){
				$type_lien = $_SESSION['cste']['_DIMS_LABEL_EMPLOYEUR'];
			}
			$fonction = base64_decode(dims_load_securvalue('function',dims_const::_DIMS_CHAR_INPUT,true,true,true));
			$email = base64_decode(dims_load_securvalue('email',dims_const::_DIMS_CHAR_INPUT,true,true,true));
			$comm = base64_decode(dims_load_securvalue('comment',dims_const::_DIMS_CHAR_INPUT,true,true,true));
			$phone = base64_decode(dims_load_securvalue('phone',dims_const::_DIMS_CHAR_INPUT,true,true,true));
			$mobile = base64_decode(dims_load_securvalue('mobile',dims_const::_DIMS_CHAR_INPUT,true,true,true));
			$fax = base64_decode(dims_load_securvalue('fax',dims_const::_DIMS_CHAR_INPUT,true,true,true));
			$address = base64_decode(dims_load_securvalue('address',dims_const::_DIMS_CHAR_INPUT,true,true,true));
			$postalcode = base64_decode(dims_load_securvalue('postalcode',dims_const::_DIMS_CHAR_INPUT,true,true,true));
			$city_id = base64_decode(dims_load_securvalue('city_id',dims_const::_DIMS_CHAR_INPUT,true,true,true));
			$country_id = base64_decode(dims_load_securvalue('country_id',dims_const::_DIMS_CHAR_INPUT,true,true,true));
			$tags = base64_decode(dims_load_securvalue('tags',dims_const::_DIMS_CHAR_INPUT,true,true,true));
			$photo = base64_decode(dims_load_securvalue('photo_path_contact',dims_const::_DIMS_CHAR_INPUT,true,true,true));
			$tiers_selected = base64_decode(dims_load_securvalue('tiers_selected',dims_const::_DIMS_CHAR_INPUT,true,true));

			if (!isset($_SESSION['desktopv2']['opportunity']['tiers_selected']) && $tiers_selected > 0) {
				$_SESSION['desktopv2']['opportunity']['tiers_selected'] = $tiers_selected;
			}

			if(!empty($tiers_selected)) {
				$_SESSION['desktopv2']['opportunity']['tiers_tolink'][$tiers_selected] = _TIER_LINK;
			}

			if ($firstname != '' && $lastname != ''){
				if ($country_id > 0) {
					require_once DIMS_APP_PATH.'modules/system/class_country.php';
					$country = new country();
					$country->open($country_id);
				}
				require_once DIMS_APP_PATH.'modules/system/class_city.php';
				$city = new city();
				if ($city_id > 0) {
					$city->open($city_id);
				}
				else {
					$city->init_description();
				}

				$ct = new contact();
				$ct->init_description();
				$ct->setugm();
				$ct->fields['civilite'] = $title;
				$ct->fields['firstname'] = $firstname;
				$ct->fields['lastname'] = $lastname;
				$ct->fields['nickname'] = $nickname;
				$ct->fields['email'] = $email;
				$ct->fields['comments'] = $comm;
				$ct->fields['phone'] = $phone;
				$ct->fields['mobile'] = $mobile;
				$ct->fields['fax'] = $fax;
				$ct->fields['address'] = $address;
				$ct->fields['postalcode'] = $postalcode;
				$ct->fields['city'] = $city->fields['label'];
				$ct->fields['country'] = $country->fields['printable_name'];
				$ct->fields['id_country'] = $country->fields['id'];
				$ct->fields['date_create'] = dims_createtimestamp();
				$ct->fields['timestp_modify'] = $ct->fields['date_create'];
				$ct->fields['id_user_create'] = $ct->fields['id_user'];

				if($ct->save()){
					if (isset($_SESSION['desktopv2']['opportunity']['tiers_selected']) && $_SESSION['desktopv2']['opportunity']['tiers_selected'] > 0){
						require_once DIMS_APP_PATH.'modules/system/class_tiers_contact.php';
						$tiersCt = new tiersct();
						$tiersCt->init_description();
						$tiersCt->fields['id_tiers'] = $_SESSION['desktopv2']['opportunity']['tiers_selected'];
						$tiersCt->fields['id_contact'] = $ct->fields['id'];
						$tiersCt->fields['type_lien'] = $type_lien;
						$tiersCt->fields['function'] = $fonction;
						$tiersCt->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
						$tiersCt->fields['id_user'] = $_SESSION['dims']['userid'];
						$tiersCt->fields['date_create'] = dims_createtimestamp();
						$tiersCt->fields['date_deb'] = dims_createtimestamp();
						$tiersCt->fields['link_since'] = 0;
						$tiersCt->fields['link_level'] = 2;
						$tiersCt->fields['id_ct_user_create'] = $_SESSION['dims']['userid'];
						$tiersCt->save();
						// unset($_SESSION['desktopv2']['opportunity']['tiers_selected']);
					}

					$_SESSION['desktopv2']['opportunity']['ct_added'][$ct->fields['id']]['id'] = $ct->fields['id'];
					if (isset($_SESSION['desktopv2']['opportunity']['tiers_selected'])) {
						$_SESSION['desktopv2']['opportunity']['ct_added'][$ct->fields['id']]['src'] = $_SESSION['desktopv2']['opportunity']['tiers_selected'];
					}
					else {
						$_SESSION['desktopv2']['opportunity']['ct_added'][$ct->fields['id']]['src'] = -1;
					}
					echo $ct->fields['id_globalobject'];
				}
				else echo 0;
				die();
			}
			break;
		case 'opportunity_save_contact':
			ob_clean();
			$firstname = base64_decode(trim(dims_load_securvalue('firstname',dims_const::_DIMS_CHAR_INPUT,true,true,true)));
			$lastname = base64_decode(trim(dims_load_securvalue('lastname',dims_const::_DIMS_CHAR_INPUT,true,true,true)));
			$nickname = base64_decode(trim(dims_load_securvalue('nickname',dims_const::_DIMS_CHAR_INPUT,true,true,true)));
			$title = base64_decode(dims_load_securvalue('title',dims_const::_DIMS_CHAR_INPUT,true,true,true));
			$type_lien = base64_decode(dims_load_securvalue('type_lien',dims_const::_DIMS_CHAR_INPUT,true,true,true));
			if(!isset($type_lien) || empty($type_lien)){
				$type_lien = $_SESSION['cste']['_DIMS_LABEL_EMPLOYEUR'];
			}
			$fonction = base64_decode(dims_load_securvalue('function',dims_const::_DIMS_CHAR_INPUT,true,true,true));
			$email = base64_decode(dims_load_securvalue('email',dims_const::_DIMS_CHAR_INPUT,true,true,true));
			$comm = base64_decode(dims_load_securvalue('comment',dims_const::_DIMS_CHAR_INPUT,true,true,true));
			$phone = base64_decode(dims_load_securvalue('phone',dims_const::_DIMS_CHAR_INPUT,true,true,true));
			$mobile = base64_decode(dims_load_securvalue('mobile',dims_const::_DIMS_CHAR_INPUT,true,true,true));
			$fax = base64_decode(dims_load_securvalue('fax',dims_const::_DIMS_CHAR_INPUT,true,true,true));
			$address = base64_decode(dims_load_securvalue('address',dims_const::_DIMS_CHAR_INPUT,true,true,true));
			$postalcode = base64_decode(dims_load_securvalue('postalcode',dims_const::_DIMS_CHAR_INPUT,true,true,true));
			$city = base64_decode(dims_load_securvalue('city',dims_const::_DIMS_CHAR_INPUT,true,true,true));
			$country = base64_decode(dims_load_securvalue('country',dims_const::_DIMS_CHAR_INPUT,true,true,true));
			$tags = base64_decode(dims_load_securvalue('tags',dims_const::_DIMS_CHAR_INPUT,true,true,true));
			$photo = base64_decode(dims_load_securvalue('photo_path_contact',dims_const::_DIMS_CHAR_INPUT,true,true,true));
			$tiers_selected = base64_decode(dims_load_securvalue('tiers_selected',dims_const::_DIMS_CHAR_INPUT,true,true));

			if (!isset($_SESSION['desktopv2']['opportunity']['tiers_selected']) && $tiers_selected > 0) {
				$_SESSION['desktopv2']['opportunity']['tiers_selected'] = $tiers_selected;
			}

			if(!empty($tiers_selected)) {
				$_SESSION['desktopv2']['opportunity']['tiers_tolink'][$tiers_selected] = _TIER_LINK;
			}

			if ($firstname != '' && $lastname != ''){
				$ct = new contact();
				$ct->init_description();
				$ct->setugm();
				$ct->fields['civilite'] = $title;
				$ct->fields['firstname'] = $firstname;
				$ct->fields['lastname'] = $lastname;
				$ct->fields['nickname'] = $nickname;
				$ct->fields['email'] = $email;
				$ct->fields['comments'] = $comm;
				$ct->fields['phone'] = $phone;
				$ct->fields['mobile'] = $mobile;
				$ct->fields['fax'] = $fax;
				$ct->fields['address'] = $address;
				$ct->fields['postalcode'] = $postalcode;
				$ct->fields['city'] = $city;
				$ct->fields['country'] = $country;
				$ct->fields['date_create'] = dims_createtimestamp();
				$ct->fields['timestp_modify'] = $ct->fields['date_create'];
				$ct->fields['id_user_create'] = $ct->fields['id_user'];

				if($ct->save()){
					if ($photo != '' && file_exists(realpath('.').$photo)){
						$time = time();
						$tab_ratio = array(0 => 60, 1 => 300);

						$ext = explode('.', $photo);
						$ext = strtolower($ext[count($ext)-1]);
						$path = DIMS_WEB_PATH.'data/photo_cts/contact_'.$ct->fields['id'];
						if (!file_exists($path)) mkdir($path, 0777,true);

						$path .= '/tmp_'.$ct->fields['id'].'.'.$ext;
						//on cree une image temporaire pour la redimensionner
						rename(realpath('.').$photo,$path);
						switch($ext) {
							case "png" :
								$img_src = imagecreatefrompng($path);
							break;
							case "jpg" :
							case "jpeg" :
								$img_src = imagecreatefromjpeg($path);
							break;
							case "gif" :
								$img_src = imagecreatefromgif($path);
							break;
						}
						$size = getimagesize($path);
						foreach($tab_ratio as $key => $ratio) {
							//modification de la taille du ratio si l'image est plus petite que ratio
							if($ratio > $size[0] && $ratio > $size[1]) {
								if($size[0] > $size[1])  $ratio = $size[0];
								else $ratio = $size[1];
							}
							//largeur > hauteur
							if($size[0] > $size[1]) {

								$hauteur = $max_y = $ratio;
								$largeur = round((($ratio*$size[0])/$size[1]),0);

								//decalage
								$x = round((($ratio-$largeur)/2),0);
								$y = 0;

								$max_x = $ratio;
							}

							//image carrée
							if($size[0] == $size[1]) {
								$hauteur = $ratio;
								$largeur = $ratio;
								$x = 0;
								$y = 0;
								$max_x = $max_y = $ratio;
							}

							//hauteur > largeur
							//La hauteur max sera de 130% du ratio
							if($size[0] < $size[1]) {
								$largeur = $max_x = $ratio;
								$hauteur = round((($ratio/$size[0])*$size[1]),0);

								//decalage
								$x = 0;
								$y = 0;

								//taille image
								if($size[1] > (1.3 * $size[0])){
									$max_y = $ratio * 1.3;
								}else{
									$max_y = round((($ratio/$size[0])*$size[1]),0);
								}
							}

							//On cree l'image
							$image = imagecreatetruecolor($max_x,$max_y);
							imagecopyresized($image,$img_src,$x,$y,0,0,$largeur,$hauteur,$size[0],$size[1]);
							imagepng($image,dirname($path)."/photo".$tab_ratio[$key]."_".$time.".png");

							imagedestroy($image);
						}
						unlink($path);
						$ct->fields['photo'] = "_".$time;
						$ct->save();
					}
					$lstTags = explode(',',$tags);
					$tmspt = dims_createtimestamp();
					foreach($lstTags as $tag){
						if ($tag != '' && $tag > 0) {
							$db->query("INSERT INTO dims_tag_globalobject VALUES (:tag, :idglobalobject, :timestamp)", array(
								':tag' => array('type' => PDO::PARAM_INT, 'value' => $tag),
								':idglobalobject' => array('type' => PDO::PARAM_INT, 'value' => $ct->fields['id_globalobject']),
								':timestamp' => array('type' => PDO::PARAM_INT, 'value' => $tmspt),
							));
						}
					}

					if (isset($_SESSION['desktopv2']['opportunity']['tiers_selected']) && $_SESSION['desktopv2']['opportunity']['tiers_selected'] > 0){
						require_once DIMS_APP_PATH.'modules/system/class_tiers_contact.php';
						$tiersCt = new tiersct();
						$tiersCt->init_description();
						$tiersCt->fields['id_tiers'] = $_SESSION['desktopv2']['opportunity']['tiers_selected'];
						$tiersCt->fields['id_contact'] = $ct->fields['id'];
						$tiersCt->fields['type_lien'] = $type_lien;
						$tiersCt->fields['function'] = $fonction;
						$tiersCt->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
						$tiersCt->fields['id_user'] = $_SESSION['dims']['userid'];
						$tiersCt->fields['date_create'] = dims_createtimestamp();
						$tiersCt->fields['date_deb'] = dims_createtimestamp();
						$tiersCt->fields['link_since'] = 0;
						$tiersCt->fields['link_level'] = 2;
						$tiersCt->fields['id_ct_user_create'] = $_SESSION['dims']['userid'];
						$tiersCt->save();
						// unset($_SESSION['desktopv2']['opportunity']['tiers_selected']);
					}

					$_SESSION['desktopv2']['opportunity']['ct_added'][$ct->fields['id']]['id'] = $ct->fields['id'];
					if (isset($_SESSION['desktopv2']['opportunity']['tiers_selected'])) {
						$_SESSION['desktopv2']['opportunity']['ct_added'][$ct->fields['id']]['src'] = $_SESSION['desktopv2']['opportunity']['tiers_selected'];
					}
					else {
						$_SESSION['desktopv2']['opportunity']['ct_added'][$ct->fields['id']]['src'] = -1;
					}
				}
			}
			$lstTiers = $desktop->constructLstTiersFromCt(array(),$_SESSION['desktopv2']['opportunity']['ct_added']);
			foreach($lstTiers as $tiers)
				$tiers->display(_DESKTOP_TPL_LOCAL_PATH.'/opportunity/added_contact_tiers.tpl.php');
			die();
			break;
		case 'opportunity_save_contact_existing':
			ob_clean();
			$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
			$idtiers = dims_load_securvalue('id_tiers',dims_const::_DIMS_NUM_INPUT,true,true,true);
			if ($id != '' && $id > 0){
				$ct = new contact();
				$ct->open($id);
				foreach ($_GET as $key => $val){
					if ($key != 'dims_op' && $key != 'action' && $key != 'type_lien' && $key != 'function' && $key != 'id' && $key != 'id_tiers'){
						$elem = base64_decode(trim(dims_load_securvalue($key,dims_const::_DIMS_CHAR_INPUT,true,true,true)));
						$ct->fields[$key] = $elem;
					}
				}
				$ct->save();
				if(isset($idtiers) && $idtiers>0){
					$restc=$db->query('	SELECT	*
								FROM	dims_mod_business_tiers_contact
								WHERE	id_tiers=:idtiers
								AND	id_contact=:idcontact', array(
						':idtiers' => array('type' => PDO::PARAM_INT, 'value' => $idtiers),
						':idcontact' => array('type' => PDO::PARAM_INT, 'value' => $id),
					));
					require_once DIMS_APP_PATH.'modules/system/class_tiers_contact.php';
					$tiersCt = new tiersct();
					if ($cttiers = $db->fetchrow($restc)) {
						$tiersCt->openWithFields($cttiers);
					}else{
						$tiersCt->init_description();
						$tiersCt->fields['date_create'] = dims_createtimestamp();
						$tiersCt->fields['id_tiers'] = $idtiers;
						$tiersCt->fields['id_contact'] = $id;
						$tiersCt->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
						$tiersCt->fields['id_user'] = $_SESSION['dims']['userid'];
						$tiersCt->fields['link_level'] = 2;
						$tiersCt->fields['link_since'] = 0;
						$tiersCt->fields['id_user_create'] = $_SESSION['dims']['userid'];
					}
					$ty_link = base64_decode(trim(dims_load_securvalue('type_lien',dims_const::_DIMS_CHAR_INPUT,true,true,true)));
					if(!isset($ty_link) || empty($ty_link)){
						$ty_link = $_SESSION['cste']['_DIMS_LABEL_EMPLOYEUR'];
					}
					$tiersCt->fields['type_lien'] = $ty_link;
					$tiersCt->fields['function'] = base64_decode(trim(dims_load_securvalue('function',dims_const::_DIMS_CHAR_INPUT,true,true,true)));
					$tiersCt->save();
				}
				$_SESSION['desktopv2']['opportunity']['ct_added'][$id]['id'] = $id;
				$_SESSION['desktopv2']['opportunity']['ct_added'][$id]['src'] = $idtiers;
			}
			?>
			<script type="text/javascript">
				$(document).ready(function(){
					addContactInOpportunity(<? echo "'$id','$idtiers'"; ?>);
				});
			</script>
			<?
			die();
			break;
		case 'opportunity_company_default_form':
			ob_clean();
		$id_tiers = dims_load_securvalue('id_tiers',dims_const::_DIMS_NUM_INPUT,true,true,true);
			$tiers = new tiers();
			$tiers->init_description();

			if ($id_tiers>0) {
				$tiers->open($id_tiers);
			}

			$tiers->display(_DESKTOP_TPL_LOCAL_PATH.'/new_company/new_company_form.tpl.php');
			die();
			break;
		case 'opportunity_select_company':
			ob_clean();
			$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
			if ($id != '' && $id > 0){
				$tiers = new tiers();
				if ($tiers->open($id)){
					$_SESSION['desktopv2']['opportunity']['tiers_selected'] = $tiers->fields['id'];

					$employees = $tiers->getContactsLinkedByType('_DIMS_LABEL_EMPLOYEUR');
					$tiers->setLightAttribute('employees', $employees);
					$tiers->display(_DESKTOP_TPL_LOCAL_PATH.'/new_company/selected_company.tpl.php');
					?>
					<script type="text/javascript">
						$(document).ready(function(){
							dims_xmlhttprequest_todiv("/admin.php","dims_op=desktopv2&action=opportunity_switch_to_form","",'zone_form_selected_company');
							hide_new_company_form();

							<?php
							/*
							if (!sizeof($employees)) {
								?>
								show_new_company_form();
								<?php
							}
							else {
								?>
								hide_new_company_form();
								<?php
							}
							*/
							?>
						});
					</script>
					<?
				}
			}elseif(isset($_SESSION['desktopv2']['opportunity']['tiers_selected']) && $_SESSION['desktopv2']['opportunity']['tiers_selected'] > 0){
				$tiers = new tiers();
				if ($tiers->open($_SESSION['desktopv2']['opportunity']['tiers_selected'])){
					$employees = $tiers->getContactsLinkedByType('_DIMS_LABEL_EMPLOYEUR');
					$tiers->setLightAttribute('employees', $employees);
					$tiers->display(_DESKTOP_TPL_LOCAL_PATH.'/new_company/selected_company.tpl.php');
					?>
					<script type="text/javascript">
						$(document).ready(function(){
							dims_xmlhttprequest_todiv("/admin.php","dims_op=desktopv2&action=opportunity_switch_to_form","",'zone_form_selected_company');
							hide_new_company_form();
						});
					</script>
					<?
				}
			}
			die();
			break;
		case 'opportunity_unselect_company' :
			ob_clean();
			$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
			if ($id != '' && $id > 0 && $_SESSION['desktopv2']['opportunity']['tiers_selected'] == $id)
				unset($_SESSION['desktopv2']['opportunity']['tiers_selected']);
			die();
			break;
		case 'selecte_tag_opp':
			ob_clean();
			$id = dims_load_securvalue('id',dims_const::_DIMS_CHAR_INPUT,true,true,true);
			$class = dims_load_securvalue('class',dims_const::_DIMS_CHAR_INPUT,true,true,true);
			unset($_SESSION['desktopv2']['opportunity']['sel_tags'][$class]);
			if ($id != 'null'){
				$_SESSION['desktopv2']['opportunity']['sel_tags'][$class] = explode(',',$id);
			}
			die();
			break;
		case 'add_new_tag':
			ob_clean();
			$val = trim(dims_load_securvalue('val',dims_const::_DIMS_CHAR_INPUT,true,true,true));
			if ($val != ''){
				$tag = new tag();
				$tag->init_description();
				$tag->fields['private'] = 0;
				$tag->fields['id_user'] = $_SESSION['dims']['userid'];
				$tag->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
				$tag->fields['type'] = 0;
				$tag->fields['group'] = 0;
				$tag->fields['tag'] = $val;
				if ($tag->save()){
					$elem = array();
					$elem['id'] = $tag->fields['id'];
					$elem['tag'] = $tag->fields['tag'];
					die(json_encode($elem));
				}
			}
			die();
			break;
		case 'add_new_tag_categ':
			ob_clean();
			$val = trim(dims_load_securvalue('val',dims_const::_DIMS_CHAR_INPUT,true,true,true));
			$id_cat = dims_load_securvalue('id_cat',dims_const::_DIMS_NUM_INPUT,true,true,true);
			$typeobj = dims_load_securvalue('typeobj',dims_const::_DIMS_NUM_INPUT,true,true,true);
			$idSel = 0;
			if ($val != ''){
				$tag = new tag();
				$tag->init_description();
				$tag->fields['private'] = 0;
				$tag->fields['id_user'] = $_SESSION['dims']['userid'];
				$tag->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
				$tag->fields['type'] = 0;
				$tag->fields['id_category'] = $id_cat;
				$tag->fields['group'] = 0;
				$tag->fields['tag'] = $val;
				$tag->save();
				$idSel = $tag->get('id');
			}
			$options = "";
			$lstCateg = tag_category::getForObject($typeobj);
			$addTagOptions = '<option value="0">'.$_SESSION['cste']['_UNCATEGORIZED'].'</option>';
			foreach($lstCateg as $cat){
				$lstTag = $cat->getTagLink();
				if(count($lstTag)){
					$options .= '<optgroup label="'.$cat->get('label').'">';
					foreach($lstTag as $tag){
						if($idSel == $tag->get('id')){
							$options .= '<option selected=true value="'.$tag->get('id').'">'.$tag->get('tag').'</option>';
						}else{
							$options .= '<option value="'.$tag->get('id').'">'.$tag->get('tag').'</option>';
						}
					}
					$options .= '</optgroup>';
				}
			}
			$lstTag = tag::find_by(array('id_workspace'=>$_SESSION['dims']['workspaceid'], 'id_category'=>0),' ORDER BY tag ');
			if(count($lstTag)){
				$options .= '<optgroup label="'.$_SESSION['cste']['_UNCATEGORIZED'].'">';
				foreach($lstTag as $tag){
					if($idSel == $tag->get('id')){
						$options .= '<option selected=true value="'.$tag->get('id').'">'.$tag->get('tag').'</option>';
					}else{
						$options .= '<option value="'.$tag->get('id').'">'.$tag->get('tag').'</option>';
					}
				}
				$options .= '</optgroup>';
			}
			die($options);
			break;
		case 'add_new_group_ct':
			ob_clean();
			require_once DIMS_APP_PATH."modules/system/class_ct_group.php";
			$val = trim(dims_load_securvalue('val',dims_const::_DIMS_CHAR_INPUT,true,true,true));
			if ($val != ''){
				$type = new ct_group();
				$type->init_description();
				$type->set('id_workspace',$_SESSION['dims']['workspaceid']);
				$type->set('id_user_create',$_SESSION['dims']['userid']);
				$type->set('date_create',dims_createtimestamp());
				$type->set('label',$val);
				$type->save();
				$idSel = $type->get('id');
			}
			$groups = ct_group::find_by(array('id_workspace'=>$_SESSION['dims']['workspaceid']),' ORDER BY label ');
			$options = "";
			foreach($groups as $gr){
				if($idSel == $gr->get('id')){
					$options .= '<option selected=true value="'.$gr->get('id').'">'.$gr->get('label').'</option>';
				}else{
					$options .= '<option value="'.$gr->get('id').'">'.$gr->get('label').'</option>';
				}
			}
			die($options);
			break;
		case 'add_new_city':
			ob_clean();
			$val = trim(dims_load_securvalue('val',dims_const::_DIMS_CHAR_INPUT,true,true,true));
			$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
			if ($val != '' && $id != '' && $id > 0){
				require_once DIMS_APP_PATH.'modules/system/class_city.php';
				$city = new city();
				$city->init_description();
				$city->fields['id_country'] = $id;
				$city->fields['label'] = $val;
				if ($city->save()){
					$elem = array();
					$elem['id'] = $city->fields['id'];
					$elem['label'] = $city->fields['label'];
					die(json_encode($elem));
				}
			}
			die();
			break;
		case 'add_new_sector' :
			ob_clean();
			$val = trim(dims_load_securvalue('val',dims_const::_DIMS_CHAR_INPUT,true,true,true));
			if ($val != ''){
				require_once DIMS_APP_PATH.'modules/system/opportunity/class_sector.php';
				$sector = new opportunity_sector();
				$sector->init_description();
				$sector->fields['label'] = $val;
				if ($sector->save()){
					$elem = array();
					$elem['id'] = $sector->fields['id'];
					$elem['label'] = $sector->fields['label'];
					die(json_encode($elem));
				}
			}
			die();
			break;
		case 'add_new_type' :
			ob_clean();
			$val = trim(dims_load_securvalue('val',dims_const::_DIMS_CHAR_INPUT,true,true,true));
			if ($val != ''){
				require_once DIMS_APP_PATH.'modules/system/opportunity/class_type.php';
				$type = new opportunity_type();
				$type->init_description();
				$type->fields['label'] = $val;
				if ($type->save()){
					$elem = array();
					$elem['id'] = $type->fields['id'];
					$elem['label'] = $type->fields['label'];
					die(json_encode($elem));
				}
			}
			die();
			break;
		case 'get_all_city_from':
			ob_clean();
			$id = dims_load_securvalue('val',dims_const::_DIMS_NUM_INPUT,true,true,true);
			$lst = array(array('id'=>'dims_nan', 'label'=>''));
			if($id != '' && $id > 0){
				require_once DIMS_APP_PATH.'modules/system/class_country.php';
				$country = new country();
				$country->open($id);
				foreach($country->getAllCity(10) as $city){
					$elem = array();
					$elem['id'] = $city->get('id');
					$elem['label'] = $city->get('label');
					$lst[] = $elem;
				}
			}
			echo json_encode($lst);
			die();
			break;
		case 'opportunity_refresh_city':
			ob_clean();
			$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
			$ref = dims_load_securvalue('ref',dims_const::_DIMS_CHAR_INPUT,true,true,true);
			$sel = dims_load_securvalue('sel',dims_const::_DIMS_NUM_INPUT,true,true,true);
			?>
			<select data-placeholder="Start to type the name of city" rel="requis" id="<? echo $ref; ?>" type="text" name="city" class="<? echo $ref; ?>" style="min-width:250px;">
				<option value=""></option>
			<?
			if ($id != '' && $id > 0){
				require_once DIMS_APP_PATH.'modules/system/class_country.php';
				$country = new country();
				$country->open($id);
				foreach($country->getAllCity() as $city){
					if($sel == $city->getId())
						echo '<option value="'.$city->getId().'" selected="true">'.$city->fields['label'].'</option>';
					else
						echo '<option value="'.$city->getId().'">'.$city->fields['label'].'</option>';
				}
			}
			?>
			</select>
			<script type="text/javascript">
				$(document).ready(function(){
					$("select#<? echo $ref; ?>").trigger("liszt:updated");
				});
			</script>
			<?
			die();
			break;
		case 'opportunity_switch_to_vcard':
			ob_clean();
			include(_DESKTOP_TPL_LOCAL_PATH.'/new_company/new_company_contact_from_vcard.tpl.php');
			die();
			break;
		case 'opportunity_switch_to_form':
			ob_clean();
			include(_DESKTOP_TPL_LOCAL_PATH.'/new_company/new_company_contact.tpl.php');
			die();
			break;
		case 'opportunity_switch_vcard_form':
			ob_clean();
			$type = dims_load_securvalue('type',dims_const::_DIMS_CHAR_INPUT,true,true,true);
			switch($type){
				case 'existing':
					include(_DESKTOP_TPL_LOCAL_PATH.'/new_company/new_contact_search_vcard.tpl.php');
					break;
				case 'computer':
				default:
					break;
			}
			die();
			break;
		case 'opportunity_switch_vcard_form2':
		case 'opportunity_unload_excel':
			ob_clean();
			$type = dims_load_securvalue('type',dims_const::_DIMS_CHAR_INPUT,true,true,true);
			switch($type){
				case 'existing':
					break;
				case 'computer':
				default:
					include(_DESKTOP_TPL_LOCAL_PATH.'/new_company/new_contact_from_computer_vcard.tpl.php');
					break;
			}
			die();
			break;
			ob_clean();
			$name = dims_load_securvalue('name',dims_const::_DIMS_CHAR_INPUT,true,true,true);
			if ($name != '' && $db->tableexist($name)){
				// FIXME : Allow dropping ANY table from database !
				$del = "DROP TABLE IF EXISTS `".str_replace('`', '', $name)."`";
				$db->query($del);
				unset($_SESSION['desktopv2']['opportunity']['excel']['created']);
				unset($_SESSION['desktopv2']['opportunity']['excel']['updated']);
				unset($_SESSION['desktopv2']['opportunity']['excel']['tupdated']);
				unset($_SESSION['desktopv2']['opportunity']['excel']['tcreated']);
			}
			die();
			break;
		case 'opportunity_load_excel':
			ob_clean();
			$id_popup = dims_load_securvalue('id_popup',dims_const::_DIMS_CHAR_INPUT,true,true,true);
			$file = dims_load_securvalue('file',dims_const::_DIMS_CHAR_INPUT,true,true,true);
			if ($file != '' && file_exists(realpath('.').$file)){
				$db = dims::getInstance()->getDb();
				require_once DIMS_APP_PATH.'modules/system/desktopV2/include/class_import_excel.php';
				ini_set('max_execution_time',-1);
				ini_set('memory_limit','1024M');
				$excelImport = new dims_excel_import(realpath('.').$file);
				$excelImport->setStartRow(2);
				$excelImport->import();
				$temptable=$excelImport->getTableTemp();
				$db->query("ALTER TABLE `".str_replace('`', '', $temptable)."` ADD `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST");
				$db->query("ALTER TABLE `".str_replace('`', '', $temptable)."` ADD `status` INT( 2 ) NOT NULL DEFAULT '".dims_const_desktopv2::_DESKTOP_V2_EXCEL_NEW."'");
				unlink(realpath('.').$file);

				$_SESSION['desktopv2']['opportunity']['excel']['updated'] = array();
				$_SESSION['desktopv2']['opportunity']['excel']['created'] = array();
				$_SESSION['desktopv2']['opportunity']['excel']['tupdated'] = array();
				$_SESSION['desktopv2']['opportunity']['excel']['tcreated'] = array();
				?>
				<div>
					<div class="actions">
						<a href="Javascript: void(0);" onclick="Javascript:dims_xmlhttprequest('/admin.php','dims_op=desktopv2&action=opportunity_unload_excel&name=<? echo $temptable; ?>'); $('#opp_import_excel').uploadifyCancel(); dims_closeOverlayedPopup('<?php echo $id_popup; ?>');">
							<img src="<? echo _DESKTOP_TPL_PATH; ?>/gfx/common/close.png" />
						</a>
					</div>
					<h2><? echo $_SESSION['cste']['_IMPORT_DOWNLOAD_FILE']; ?></h2>
					<div style="max-height:460px;overflow-y:auto;">
						<?
						// liste des tiers mis à jour
						$sel = "SELECT		tp.*, t.*
								FROM		$temptable as tp
								INNER JOIN	dims_mod_business_tiers as t
								ON			t.intitule LIKE tp.row_7
								AND			t.inactif = 0
								WHERE		tp.row_7 != ''
								AND			tp.status = :status
								GROUP BY	tp.row_7";
						$res = $db->query($sel, array(
							':status' => dims_const_desktopv2::_DESKTOP_V2_EXCEL_NEW
						));
						$tAlready = array();
						$tAlready[] = 0;
						foreach($db->split_resultset($res) as $r){
							$tiers = new tiers();
							$tiers->init_description();
							$tiers->openWithFields($r['t']);
							$tiers->setLightAttribute('new_data',$r['tp']);
							$_SESSION['desktopv2']['opportunity']['excel']['tupdated'][$r['tp']['row_7']] = $tiers;
							$tAlready[] = $r['tp']['id'];
						}
						// liste des tiers créés
						$params = array();
						$sel = "SELECT		*
								FROM		$temptable
								WHERE		id NOT IN (".$db->getParamsFromArray($tAlready, 'id', $params).")
								AND		row_7 != ''
								AND		status = :status
								GROUP BY	row_7";
						$params[':status'] = dims_const_desktopv2::_DESKTOP_V2_EXCEL_NEW;
						$res = $db->query($sel, $params);
						while ($r = $db->fetchrow($res))
							$_SESSION['desktopv2']['opportunity']['excel']['tcreated'][$r['row_7']] = $r;
						// liste des contacts mis à jour
						$already = array();
						$already[] = 0;
						$sel = "SELECT		tp.*, ct.*
								FROM		$temptable as tp
								INNER JOIN	dims_mod_business_contact ct
								ON			ct.firstname LIKE tp.row_2
								AND			ct.lastname LIKE tp.row_1
								AND			ct.inactif = 0
								WHERE		tp.status = :status
								ORDER BY	tp.row_7, tp.row_1, tp.row_2";
						$res = $db->query($sel, array(
							':status'	=> dims_const_desktopv2::_DESKTOP_V2_EXCEL_NEW
						));
						$testTable = ($db->numrows($res) > 0);
						$tr = 'trl1';
						if ($testTable){
							?>
							<table cellpadding="0" cellspacing="0" style="width:100%;">
								<tr>
									<th colspan="5">
										<? echo $_SESSION['cste']['_CONTACTS_WHICH_ARE_UPDATED']." : ".$db->numrows($res); ?>
									</th>
								</tr>
								<?
								$company = '';
								foreach ($db->split_resultset($res) as $r){
									$ct = new contact();
									$ct->openWithFields($r['ct']);
									$ct->setLightAttribute('new_data',$r['tp']);
									$_SESSION['desktopv2']['opportunity']['excel']['updated'][] = $ct;
									$already[] = $r['tp']['id'];
									if (($company != trim($r['tp']['row_7']) && trim($r['tp']['row_7']) != '') || (trim($r['tp']['row_7']) == '' && $company != $_SESSION['cste']['_IMPORT_UNKNOWN_TIER'])){
										if (trim($r['tp']['row_7']) != '')
											$company = trim($r['tp']['row_7']);
										else
											$company = $_SESSION['cste']['_IMPORT_UNKNOWN_TIER'];
										?>
										<tr>
											<td colspan="5" style="background:#383838;height:1px;">
											</td>
										</tr>
										<tr>
											<td colspan="5" style="font-weight:bold;">
												<?
												echo $company;
												if (isset($_SESSION['desktopv2']['opportunity']['excel']['tupdated'][$company]))
													echo " (".$_SESSION['cste']['_DIMS_LABEL_UPDATE'].")";
												else
													echo " (".$_SESSION['cste']['_IMPORT_TAB_NEW_COMPANY'].")";
												?>
											</td>
										</tr>
										<tr>
											<td colspan="5" style="background:#383838;height:1px;">
											</td>
										</tr>
										<?
									}
									?>
									<tr style="vertical-align:top;" class="<? echo $tr; ?>">
										<td>
											<? echo $r['tp']['row_6']." ".$r['tp']['row_2']." ".$r['tp']['row_1']; ?><br />
											<? echo $r['tp']['row_3']; ?>
										</td>
										<td>
											<? echo $r['tp']['row_10']."<br />".$r['tp']['row_11']." ".$r['tp']['row_12']; ?>
										</td>
										<td>
											<? echo $r['tp']['row_13']; ?>
										</td>
										<td>
											<? echo $r['tp']['row_14']; ?>
										</td>
										<td>
											<? echo $r['tp']['row_15']; ?>
										</td>
									</tr>
									<?
									$tr = ($tr == 'trl1') ? 'trl2' : 'trl1';
								}
								?>
							<?
						}
						// liste des contacts créés
						$params = array();
						$sel = "SELECT		tp.*
								FROM		$temptable as tp
								WHERE		tp.status = 1
								AND		tp.id NOT IN (".$db->getParamsFromArray($already, 'id', $params).")
								ORDER BY	tp.row_7, tp.row_1, tp.row_2";
						$res = $db->query($sel, $params);
						if ($db->numrows($res) > 0){
							if (!$testTable){
							?>
							<table cellpadding="0" cellspacing="0" style="width:100%;">
							<?
							}
							?>
								<tr>
									<th colspan="5">
										<? echo $_SESSION['cste']['_CONTACT_CREATED_THANKS_IMPORT']." : ".$db->numrows($res); ?>
									</th>
								</tr>
								<?
								$company = '';
								while ($r = $db->fetchrow($res)){
									$_SESSION['desktopv2']['opportunity']['excel']['created'][] = $r;
									if (($company != trim($r['row_7']) && trim($r['row_7']) != '') || (trim($r['row_7']) == '' && $company != $_SESSION['cste']['_IMPORT_UNKNOWN_TIER'])){
										if (trim($r['row_7']) != '')
											$company = trim($r['row_7']);
										else
											$company = $_SESSION['cste']['_IMPORT_UNKNOWN_TIER'];
										?>
										<tr>
											<td colspan="5" style="background:#383838;height:1px;">
											</td>
										</tr>
										<tr>
											<td colspan="5" style="font-weight:bold;">
												<?
												echo $company;
												if (isset($_SESSION['desktopv2']['opportunity']['excel']['tupdated'][$company]))
													echo " (".$_SESSION['cste']['_DIMS_LABEL_UPDATE'].")";
												else
													echo " (".$_SESSION['cste']['_IMPORT_TAB_NEW_COMPANY'].")";
												?>
											</td>
										</tr>
										<tr>
											<td colspan="5" style="background:#383838;height:1px;">
											</td>
										</tr>
										<?
									}
									?>
									<tr style="vertical-align:top;" class="<? echo $tr; ?>">
										<td>
											<? echo $r['row_6']." ".$r['row_2']." ".$r['row_1']; ?><br />
											<? echo $r['row_3']; ?>
										</td>
										<td>
											<? echo $r['row_10']."<br />".$r['row_11']." ".$r['row_12']; ?>
										</td>
										<td>
											<? echo $r['row_13']; ?>
										</td>
										<td>
											<? echo $r['row_14']; ?>
										</td>
										<td>
											<? echo $r['row_15']; ?>
										</td>
									</tr>
									<?
									$tr = ($tr == 'trl1') ? 'trl2' : 'trl1';
								}
								?>
							</table>
							<?
						}elseif($testTable){
							?>
							</table>
							<?
						}
						?>
					</div>
					<?
					echo dims_create_button($_SESSION['cste']['_DIMS_LABEL_CANCEL'],'close',"Javascript:dims_xmlhttprequest('/admin.php','dims_op=desktopv2&action=opportunity_unload_excel&name=$temptable'); $('#opp_import_excel').uploadifyCancel(); dims_closeOverlayedPopup('$id_popup');",'','float:right;margin:10px;');
					echo dims_create_button($_SESSION['cste']['_IMPORT_THE_FILE'],'check',"Javascript:dims_xmlhttprequest_todiv('/admin.php','dims_op=desktopv2&action=opportunity_import_excel&name=$temptable','','div_list_added'); $('#opp_import_excel').uploadifyCancel(); dims_closeOverlayedPopup('$id_popup');",'','float:right;margin:10px;');
					?>
				</div>
				<?
			}else{
				?>
				<script type="text/javascript">
					$('#opp_import_excel').uploadifyCancel();
					dims_closeOverlayedPopup('<?php echo $id_popup; ?>');
				</script>
				<?
			}
			die();
			break;
		case 'opportunity_import_excel':
			ob_clean();
			$name = dims_load_securvalue('name',dims_const::_DIMS_CHAR_INPUT,true,true,true);
			if ($name != ''){
				// tiers à mettre à jour
				$lstTiers = array();
				if (isset($_SESSION['desktopv2']['opportunity']['excel']['tupdated']) && count($_SESSION['desktopv2']['opportunity']['excel']['tupdated']) > 0){
					foreach($_SESSION['desktopv2']['opportunity']['excel']['tupdated'] as $tiers){
						$data2 = $tiers->getLightAttribute('new_data');
						$tiers->fields['adresse'] = $data2['row_10'];
						$tiers->fields['codepostal'] = $data2['row_11'];
						$tiers->fields['ville'] = $data2['row_12'];
						$tiers->fields['telephone'] = $data2['row_13'];
						$tiers->fields['telecopie'] = $data2['row_15'];
						$tiers->fields['pays'] = $data2['row_5'];
						$tiers->save();
						$tiers->setLightAttribute('id_country', country::getCountryFromLabel($data2['row_5']));
						$lstTiers[$tiers->fields['intitule']] = $tiers;
					}
				}

				// tiers à créer
				if (isset($_SESSION['desktopv2']['opportunity']['excel']['tcreated']) && count($_SESSION['desktopv2']['opportunity']['excel']['tcreated']) > 0){
					foreach($_SESSION['desktopv2']['opportunity']['excel']['tcreated'] as $data2){
						$tiers = new tiers();
						$tiers->init_description();
						$tiers->setugm();
						$tiers->fields['intitule'] = $data2['row_7'];
						$tiers->fields['date_creation'] = dims_createtimestamp();
						$tiers->fields['adresse'] = $data2['row_10'];
						$tiers->fields['codepostal'] = $data2['row_11'];
						$tiers->fields['ville'] = $data2['row_12'];
						$tiers->fields['telephone'] = $data2['row_13'];
						$tiers->fields['telecopie'] = $data2['row_15'];
						$tiers->fields['pays'] = $data2['row_5'];
						$tiers->save();
						$tiers->setLightAttribute('id_country', country::getCountryFromLabel($data2['row_5']));
						$lstTiers[$tiers->fields['intitule']] = $tiers;
					}
				}

				require_once DIMS_APP_PATH."modules/system/class_tiers_contact.php";
				require_once DIMS_APP_PATH."modules/system/class_matrix.php";
				// contacts à mettre à jour
				if (isset($_SESSION['desktopv2']['opportunity']['excel']['updated']) && count($_SESSION['desktopv2']['opportunity']['excel']['updated']) > 0){
					$lstUpdated = array();
					$lstUpdated[] = 0;
					foreach($_SESSION['desktopv2']['opportunity']['excel']['updated'] as $updated){
						$data = $updated->getLightAttribute('new_data');
						if (trim($updated->fields['address']) == ''){
							$updated->fields['address'] = $data['row_10'];
							$updated->fields['postalcode'] = $data['row_11'];
							$updated->fields['city'] = $data['row_12'];
						}
						$updated->fields['mobile'] = $data['row_14'];
						$updated->fields['email'] = $data['row_3'];
						if (trim($updated->fields['phone']) == '') $updated->fields['phone'] = $data['row_13'];
						if (trim($updated->fields['fax']) == '') $updated->fields['fax'] = $data['row_15'];
						if (trim($updated->fields['civilite']) == '') $updated->fields['civilite'] = $data['row_6'];


						$updated->save();
						$matrice = new matrix();
						$lstUpdated[] = $data['id'];
						if (isset($lstTiers[$data['row_7']])){
							$tc = new tiersct();
							$tc->init_description();
							$tc->fields['id_tiers'] = $lstTiers[$data['row_7']]->fields['id'];
							$tc->fields['id_contact'] = $updated->fields['id'];
							$tc->fields['type_lien'] = $_SESSION['cste']['_DIMS_LABEL_EMPLOYEUR'];
							$tc->fields['function'] = $data['row_9'];
							$tc->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
							$tc->fields['id_user'] = $_SESSION['dims']['userid'];
							$tc->fields['date_create'] = dims_createtimestamp();
							$tc->fields['link_level'] = 2;
							$tc->fields['link_since'] = 0;
							$tc->fields['id_user_create'] = $_SESSION['dims']['userid'];
							$tc->fields['commentaire'] = $data['row_8'];
							$tc->save();

							if (!$matrice->open(0,0,$lstTiers[$data['row_7']]->fields['id_globalobject'],$updated->fields['id_globalobject'],0,$lstTiers[$data['row_7']]->getLightAttribute('id_country'),$data['row_4'],0)){
								$matrice->fields['id_tiers'] = $lstTiers[$data['row_7']]->fields['id_globalobject'];
								$matrice->fields['id_contact'] = $updated->fields['id_globalobject'];
								$matrice->fields['id_country'] = $lstTiers[$data['row_7']]->getLightAttribute('id_country');
								$matrice->fields['year'] = $data['row_4'];
							}
						}else{
							$id_country = country::getCountryFromLabel($data['row_5']);
							if (!$matrice->open(0,0,0,$updated->fields['id'],0,$id_country,$data['row_4'],0)){
								$matrice->fields['id_contact'] = $updated->fields['id_globalobject'];
								$matrice->fields['id_country'] = $id_country;
								$matrice->fields['year'] = $data['row_4'];
							}
						}
						$matrice->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
						$matrice->fields['timestp_modify'] = dims_createtimestamp();
						$matrice->save();
						$_SESSION['desktopv2']['opportunity']['ct_added'] = $updated->fields['id'];

					}
					$params = array();
					$params['status'] = dims_const_desktopv2::_DESKTOP_V2_EXCEL_UPDATE;
					$db->query("UPDATE `".str_replace('`', '', $name)."`
								SET status = :status
								WHERE id IN (".$db->getParamsFromArray($lstUpdated, 'id', $params).")", $params);
				}

				$lstUpdated = array();
				$lstUpdated[] = 0;
				if (isset($_SESSION['desktopv2']['opportunity']['excel']['created']) && count($_SESSION['desktopv2']['opportunity']['excel']['created']) > 0){
					foreach($_SESSION['desktopv2']['opportunity']['excel']['created'] as $created){
						$ct = new contact();
						$ct->init_description();
						$ct->setugm();
						$ct->fields['lastname'] = $created['row_1'];
						$ct->fields['firstname'] = $created['row_2'];
						$ct->fields['civilite'] = $created['row_6'];
						$ct->fields['address'] = $created['row_10'];
						$ct->fields['postalcode'] = $created['row_11'];
						$ct->fields['city'] = $created['row_12'];
						$ct->fields['phone'] = $created['row_13'];
						$ct->fields['fax'] = $created['row_15'];
						$ct->fields['mobile'] = $created['row_14'];
						$ct->fields['email'] = $created['row_3'];
						$ct->save();

						$matrice = new matrix();
						$lstUpdated[] = $created['id'];
						if (isset($lstTiers[$created['row_7']])){
							$tc = new tiersct();
							$tc->init_description();
							$tc->fields['id_tiers'] = $lstTiers[$created['row_7']]->fields['id'];
							$tc->fields['id_contact'] = $ct->fields['id'];
							$tc->fields['type_lien'] = $_SESSION['cste']['_DIMS_LABEL_EMPLOYEUR'];
							$tc->fields['function'] = $created['row_9'];
							$tc->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
							$tc->fields['id_user'] = $_SESSION['dims']['userid'];
							$tc->fields['date_create'] = dims_createtimestamp();
							$tc->fields['link_level'] = 2;
							$tc->fields['link_since'] = 0;
							$tc->fields['id_user_create'] = $_SESSION['dims']['userid'];
							$tc->fields['commentaire'] = $created['row_8'];
							$tc->save();

							if (!$matrice->open(0,0,$lstTiers[$created['row_7']]->fields['id_globalobject'],$ct->fields['id_globalobject'],0,$lstTiers[$created['row_7']]->getLightAttribute('id_country'),$data['row_4'],0)){
								$matrice->fields['id_tiers'] = $lstTiers[$created['row_7']]->fields['id_globalobject'];
								$matrice->fields['id_contact'] = $ct->fields['id_globalobject'];
								$matrice->fields['id_country'] = $lstTiers[$created['row_7']]->getLightAttribute('id_country');
								$matrice->fields['year'] = $created['row_4'];
							}
						}else{
							$id_country = country::getCountryFromLabel($created['row_5']);
							if (!$matrice->open(0,0,0,$ct->fields['id'],0,$id_country,$created['row_4'],0)){
								$matrice->fields['id_contact'] = $ct->fields['id_globalobject'];
								$matrice->fields['id_country'] = $id_country;
								$matrice->fields['year'] = $created['row_4'];
							}
						}
						$matrice->fields['timestp_modify'] = dims_createtimestamp();
						$matrice->save();
						$_SESSION['desktopv2']['opportunity']['ct_added'] = $tc->fields['id'];
					}
					$params = array();
					$params[':status'] = dims_const_desktopv2::_DESKTOP_V2_EXCEL_UPDATE;
					$db->query("UPDATE `".str_replace('`', '', $name)."`
								SET status = :status
								WHERE id IN (".$db->getParamsFromArray($lstUpdated, 'id', $params).")", $params);
				}

				unset($_SESSION['desktopv2']['opportunity']['excel']['created']);
				unset($_SESSION['desktopv2']['opportunity']['excel']['updated']);
				unset($_SESSION['desktopv2']['opportunity']['excel']['tupdated']);
				unset($_SESSION['desktopv2']['opportunity']['excel']['tcreated']);
			}
			$lstTiers = $desktop->constructLstTiersFromCt(array(),$_SESSION['desktopv2']['opportunity']['ct_added']);
			foreach($lstTiers as $tiers)
				$tiers->display(_DESKTOP_TPL_LOCAL_PATH.'/opportunity/added_contact_tiers.tpl.php');
			die();
			break;

		case 'view_geographic':
			ob_clean();
			if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {
				$mode = dims_load_securvalue('mode',dims_const::_DIMS_CHAR_INPUT,true,true,true);
				$_SESSION['dims']['map']['selector'] = $mode;
				require_once DIMS_APP_PATH.'/modules/system/class_matrix.php';
				$matrix = new matrix();
				$repartition = $matrix->getCountriesActivity($_SESSION['dims']['currentlang'], $mode);
				echo json_encode($repartition);
			}
			die();
			break;

		case 'opportunity_search_vcard':
			ob_clean();
			$val = dims_load_securvalue('label',dims_const::_DIMS_CHAR_INPUT,true,true,true);
			$lstVcards = $desktop->getVcard($val);
			foreach($lstVcards as $vcard)
				$vcard->display(_DESKTOP_TPL_LOCAL_PATH.'/new_company/new_contact_display_vcard.tpl.php');
			die();
			break;
		case 'displayInfoFromVcfExisting' :
			ob_clean();
			$num = dims_load_securvalue('num',dims_const::_DIMS_NUM_INPUT,true,true,true);
			$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
			$id_popup = dims_load_securvalue('id_popup',dims_const::_DIMS_CHAR_INPUT,true,true,true);
			if ($id != '' && $id > 0){
				$doc = new docfile();
				$doc->open($id);
				$datas = $doc->getParseVcf();
				if ($num == '' || $num == 0) $num = 1;
				if (isset($datas[$num-1])){
					$key = $num-1;
					$data = $datas[$num-1];
					?>
					<div>
						<div class="actions">
							<a href="Javascript: void(0);" onclick="javascript:dims_closeOverlayedPopup('<?php echo $id_popup; ?>');">
								<img src="<? echo _DESKTOP_TPL_PATH; ?>/gfx/common/close.png" />
							</a>
						</div>
						<h2><? echo $_SESSION['cste']['_IMPORT_DOWNLOAD_FILE']; ?></h2>
						<div style="max-height:350px;overflow-y:auto;">
							<div id="import_vcard_<? echo $key; ?>" style="border-bottom:1px solid #9E9E9E;float:left;width:680px;">
								<?
								include _DESKTOP_TPL_LOCAL_PATH.'/new_company/new_contact_display_popup_vcard.tpl.php';
								$res = $dims->dims_levenshtein($data['prenom'],$data['nom'],2);
								if (count($res) > 0){
									?>
									<table cellpadding="0" cellspacing="0">
									<?
									foreach($res as $key2 => $dispo){
										?>
										<tr>
											<td>
												<input <? if ($key2==0) echo "checked=true"; ?> type="radio" name="sel_ct_<? echo $key; ?>" value="<? echo $dispo['id_contact']; ?>" />
											</td>
											<td>
												<? echo $dispo['firstname']." ".$dispo['lastname']; ?>
											</td>
										</tr>
										<?
									}
									?>
									</table>
									<?
									echo dims_create_button($_SESSION['cste']['_DIMS_LABEL_UPDATE'],'pencil',"Javascript:mergeVcardExistingWithContact($id,$key);",'','float:right;margin:10px;');
								}
								echo dims_create_button($_SESSION['cste']['_DIMS_IMPORT_CT_NO_SAME'],'person',"Javascript:createContactFromVcardExisting($id,$key);",'','float:right;margin:10px;');
								?>
							</div>
						</div>
						<?
						echo dims_create_button($_SESSION['cste']['_DIMS_LABEL_CANCEL'],'close',"Javascript: dims_closeOverlayedPopup('$id_popup');",'','float:right;margin:10px;');
					?>
					</div>
					<?
				}
			}else{
				?>
				<script type="text/javascript">
					dims_closeOverlayedPopup('<?php echo $id_popup; ?>');
				</script>
				<?
			}
			die();
			break;
		case 'displayInfoFromVcf':
			ob_clean();
			$id_popup = dims_load_securvalue('id_popup',dims_const::_DIMS_CHAR_INPUT,true,true,true);
			$file = dims_load_securvalue('path',dims_const::_DIMS_CHAR_INPUT,true,true,true);
			if ($file != '' && file_exists(realpath('.').$file)){
				?>
				<div>
					<div class="actions">
						<a href="Javascript: void(0);" onclick="javascript:$('#opp_vcard').uploadifyCancel(); dims_closeOverlayedPopup('<?php echo $id_popup; ?>');">
							<img src="<? echo _DESKTOP_TPL_PATH; ?>/gfx/common/close.png" />
						</a>
					</div>
					<h2><? echo $_SESSION['cste']['_IMPORT_DOWNLOAD_FILE']; ?></h2>
					<div style="max-height:350px;overflow-y:auto;">
					<?
					$datas = docfile::parseExternalVcf(DIMS_APP_PATH.substr($file,1));
					foreach($datas as $key => $data){
						?>
						<div id="import_vcard_<? echo $key; ?>" style="border-bottom:1px solid #9E9E9E;float:left;width:680px;">
							<?
							include _DESKTOP_TPL_LOCAL_PATH.'/new_company/new_contact_display_popup_vcard.tpl.php';
							$res = $dims->dims_levenshtein($data['prenom'],$data['nom'],2);
							if (count($res) > 0){
								?>
								<table cellpadding="0" cellspacing="0">
								<?
								foreach($res as $key2 => $dispo){
									?>
									<tr>
										<td>
											<input <? if ($key2==0) echo "checked=true"; ?> type="radio" name="sel_ct_<? echo $key; ?>" value="<? echo $dispo['id_contact']; ?>" />
										</td>
										<td>
											<? echo $dispo['firstname']." ".$dispo['lastname']; ?>
										</td>
									</tr>
									<?
								}
								?>
								</table>
								<?
								echo dims_create_button($_SESSION['cste']['_DIMS_LABEL_UPDATE'],'pencil',"Javascript:mergeVcardWithContact('$file',$key);",'','float:right;margin:10px;');
							}
							echo dims_create_button($_SESSION['cste']['_DIMS_IMPORT_CT_NO_SAME'],'person',"Javascript:createContactFromVcardPc('$file',$key);",'','float:right;margin:10px;');
							?>
						</div>
						<?
					}
					?>
					</div>
					<?
					echo dims_create_button($_SESSION['cste']['_DIMS_LABEL_CANCEL'],'close',"Javascript: $('#opp_vcard').uploadifyCancel(); dims_closeOverlayedPopup('$id_popup');",'','float:right;margin:10px;');
					?>
				</div>
				<?
			}else{
				?>
				<script type="text/javascript">
					$('#opp_import_excel').uploadifyCancel();
					dims_closeOverlayedPopup('<?php echo $id_popup; ?>');
				</script>
				<?
			}
			die();
			break;
		case 'opp_existing_merge_vcard_with_ct':
			ob_clean();
			$id = dims_load_securvalue('id',dims_const::_DIMS_CHAR_INPUT,true,true,true);
			$num = dims_load_securvalue('num',dims_const::_DIMS_NUM_INPUT,true,true,true);
			$id_ct = dims_load_securvalue('id_ct',dims_const::_DIMS_NUM_INPUT,true,true,true);
			if ($id != '' && $id > 0){
				$doc = new docfile();
				$doc->open($id);
				$datas = $doc->getParseVcf();
				if (isset($datas[$num])){
					$data = $datas[$num];
					$ct = new contact();
					$ct->init_description();
					if ($id_ct != '' && $id_ct > 0)
						$ct->open($id_ct);
					$ct->fields['firstname'] = $data['prenom'];
					$ct->fields['lastname'] = $data['nom'];
					if (isset($data['title']))
						$ct->fields['civilite'] = $data['title'];
					if(isset($data['email']) && count($data['email']) > 0){
						$i = 1;
						foreach($data['email'] as $email){
							if ($i == 1)
								$ct->fields['email'] = $email;
							elseif($i <= 3)
								$ct->fields["email$i"] = $email;
							$i ++;
						}
					}
					if (isset($data['tel']['cell']))
						$ct->fields['mobile'] = $data['tel']['cell'];
					if (isset($data['tel']['work'])){
						$ct->fields['phone'] = $data['tel']['work'];
						if(isset($data['tel']['home']))
							$ct->fields['phone2'] = $data['tel']['home'];
					}elseif(isset($data['tel']['home']))
						$ct->fields['phone'] = $data['tel']['home'];
					if (isset($data['adr']['work'])){
						$ct->fields['address'] = $data['adr']['work']['rue'];
						$ct->fields['city'] = $data['adr']['work']['city'];
						$ct->fields['postalcode'] = $data['adr']['work']['cp'];
						$ct->fields['country'] = $data['adr']['work']['pays'];
					}elseif(isset($data['adr']['home'])){
						$ct->fields['address'] = $data['adr']['home']['rue'];
						$ct->fields['city'] = $data['adr']['home']['city'];
						$ct->fields['postalcode'] = $data['adr']['home']['cp'];
						$ct->fields['country'] = $data['adr']['home']['pays'];
					}
					$ct->save();
					if (isset($data['photo']) && file_exists($data['photo'])){
						$photo = substr($data['photo'],1);
						$time = time();
						$tab_ratio = array(0 => 60, 1 => 300);

						$ext = explode('.', $photo);
						$ext = strtolower($ext[count($ext)-1]);
						$path = DIMS_WEB_PATH.'data/photo_cts/contact_'.$ct->fields['id'];
						if (!file_exists($path)) mkdir($path, 0777,true);

						$path .= '/tmp_'.$ct->fields['id'].'.'.$ext;
						//on cree une image temporaire pour la redimensionner
						rename(realpath('.').$photo,$path);
						switch($ext) {
							case "png" :
								$img_src = imagecreatefrompng($path);
							break;
							case "jpg" :
							case "jpeg" :
								$img_src = imagecreatefromjpeg($path);
							break;
							case "gif" :
								$img_src = imagecreatefromgif($path);
							break;
						}
						$size = getimagesize($path);
						foreach($tab_ratio as $key => $ratio) {
							//modification de la taille du ratio si l'image est plus petite que ratio
							if($ratio > $size[0] && $ratio > $size[1]) {
								if($size[0] > $size[1])  $ratio = $size[0];
								else $ratio = $size[1];
							}
							//largeur > hauteur
							if($size[0] > $size[1]) {

								$hauteur = $max_y = $ratio;
								$largeur = round((($ratio*$size[0])/$size[1]),0);

								//decalage
								$x = round((($ratio-$largeur)/2),0);
								$y = 0;

								$max_x = $ratio;
							}

							//image carrée
							if($size[0] == $size[1]) {
								$hauteur = $ratio;
								$largeur = $ratio;
								$x = 0;
								$y = 0;
								$max_x = $max_y = $ratio;
							}

							//hauteur > largeur
							//La hauteur max sera de 130% du ratio
							if($size[0] < $size[1]) {
								$largeur = $max_x = $ratio;
								$hauteur = round((($ratio/$size[0])*$size[1]),0);

								//decalage
								$x = 0;
								$y = 0;

								//taille image
								if($size[1] > (1.3 * $size[0])){
									$max_y = $ratio * 1.3;
								}else{
									$max_y = round((($ratio/$size[0])*$size[1]),0);
								}
							}

							//On cree l'image
							$image = imagecreatetruecolor($max_x,$max_y);
							imagecopyresized($image,$img_src,$x,$y,0,0,$largeur,$hauteur,$size[0],$size[1]);
							imagepng($image,dirname($path)."/photo".$tab_ratio[$key]."_".$time.".png");

							imagedestroy($image);
						}
						unlink($path);
						$ct->fields['photo'] = "_".$time;
						$ct->save();
					}
					if (isset($_SESSION['desktopv2']['opportunity']['tiers_selected']) && $_SESSION['desktopv2']['opportunity']['tiers_selected'] > 0){
						require_once DIMS_APP_PATH.'modules/system/class_tiers_contact.php';
						$tiersCt = new tiersct();
						$tiersCt->init_description();
						$tiersCt->fields['id_tiers'] = $_SESSION['desktopv2']['opportunity']['tiers_selected'];
						$tiersCt->fields['id_contact'] = $ct->fields['id'];
						$tiersCt->fields['type_lien'] = $_SESSION['cste']['_DIMS_LABEL_EMPLOYEUR'];
						$tiersCt->fields['function'] = "";
						$tiersCt->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
						$tiersCt->fields['id_user'] = $_SESSION['dims']['userid'];
						$tiersCt->fields['date_create'] = dims_createtimestamp();
						$tiersCt->fields['date_deb'] = dims_createtimestamp();
						$tiersCt->fields['link_since'] = 0;
						$tiersCt->fields['link_level'] = 2;
						$tiersCt->fields['id_ct_user_create'] = $_SESSION['dims']['userid'];
						$tiersCt->save();
					}
					require_once DIMS_APP_PATH."modules/system/class_dims_vcard.php";
					$vcard = new dims_vcard();
					$vcard->open($doc->fields['id'],($num+1));
					$vcard->fields['id_contact'] = $ct->fields['id'];
					$vcard->fields['date_modify'] = dims_createtimestamp();
					$vcard->save();

					$_SESSION['desktopv2']['opportunity']['ct_added'][$ct->fields['id']] = $ct->fields['id'];
					if ($id_ct != '' && $id_ct > 0)
						echo $_SESSION['cste']['_DIMS_LABEL_MOD_SHEET_IMP'];
					else
						echo $_SESSION['cste']['_DIMS_LABEL_CREATE_PROFILE'];
					?>
					<script type="text/javascript">
						$(document).ready(function(){
							/*if (document.getElementById('editbox_search_contact').value != '')
								searchOpportunityCt(document.getElementById('editbox_search_contact').value);*/
							dims_xmlhttprequest_todiv("/admin.php","dims_op=desktopv2&action=add_contact_in_opportunity&id=0&idtiers=<?php echo $_SESSION['desktopv2']['opportunity']['tiers_selected'];?>","","div_list_added");
							oppSearchVcard(document.getElementById('editbox_search_vcard').value);
						});
					</script>
					<?
				}
			}
			die();
			break;
		case 'opp_pc_merge_vcard_with_ct':
			ob_clean();
			$file = dims_load_securvalue('file',dims_const::_DIMS_CHAR_INPUT,true,true,true);
			$num = dims_load_securvalue('num',dims_const::_DIMS_NUM_INPUT,true,true,true);
			$id_ct = dims_load_securvalue('id_ct',dims_const::_DIMS_NUM_INPUT,true,true,true);
			if ($file != '' && file_exists(realpath('.').$file)){
				$datas = docfile::parseExternalVcf(DIMS_APP_PATH.substr($file,1));
				if (isset($datas[$num])){
					$data = $datas[$num];
					$ct = new contact();
					$ct->init_description();
					if ($id_ct != '' && $id_ct > 0)
						$ct->open($id_ct);
					$ct->fields['firstname'] = $data['prenom'];
					$ct->fields['lastname'] = $data['nom'];
					if (isset($data['title']))
						$ct->fields['civilite'] = $data['title'];
					if(isset($data['email']) && count($data['email']) > 0){
						$i = 1;
						foreach($data['email'] as $email){
							if ($i == 1)
								$ct->fields['email'] = $email;
							elseif($i <= 3)
								$ct->fields["email$i"] = $email;
							$i ++;
						}
					}
					if (isset($data['tel']['cell']))
						$ct->fields['mobile'] = $data['tel']['cell'];
					if (isset($data['tel']['work'])){
						$ct->fields['phone'] = $data['tel']['work'];
						if(isset($data['tel']['home']))
							$ct->fields['phone2'] = $data['tel']['home'];
					}elseif(isset($data['tel']['home']))
						$ct->fields['phone'] = $data['tel']['home'];
					if (isset($data['adr']['work'])){
						$ct->fields['address'] = $data['adr']['work']['rue'];
						$ct->fields['city'] = $data['adr']['work']['city'];
						$ct->fields['postalcode'] = $data['adr']['work']['cp'];
						$ct->fields['country'] = $data['adr']['work']['pays'];
					}elseif(isset($data['adr']['home'])){
						$ct->fields['address'] = $data['adr']['home']['rue'];
						$ct->fields['city'] = $data['adr']['home']['city'];
						$ct->fields['postalcode'] = $data['adr']['home']['cp'];
						$ct->fields['country'] = $data['adr']['home']['pays'];
					}
					$ct->save();
					if (isset($data['photo']) && file_exists($data['photo'])){
						$photo = substr($data['photo'],1);
						$time = time();
						$tab_ratio = array(0 => 60, 1 => 300);

						$ext = explode('.', $photo);
						$ext = strtolower($ext[count($ext)-1]);
						$path = DIMS_WEB_PATH.'data/photo_cts/contact_'.$ct->fields['id'];
						if (!file_exists($path)) mkdir($path, 0777,true);

						$path .= '/tmp_'.$ct->fields['id'].'.'.$ext;
						//on cree une image temporaire pour la redimensionner
						rename(realpath('.').$photo,$path);
						switch($ext) {
							case "png" :
								$img_src = imagecreatefrompng($path);
							break;
							case "jpg" :
							case "jpeg" :
								$img_src = imagecreatefromjpeg($path);
							break;
							case "gif" :
								$img_src = imagecreatefromgif($path);
							break;
						}
						$size = getimagesize($path);
						foreach($tab_ratio as $key => $ratio) {
							//modification de la taille du ratio si l'image est plus petite que ratio
							if($ratio > $size[0] && $ratio > $size[1]) {
								if($size[0] > $size[1])  $ratio = $size[0];
								else $ratio = $size[1];
							}
							//largeur > hauteur
							if($size[0] > $size[1]) {

								$hauteur = $max_y = $ratio;
								$largeur = round((($ratio*$size[0])/$size[1]),0);

								//decalage
								$x = round((($ratio-$largeur)/2),0);
								$y = 0;

								$max_x = $ratio;
							}

							//image carrée
							if($size[0] == $size[1]) {
								$hauteur = $ratio;
								$largeur = $ratio;
								$x = 0;
								$y = 0;
								$max_x = $max_y = $ratio;
							}

							//hauteur > largeur
							//La hauteur max sera de 130% du ratio
							if($size[0] < $size[1]) {
								$largeur = $max_x = $ratio;
								$hauteur = round((($ratio/$size[0])*$size[1]),0);

								//decalage
								$x = 0;
								$y = 0;

								//taille image
								if($size[1] > (1.3 * $size[0])){
									$max_y = $ratio * 1.3;
								}else{
									$max_y = round((($ratio/$size[0])*$size[1]),0);
								}
							}

							//On cree l'image
							$image = imagecreatetruecolor($max_x,$max_y);
							imagecopyresized($image,$img_src,$x,$y,0,0,$largeur,$hauteur,$size[0],$size[1]);
							imagepng($image,dirname($path)."/photo".$tab_ratio[$key]."_".$time.".png");

							imagedestroy($image);
						}
						unlink($path);
						$ct->fields['photo'] = "_".$time;
						$ct->save();
					}
					if (isset($_SESSION['desktopv2']['opportunity']['tiers_selected']) && $_SESSION['desktopv2']['opportunity']['tiers_selected'] > 0){
						require_once DIMS_APP_PATH.'modules/system/class_tiers_contact.php';
						$tiersCt = new tiersct();
						$tiersCt->init_description();
						$tiersCt->fields['id_tiers'] = $_SESSION['desktopv2']['opportunity']['tiers_selected'];
						$tiersCt->fields['id_contact'] = $ct->fields['id'];
						$tiersCt->fields['type_lien'] = $_SESSION['cste']['_DIMS_LABEL_EMPLOYEUR'];
						$tiersCt->fields['function'] = "";
						$tiersCt->fields['id_workspace'] = $_SESSION['dims']['workspaceid'];
						$tiersCt->fields['id_user'] = $_SESSION['dims']['userid'];
						$tiersCt->fields['date_create'] = dims_createtimestamp();
						$tiersCt->fields['date_deb'] = dims_createtimestamp();
						$tiersCt->fields['link_since'] = 0;
						$tiersCt->fields['link_level'] = 2;
						$tiersCt->fields['id_ct_user_create'] = $_SESSION['dims']['userid'];
						$tiersCt->save();
					}

					$_SESSION['desktopv2']['opportunity']['ct_added'][$ct->fields['id']] = $ct->fields['id'];
					if ($id_ct != '' && $id_ct > 0)
						echo $_SESSION['cste']['_DIMS_LABEL_MOD_SHEET_IMP'];
					else
						echo $_SESSION['cste']['_DIMS_LABEL_CREATE_PROFILE'];
					?>
					<script type="text/javascript">
						$(document).ready(function(){
							/*if (document.getElementById('editbox_search_contact').value != '')
								searchOpportunityCt(document.getElementById('editbox_search_contact').value);*/
							dims_xmlhttprequest_todiv("/admin.php","dims_op=desktopv2&action=add_contact_in_opportunity&id=0&idtiers=<?php echo $_SESSION['desktopv2']['opportunity']['tiers_selected']; ?>","","div_list_added");
						});
					</script>
					<?
				}
			}
			die();
			break;
		case 'get_current_mapmode':
			ob_clean();
			if(isset($_SESSION['dims']['map']['selector']))echo $_SESSION['dims']['map']['selector']; else echo 'd15';
			die();
			break;
		case 'toggle_content_right':
			ob_clean();
			if(!isset($_SESSION['desktopV2']['content_droite'])) $_SESSION['desktopV2']['content_droite'] = array();

			$bloc = dims_load_securvalue('bloc',dims_const::_DIMS_CHAR_INPUT, true, true);
			$visible = dims_load_securvalue('visible',dims_const::_DIMS_NUM_INPUT, true, true);

			if(!isset($_SESSION['desktopV2']['content_droite'][$bloc])) $_SESSION['desktopV2']['content_droite'][$bloc] = 0;

			$_SESSION['desktopV2']['content_droite'][$bloc] = $visible;
			die();
			break;

		case 'delete_concept':
			//gestion de la suppression d'éléments ---------------------------------------------------------------------------------------------------------
			$type = dims_load_securvalue('type', dims_const::_DIMS_CHAR_INPUT, true, true);
			$go = dims_load_securvalue('go', dims_const::_DIMS_NUM_INPUT, true, true);
			$desktop = dims_load_securvalue('desktop', dims_const::_DIMS_NUM_INPUT, true, true);
			$from = dims_load_securvalue('from', dims_const::_DIMS_CHAR_INPUT, true, true);
			$deleted = false;
			if(isset($go) && !empty($go)){
				switch($type){
					case 'contact':
						$ct = new contact();
						$ct->openWithGB($go);
						if(!$ct->isNew()){
							$ct->desactive();//permet de ne pas supprimer définitivement la fiche pour des raisons d'historique (ex : inscription à un event)
							$ct->save();
							$deleted = true;
						}
						break;
					case 'tiers':
						$ti = new tiers();
						$ti->openWithGB($go);
						if(!$ti->isNew()){
							$ti->desactive();//permet de ne pas supprimer définitivement la fiche pour des raisons d'historique (ex : inscription à un event)
							$ti->save();
							$deleted = true;
						}
						break;
					case 'document':
						$doc = new docfile();
						$doc->openWithGB($go);
						if(!$doc->isNew()){
							$doc->cutMyLinks();//notamment pour la matrice
							$doc->delete();//permet de ne pas supprimer définitivement la fiche pour des raisons d'historique (ex : inscription à un event)
							$doc->save();
							$deleted = true;
						}
						break;
				}
			}
			if(!$deleted){
				dims_redirect($dims->getScriptEnv());
			}
			else{
				//suppression de la ligne de résultat dans la recherche courrante des gens connectés pour ne plus qu'il ressorte si elle existe après un F5
				require_once DIMS_APP_PATH.'modules/system/class_search_expression.php';
				$search = new search_expression();
				$search->deleteRow($go);
				if(isset($from) && !empty($from) && $from=='concept'){
					//dans ce cas on va chercher le lien de retour qui est en session
					if(isset($_SESSION['desktop']['return_link']['link'])){
						dims_redirect($_SESSION['desktop']['return_link']['link']);
					}
					//pas de else on ira directement dans le cas suivant
				}

				if(isset($desktop) && $desktop > 0){
					dims_redirect($dims->getScriptEnv()."?submenu=1&force_desktop=1");
				}
				else dims_redirect($dims->getScriptEnv());
			}
			break;
		case 'add_global_selection':
			ob_clean();
			$id = json_decode(dims_load_securvalue('id', dims_const::_DIMS_CHAR_INPUT, true, true));
			if (!empty($id)){
				$id_categ = dims_load_securvalue('id_categ', dims_const::_DIMS_NUM_INPUT, true, true);
				$label = dims_load_securvalue('label', dims_const::_DIMS_CHAR_INPUT, true, true);
				$categ = new selection_categ();
				if ($id_categ != '' && $id_categ > 0)
					$categ->open($id_categ);
				elseif(trim($label) != ''){
					$categ->init_description();
					$categ->setugm();
					$categ->fields['timestp'] = dims_createtimestamp();
					$categ->fields['label'] = trim($label);
					$categ->save();
				}
				//$categ = selection_categ::getDefault();
				if(is_array($id)) {
					foreach($id as $i) {
						$categ->addElement($i);
					}
				}
				else {
					$categ->addElement($id);
				}
			}
			include _DESKTOP_TPL_LOCAL_PATH.'/selection/selection.tpl.php';
			die();
			break;
		case 'choose_global_selection':
			ob_clean();
			$id_popup = dims_load_securvalue('id_popup',dims_const::_DIMS_CHAR_INPUT,true,true);

			if(is_array($_GET['id'])) {
				$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true);
			}
			else {
				$id = (int)dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true);
			}
			?>
			<div>
				<div class="actions">
					<a href="Javascript: void(0);" onclick="javascript:dims_closeOverlayedPopup('<?php echo $id_popup; ?>');">
						<img src="<? echo _DESKTOP_TPL_PATH; ?>/gfx/common/close.png" />
					</a>
				</div>
				<h2><? echo $_SESSION['cste']['_LIST_SELECTION']; ?></h2>
				<div style="max-height:350px;overflow-y:auto;">
					<input type="hidden" value="<? echo $id; ?>" id="id" />
					<?
					foreach(selection_categ::getCategories() as $categ){
						?>
						<div>
							<input <? if($categ->fields['is_default']) echo 'checked=true'; ?> type="radio" name="id_categ" class="id_categ" value="<? echo $categ->fields['id']; ?>"  onclick="javascript:$('input.text_label').attr('disabled',true);" />
							<label><? echo ((isset($_SESSION['cste'][$categ->fields['label']]))?$_SESSION['cste'][$categ->fields['label']]:$categ->fields['label']); ?></label><br />
						</div>
						<?
					}
					?>
					<div>
						<input type="radio" name="id_categ" class="id_categ" value="0" onclick="javascript:$('input.text_label').attr('disabled',false);" />
						<label><? echo $_SESSION['cste']['_CREATE_SELECTION']; ?></label>
						<input type="text" value="" disabled=true class="text_label" />
					</div>
					<div>
						<input onclick="javascript:addGlobalSelection('<? echo json_encode($id); ?>',$('input.id_categ:checked').val(),$('input.text_label').val()); dims_closeOverlayedPopup('<?php echo $id_popup; ?>');" type="button" value="<? echo $_SESSION['cste']['_DIMS_SAVE']; ?>" />
						<input type="button" onclick="javascript:dims_closeOverlayedPopup('<?php echo $id_popup; ?>');" value="<? echo $_SESSION['cste']['_DIMS_LABEL_CANCEL']; ?>" />
					</div>
				</div>
			</div>
			<?
			die();
			break;

		case 'delete_selection_categ':
			//gestion de la suppression d'éléments ---------------------------------------------------------------------------------------------------------
			$id_selcateg = dims_load_securvalue('id_selcateg', dims_const::_DIMS_NUM_INPUT, true, true);
			$desktop = dims_load_securvalue('desktop', dims_const::_DIMS_NUM_INPUT, true, true);
			$from = dims_load_securvalue('from', dims_const::_DIMS_CHAR_INPUT, true, true);

			$categ = new selection_categ();

			$categ->open($id_selcateg);

			$categ->delete();

			if(isset($desktop) && $desktop > 0){
				dims_redirect($dims->getScriptEnv()."?submenu=1&force_desktop=1");
			}
			else dims_redirect($dims->getScriptEnv());

			break;

		case 'delete_selection':
			//gestion de la suppression d'éléments ---------------------------------------------------------------------------------------------------------
			$id_selcateg = dims_load_securvalue('id_selcateg', dims_const::_DIMS_NUM_INPUT, true, true);
			$idgo_elem = dims_load_securvalue('idgo_elem', dims_const::_DIMS_NUM_INPUT, true, true);
			$desktop = dims_load_securvalue('desktop', dims_const::_DIMS_NUM_INPUT, true, true);
			$from = dims_load_securvalue('from', dims_const::_DIMS_CHAR_INPUT, true, true);
			$ajax = dims_load_securvalue('ajax', dims_const::_DIMS_NUM_INPUT, true, true);

			$categ = new selection_categ();

			$categ->open($id_selcateg);

			$categ->deleteElem($idgo_elem);

			if(!$ajax) {
				if(isset($desktop) && $desktop > 0){
					dims_redirect($dims->getScriptEnv()."?submenu=1&force_desktop=1");
				}
				else dims_redirect($dims->getScriptEnv());
			}
			else {
				ob_clean();
				die();
			}

			break;

		case 'selectionVcard':
			ob_clean();
			require_once DIMS_APP_PATH . '/modules/system/class_search_expression.php' ;
			require_once DIMS_APP_PATH . '/modules/system/class_search_expression_tag.php' ;
			require_once DIMS_APP_PATH . '/modules/system/class_search.php' ;

			$id_selcateg = dims_load_securvalue('id_selcateg', dims_const::_DIMS_NUM_INPUT, true, true);

			if(!empty($id_selcateg)) {
				$categ = new selection_categ();

				$categ->open($id_selcateg);

				$courPath = realpath('.');
				$dirExport = DIMS_TMP_PATH . '/vcardexport/';
				if (!file_exists($dirExport))
					mkdir($dirExport);
				$sid = session_id();

				if (!file_exists($dirExport.$sid))
					mkdir($dirExport.$sid);
				$file2 = $dirExport.$sid."/vcards_search.vcf";
				$vcard = fopen($file2,'w+');

				foreach($categ->getElements() as $res){
					if($res->fields['type_object'] == dims_const::_SYSTEM_OBJECT_CONTACT){
						$obj = new contact();
						$obj->openWithGB($res->fields['id_globalobject']);
						$content = file_get_contents($obj->getVcard())."\n";
						fwrite($vcard,$content);
					}
				}

				$files = array();

				foreach($categ->getElements() as $res){
					if($res->fields['type_object'] == dims_const::_SYSTEM_OBJECT_TIERS){
						$obj = new tiers();
						$obj->openWithGB($res->fields['id_globalobject']);
						$content = file_get_contents($obj->getVcard())."\n";
						fwrite($vcard,$content);
					}
				}
				fclose($vcard);
				chdir($courPath);
				//$files = array();
				$file['filename'] = $dirExport.$sid."/vcards_search.vcf";
				$file['name'] = "vcards_search.vcf";
				$file['mime-type'] = 'text/x-vcard; charset=utf-8';
				$files[] = $file;


				dims_send_mail_with_files($_SESSION['dims']['user']['email'],$_SESSION['dims']['user']['email'],"vCard : search","",$files);
			}
			dims_redirect(dims::getInstance()->getScriptEnv());
			die();
			break;
		case 'exportSelectionExcel':
			function add_txt($add,$org,$br=true)
			{
				if($add!='')
				{
					$result = $add;
					if($br)
					{
						if($org!='') $result = $org.chr(10).$add;
					}
					else
					{
						if($org!='') $result = $org.' '.$add;
					}
					return $result;
				}
				else
				{
					return $org;
				}
			}

			$id_selcateg = dims_load_securvalue('id_selcateg', dims_const::_DIMS_NUM_INPUT, true, true);

			if(!empty($id_selcateg)) {
				$categ = new selection_categ();

				$categ->open($id_selcateg);

				ini_set('display_erros', '0');

				require_once 'Spreadsheet/Excel/Writer.php';

				// Creating a workbook
				$workbook = new Spreadsheet_Excel_Writer();

				// sending HTTP headers
				$workbook->send("excel.xls");

				$format_title =& $workbook->addFormat( array( 'Align' => 'center', 'Bold'  => 1, 'Color'  => 'black', 'Size'  => 10, 'FgColor' => 'silver'));
				$format =& $workbook->addFormat(array('TextWrap' => 1, 'Align' => 'left', 'Bold'  => 0, 'Color'  => 'black', 'Size'  => 10));

				// Retrieve contacts
				$contactGoId = array();
				foreach($categ->getElements() as $res) {
					if($res->fields['type_object'] == dims_const::_SYSTEM_OBJECT_CONTACT) {
						$contactGoId[] = $res->fields['id_globalobject'];
					}
				}

				if(!empty($contactGoId)) {
					$params = array();
					$sql = 'SELECT c.*
							FROM dims_mod_business_contact c
							WHERE c.id_globalobject IN ('.$db->getParamsFromArray($contactGoId, 'idglobalobject', $params).')';

					$exported_contacts = array();

					$res = $db->query($sql, $params);
					while ($fields = $db->fetchrow($res)){
						if(!$only_fields){
							$ct = new contact();
							$ct->openFromResultSet($fields);
							$exported_contacts[] = $ct;
						}
						else $exported_contacts[$fields['id']] = $fields;
					}

					//dims_print_r($exported_contacts);die();
					//traitement repris de Pat (cf. include/op.php) de l'ancien desktop ----------------------------
					$sql =	"
									SELECT		mf.*,mc.label as categlabel, mc.id as id_cat,
												mb.protected,mb.name as namefield,mb.label as titlefield
									FROM		dims_mod_business_meta_field as mf
									INNER JOIN	dims_mb_field as mb
									ON			mb.id=mf.id_mbfield
									RIGHT JOIN	dims_mod_business_meta_categ as mc
									ON			mf.id_metacateg=mc.id
									WHERE		mf.id_object = :idobject
									AND			mf.used=1
									AND			mf.option_exportview=1
									ORDER BY	mc.position, mf.position
									";
					$rs_fields=$db->query($sql, array(
						':idobject' => dims_const::_SYSTEM_OBJECT_CONTACT
					));
					$_SESSION['business']['exportdata']=array();
					while ($fields = $db->fetchrow($rs_fields)) {
							$sql_s .= ",c.".$fields['namefield'];

							if (isset($_DIMS['cste'][$fields['titlefield']])) $namevalue= $_DIMS['cste'][$fields['titlefield']];
							else $namevalue=$fields['name'];
							$elem=array();
							$elem['title']=$namevalue;
							$elem['namefield']=$fields['namefield'];

							$_SESSION['business']['exportdata'][]=$elem;
					}

					foreach($exported_contacts as $contact){
						//on va chercher les champs métier dans les layers
						$sql_l =	"SELECT		*
									FROM		dims_mod_business_contact_layer
									WHERE		id = :idcontact
									AND		type_layer = 1
									AND		id_layer = :idlayer";
						$res_l = $db->query($sql_l, array(
							':idcontact' => array('type' => PDO::PARAM_INT, 'value' => $contact->getId()),
							':idlayer' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['workspaceid']),
						));
						if($db->numrows($res_l) > 0) {
							while($lay = $db->fetchrow($res_l)) {
								if($lay['civilite'] != '')		$contact->fields['civilite'] = $lay['civilite'];
								if($lay['address'] != '')		$contact->fields['address'] = $lay['address'];
								if($lay['postalcode'] != '')	$contact->fields['postalcode'] = $lay['postalcode'];
								if($lay['city'] != '')			$contact->fields['city'] = $lay['city'];
								if($lay['country'] != '')		$contact->fields['country'] = $lay['country'];
								if($lay['phone'] != '')			$contact->fields['phone'] = $lay['phone'];
								if($lay['phone2'] != '')		$contact->fields['phone2'] = $lay['phone2'];
								if($lay['fax'] != '')			$contact->fields['fax'] = $lay['fax'];
								if($lay['mobile'] != '')		$contact->fields['mobile'] = $lay['mobile'];
								if($lay['email'] != '')			$contact->fields['email'] = $lay['email'];
								if($lay['email2'] != '')		$contact->fields['email2'] = $lay['email2'];
								if($lay['email3'] != '')		$contact->fields['email3'] = $lay['email3'];
								if($lay['comments'] != '')		$contact->fields['comments'] = $lay['comments'];
							}
						}
					}

					$l=0;

					$id_rub1= '';
					//creation du tableau
					$worksheet =& $workbook->addWorksheet("contacts");
					if (isset($_SESSION['business']['exportdata'])) {
						$i=0;
						foreach($_SESSION['business']['exportdata'] as $d) {
							$worksheet->setColumn($i, $i, 30); //voir peut etre pour agrandir en fonction des champs
							$i++;
						}
					}

					if(!empty($_SESSION['business']['search_ent']['intitule']) || !empty($_SESSION['business']['search_lkent']['type_lien'])) {
						$worksheet->setColumn($i, $i++, 20);
						$worksheet->setColumn($i, $i++, 15);
						$worksheet->setColumn($i, $i++, 15);
					}

					//entetes de colonnes
					if (isset($_SESSION['business']['exportdata'])) {
						$i=0;
						foreach($_SESSION['business']['exportdata'] as $f) {
							$worksheet->writeString($l, $i++, utf8_decode(html_entity_decode($f['title'])), $format_title);
						}
					}

					if(!empty($_SESSION['business']['search_ent']['intitule']) || !empty($_SESSION['business']['search_lkent']['type_lien'])) {
						$worksheet->writeString($l, $i++, $_DIMS['cste']['_DIMS_LABEL_ENT_NAME'], $format_title);
						$worksheet->writeString($l, $i++, $_DIMS['cste']['_DIMS_LABEL_LINK_TYPE'], $format_title);
						$worksheet->writeString($l, $i++, $_DIMS['cste']['_DIMS_LABEL_FUNCTION'], $format_title);
					}

					//$ancien_id = 0;
					foreach($exported_contacts as $contact) {
						//while ($fields = $db->fetchrow($rs)) {
						$l++;
						if (isset($_SESSION['business']['exportdata'])) {
							$i=0;
							foreach($_SESSION['business']['exportdata'] as $f) {
								//ajout de utf8_decode pour export excel
								$worksheet->writeString($l, $i++, utf8_decode($contact->fields[$f['namefield']]), $format);
							}
						}

						if(!empty($_SESSION['business']['search_ent']['intitule']) || !empty($_SESSION['business']['search_lkent']['type_lien'])) {
							$worksheet->writeString($l, $i++, utf8_decode($contact->fields['intitule']), $format);
							$worksheet->writeString($l, $i++, utf8_decode($contact->fields['type_lien']), $format);
							$worksheet->writeString($l, $i++, utf8_decode($contact->fields['function']), $format);
						}
					}
				}

				// Retrieve tiers
				$tiersGoId = array();
				foreach($categ->getElements() as $res) {
					if($res->fields['type_object'] == dims_const::_SYSTEM_OBJECT_TIERS) {
						$tiersGoId[] = $res->fields['id_globalobject'];
					}
				}

				if(!empty($tiersGoId)) {
					$params = array();
					$sql = 'SELECT t.*
							FROM dims_mod_business_tiers t
							WHERE t.id_globalobject IN ('.$db->getParamsFromArray($tiersGoId, 'idglobalobject', $params).')';

					$exported_tiers = array();

					$res = $db->query($sql, $params);
					while ($fields = $db->fetchrow($res)){
						if(!$only_fields){
							$t = new tiers();
							$t->openFromResultSet($fields);
							$exported_tiers[] = $t;
						}
						else $exported_tiers[$fields['id']] = $fields;
					}

					$sql =	"
								SELECT		mf.*,mc.label as categlabel, mc.id as id_cat,
											mb.protected,mb.name as namefield,mb.label as titlefield
								FROM		dims_mod_business_meta_field as mf
								INNER JOIN	dims_mb_field as mb
								ON			mb.id=mf.id_mbfield
								RIGHT JOIN	dims_mod_business_meta_categ as mc
								ON		mf.id_metacateg=mc.id
								WHERE		  mf.id_object = :idobject
								AND			mf.used=1
								AND			mf.option_exportview=1
								ORDER BY	mc.position, mf.position
								";

					$rs_fields=$db->query($sql, array(
						':idobject' => dims_const::_SYSTEM_OBJECT_TIERS
					));
					$_SESSION['business']['exportdata']=array();

					while ($fields = $db->fetchrow($rs_fields)) {
						$sql_s .= ",t.".$fields['namefield'];

						if (isset($_DIMS['cste'][$fields['titlefield']])) $namevalue= $_DIMS['cste'][$fields['titlefield']];
						else $namevalue=$fields['name'];
						$elem=array();
						$elem['title']=$namevalue;
						$elem['namefield']=$fields['namefield'];

						$_SESSION['business']['exportdata'][]=$elem;
					}

					$l=0;

					$id_rub1= '';
					//creation du tableau
					$worksheet =& $workbook->addWorksheet("Company");

					if (isset($_SESSION['business']['exportdata'])) {
						$i=0;
						foreach($_SESSION['business']['exportdata'] as $d) {
							$worksheet->setColumn($i, $i, 30); //voir peut etre pour agrandir en fonction des champs
							$i++;
						}
					}

					if(!empty($_SESSION['business']['ent_search_ct']['lastname']) || !empty($_SESSION['business']['ent_search_ct']['firstname']) || !empty($_SESSION['business']['ent_search_ct']['email']) || !empty($_SESSION['business']['search_lkct']['type_lien'])) {
						$worksheet->setColumn($i, $i++, 30);
						$worksheet->setColumn($i, $i++, 30);
						$worksheet->setColumn($i, $i++, 30);
						$worksheet->setColumn($i, $i++, 15);
					}

					//entetes de colonnes
					if (isset($_SESSION['business']['exportdata'])) {
						$i=0;
						foreach($_SESSION['business']['exportdata'] as $f) {
							$worksheet->writeString($l, $i++, html_entity_decode($f['title']), $format_title);
						}
					}

					foreach($exported_tiers as $tiers){
						$l++;
						if (isset($_SESSION['business']['exportdata'])) {
							$i=0;
							foreach($_SESSION['business']['exportdata'] as $f) {
								$worksheet->writeString($l, $i++, strtolower(utf8_decode($tiers->fields[$f['namefield']])), $format);
							}
						}
						//entetes de colonnes
						if(!empty($_SESSION['business']['ent_search_ct']['lastname']) || !empty($_SESSION['business']['ent_search_ct']['firstname']) || !empty($_SESSION['business']['ent_search_ct']['email']) || !empty($_SESSION['business']['search_lkct']['type_lien'])) {
							$worksheet->writeString($l, $i++, strtoupper(utf8_decode($tiers->fields['lastname'])), $format);
							$worksheet->writeString($l, $i++, strtoupper(utf8_decode($tiers->fields['firstname'])), $format);
							$worksheet->writeString($l, $i++, utf8_decode($tiers->fields['email']), $format);
							$worksheet->writeString($l, $i++, utf8_decode($tiers->fields['type_lien']), $format);
						}
					}
				}

				ob_clean();
				$workbook->close();
			}
			die();
			break;
		case 'tagSelection':
			break;
	case 'get_infos_ct_tiers':
		ob_clean();
		$type = dims_load_securvalue('type', dims_const::_DIMS_NUM_INPUT, true, true);
		$id = dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true, true);
		$res = array();
		if (!empty($id))
		switch($type){
			case dims_const::_SYSTEM_OBJECT_CONTACT:
			$ct = new contact();
			$ct->open($id);
			$res = $ct->fields;
			break;
			case dims_const::_SYSTEM_OBJECT_TIERS:
			$ct = new tiers();
			$ct->open($id);
			$res = $ct->fields;
			break;
		}
		echo json_encode($res);
		die();
		break;

		/* Gestion des suivis */
		case 'ajouter_suivi':
			@ob_end_clean();
			$id_popup = dims_load_securvalue('id_popup', dims_const::_DIMS_NUM_INPUT, true, false);

			$suivi = new suivi();
			$suivi->setLightAttribute('id_popup', $id_popup);
			$suivi->display(DIMS_APP_PATH.'modules/system/desktopV2/templates/concepts/bloc_suivi/suivi_new.tpl.php');
			die();
			break;
		case 'editer_suivi':
			@ob_end_clean();
			$id_suivi	= dims_load_securvalue('id_suivi', dims_const::_DIMS_NUM_INPUT, true, false);
			$id_popup	= dims_load_securvalue('id_popup', dims_const::_DIMS_NUM_INPUT, true, false);

			if (!empty($id_suivi) && !empty($id_popup)) {
				$suivi = new suivi();
				$suivi->open($id_suivi);
				$suivi->setLightAttribute('id_popup', $id_popup);
				$suivi->display(DIMS_APP_PATH.'modules/system/desktopV2/templates/concepts/bloc_suivi/suivi_edit.tpl.php');
			}
			die();
			break;
		case 'suivi_enregistrer':
			// ALTER TABLE `dims_mod_business_suivi` ADD `contact_id` int(10) unsigned NOT NULL DEFAULT '0' AFTER `tiers_id`;

			$id_suivi			= dims_load_securvalue('id_suivi', dims_const::_DIMS_NUM_INPUT, false, true);
			$suivi_datejour		= dims_load_securvalue('suivi_datejour', dims_const::_DIMS_CHAR_INPUT, false, true);
			$suivi_libelle		= dims_load_securvalue('suivi_libelle', dims_const::_DIMS_CHAR_INPUT, false, true);
			$suivi_remise		= dims_load_securvalue('suivi_remise', dims_const::_DIMS_NUM_INPUT, false, true);
			$suivi_dossier_id	= dims_load_securvalue('suivi_dossier_id', dims_const::_DIMS_NUM_INPUT, false, true);

			require_once DIMS_APP_PATH.'modules/system/class_matrix.php';
			require_once DIMS_APP_PATH.'modules/system/class_search.php';

			if ($_SESSION['desktopv2']['concepts']['filters']['stack'][0][0] == 'contact') {
			// on est sur la fiche client
			$contact_go = $_SESSION['desktopv2']['concepts']['filters']['stack'][0][1];
			}
			elseif ($_SESSION['desktopv2']['concepts']['filters']['stack'][0][0] == 'company') {
			// on est sur la fiche client
			$tiers_go = $_SESSION['desktopv2']['concepts']['filters']['stack'][0][1];
			}

			$suivi = new suivi();
			if (!empty($id_suivi)) {
				$suivi->open($id_suivi);
			}
			else {
				$suivi->init_description();
				$suivi->setugm(); // nouveau suivi

				if (isset($tiers_go)) {
			// recherche du tiers_id
			$rs = $db->query('SELECT id FROM dims_mod_business_tiers WHERE id_globalobject = :idglobalobject LIMIT 0, 1', array(
				':idglobalobject' => array('type' => PDO::PARAM_INT, 'value' => $tiers_go),
			));
			if ($db->numrows($rs)) {
				$row = $db->fetchrow($rs);
				$suivi->fields['tiers_id'] = $row['id'];
			}
				}
				elseif (isset($contact_go)) {
			// recherche du tiers_id
			$rs = $db->query('SELECT id FROM dims_mod_business_contact WHERE id_globalobject = :idglobalobject LIMIT 0, 1', array(
				':idglobalobject' => array('type' => PDO::PARAM_INT, 'value' => $contact_go),
			));
			if ($db->numrows($rs)) {
				$row = $db->fetchrow($rs);
				$suivi->fields['contact_id'] = $row['id'];
			}
				}
			}

			if (isset($_POST['suivi_tiers_id']) && $suivi->fields['tiers_id']!=$_POST['suivi_tiers_id']) {
				unset($_SESSION['desktopv2']['business']['popup']);
				unset($_SESSION['desktopv2']['business']['id_suivi']);
			}
			elseif (isset($_POST['suivi_contact_id']) && $suivi->fields['contact_id']!=$_POST['suivi_contact_id']) {
				unset($_SESSION['desktopv2']['business']['popup']);
				unset($_SESSION['desktopv2']['business']['id_suivi']);
			}
			else {
				// enregistrement en session pour réouverture du popup
				$_SESSION['desktopv2']['business']['popup'] = 'suivi_modifier';
				$_SESSION['desktopv2']['business']['id_suivi'] = $suivi->getIdSuivi();
			}

			$suivi->setvalues($_POST, 'suivi_');
			//dims_print_r($suivi);die();
			// on decoche
			if (isset($_POST['suivi_valide']) && $_POST['suivi_valide']=='on') {
				$suivi->fields['valide'] = 1;
				$suivi->fields['datevalide'] = date('Y-m-d');
			}
			else {
				$suivi->fields['valide'] = 0;
				$suivi->fields['datevalide'] = '';
			}

			// test si id_différent

			//dims_print_r($suivi->fields);die();
			$suivi->fields['datejour'] = business_datefr2us($suivi->fields['datejour']);

			//$suivi->fields['datevalide'] = business_datefr2us($suivi->fields['datevalide']);
			$suivi->save();

			// si on fait le rattachement à un tiers
			if (isset($tiers_go)) {
				// rattachement du suivi au dossier et au client dans la matrice
				$matrix = new search();
				$linkedObjectsIds = $matrix->exploreMatrice(
					null,
					null,
					null,
					array($tiers_go),
					null,
					null,
					null,
					array($suivi->getGlobalObjectId()),
					null,
					null
					);

				$dossiers = array_keys($linkedObjectsIds['distribution']['dossiers']);
				$dossier_actuel = $dossiers[0];

				// on gere le changement de dossier
				if ($dossier_actuel != $suivi_dossier_id) {
					if (matrix::exists(array( 'id_tiers' => $tiers_go, 'id_case' => $dossier_actuel, 'id_suivi' => $suivi->getGlobalObjectId() ))) {
						$matrix = new matrix();
						$matrix->cutLink(array( 'id_tiers' => $tiers_go, 'id_case' => $dossier_actuel, 'id_suivi' => $suivi->getGlobalObjectId() ));
					}
				}
				// on fait le rattachement
				if (!matrix::exists(array( 'id_tiers' => $tiers_go, 'id_case' => $suivi_dossier_id, 'id_suivi' => $suivi->getGlobalObjectId() ))) {
					$matrix = new matrix();
					$matrix->addLink(array( 'id_tiers' => $tiers_go, 'id_case' => $suivi_dossier_id, 'id_suivi' => $suivi->getGlobalObjectId() ));
				}
			}
			// si on fait le rattachement à un contact
			elseif (isset($contact_go)) {
				// rattachement du suivi au dossier et au client dans la matrice
				$matrix = new search();
				$linkedObjectsIds = $matrix->exploreMatrice(
					null,
					null,
					null,
					null,
					array($contact_go),
					null,
					null,
					array($suivi->getGlobalObjectId()),
					null,
					null
					);

				$dossiers = array_keys($linkedObjectsIds['distribution']['dossiers']);
				$dossier_actuel = $dossiers[0];

				// on gere le changement de dossier
				if ($dossier_actuel != $suivi_dossier_id) {
					if (matrix::exists(array( 'id_contact' => $contact_go, 'id_case' => $dossier_actuel, 'id_suivi' => $suivi->getGlobalObjectId() ))) {
						$matrix = new matrix();
						$matrix->cutLink(array( 'id_contact' => $contact_go, 'id_case' => $dossier_actuel, 'id_suivi' => $suivi->getGlobalObjectId() ));
					}
				}
				// on fait le rattachement
				if (!matrix::exists(array( 'id_contact' => $contact_go, 'id_case' => $suivi_dossier_id, 'id_suivi' => $suivi->getGlobalObjectId() ))) {
					$matrix = new matrix();
					$matrix->addLink(array( 'id_contact' => $contact_go, 'id_case' => $suivi_dossier_id, 'id_suivi' => $suivi->getGlobalObjectId() ));
				}
			}




			dims_redirect($dims->getScriptEnv());
			break;
		case 'supprimer_suivi':
			$id_suivi = dims_load_securvalue('id_suivi', dims_const::_DIMS_NUM_INPUT, true, false);

			if (!empty($id_suivi)) {
				$suivi = new suivi();
				$suivi->open($id_suivi);

				// suppression des rattachements du suivi dans la matrice
				require_once DIMS_APP_PATH.'modules/system/class_matrix.php';
				$matrix = new matrix();
				$matrix->purgeData('id_suivi', $suivi->getGlobalObjectId());

		$suivi->delete();
			}

			dims_redirect($dims->getScriptEnv());
			break;
	case 'imprimer_suivi':

		$id_suivi		= dims_load_securvalue('id_suivi', dims_const::_DIMS_NUM_INPUT, true, true);
		$suivi_modele	= dims_load_securvalue('suivi_modele', dims_const::_DIMS_NUM_INPUT, true, true);
		$format			= dims_load_securvalue('format', dims_const::_DIMS_CHAR_INPUT, true, true);

		if (!empty($id_suivi)) {
				require_once DIMS_APP_PATH.'/modules/system/desktopV2/include/class_gescom_param.php';
				require_once DIMS_APP_PATH.'/modules/system/desktopV2/include/class_xmlmodel.php';
				require_once DIMS_APP_PATH.'/modules/system/desktopV2/include/xmlparser_content.php';

				// chargement des params
				$params = class_gescom_param::getAllParams();

				$suivi = new suivi();
				$suivi->open($id_suivi);

				$tiers = new tiers();
				$contact = new contact();
				if ($suivi->fields['tiers_id']>0) {
					$tiers->open($suivi->fields['tiers_id']);
					$intitule=isset($tiers->fields['intitule']) ? $tiers->fields['intitule'] : '';
					$adresse=isset($tiers->fields['adresse']) ? $tiers->fields['adresse'] : '';
					$codepostal=isset($tiers->fields['codepostal']) ? $tiers->fields['codepostal'] : '';
					$ville=isset($tiers->fields['ville']) ? $tiers->fields['ville'] : '';
					$pays=isset($tiers->fields['pays']) ? $tiers->fields['pays'] : '';
					$telclient=isset($tiers->fields['telephone']) ? $tiers->fields['telephone'] : '';
					$mobileclient = isset($contact->fields['telmobile']) ? $contact->fields['telmobile'] : '';
				}
				elseif ($suivi->fields['contact_id']>0) {
					$contact->open($suivi->fields['contact_id']);
					$intitule=$contact->fields['lastname']." ".$contact->fields['firstname'];
					$adresse=isset($contact->fields['address']) ? $contact->fields['address'] : '';
					$codepostal=isset($contact->fields['postalcode']) ? $contact->fields['postalcode'] : '';
					$ville=isset($contact->fields['city']) ? $contact->fields['city'] : '';
					$pays=isset($contact->fields['country']) ? $contact->fields['country'] : '';
					$telclient=isset($contact->fields['phone']) ? $contact->fields['phone'] : '';
					$mobileclient=isset($contact->fields['mobile']) ? $contact->fields['mobile'] : '';
				}


				if (empty($format)) $format = 'ODT';

				$model_filename = 'suivi_graphique.odt';
				$folder_src = DIMS_APP_PATH . '/modules/system/desktopV2/templates/suivis/documents/';
				if ( ! empty($suivi_modele)){
					require_once DIMS_APP_PATH."modules/system/suivi/class_suivi_type.php";
					require_once DIMS_APP_PATH."modules/system/suivi/class_print_model.php";
					$model = new print_model();
					$model->open($suivi_modele);
					if( ! $model->isNew() ){
						$doc = new docfile();
						$doc->open($model->getDocId());
						if( ! $doc->isNew() ){
							$model_filename = "{$doc->fields['id']}_{$doc->fields['version']}.{$doc->fields['extension']}";
							$folder_src = $doc->getbasepath();
						}
					}
				}

				$modele_content = DIMS_APP_PATH . "/modules/system/desktopV2/templates/suivis/documents/tmp/content.xml" ;
				$modele_styles = DIMS_APP_PATH . "/modules/system/desktopV2/templates/suivis/documents/tmp/styles.xml" ;
				dims_deletedir(realpath('.')."/modules/system/desktopV2/templates/suivis/documents/tmp/");
				dims_makedir(realpath('.')."/modules/system/desktopV2/templates/suivis/documents/tmp/");
				$tmp_path = DIMS_APP_PATH . "/modules/system/desktopV2/templates/suivis/documents/tmp/" ;

				$output_path = DIMS_APP_PATH . "/modules/system/desktopV2/templates/suivis/documents/" ;


				if ($format != 'ODT') {
					switch($format) {
						case 'PDF':
							$output_file = $suivi->getType().'_'.$suivi->getExercice().'_'.$suivi->getId().'.pdf';
							break;
						case 'DOC':
							$output_file = $suivi->getType().'_'.$suivi->getExercice().'_'.$suivi->getId().'.doc';
							break;
						case 'SXW':
							$output_file = $suivi->getType().'_'.$suivi->getExercice().'_'.$suivi->getId().'.sxw';
							break;
						case 'RTF':
							$output_file = $suivi->getType().'_'.$suivi->getExercice().'_'.$suivi->getId().'.rtf';
							break;
						case 'XML':
							$output_file = "{$suivi_type}_{$suivi_exercice}_{$suivi_id}.xml" ;
							$output_odt = $suivi->getType().'_'.$suivi->getExercice().'_'.$suivi->getId().'.odt';
							break;
					}
				}

				$output_odt = $suivi->getType().'_'.$suivi->getExercice().'_'.$suivi->getId().'.odt';
				//die($model_filename . ' --> in '.$folder_src);
				dims_unzip($model_filename, $folder_src, DIMS_APP_PATH . '/modules/system/desktopV2/templates/suivis/documents/tmp/') ;

				$xml_content = '';
				$xml_styles = '';

				if ($f = fopen( $modele_content, "r" )) {
					while (!feof($f)) $xml_content .= fgets($f, 4096);
					fclose($f);
				}
				else die("erreur avec le fichier $modele_content");

				if ($f = fopen( $modele_styles, "r" )) {
					while (!feof($f)) $xml_styles .= fgets($f, 4096);
					fclose($f);
				}
				else die("erreur avec le fichier $modele_styles");


				global $xmlmodel;
				global $output;
				global $modeleligne;
				$output = '';

				// construction des tags à remplacer
				$xmlmodel = new xmlmodel('');
				$xmlmodel->addtag('(NOMSUIVI)', $suivi->getLibelle());
				$xmlmodel->addtag('(NUMEROSUIVI)', $suivi->getNumero());
				$xmlmodel->addtag('(TYPESUIVI)', $suivi->getType());
				$xmlmodel->addtag('(DATESUIVI)', $suivi->getDateJour());
				$xmlmodel->addtag('(ADRESSE1)', $intitule);
				$xmlmodel->addtag('(ADRESSE2)', $adresse);
				$xmlmodel->addtag('(ADRESSE3)', '');
				$xmlmodel->addtag('(CODEPOSTAL)', $codepostal);
				$xmlmodel->addtag('(VILLE)', $ville);
				$xmlmodel->addtag('(TELEPHONE)', $telclient);
				$xmlmodel->addtag('(MOBILE)', $mobileclient);
				if ($pays != $params['pays']) $xmlmodel->addtag('(PAYS)', $pays);
				else $xmlmodel->addtag('(PAYS)', '');

				$detail_commande = $suivi->get_detail();

				$c = 0;
				foreach($detail_commande['taux'] as $taux => $detail) {
					$c++;
					$xmlmodel->addtag("(TAUX{$c})", number_format(round($taux, 2), 2, ',', ' ').' %');
					$xmlmodel->addtag("(TAUX{$c}_MONTANTHT)", number_format(round($detail['total_ht'] - $detail['remise_ht'], 2), 2, ',', ' '));
					$xmlmodel->addtag("(TAUX{$c}_MONTANTTVA)", number_format(round($detail['total_tva'] - $detail['remise_tva'], 2), 2, ',', ' '));
				}

				for ($c=$c+1;$c<=5;$c++) {
					$xmlmodel->addtag("(TAUX{$c})", '');
					$xmlmodel->addtag("(TAUX{$c}_MONTANTHT)", '');
					$xmlmodel->addtag("(TAUX{$c}_MONTANTTVA)", '');
				}


				$xmlmodel->addtag('(REMISEP100)', number_format(round($suivi->fields['remise'], 2), 2, ',', ' '));
				$xmlmodel->addtag('(REMISEHT)', number_format(round($detail_commande['remise_ht'], 2), 2, ',', ' '));
				$xmlmodel->addtag('(MONTANTHT_SANS_R)', number_format(round($detail_commande['montant_ht_sans_remise'], 2), 2, ',', ' '));
				$xmlmodel->addtag('(MONTANTHT)', number_format(round($detail_commande['montant_ht'], 2), 2, ',', ' '));
				$xmlmodel->addtag('(MONTANTTVA)', number_format(round($detail_commande['montant_tva'], 2), 2, ',', ' '));
				$xmlmodel->addtag('(MONTANTTTC)', number_format(round($detail_commande['montant_ttc'], 2), 2, ',', ' '));

				// on calcule les montants 5, 10, 15, etc
				for ($jj = 5; $jj <= 95; $jj += 5) {
					$xmlmodel->addtag('(MONTANT_'.$jj.'HT)', number_format(round($detail_commande['montant_ht'] * $jj / 100, 2), 2, ',', ' '));
					$xmlmodel->addtag('(MONTANT_'.$jj.'TTC)', number_format(round($detail_commande['montant_ttc'] * $jj / 100, 2), 2, ',', ' '));
				}

				$xmlmodel->addtag('(COMMENTAIRE)', $suivi->fields['description']);
				$xmlmodel->addtag('(CONDITIONPAIEMENT)', $params['conditionpaiement']);


				$select = "
					SELECT	*
					FROM	dims_mod_business_versement
					WHERE	suivi_id = :idsuivi
					AND	suivi_type = :typesuivi
					AND	suivi_exercice = :exercicesuivi
					AND	id_workspace = :idworkspace
					ORDER BY date_paiement";

				$res=$db->query($select, array(
					':idsuivi' => array('type' => PDO::PARAM_INT, 'value' => $suivi->getId()),
					':typesuivi' => array('type' => PDO::PARAM_STR, 'value' => $suivi->fields['type']),
					':exercicesuivi' => array('type' => PDO::PARAM_STR, 'value' => $suivi->fields['exercice']),
					':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['workspaceid']),
				));

				$lignes = '';
				$xml_debug = 1;
				$montant_verse=0;
				$restant_du=0;
				$ivers=1;
				$texte_versement='';
				$montant_versement='';
				$format_date = 'd/m/Y';

				if ($db->numrows($res)>0) {
					while ($fields = $db->fetchrow($res)) {
					if ($ivers>1) {
						$texte_versement.="\r";
						$montant_versement.="\r";
					}

					$date_fr = dims_timestamp2local($fields['date_paiement']);
					$texteverse='';
					switch ($ivers) {
						case 1:
						$texteverse="1 er acompte";
						break;
						default:
						$texteverse=$ivers." ème acompte";
						break;
					}
					$texte_versement.=$texteverse." versé le ".$date_fr['date'];
					$montant_versement.=number_format(round($fields['montant'], 2), 2, ',', ' ')." €";
					$montant_verse+=$fields['montant'];
					$ivers++;
					}
				}
				else {
					$texte_versement="Acompte déjà versé";
					$restant_du=0;
					$montant_versement=number_format(round($restant_du, 2), 2, ',', ' ');
				}

				$restant_du=$detail_commande['montant_ttc']-$montant_verse;

				$xmlmodel->addtag('(TEXTE_VERSEMENT)', $texte_versement);
				$xmlmodel->addtag('(MONTANT_VERSEMENT)', $montant_versement);
				//$xmlmodel->addtag('(MONTANT_VERSEMENT)', number_format(round($montant_verse, 2), 2, ',', ' '));
				$xmlmodel->addtag('(RESTANT_DU)', number_format(round($restant_du, 2), 2, ',', ' '));

				$xml_parser = xmlparser_content();
				if (!xml_parse($xml_parser, $xml_content, TRUE)) {
					printf("Erreur XML: %s	(ligne %d)", xml_error_string(xml_get_error_code($xml_parser)), xml_get_current_line_number($xml_parser)); //  erreur de structure XML
				}

				$content = '<?xml version="1.0" encoding="UTF-8"?>'.$output;

				$xml_modeleligne = $modeleligne;

				$xml_modeleligne_versement = $modeleligneversement;

				$output = '';
				$etape = '';
				$modeleligne = '';

				$xml_parser = xmlparser_content();
				if (!xml_parse($xml_parser, $xml_styles, TRUE)) {
					printf("Erreur XML: %s	(ligne %d)", xml_error_string(xml_get_error_code($xml_parser)), xml_get_current_line_number($xml_parser)); //  erreur de structure XML
				}

				$styles = '<?xml version="1.0" encoding="UTF-8"?>'.$output;



				$select = "
					SELECT	*
					FROM	dims_mod_business_suivi_detail
					WHERE	suivi_id = :idsuivi
					AND		suivi_type = :typesuivi
					AND		suivi_exercice = :exercicesuivi
					AND	id_workspace = :idworkspace
					ORDER BY position";

				$db->query($selec, array(
					':idsuivi' => array('type' => PDO::PARAM_INT, 'value' => $suivi->getId()),
					':typesuivi' => array('type' => PDO::PARAM_STR, 'value' => $suivi->fields['type']),
					':exercicesuivi' => array('type' => PDO::PARAM_STR, 'value' => $suivi->fields['exercice']),
					':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['workspaceid']),
				));

				$lignes = '';
				$xml_debug = 1;

				while ($fields = $db->fetchrow()) {
					$xmlmodel = new xmlmodel('');
					$xmlmodel->addtag('(CODE)', $fields['code']);
					$xmlmodel->addtag('(LIBELLE)', $fields['libelle']);
					$xmlmodel->addtag('(DESCRIPTION)', $fields['description']);
					$xmlmodel->addtag('(QTE)', $fields['qte']);
					$xmlmodel->addtag('(TVA)', number_format(round($fields['tauxtva'], 2), 2, ',', ' '));
					$xmlmodel->addtag('(PU)', number_format(round($fields['pu'], 2), 2, ',', ' '));
					$xmlmodel->addtag('(MONTANT)', number_format(round($fields['pu'] * $fields['qte'], 2), 2, ',', ' '));

					$output = '';
					$etape = '';
					$modeleligne = '';
					$ligne = '';
					$params = '';


					$xml_parser = xmlparser_content();
					if (!xml_parse($xml_parser, $xml_modeleligne, TRUE)) {
						printf("Erreur XML: %s	(ligne %d)", xml_error_string(xml_get_error_code($xml_parser)), xml_get_current_line_number($xml_parser)); //  erreur de structure XML
					}

					$lignes .= $output;
				}

				$content = str_replace($xml_modeleligne,$lignes,$content);



				// Assurons nous que le fichier est accessible en écriture
				if (is_writable($output_path)) {
					if (!$handle = fopen($modele_styles, 'w')) {
						 echo "Impossible d'ouvrir le fichier $modele_styles";
						 exit;
					}

					if (fwrite($handle, $styles) === FALSE) {
						echo "Impossible d'écrire dans le fichier $modele_styles";
						exit;
					}

					if (!$handle = fopen($modele_content, 'w')) {
						 echo "Impossible d'ouvrir le fichier $modele_content";
						 exit;
					}

					if (fwrite($handle, $content) === FALSE) {
						echo "Impossible d'écrire dans le fichier $modele_content";
						exit;
					}

					fclose($handle);

					$res = array();
					$cwd = getcwd();
					chdir($tmp_path);
					shell_exec(escapeshellcmd("zip -r ".escapeshellarg("../$output_odt")." . -i *"));
					shell_exec(escapeshellcmd("rm -rf *"));
					chdir($cwd);

					if ($format != 'ODT') {
						$converter_path = realpath(DIMS_APP_PATH . '/lib/jooconverter/').'/jooconverter-2.0rc2.jar';
						$cmd = "`which java` -jar ".escapeshellarg($converter_path)." ".escapeshellarg(realpath("{$output_path}/{$output_odt}")).' '.escapeshellarg(realpath("{$output_path}")."/{$output_file}");

						// $cmd_error = shell_exec(escapeshellcmd($cmd));
						$cmd_error = shell_exec(escapeshellcmd($cmd));
						if (!$cmd_error) unlink(realpath($output_path.$output_odt));
						dims_downloadfile(realpath($output_path.$output_file),$output_file, true, true);
					}
					else {
						dims_downloadfile(realpath($output_path.$output_odt), $output_odt, true, true);
					}
				}
				else {
					echo "Le dossier $output_path n'est pas accessible en écriture.";
				}
			}
		break;
		case 'editer_suivi_detail':
			@ob_end_clean();
			$id_suivi			= dims_load_securvalue('id_suivi', dims_const::_DIMS_NUM_INPUT, true, false);
			$suivi_detail_id	= dims_load_securvalue('suivi_detail_id', dims_const::_DIMS_NUM_INPUT, true, false);

			if (!empty($id_suivi)) {
				$suivi = new suivi();
				$suivi->open($id_suivi);

		$suividetail = new suividetail();
		if (!empty($suivi_detail_id)) {
			$suividetail->open($suivi_detail_id);
		}
		else {
			$suividetail->init_description();
		}

				$suividetail->setLightAttribute('suivi', $suivi);
		$suividetail->display(DIMS_APP_PATH.'modules/system/desktopV2/templates/concepts/bloc_suivi/suivi_detail_edit.tpl.php');
			}
			die();
			break;
	case 'suivi_detail_enregistrer':
		$id_suivi			= dims_load_securvalue('id_suivi', dims_const::_DIMS_NUM_INPUT, false, true);
		$suivi_detail_id	= dims_load_securvalue('suivi_detail_id', dims_const::_DIMS_NUM_INPUT, false, true);

		if (!empty($id_suivi)) {
		$suivi = new suivi();
		$suivi->open($id_suivi);
		}

		$suividetail = new suividetail();
		if (!empty($suivi_detail_id)) {
		$suividetail->open($suivi_detail_id);
		}
		else {
		$suividetail->init_description();
		$suividetail->fields['suivi_id'] = $suivi->getId();
		$suividetail->fields['suivi_type'] = $suivi->getType();
		$suividetail->fields['suivi_exercice'] = $suivi->getExercice();
		}

		$suividetail->setvalues($_POST, 'suivi_detail_');
		$suividetail->save();

		if (!empty($id_suivi)) {
		// mise à jour du montant
		$suivi->save();

				// enregistrement en session pour réouverture du popup
				$_SESSION['desktopv2']['business']['popup'] = 'suivi_modifier';
				$_SESSION['desktopv2']['business']['id_suivi'] = $suivi->getIdSuivi();
		}

			dims_redirect($dims->getScriptEnv());
		break;
	case 'supprimer_suivi_detail':
		$id_suivi		= dims_load_securvalue('id_suivi', dims_const::_DIMS_NUM_INPUT, true, false);
		$suivi_detail_id	= dims_load_securvalue('suivi_detail_id', dims_const::_DIMS_NUM_INPUT, true, false);

		if (!empty($suivi_detail_id)) {
		$suividetail = new suividetail();
		$suividetail->open($suivi_detail_id);
		$suividetail->delete();
		}

		if (!empty($id_suivi)) {
		// mise à jour du montant
		$suivi = new suivi();
		$suivi->open($id_suivi);
		$suivi->save();

		// enregistrement en session pour réouverture du popup
		$_SESSION['desktopv2']['business']['popup'] = 'suivi_modifier';
		$_SESSION['desktopv2']['business']['suivi_id'] = $suivi->getIdSuivi();
		}

			dims_redirect($dims->getScriptEnv());
		break;
	case 'generer_facture':
	case 'dupliquer_suivi':
		$id_suivi = dims_load_securvalue('id_suivi', dims_const::_DIMS_NUM_INPUT, true, false);

		if (!empty($id_suivi)) {
		$suivi = new suivi();
		$suivi->open($id_suivi);

		if ($action == 'generer_facture') {
			$clone_suivi = $suivi->dupliquer('Facture');
		}
				else {
			$clone_suivi = $suivi->dupliquer();
		}

		// enregistrement en session pour réouverture du popup
		$_SESSION['desktopv2']['business']['popup'] = 'suivi_modifier';
		$_SESSION['desktopv2']['business']['id_suivi'] = $clone_suivi->getIdSuivi();
		}

		dims_redirect($dims->getScriptEnv());
		break;
	case 'ajouter_versement':
		$id_suivi	= dims_load_securvalue('id_suivi', dims_const::_DIMS_NUM_INPUT, false, true);
		$montant	= dims_load_securvalue('montant', dims_const::_DIMS_NUM_INPUT, false, true);

		if (!empty($id_suivi) && $montant > 0) {
		require_once DIMS_APP_PATH.'modules/system/suivi/class_versement.php';
		$suivi = new suivi();
		$suivi->open($id_suivi);
		$versement = new versement();
		$versement->fields['date_paiement'] = dims_createtimestamp();
		$versement->fields['montant'] = $montant;
		$versement->fields['suivi_id'] = $suivi->getId();
		$versement->fields['suivi_type'] = $suivi->getType();
		$versement->fields['suivi_exercice'] = $suivi->getExercice();
		$versement->save();
		$suivi->save();

		// enregistrement en session pour réouverture du popup
		$_SESSION['desktopv2']['business']['popup'] = 'suivi_modifier';
		$_SESSION['desktopv2']['business']['id_suivi'] = $id_suivi;
		}
		dims_redirect($dims->getScriptEnv());
			break;
	case 'supprimer_versement':
		$id_suivi		= dims_load_securvalue('id_suivi', dims_const::_DIMS_NUM_INPUT, true, false);
		$versement_id	= dims_load_securvalue('versement_id', dims_const::_DIMS_NUM_INPUT, true, false);

		if (!empty($id_suivi) && !empty($versement_id)) {
		require_once DIMS_APP_PATH.'modules/system/suivi/class_versement.php';
		$suivi = new suivi();
		$suivi->open($id_suivi);
		$versement = new versement();
		$versement->open($versement_id);
		$versement->delete();
		$suivi->save();

		// enregistrement en session pour réouverture du popup
		$_SESSION['desktopv2']['business']['popup'] = 'suivi_modifier';
		$_SESSION['desktopv2']['business']['id_suivi'] = $id_suivi;
		}
		dims_redirect($dims->getScriptEnv());
		break;
	case 'solder_suivi':
		$id_suivi = dims_load_securvalue('id_suivi', dims_const::_DIMS_NUM_INPUT, true, false);

		if (!empty($id_suivi)) {
		require_once DIMS_APP_PATH.'modules/system/suivi/class_versement.php';
		$suivi = new suivi();
		$suivi->open($id_suivi);
		$versement = new versement();
		$versement->fields['date_paiement'] = dims_createtimestamp();
		$versement->fields['montant'] = $suivi->getSoldeTTC();
		$versement->fields['suivi_id'] = $suivi->getId();
		$versement->fields['suivi_type'] = $suivi->getType();
		$versement->fields['suivi_exercice'] = $suivi->getExercice();
		$versement->save();
		$suivi->save();

		// enregistrement en session pour réouverture du popup
		$_SESSION['desktopv2']['business']['popup'] = 'suivi_modifier';
		$_SESSION['desktopv2']['business']['id_suivi'] = $id_suivi;
		}
		dims_redirect($dims->getScriptEnv());
			break;
		case 'display_add_todo':
			ob_clean();
			$id_record = dims_load_securvalue('id_record',dims_const::_DIMS_NUM_INPUT,true,true,true);
			$id_object = dims_load_securvalue('id_object',dims_const::_DIMS_NUM_INPUT,true,true,true);
			$id_popup = dims_load_securvalue('id_popup',dims_const::_DIMS_NUM_INPUT,true,true,true);
			if ($id_record != '' && $id_record > 0 && $id_object != '' && $id_object > 0){
				?>
			<div style="">
				<div class="actions">
					<a title="fermer les commentaires" href="Javascript: void(0);" onclick="Javascript: dims_closeOverlayedPopup('<?php echo $id_popup; ?>');">
						<img src="<? echo dims_module_oeuvre::getTemplateWebPath(); ?>/gfx/common/close.png" />
					</a>
				</div>
				<h2>
					<? echo $_SESSION['cste']['_DIMS_LABEL_ASSUR_TASK_TITLE']; ?>
				</h2>
				<div>
					<form name="add_todo_form" id="add_todo_form" method="POST" action="<? echo $dims->getScriptEnv(); ?>?action=add_todo">
						<?
							// Sécurisation du formulaire par token
							require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
							$token = new FormToken\TokenField;
							$token->field("liste_dest");
							$token->field("id_record",	$id_record);
							$token->field("id_object",	$id_object);
							$token->field("todo_dest");
							$token->field("todo_commentaire");
							$token->field("date_echeange");
							$token->field("priority");
							$tokenHTML = $token->generate();
							echo $tokenHTML;
						?>
						<input type="hidden" rel="requis" name="liste_dest" id="liste_dest"/>
						<input type="hidden" rel="requis" name="id_record" value="<? echo $id_record; ?>" />
						<input type="hidden" rel="requis" name="id_object" value="<? echo $id_object; ?>" />
						<table class="todo_form" style="width:100%;">
							<tr>
								<td>
									<label for="todo_dest"><?php echo $_SESSION['cste']['_DIMS_LABEL_ASSUR_TASK_RECIPIENTS']; ?></label>
									<span style="color:#FF3333;">*</span>
								</td>
								<td>
									<select name="todo_dest" id="todo_dest" onchange="javascript:addDest();">
										<option id="dims_nan" value="dims_nan">--</option>
										<?php
										$workspaceCurrent = new workspace();
										$workspaceCurrent->open($_SESSION['dims']['workspaceid']);
										foreach($workspaceCurrent->getUsers() as $dest) {
											?>
											<option id="<?php echo $dest['id'] ?>-<? echo dims_const::_SYSTEM_OBJECT_USER; ?>" value="<?php echo $dest['id'] ?>-<? echo dims_const::_SYSTEM_OBJECT_USER; ?>">
												<?php echo $dest['firstname'].' '.$dest['lastname'] ?>
											</option>
											<?php
										}
										$db = dims::getInstance()->db;
										$params = array();
										$sql = "SELECT		*
												FROM		dims_group
												WHERE		id_workspace IN (0,".$db->getParamsFromArray(explode(',', dims_viewworkspaces($_SESSION['dims']['moduleid'])), 'idworkspace', $params).")
												AND		id_group > 0
												ORDER BY	label";
										$res = $db->query($sql, $params);
										$lstGr = array();
										while($r = $db->fetchrow($res)){
											?>
											<option id="<?php echo $r['id'] ?>-<? echo dims_const::_SYSTEM_OBJECT_GROUP; ?>" value="<?php echo $r['id'] ?>-<? echo dims_const::_SYSTEM_OBJECT_GROUP; ?>">
												<?php echo $r['label'].' ('.$_SESSION['cste']['_GROUP'].')'; ?>
											</option>
											<?php
										}
										?>
									</select>
									<div id="error_add_dest"></div>
								</td>
							</tr>
							<tr>
								<td colspan="2">
									<table class="liste_dests">
									</table>
									<div id="def_liste_dest" class="todo_form_error"></div>
								</td>
							</tr>
							<tr>
								<td colspan="2">
									<label for="todo_dest"><?php echo $_SESSION['cste']['_DIMS_COMMENTS']; ?></label>
									<span style="color:#FF3333;">*</span>
								</td>
							</tr>
							<tr>
								<td colspan="2">
									<textarea style="width:98%;resize:vertical;height:80px;" id="todo_commentaire" rel="requis" name="todo_commentaire"></textarea>
									<div id="def_todo_commentaire" class="todo_form_error"></div>
								</td>
							</tr>
							<tr>
								<td>
									<label for="todo_dest"><?php echo $_SESSION['cste']['_OEUVRE_MATURITY_DATE']; ?><span style="color:#FF3333;">*</span> :</label>
								</td>
								<td>
									<input rev="date_jj/mm/yyyy" rel="requis" style="width:90px;" type="text" name="date_echeange" id="date_echeange" maxlength="10" />
									<div id="def_date_echeange" class="todo_form_error"></div>
								</td>
							</tr>
							<tr>
								<td colspan="2">
									<label for="todo_dest"><?php echo $_SESSION['cste']['_FORM_TASK_PRIORITY']; ?> :</label>
									<input type="radio" name="priority" value="1"/><?php echo $_SESSION['cste']['_DIMS_LABEL_ASSUR_TASK_PRIORITY_LOW']; ?>
									<input type="radio" name="priority" value="2" checked="checked"/><?php echo $_SESSION['cste']['_DIMS_LABEL_ASSUR_TASK_PRIORITY_NORMAL']; ?>
									<input type="radio" name="priority" value="3"/><?php echo $_SESSION['cste']['_DIMS_LABEL_ASSUR_TASK_PRIORITY_HIGH']; ?>
								</td>
							</tr>
							<tr>
								<td colspan="2">
									<div class="todo_button">
										<span style="color:#FF3333;">*</span> <? echo $_SESSION['cste']['_DIMS_LABEL_MANDATORY_FIELDS']; ?>
										<input type="submit" value="<?php echo $_SESSION['cste']['_DIMS_SEND']; ?>" />
										&nbsp;<? echo $_SESSION['cste']['_DIMS_OR']; ?>&nbsp;
										<a href="Javascript: void(0);" onclick="javascript: dims_closeOverlayedPopup('<?php echo $id_popup; ?>');">
											<?php echo $_SESSION['cste']['_DIMS_CLOSE']; ?>
										</a>
										<div id="global_error" class="todo_form_error"></div>
									</div>
								</td>
							</tr>
						</table>
					</form>
				</div>
			</div>
			<script type="text/javascript">
				$(document).ready(function() {
					$('#date_echeange').datepicker({dateFormat: 'dd/mm/yy',
													changeMonth: true,
													changeYear: true,
													buttonImage: '<?php echo dims_module_oeuvre::getTemplateWebPath('/gfx/oeuvre/calendar.png'); ?>',
													showOn: 'both',
													buttonImageOnly: true,
													buttonText: '<? echo $_SESSION['cste']['_OEUVRE_SELECT_DATE']; ?>'});
					$('form#add_todo_form').dims_validForm({messages: {	defaultError: '<? echo $_SESSION['cste']['_OEUVRE_THIS_FIELD_IS_COMPULSORY']; ?>',
															formatDate: '<? echo $_SESSION['cste']['_OEUVRE_ERROR_FORMAT_DATE']; ?>',
															globalMessage: '<? echo $_SESSION['cste']['_OEUVRE_ERROR_FIELDS_SEIZED']; ?>'
															},
													displayMessages: true,
													refId: 'def',
													globalId: 'global_error',
													classInput: 'dims_error_input'});
				});
			</script>
				<?
			}
			die();
			break;
		case 'save_todo':
			$id_todo = dims_load_securvalue('id_todo',dims_const::_DIMS_NUM_INPUT,true,true,false);
			if ($id_todo != '' && $id_todo > 0){
				if(_DIMS_ENCODING == "UTF-8"){
					$encoding = "UTF-8" ;
				}else{
					$encoding = "ISO-8859-1";
				}
				$comment	= nl2br(htmlentities(dims_load_securvalue('todo_commentaire',dims_const::_DIMS_CHAR_INPUT, true, true, false), ENT_COMPAT, $encoding));
				$dests		= dims_load_securvalue('liste_dest',dims_const::_DIMS_CHAR_INPUT, true, true);
				$priority	= dims_load_securvalue('priority',dims_const::_DIMS_NUM_INPUT, true, true);
				if(!empty($dests)) {//inutile de faire quoi que ce soit s'il n'y a pas de destinataire

					$db = dims::getInstance()->getDb();
					$todo = new todo();
					$todo->open($id_todo);

					$todo->setContent($comment);
					$todo->setPriority($priority);
					$fin = dims_load_securvalue('date_echeange',dims_const::_DIMS_CHAR_INPUT,true,true);
					$fin2 = explode('/',$fin);
					$todo->fields['date_term'] = $fin2[2]."-".$fin2[1]."-".$fin2[0]." 00:00:00";
					$todo->save();

					//gestion des destinataires
					$tab = explode('|',$dests);
					$todo->removeDests();
					foreach($tab as $d){
						$valD = explode('-',$d);
						switch($valD[1]){
							case dims_const::_SYSTEM_OBJECT_USER :
								$todo->addDestinataire($valD[0], $_SESSION['dims']['userid']);
								break;
							case dims_const::_SYSTEM_OBJECT_GROUP :
								$todo->addGrDestinataire($valD[0], $_SESSION['dims']['userid']);
								break;
						}
					}
					$_SESSION['oeuvre']['todo']['add_message'] = "Le todo a été envoyé";

				}
				switch($todo->fields['id_object']){
					case dims_const::_SYSTEM_OBJECT_OEUVRE:
						dims_redirect(dims::getInstance()->getScriptenv().'?id_oeuvre='.$todo->fields['id_record']."&sub=".dims_const_oeuvre::_OEUVRE_FICHE_TODO_VISU."&todo=".$id_todo);
						break;
					case dims_const::_SYSTEM_OBJECT_TIERS:
					case dims_const::_SYSTEM_OBJECT_CONTACT:
						dims_redirect(dims::getInstance()->getScriptenv()."?sub=".dims_const_oeuvre::_OEUVRE_FICHE_CT_TODO_VISU."&todo=".$id_todo);
						break;
					default:
						dims_redirect(dims::getInstance()->getScriptenv());
						break;
				}
			}else
				dims_redirect(dims::getInstance()->getScriptenv());
			break;
		case 'valid_todo':
			ob_clean();
			$id = dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true, true,true);
			$rid=0;
			if ($id > 0 && $id != '') {
				require_once DIMS_APP_PATH.'include/class_todo.php';
				$todo = new todo();
				$todo->openWithGB($id);
				$todo->valideTodo(dims_load_securvalue('commentaire',dims_const::_DIMS_CHAR_INPUT,true,true,false));
				$rid = $todo->fields['id'];
			}
			die();
			break;
		case 'unvalid_todo':
			ob_clean();
			$id = dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true, true,true);
			$rid=0;
			if ($id > 0 && $id != '') {
				require_once DIMS_APP_PATH.'include/class_todo.php';
				$todo = new todo();
				$todo->openWithGB($id);
				$todo->unvalideTodo();
				$rid = $todo->fields['id'];
			}
			die();
			break;

		// ACTIVITES
		case 'load_activities':
			ob_clean();
			require DIMS_APP_PATH.'modules/system/activity/class_activity.php';
			$table = new dims_activity();
			skin_common::table_ajax_managing($table);
			die();
			break;
		case 'activity_get_linked_objects':
			$activity_id_go = dims_load_securvalue('activity_id_go', dims_const::_DIMS_NUM_INPUT, true, false, false);

			if ($activity_id_go) {
				require DIMS_APP_PATH.'modules/system/class_search.php';
				$matrix = new search();
				$linkedObjectsIds = $matrix->exploreMatrice($_SESSION['dims']['workspaceid'], null, array($activity_id_go));

				$a_objects = array( 'contacts' => array(), 'opportunities' => array(), 'docs' => array() );
				if (!empty($linkedObjectsIds['distribution']['contacts'])) {
					$params = array();
					$rs = $db->query('
						SELECT	c.*, t.*
						FROM	dims_mod_business_contact c
						LEFT JOIN	dims_mod_business_tiers_contact tc
						ON			tc.id_contact = c.id
						AND		tc.type_lien = \'employer\'
						LEFT JOIN	dims_mod_business_tiers t
						ON			t.id = tc.id_tiers
						WHERE	c.id_globalobject IN ('.$db->getParamsFromArray(array_keys($linkedObjectsIds['distribution']['contacts']), 'idglobalobject', $params).')
						GROUP BY c.id', $params);
					if ($db->numrows($rs)) {
						foreach ($separation = $db->split_resultset($rs) as $sep) {
							if ($sep['c']['photo'] != '') {
								$contact = new contact();
								$contact->openFromResultSet($sep['c']);
								$sep['c']['photoPath'] = $contact->getPhotoWebPath(40);
							}
							else {
								$sep['c']['photoPath'] = _DESKTOP_TPL_PATH.'/gfx/common/human40.png';
							}

							if ($sep['t']['intitule'] == null) {
								$sep['t']['intitule'] = '';
							}

							// on ajoute le contact a la liste
							if (!isset($_SESSION['desktopv2']['activity']['ct_added'])) {
								$_SESSION['desktopv2']['activity']['ct_added'] = array();
							}
							$_SESSION['desktopv2']['activity']['ct_added'][$sep['c']['id_globalobject']] = $sep['c']['id_globalobject'];

							$a_objects['contacts'][] = $sep;
						}
					}
				}
				if (!empty($linkedObjectsIds['distribution']['opportunities'])) {
					$params = array();
					$rs = $db->query('
						SELECT	*
						FROM	dims_mod_business_action
						WHERE	id_globalobject IN ('.$db->getParamsFromArray(array_keys($linkedObjectsIds['distribution']['opportunities']), 'idglobalobject', $params).')', $params);
					if ($db->numrows($rs)) {
						while ($row = $db->fetchrow($rs)) {
							// stripslashes
							foreach ($row as $k => $v) { $row[$k] = stripslashes($v); }

							// on ajoute l'opportunité a la liste
							if (!isset($_SESSION['desktopv2']['activity']['opp_added'])) {
								$_SESSION['desktopv2']['activity']['opp_added'] = array();
							}
							$_SESSION['desktopv2']['activity']['opp_added'][$row['id_globalobject']] = $row['id_globalobject'];

							$a_objects['opportunities'][] = $row;
						}
					}
				}
				if (!empty($linkedObjectsIds['distribution']['docs'])) {
					$params = array();
					$rs = $db->query('
						SELECT	*
						FROM	dims_mod_doc_file
						WHERE	id_globalobject IN ('.$db->getParamsFromArray(array_keys($linkedObjectsIds['distribution']['docs']), 'idglobalobject', $params).')', $params);
					if ($db->numrows($rs)) {
						while ($row = $db->fetchrow($rs)) {
							// stripslashes
							foreach ($row as $k => $v) { $row[$k] = stripslashes($v); }

							// on ajoute le document a la liste
							if (!isset($_SESSION['desktopv2']['activity']['doc_added'])) {
								$_SESSION['desktopv2']['activity']['doc_added'] = array();
							}
							$_SESSION['desktopv2']['activity']['doc_added'][$row['id_globalobject']] = $row['id_globalobject'];

							$a_objects['docs'][] = $row;
						}
					}
				}
			}
			die(json_encode($a_objects));
			break;
		case 'suivi_search_article':
			$searchString = dims_load_securvalue('searchString', dims_const::_DIMS_CHAR_INPUT, true, false, true);

			$articles = array();

			if ($searchString != '') {
				require_once(DIMS_APP_PATH . '/modules/catalogue/include/class_catalogue.php');
				$cata = new catalogue();

				$articles=$cata->getArticles($searchString);
			}
			die(json_encode($articles));
			break;

		case 'activity_search_contact':
			$searchString = dims_load_securvalue('searchString', dims_const::_DIMS_CHAR_INPUT, true, false, true);

			$a_contacts = array();

			if ($searchString != '') {
				$params = array();
				$sql = '
					SELECT *
					FROM dims_mod_business_contact
					WHERE	(
						lastname LIKE :searchstring
						OR firstname LIKE :searchstring
						OR email LIKE :searchstring
						)
					AND	inactif = 0';
				$params[':searchstring'] = array('type' => PDO::PARAM_STR, 'value' => $searchString);
				if (!empty($_SESSION['desktopv2']['activity']['ct_added'])) {
					$sql .= ' AND id_globalobject NOT IN ('.$db->getParamsFromArray($_SESSION['desktopv2']['activity']['ct_added'], 'idglobalobject', $params).')';
				}
				$sql .= ' ORDER BY lastname, firstname';
				$rs = $db->query($sql, $params);
				while ($row = $db->fetchrow($rs)) {
					$contact = new contact();
					$contact->openFromResultSet($row);
					$a_contacts[] = $contact->fields;
				}
			}
			die(json_encode($a_contacts));
			break;
		case 'activity_add_contact':
			$contact_id_go = dims_load_securvalue('contact_id_go', dims_const::_DIMS_NUM_INPUT, true, false, false);

			// recherche du contact, de sa photo et son employeur
			$rs = $db->query('
				SELECT	c.*, t.*
				FROM	dims_mod_business_contact c
				LEFT JOIN	dims_mod_business_tiers_contact tc
				ON			tc.id_contact = c.id
				AND		tc.type_lien = \'employer\'
				LEFT JOIN	dims_mod_business_tiers t
				ON			t.id = tc.id_tiers
				WHERE	c.id_globalobject = :idglobalobject
				LIMIT 0, 1', array(
				':idglobalobject' => array('type' => PDO::PARAM_INT, 'value' => $contact_id_go),
			));
			if ($db->numrows($rs)) {
				$separation = $db->split_resultset($rs);
				if ($separation[0]['c']['photo'] != '') {
					$contact = new contact();
					$contact->openFromResultSet($separation[0]['c']);
					$separation[0]['c']['photoPath'] = $contact->getPhotoWebPath(40);
				}
				else {
					$separation[0]['c']['photoPath'] = _DESKTOP_TPL_PATH.'/gfx/common/human40.png';
				}

				if ($separation[0]['t']['intitule'] == null) {
					$separation[0]['t']['intitule'] = '';
				}

				// on ajoute le contact a la liste
				if (!isset($_SESSION['desktopv2']['activity']['ct_added'])) {
					$_SESSION['desktopv2']['activity']['ct_added'] = array();
				}
				$_SESSION['desktopv2']['activity']['ct_added'][$contact_id_go] = $contact_id_go;

				die(json_encode($separation[0]));
			}
			else {
				die();
			}

			break;
		case 'activity_remove_contact':
			$contact_id_go = dims_load_securvalue('contact_id_go', dims_const::_DIMS_NUM_INPUT, true, false, false);
			if (isset($_SESSION['desktopv2']['activity']['ct_added'][$contact_id_go])) {
				dims_print_r($_SESSION['desktopv2']['activity']['ct_added']);
				unset($_SESSION['desktopv2']['activity']['ct_added'][$contact_id_go]);
			}
			dims_print_r($_SESSION['desktopv2']['activity']['ct_added']);
			die();
			break;
		case 'activity_search_opportunity':
			require_once DIMS_APP_PATH.'/modules/system/activity/class_activity.php';
			$searchString = dims_load_securvalue('searchString', dims_const::_DIMS_CHAR_INPUT, true, true, true);

			$a_opps = array();

			if ($searchString != '') {
				$params = array();
				$sql = '
					SELECT *
					FROM '.dims_activity::TABLE_NAME.'
					WHERE libelle LIKE :searchstring
					AND typeaction = \''.dims_activity::TYPE_ACTION.'\'';
				$params[':searchstring'] = array('type' => PDO::PARAM_STR, 'value' => '%'.$searchString.'%');
				if (!empty($_SESSION['desktopv2']['activity']['opp_added'])) {
					$sql .= ' AND id_globalobject NOT IN ('.$db->getParamsFromArray($_SESSION['desktopv2']['activity']['opp_added'], 'idglobalobject', $params).')';
				}
				$sql .= ' ORDER BY libelle';
				$rs = $db->query($sql, $params);
				while ($row = $db->fetchrow($rs)) {
					$opp = new dims_activity();
					$opp->openFromResultSet($row);
					$opp->fields['libelle'] = stripslashes($opp->fields['libelle']);
					$a_opps[] = $opp->fields;
				}
			}
			die(json_encode($a_opps));
			break;
		case 'activity_add_opportunity':
			$opportunity_id_go = dims_load_securvalue('opportunity_id_go', dims_const::_DIMS_NUM_INPUT, true, false, false);

			// recherche de l'opportunité
			$rs = $db->query('
				SELECT	*
				FROM	dims_mod_business_action
				WHERE	id_globalobject = :idglobalobject
				LIMIT 0, 1', array(
				':idglobalobject' => array('type' => PDO::PARAM_INT, 'value' => $opportunity_id_go),
			));
			if ($db->numrows($rs)) {
				$row = $db->fetchrow($rs);
				$row['libelle'] = stripslashes($row['libelle']);

				// on ajoute l'opportunité a la liste
				if (!isset($_SESSION['desktopv2']['activity']['opp_added'])) {
					$_SESSION['desktopv2']['activity']['opp_added'] = array();
				}
				$_SESSION['desktopv2']['activity']['opp_added'][$opportunity_id_go] = $opportunity_id_go;

				die(json_encode($row));
			}
			else {
				die();
			}
			break;
		case 'activity_remove_opportunity':
			$opportunity_id_go = dims_load_securvalue('opportunity_id_go', dims_const::_DIMS_NUM_INPUT, true, false, false);
			if (isset($_SESSION['desktopv2']['activity']['opp_added'][$opportunity_id_go])) {
				unset($_SESSION['desktopv2']['activity']['opp_added'][$opportunity_id_go]);
			}
			die();
			break;
		case 'activity_search_document':
			$searchString = dims_load_securvalue('searchString', dims_const::_DIMS_CHAR_INPUT, true, false, true);

			$a_documents = array();

			if ($searchString != '') {
				$sql = '
					SELECT *
					FROM dims_mod_doc_file
					WHERE	(
						name LIKE \'%'.$searchString.'%\'
						OR description LIKE \'%'.$searchString.'%\'
						)';
				$params[':searchstring'] = array('type' => PDO::PARAM_STR, 'value' => '%'.$searchString.'%');
				if (!empty($_SESSION['desktopv2']['activity']['doc_added'])) {
					$sql .= ' AND id_globalobject NOT IN ('.$db->getParamsFromArray($_SESSION['desktopv2']['activity']['doc_added'], 'idglobalobject', $params).')';
				}
				$sql .= ' ORDER BY name';
				$rs = $db->query($sql, $params);
				while ($row = $db->fetchrow($rs)) {
					$doc = new docfile();
					$doc->openFromResultSet($row);
					$a_documents[] = $doc->fields;
				}
			}
			die(json_encode($a_documents));
			break;
		case 'activity_add_document':
			$doc_id_go = dims_load_securvalue('doc_id_go', dims_const::_DIMS_NUM_INPUT, true, false, false);

			// recherche du document
			$rs = $db->query('
				SELECT	*
				FROM	dims_mod_doc_file
				WHERE	id_globalobject = :idglobalobject
				LIMIT 0, 1', array(
				':idglobalobject' => array('type' => PDO::PARAM_INT, 'value' => $doc_id_go),
			));
			if ($db->numrows($rs)) {
				// on ajoute le contact a la liste
				if (!isset($_SESSION['desktopv2']['activity']['doc_added'])) {
					$_SESSION['desktopv2']['activity']['doc_added'] = array();
				}
				$_SESSION['desktopv2']['activity']['doc_added'][$doc_id_go] = $doc_id_go;

				die(json_encode($db->fetchrow($rs)));
			}
			else {
				die();
			}

			break;
		case 'activity_remove_document':
			$doc_id_go = dims_load_securvalue('doc_id_go', dims_const::_DIMS_NUM_INPUT, true, false, false);
			if (isset($_SESSION['desktopv2']['activity']['doc_added'][$doc_id_go])) {
				unset($_SESSION['desktopv2']['activity']['doc_added'][$doc_id_go]);
			}
			die();
			break;

		// OPPORTUNITES (LEADS)
		case 'load_leads':
			ob_clean();
			require DIMS_APP_PATH.'modules/system/leads/class_lead.php';
			$table = new dims_lead();
			skin_common::table_ajax_managing($table);
			die();
			break;
		case 'load_appointment':
			ob_clean();
			require DIMS_APP_PATH.'modules/system/appointment_offer/class_appointment_offer.php';
			$table = new dims_appointment_offer();
			skin_common::table_ajax_managing($table);
			die();
			break;
		case 'lead_get_linked_objects':
			$lead_id_go = dims_load_securvalue('lead_id_go', dims_const::_DIMS_NUM_INPUT, true, false, false);

			if ($lead_id_go) {
				require DIMS_APP_PATH.'modules/system/class_search.php';
				$matrix = new search();
				$linkedObjectsIds = $matrix->exploreMatrice($_SESSION['dims']['workspaceid'], null, null, array($lead_id_go));

				$a_objects = array( 'contacts' => array(), 'docs' => array() );
				if (!empty($linkedObjectsIds['distribution']['contacts'])) {
					$params = array();
					$rs = $db->query('
						SELECT	c.*, t.*
						FROM	dims_mod_business_contact c
						LEFT JOIN	dims_mod_business_tiers_contact tc
						ON			tc.id_contact = c.id
						AND		tc.type_lien = \'employer\'
						LEFT JOIN	dims_mod_business_tiers t
						ON			t.id = tc.id_tiers
						WHERE	c.id_globalobject IN ('.$db->getParamsFromArray(array_keys($linkedObjectsIds['distribution']['contacts']), 'idglobalobject', $params).')
						GROUP BY c.id', $params);
					if ($db->numrows($rs)) {
						foreach ($separation = $db->split_resultset($rs) as $sep) {
							if ($sep['c']['photo'] != '') {
								$contact = new contact();
								$contact->openFromResultSet($sep['c']);
								$sep['c']['photoPath'] = $contact->getPhotoWebPath(40);
							}
							else {
								$sep['c']['photoPath'] = _DESKTOP_TPL_PATH.'/gfx/common/human40.png';
							}

							if ($sep['t']['intitule'] == null) {
								$sep['t']['intitule'] = '';
							}

							// on ajoute le contact a la liste
							if (!isset($_SESSION['desktopv2']['lead']['ct_added'])) {
								$_SESSION['desktopv2']['lead']['ct_added'] = array();
							}
							$_SESSION['desktopv2']['lead']['ct_added'][$sep['c']['id_globalobject']] = $sep['c']['id_globalobject'];

							$a_objects['contacts'][] = $sep;
						}
					}
				}
				if (!empty($linkedObjectsIds['distribution']['docs'])) {
					$params = array();
					$rs = $db->query('
						SELECT	*
						FROM	dims_mod_doc_file
						WHERE	id_globalobject IN ('.$db->getParamsFromArray(array_keys($linkedObjectsIds['distribution']['docs']), 'idglobalobject', $params).')', $params);
					if ($db->numrows($rs)) {
						while ($row = $db->fetchrow($rs)) {
							// on ajoute le document a la liste
							if (!isset($_SESSION['desktopv2']['lead']['doc_added'])) {
								$_SESSION['desktopv2']['lead']['doc_added'] = array();
							}
							$_SESSION['desktopv2']['lead']['doc_added'][$row['id_globalobject']] = $row['id_globalobject'];

							$a_objects['docs'][] = $row;
						}
					}
				}
			}
			die(json_encode($a_objects));
			break;
		case 'lead_search_contact':
			$searchString = dims_load_securvalue('searchString', dims_const::_DIMS_CHAR_INPUT, true, false, true);

			$a_contacts = array();

			if ($searchString != '') {
				$params = array();
				$sql = '
					SELECT *
					FROM dims_mod_business_contact
					WHERE	(
						lastname LIKE :searchstring
						OR firstname LIKE :searchstring
						OR email LIKE :searchstring
						)
					AND	inactif = 0';
				$params[':searchstring'] = array('type' => PDO::PARAM_STR, 'value' => '%'.$searchString.'%');
				if (!empty($_SESSION['desktopv2']['lead']['ct_added'])) {
					$sql .= ' AND id_globalobject NOT IN ('.$db->getParamsFromArray($_SESSION['desktopv2']['lead']['ct_added'], 'idglobalobject', $params).')';
				}
				$sql .= ' ORDER BY lastname, firstname';

				$rs = $db->query($sql, $params);
				while ($row = $db->fetchrow($rs)) {
					$contact = new contact();
					$contact->openFromResultSet($row);
					$a_contacts[] = $contact->fields;
				}
			}
			die(json_encode($a_contacts));
			break;
		case 'lead_add_contact':
			$contact_id_go = dims_load_securvalue('contact_id_go', dims_const::_DIMS_NUM_INPUT, true, false, false);

			// recherche du contact, de sa photo et son employeur
			$rs = $db->query('
				SELECT	c.*, t.*
				FROM	dims_mod_business_contact c
				LEFT JOIN	dims_mod_business_tiers_contact tc
				ON			tc.id_contact = c.id
				AND		tc.type_lien = \'employer\'
				LEFT JOIN	dims_mod_business_tiers t
				ON			t.id = tc.id_tiers
				WHERE	c.id_globalobject = :idglobalobject
				LIMIT 0, 1', array(
				':idglobalobject' => array('type' => PDO::PARAM_INT, 'value' => $contact_id_go),
			));
			if ($db->numrows($rs)) {
				$separation = $db->split_resultset($rs);
				if ($separation[0]['c']['photo'] != '') {
					$contact = new contact();
					$contact->openFromResultSet($separation[0]['c']);
					$separation[0]['c']['photoPath'] = $contact->getPhotoWebPath(40);
				}
				else {
					$separation[0]['c']['photoPath'] = _DESKTOP_TPL_PATH.'/gfx/common/human40.png';
				}

				if ($separation[0]['t']['intitule'] == null) {
					$separation[0]['t']['intitule'] = '';
				}

				// on ajoute le contact a la liste
				if (!isset($_SESSION['desktopv2']['lead']['ct_added'])) {
					$_SESSION['desktopv2']['lead']['ct_added'] = array();
				}
				$_SESSION['desktopv2']['lead']['ct_added'][$contact_id_go] = $contact_id_go;

				die(json_encode($separation[0]));
			}
			else {
				die();
			}

			break;
		case 'lead_remove_contact':
			$contact_id_go = dims_load_securvalue('contact_id_go', dims_const::_DIMS_NUM_INPUT, true, false, false);
			if (isset($_SESSION['desktopv2']['lead']['ct_added'][$contact_id_go])) {
				unset($_SESSION['desktopv2']['lead']['ct_added'][$contact_id_go]);
			}
			die();
			break;
		case 'lead_search_document':
			$searchString = dims_load_securvalue('searchString', dims_const::_DIMS_CHAR_INPUT, true, false, true);

			$a_documents = array();

			if ($searchString != '') {
				$params = array();
				$sql = '
					SELECT *
					FROM dims_mod_doc_file
					WHERE	(
						name LIKE :searchstring
						OR description LIKE :searchstring
						)';
				$params[':searchstring'] = array('type' => PDO::PARAM_STR, 'value' => '%'.$searchString.'%');
				if (!empty($_SESSION['desktopv2']['lead']['doc_added'])) {
					$sql .= ' AND id_globalobject NOT IN ('.$db->getParamsFromArray($_SESSION['desktopv2']['lead']['doc_added'], 'idglobalobject', $params).')';
				}
				$sql .= ' ORDER BY name';
				$rs = $db->query($sql, $params);
				while ($row = $db->fetchrow($rs)) {
					$doc = new docfile();
					$doc->openFromResultSet($row);
					$a_documents[] = $doc->fields;
				}
			}
			die(json_encode($a_documents));
			break;
		case 'lead_add_document':
			$doc_id_go = dims_load_securvalue('doc_id_go', dims_const::_DIMS_NUM_INPUT, true, false, false);

			// recherche du document
			$rs = $db->query('
				SELECT	*
				FROM	dims_mod_doc_file
				WHERE	id_globalobject = :idglobalobject
				LIMIT 0, 1', array(
				':idglobalobject' => array('type' => PDO::PARAM_INT, 'value' => $doc_id_go),
			));
			if ($db->numrows($rs)) {
				// on ajoute le contact a la liste
				if (!isset($_SESSION['desktopv2']['lead']['doc_added'])) {
					$_SESSION['desktopv2']['lead']['doc_added'] = array();
				}
				$_SESSION['desktopv2']['lead']['doc_added'][$doc_id_go] = $doc_id_go;

				die(json_encode($db->fetchrow($rs)));
			}
			else {
				die();
			}

			break;
		case 'lead_remove_document':
			$doc_id_go = dims_load_securvalue('doc_id_go', dims_const::_DIMS_NUM_INPUT, true, false, false);
			if (isset($_SESSION['desktopv2']['lead']['doc_added'][$doc_id_go])) {
				unset($_SESSION['desktopv2']['lead']['doc_added'][$doc_id_go]);
			}
			die();
			break;

		case 'client_refresh_city':
			ob_clean();
			$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
			$ref = dims_load_securvalue('ref',dims_const::_DIMS_CHAR_INPUT,true,true,true);
			$sel = dims_load_securvalue('sel',dims_const::_DIMS_NUM_INPUT,true,true,true);
			$search = dims_load_securvalue('search',dims_const::_DIMS_CHAR_INPUT,true,true,true);
			?>
			<option value=""></option>
			<?php
			if ($id != '' && $id > 0){
				require_once DIMS_APP_PATH.'modules/system/class_country.php';
				$country = new country();
				$country->open($id);
				foreach($country->getAllCity() as $city){
					if($sel == $city->get('id'))
						echo '<option value="'.$city->fields['id'].'" selected="true">'.$city->fields['label'].'</option>';
					else
						echo '<option value="'.$city->fields['id'].'">'.$city->fields['label'].'</option>';
				}
			}
			die();
			break;
		case 'client_refresh_city_by_label':
			ob_clean();
			$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
			$ref = dims_load_securvalue('ref',dims_const::_DIMS_CHAR_INPUT,true,true,true);
			$sel = dims_load_securvalue('sel',dims_const::_DIMS_NUM_INPUT,true,true,true);
			$search = dims_load_securvalue('search',dims_const::_DIMS_CHAR_INPUT,true,true,true);

			if ($id != '' && $id > 0){
				require_once DIMS_APP_PATH.'modules/system/class_country.php';
				$country = new country();
				$country->open($id);
				$a_cities = $country->getAllCitiesByLabel($search);
				if (sizeof($a_cities)) {
					echo '<option value=""></option>';
					foreach($a_cities as $city){
						if($sel == $city->get('id'))
							echo '<option value="'.$city->fields['id'].'" selected="true">'.$city->fields['label'].'</option>';
						else
							echo '<option value="'.$city->fields['id'].'">'.$city->fields['label'].'</option>';
					}
				}
			}
			die();
			break;
		case 'add_new_city':
			ob_clean();
			$val = trim(dims_load_securvalue('val',dims_const::_DIMS_CHAR_INPUT,true,true,true));
			$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
			if ($val != '' && $id != '' && $id > 0){
				require_once DIMS_APP_PATH.'modules/system/class_city.php';
				$city = new city();
				$city->init_description();
				$city->fields['id_country'] = $id;
				$city->fields['label'] = $val;
				if ($city->save()){
					$elem = array();
					$elem['id'] = $city->fields['id'];
					$elem['label'] = $city->fields['label'];
					die(json_encode($elem));
				}
			}
			die();
			break;
		case 'verify_company_title':
			ob_clean();
			$val = trim(dims_load_securvalue('value',dims_const::_DIMS_CHAR_INPUT,true,true,true));
			if (!empty($val)) {
				$resultats = array();

				require_once(DIMS_APP_PATH . "/modules/system/class_search.php");
				$dims = dims::getInstance();
				$dimsearch = new search($dims);
				global $_DIMS;

				// ajout des objects sur lequel la recherche va se baser
				$dimsearch->addSearchObject($_SESSION['dims']['moduleid'], dims_const::_SYSTEM_OBJECT_TIERS, $_DIMS['cste']['_DIMS_LABEL_GROUP_LIST']);

				// reinitialise la recherche sur ce module courant, n'efface pas le cache result
				$dimsearch->initSearchObject();

				$rech			= $val;
				$kword			= dims_load_securvalue('kword', dims_const::_DIMS_CHAR_INPUT, true, true);
				$idmodule		= dims_load_securvalue('idmodule', dims_const::_DIMS_CHAR_INPUT, true, true);
				$idobj			= dims_load_securvalue('idobj', dims_const::_DIMS_CHAR_INPUT, true, true);
				$idmetafield	= dims_load_securvalue('idmetafield', dims_const::_DIMS_CHAR_INPUT, true, true);
				$sens			= dims_load_securvalue('sens', dims_const::_DIMS_CHAR_INPUT, true, true);

				$dimsearch->executeSearch($rech, $kword, $idmodule, $idobj, $idmetafield, $sens);

				$i = 0;
				foreach ($_SESSION['dims']['modsearch']['tabresultat'][1][dims_const::_SYSTEM_OBJECT_TIERS] as $id_tiers) {
					$tiers = new tiers();
					$tiers->open($id_tiers);
					$resultats[$i]['intitule'] = $tiers->getIntitule();
					$resultats[$i]['link'] = $dims->getScriptEnv().'?submenu='._DESKTOP_V2_CONCEPTS.'&id='.$tiers->fields['id'].'&type='.dims_const::_SYSTEM_OBJECT_TIERS.'&init_filters=1&from=desktop';
					$i++;
				}
				echo json_encode($resultats);
			}
			die();
			break;
		case 'verify_contact_name':
			ob_clean();
			$val = trim(dims_load_securvalue('value',dims_const::_DIMS_CHAR_INPUT,true,true,true));
			if (!empty($val)) {
				$resultats = array();

				require_once(DIMS_APP_PATH . "/modules/system/class_search.php");
				$dims = dims::getInstance();
				$dimsearch = new search($dims);
				global $_DIMS;

				// ajout des objects sur lequel la recherche va se baser
				$dimsearch->addSearchObject($_SESSION['dims']['moduleid'], dims_const::_SYSTEM_OBJECT_CONTACT, $_DIMS['cste']['_DIMS_LABEL_GROUP_LIST']);

				// reinitialise la recherche sur ce module courant, n'efface pas le cache result
				$dimsearch->initSearchObject();

				$rech			= $val;
				$kword			= dims_load_securvalue('kword', dims_const::_DIMS_CHAR_INPUT, true, true);
				$idmodule		= dims_load_securvalue('idmodule', dims_const::_DIMS_CHAR_INPUT, true, true);
				$idobj			= dims_load_securvalue('idobj', dims_const::_DIMS_CHAR_INPUT, true, true);
				$idmetafield	= dims_load_securvalue('idmetafield', dims_const::_DIMS_CHAR_INPUT, true, true);
				$sens			= dims_load_securvalue('sens', dims_const::_DIMS_CHAR_INPUT, true, true);

				$dimsearch->executeSearch2($rech, $kword, $_SESSION['dims']['moduleid'], $idobj, $idmetafield, $sens,0,null,$_SESSION['dims']['workspaceid']);

				$i = 0;
				foreach ($_SESSION['dims']['modsearch']['tabresultat'][1][dims_const::_SYSTEM_OBJECT_CONTACT] as $go => $datas) {
					$contact = new contact();
					$contact->openWithGB($go);
					$resultats[$i]['firstname'] = $contact->getFirstname();
					$resultats[$i]['lastname'] = $contact->getLastname();
					$resultats[$i]['link'] = $dims->getScriptEnv().'?submenu='._DESKTOP_V2_CONCEPTS.'&id='.$contact->fields['id'].'&type='.dims_const::_SYSTEM_OBJECT_CONTACT.'&init_filters=1&from=desktop';
					$i++;
				}
				echo json_encode($resultats);
			}
			die();
			break;

		// GESTION DES PROPOSITIONS DE RENDEZ-VOUS
		case 'appointment_offer_get_linked_objects':
			$app_offer_id_go = dims_load_securvalue('app_offer_id_go', dims_const::_DIMS_NUM_INPUT, true, false, false);

			if ($app_offer_id_go) {
				require DIMS_APP_PATH.'modules/system/class_search.php';
				$matrix = new search();
				$linkedObjectsIds = $matrix->exploreMatrice($_SESSION['dims']['workspaceid'], null, null, null, null, null, null, null, null, null, null, '', '', array($app_offer_id_go));

				$a_objects = array( 'contacts' => array(), 'docs' => array() );
				if (!empty($linkedObjectsIds['distribution']['contacts'])) {
					$params = array();
					$rs = $db->query('
						SELECT	c.*, t.*
						FROM	dims_mod_business_contact c
						LEFT JOIN	dims_mod_business_tiers_contact tc
						ON			tc.id_contact = c.id
						AND		tc.type_lien IN (\'employer\', \'employeur\')
						LEFT JOIN	dims_mod_business_tiers t
						ON			t.id = tc.id_tiers
						WHERE	c.id_globalobject IN ('.$db->getParamsFromArray(array_keys($linkedObjectsIds['distribution']['contacts']), 'idglobalobject', $params).')
						GROUP BY c.id', $params);
					if ($db->numrows($rs)) {
						foreach ($separation = $db->split_resultset($rs) as $sep) {
							if ($sep['c']['photo'] != '') {
								$contact = new contact();
								$contact->openFromResultSet($sep['c']);
								$sep['c']['photoPath'] = $contact->getPhotoWebPath(40);
							}
							else {
								$sep['c']['photoPath'] = _DESKTOP_TPL_PATH.'/gfx/common/human40.png';
							}

							if ($sep['t']['intitule'] == null) {
								$sep['t']['intitule'] = '';
							}

							// on ajoute le contact a la liste
							if (!isset($_SESSION['desktopv2']['appointment_offer']['ct_added'])) {
								$_SESSION['desktopv2']['appointment_offer']['ct_added'] = array();
							}
							$_SESSION['desktopv2']['appointment_offer']['ct_added'][$sep['c']['id_globalobject']] = $sep['c']['id_globalobject'];

							$a_objects['contacts'][] = $sep;
						}
					}
				}
				if (!empty($linkedObjectsIds['distribution']['docs'])) {
					$params = array();
					$rs = $db->query('
						SELECT	*
						FROM	dims_mod_doc_file
						WHERE	id_globalobject IN ('.$db->getParamsFromArray(array_keys($linkedObjectsIds['distribution']['docs']), 'idglobalobject', $params).')', $params);
					if ($db->numrows($rs)) {
						while ($row = $db->fetchrow($rs)) {
							// stripslashes
							foreach ($row as $k => $v) { $row[$k] = stripslashes($v); }

							// on ajoute le document a la liste
							if (!isset($_SESSION['desktopv2']['appointment_offer']['doc_added'])) {
								$_SESSION['desktopv2']['appointment_offer']['doc_added'] = array();
							}
							$_SESSION['desktopv2']['appointment_offer']['doc_added'][$row['id_globalobject']] = $row['id_globalobject'];

							$a_objects['docs'][] = $row;
						}
					}
				}
			}
			die(json_encode($a_objects));
			break;
		case 'appointment_offer_search_contact':
			$searchString = dims_load_securvalue('searchString', dims_const::_DIMS_CHAR_INPUT, true, false, true);

			$a_contacts = array();

			if ($searchString != '') {
				$params = array();
				$sql = '
					SELECT *
					FROM dims_mod_business_contact
					WHERE	(
						CONCAT(lastname, \' \', firstname) LIKE :searchstring
						OR CONCAT(firstname, \' \', lastname) LIKE :searchstring
						OR email LIKE :searchstring
						)
					AND	inactif = 0';
				$params[':searchstring'] = array('type' => PDO::PARAM_STR, 'value' => '%'.$searchString.'%');
				if (!empty($_SESSION['desktopv2']['appointment_offer']['ct_added'])) {
					$sql .= ' AND id_globalobject NOT IN ('.$db->getParamsFromArray($_SESSION['desktopv2']['appointment_offer']['ct_added'], 'idglobalobject', $params).')';
				}
				$sql .= ' ORDER BY lastname, firstname';
				$rs = $db->query($sql, $params);
				while ($row = $db->fetchrow($rs)) {
					$contact = new contact();
					$contact->openFromResultSet($row);
					$a_contacts[] = $contact->fields;
				}
			}
			die(json_encode($a_contacts));
			break;
		case 'appointment_offer_add_contact':
			$contact_id_go = dims_load_securvalue('contact_id_go', dims_const::_DIMS_NUM_INPUT, true, false, false);

			// recherche du contact, de sa photo et son employeur
			$rs = $db->query('
				SELECT	c.*, t.*
				FROM	dims_mod_business_contact c
				LEFT JOIN	dims_mod_business_tiers_contact tc
				ON			tc.id_contact = c.id
				AND		tc.type_lien IN (\'employer\', \'employeur\')
				LEFT JOIN	dims_mod_business_tiers t
				ON			t.id = tc.id_tiers
				WHERE	c.id_globalobject = :idglobalobject
				LIMIT 0, 1', array(
				':idglobalobject' => array('type' => PDO::PARAM_INT, 'value' => $contact_id_go),
			));
			if ($db->numrows($rs)) {
				$separation = $db->split_resultset($rs);
				if ($separation[0]['c']['photo'] != '') {
					$contact = new contact();
					$contact->openFromResultSet($separation[0]['c']);
					$separation[0]['c']['photoPath'] = $contact->getPhotoWebPath(40);
				}
				else {
					$separation[0]['c']['photoPath'] = _DESKTOP_TPL_PATH.'/gfx/common/human40.png';
				}

				if ($separation[0]['t']['intitule'] == null) {
					$separation[0]['t']['intitule'] = '';
				}

				// on ajoute le contact a la liste
				if (!isset($_SESSION['desktopv2']['appointment_offer']['ct_added'])) {
					$_SESSION['desktopv2']['appointment_offer']['ct_added'] = array();
				}
				$_SESSION['desktopv2']['appointment_offer']['ct_added'][$contact_id_go] = $contact_id_go;

				die(json_encode($separation[0]));
			}
			else {
				die();
			}
			break;
		case 'appointment_offer_remove_contact':
			$contact_id_go = dims_load_securvalue('contact_id_go', dims_const::_DIMS_NUM_INPUT, true, false, false);
			if (isset($_SESSION['desktopv2']['appointment_offer']['ct_added'][$contact_id_go])) {
				unset($_SESSION['desktopv2']['appointment_offer']['ct_added'][$contact_id_go]);
			}
			die();
			break;
		case 'appointment_offer_search_document':
			$searchString = dims_load_securvalue('searchString', dims_const::_DIMS_CHAR_INPUT, true, false, true);

			$a_documents = array();

			if ($searchString != '') {
				$params = array();
				$sql = '
					SELECT *
					FROM dims_mod_doc_file
					WHERE	(
						name LIKE :searchstring
						OR description LIKE :searchstring
						)
					AND id_workspace = :idw ';
				$params[':searchstring'] = array('type' => PDO::PARAM_STR, 'value' => '%'.$searchString.'%');
				$params[':idw'] = array('type' => PDO::PARAM_INT, 'value' => $_SESSION["dims"]['workspaceid']);
				if (!empty($_SESSION['desktopv2']['appointment_offer']['doc_added'])) {
					$sql .= ' AND id_globalobject NOT IN ('.$db->getParamsFromArray($_SESSION['desktopv2']['appointment_offer']['doc_added'], 'idglobalobject', $params).')';
				}
				$sql .= ' ORDER BY name';
				$rs = $db->query($sql, $params);
				while ($row = $db->fetchrow($rs)) {
					$doc = new docfile();
					$doc->openFromResultSet($row);
					$a_documents[] = $doc->fields;
				}
			}
			die(json_encode($a_documents));
			break;
		case 'appointment_offer_add_document':
			$doc_id_go = dims_load_securvalue('doc_id_go', dims_const::_DIMS_NUM_INPUT, true, false, false);

			// recherche du document
			$rs = $db->query('
				SELECT	*
				FROM	dims_mod_doc_file
				WHERE	id_globalobject = :idglobalobject
				LIMIT 0, 1', array(
				':idglobalobject' => array('type' => PDO::PARAM_INT, 'value' => $doc_id_go),
			));
			if ($db->numrows($rs)) {
				// on ajoute le contact a la liste
				if (!isset($_SESSION['desktopv2']['appointment_offer']['doc_added'])) {
					$_SESSION['desktopv2']['appointment_offer']['doc_added'] = array();
				}
				$_SESSION['desktopv2']['appointment_offer']['doc_added'][$doc_id_go] = $doc_id_go;

				die(json_encode($db->fetchrow($rs)));
			}
			else {
				die();
			}

			break;
		case 'appointment_offer_remove_document':
			$doc_id_go = dims_load_securvalue('doc_id_go', dims_const::_DIMS_NUM_INPUT, true, false, false);
			if (isset($_SESSION['desktopv2']['appointment_offer']['doc_added'][$doc_id_go])) {
				unset($_SESSION['desktopv2']['appointment_offer']['doc_added'][$doc_id_go]);
			}
			die();
			break;
		case 'appointment_offer_get_hours':
			ob_clean();
			$curdate = dims_load_securvalue('curdate', dims_const::_DIMS_CHAR_INPUT, true, false);
			if (isset($_SESSION['desktopv2']['appointment_offer']['days'])) {
				foreach ($_SESSION['desktopv2']['appointment_offer']['days'] as $k => $day) {
					if ($day['datefrom'] == $curdate) {
						echo json_encode(array(
							'heuredeb' => $_SESSION['desktopv2']['appointment_offer']['days'][$k]['heuredeb'],
							'heurefin' => $_SESSION['desktopv2']['appointment_offer']['days'][$k]['heurefin']
							));
						break;
					}
				}
			}
			die();
			break;
		case 'appointment_offer_select_day':
			$day = dims_load_securvalue('day', dims_const::_DIMS_CHAR_INPUT, true, false);
			if ($day != '' && !isset($_SESSION['desktopv2']['appointment_offer']['days'][$day])) {
				$hour_from = sprintf('%02s', dims_load_securvalue('hour_from', dims_const::_DIMS_NUM_INPUT, true, false));
				$mins_from = sprintf('%02s', dims_load_securvalue('mins_from', dims_const::_DIMS_NUM_INPUT, true, false));
				$hour_to = sprintf('%02s', dims_load_securvalue('hour_to', dims_const::_DIMS_NUM_INPUT, true, false));
				$mins_to = sprintf('%02s', dims_load_securvalue('mins_to', dims_const::_DIMS_NUM_INPUT, true, false));

				$_SESSION['desktopv2']['appointment_offer']['days'][] = array(
					'datefrom' => $day,
					'heuredeb' => $hour_from.':'.$mins_from.':00',
					'heurefin' => $hour_to.':'.$mins_to.':00'
					);

				// on copie l'info dans un autre tableau pour le chargment des dates
				// refs: #4899
				$_SESSION['desktopv2']['appointment_offer']['last_selected_days'][] = array(
					'datefrom' => $day,
					'heuredeb' => $hour_from.':'.$mins_from.':00',
					'heurefin' => $hour_to.':'.$mins_to.':00'
					);

				echo json_encode(array($day, $hour_from, $mins_from, $hour_to, $mins_to));
			}
			die();
			break;
		case 'appointment_offer_remove_day':
			$day = dims_load_securvalue('day', dims_const::_DIMS_CHAR_INPUT, true, false);
			if ($day != '' && isset($_SESSION['desktopv2']['appointment_offer']['days'][$day])) {
				unset($_SESSION['desktopv2']['appointment_offer']['days'][$day]);
				// on copie l'info dans un autre tableau pour le chargment des dates
				// refs: #4899
				unset($_SESSION['desktopv2']['appointment_offer']['last_selected_days'][$day]);
			}
			die();
			break;
		case 'appointment_offer_load_dates':
			if (!isset($_SESSION['desktopv2']['appointment_offer']['last_selected_days'])) {
				$_SESSION['desktopv2']['appointment_offer']['last_selected_days'] = array();
			}
			if (!isset($_SESSION['desktopv2']['appointment_offer']['days'])) {
				$_SESSION['desktopv2']['appointment_offer']['days'] = $_SESSION['desktopv2']['appointment_offer']['last_selected_days'];
			}

			$appointment_offer_id = dims_load_securvalue('appointment_offer_id', dims_const::_DIMS_NUM_INPUT, true, false);
			if ($appointment_offer_id > 0) {
				require_once DIMS_APP_PATH.'/modules/system/appointment_offer/class_appointment_offer.php';
				$_SESSION['desktopv2']['appointment_offer']['days'] = array_merge_recursive($_SESSION['desktopv2']['appointment_offer']['days'], dims_appointment_offer::getAllByParent($appointment_offer_id));
			}

			die(json_encode($_SESSION['desktopv2']['appointment_offer']['days']));
			break;
		case 'validate_appointement':
			ob_clean();
			$popup = dims_load_securvalue('id_popup',dims_const::_DIMS_CHAR_INPUT,true,true);
			$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true);
			if ($id != '' && $id > 0){
					require_once DIMS_APP_PATH.'/modules/system/appointment_offer/class_appointment_offer.php';
			$app = new dims_appointment_offer();
			$app->open($id);
			$app->setLightAttribute('id_popup',$popup);
			$app->display(DIMS_APP_PATH."/modules/system/appointment_offer/validate_appointment.tpl.php");
			}
			die();
			break;
		case 'add_new_company':
			ob_clean();
			$val = trim(dims_load_securvalue('val',dims_const::_DIMS_CHAR_INPUT,true,true,true));
			if ($val != ''){
				require_once DIMS_APP_PATH.'modules/system/class_tiers.php';
				$tiers = new tiers();
				$tiers->init_description();
				$tiers->setugm();
				$tiers->fields['intitule'] = $val;
				if ($tiers->save()){
					$elem = array();
					$elem['id'] = $tiers->fields['id'];
					$elem['intitule'] = $tiers->fields['intitule'];
					die(json_encode($elem));
				}
			}
			die();
			break;
		case 'add_new_product':
			ob_clean();
			$val = trim(dims_load_securvalue('val',dims_const::_DIMS_CHAR_INPUT,true,true,true));
			if ($val != ''){
				require_once DIMS_APP_PATH.'modules/system/class_produit.php';
				$produit = new produit();
				$produit->init_description();
				$produit->fields['libelle'] = $val;
				$produit->fields['reference'] = preg_replace('#[^a-zA-Z0-9]#', '', strtoupper(dims_convertaccents($val)));

				if ($produit->save()){
					$elem = array();
					$elem['id'] = $produit->fields['id'];
					$elem['libelle'] = $produit->fields['libelle'];
					die(json_encode($elem));
				}
			}
			die();
			break;
		case 'add_new_city_addr':
			ob_clean();
			$sel = 0;
			$val = trim(dims_load_securvalue('val',dims_const::_DIMS_CHAR_INPUT,true,true,true));
			$id_country = dims_load_securvalue('id_country',dims_const::_DIMS_NUM_INPUT,true,true,true);
			$json = array(array('id'=>'dims_nan','label'=>'','selected'=>false));
			$sel = 0;
			require_once DIMS_APP_PATH.'modules/system/class_city.php';
			if ($val != '' && $id_country != '' && $id_country > 0){
				$city = new city();
				$city->init_description();
				$city->setugm();
				$city->set('label',$val);
				$city->set('id_country',$id_country);
				$city->save();
				$sel = $city->get('id');
				$elem = array();
				$elem['id'] = $city->get('id');
				$elem['label'] = $city->getLabel();
				$elem['selected'] = true;
				$json[] = $elem;
			}
			/*if($id_country != '' && $id_country > 0){
				$all = city::all("WHERE id_country = :idc", array(':idc'=>$id_country));
				foreach($all as $c){
					$elem = array();
					$elem['id'] = $c->get('id');
					$elem['label'] = $c->getLabel();
					$elem['selected'] = ($c->get('id') == $sel);
					$json[] = $elem;
				}
			}*/
			echo json_encode($json);
			die();
			break;
		case 'add_new_type_addr':
			ob_clean();
			$sel = 0;
			require_once DIMS_APP_PATH.'modules/system/class_address_type.php';
			$val = trim(dims_load_securvalue('val',dims_const::_DIMS_CHAR_INPUT,true,true,true));
			if ($val != ''){
				$type = new address_type();
				$type->init_description();
				$type->setugm();
				$type->set('label',$val);
				$type->set('is_active',1);
				$type->save();
				$sel = $type->get('id');
			}
			$all = address_type::all("WHERE is_active=1 AND id_workspace = :idwork", array(':idwork'=>$_SESSION['dims']['workspaceid']));
			$json = array();
			foreach($all as $type){
				$elem = array();
				$elem['go'] = $type->get('id');
				$elem['label'] = $type->getLabel();
				$elem['selected'] = ($type->get('id') == $sel);
				$json[] = $elem;
			}
			echo json_encode($json);
			die();
			break;
		case 'search_tiers':
			ob_clean();
			$label = trim(dims_load_securvalue('text',dims_const::_DIMS_CHAR_INPUT,true,true,true));
			$res = array();
			if($label != ''){
				$tiers = tiers::all("WHERE intitule LIKE :int AND id_workspace = :idwork", array(':int'=>"%$label%",':idwork'=>$_SESSION['dims']['workspaceid']));
				foreach($tiers as $t){
					$elem = array();
					$elem['id'] = $t->get('id');
					$elem['label'] = $t->get('intitule');
					$res[] = $elem;
				}
			}
			echo json_encode($res);
			die();
			break;
		case 'get_tiers_data':
			ob_clean();
			$id = dims_load_securvalue('value',dims_const::_DIMS_NUM_INPUT,true,true,true);
			$res = array();
			if($id != '' && $id > 0){
				$tiers = new tiers();
				$tiers->open($id);
				$res['telephone'] = $tiers->get('telephone');
				$res['telecopie'] = $tiers->get('telecopie');
				$res['mel'] = $tiers->get('mel');
				$res['photo'] = '';
				$web_path = $tiers->getPhotoWebPath(24);
				if($web_path != '' && file_exists($tiers->getPhotoPath(24))){
					$res['photo'] = $web_path;
				}
			}
			echo json_encode($res);
			die();
			break;
		case 'add_folder':
			ob_clean();
			$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
			$val = trim(dims_load_securvalue('value',dims_const::_DIMS_CHAR_INPUT,true,true,true));
			if($id != '' && $id > 0 && $val != ''){
				require_once DIMS_APP_PATH.'modules/doc/class_docfolder.php';
				$folder = new docfolder();
				$folder->open($id);
				if(!$folder->isNew()){
					$nfold = new docfolder();
					$nfold->init_description();
					$nfold->setugm();
					$nfold->set('id_folder',$folder->get('id'));
					$nfold->set('name',$val);
					$nfold->save();
					echo '<option value="'.$nfold->get('id').'">'.$nfold->get('name').'</option>';
				}
			}
			die();
			break;
		case 'searchUser':
			ob_clean();
			$val = trim(dims_load_securvalue('val',dims_const::_DIMS_CHAR_INPUT,true,true,true));
			if($val != ''){
				require_once(DIMS_APP_PATH . "/modules/system/class_search.php");
				$dimsearch = new search(dims::getInstance());
				$dimsearch->addSearchObject(dims_const::_DIMS_MODULE_SYSTEM, dims_const::_SYSTEM_OBJECT_CONTACT,$_SESSION['cste']['_DIMS_LABEL_USER']);
				$dimsearch->initSearchObject();

				$kword					= dims_load_securvalue('kword', dims_const::_DIMS_CHAR_INPUT, true, true);
				$idmodule				= dims_load_securvalue('idmodule', dims_const::_DIMS_CHAR_INPUT, true, true);
				$idobj					= dims_load_securvalue('idobj', dims_const::_DIMS_CHAR_INPUT, true, true);
				$idmetafield			= dims_load_securvalue('idmetafield', dims_const::_DIMS_CHAR_INPUT, true, true);
				$sens					= dims_load_securvalue('sens', dims_const::_DIMS_CHAR_INPUT, true, true);

				$dimsearch->executeSearch2($val, $kword,$_SESSION['dims']['moduleid'], $idobj, $idmetafield, $sens,0,null,$_SESSION['dims']['workspaceid']);

				$ids = array();
				$user = new user();
				$lstU = $user->getusersgroup("",$_SESSION['dims']['workspaceid']);
				foreach($dimsearch->tabresultat as $idmodule => $tab_objects){
					foreach($tab_objects as $idobjet => $tab_ids){
						foreach($tab_ids as $kid => $id){
							$ids[$id['id_go']] = $id['id_go'];
						}
					}
				}
				if(count($ids)){
					$lu = dims_load_securvalue('lu',dims_const::_DIMS_NUM_INPUT,true,true,true);
					$more = "";
					$db = dims::getInstance()->getDb();
					$params = array();
					if(!empty($lu)){
						$more = "AND u.id NOT IN (".$db->getParamsFromArray($lu, 'id2', $params).")";
					}
					$sel = "SELECT 		u.*
							FROM 		".user::TABLE_NAME." u
							INNER JOIN 	".contact::TABLE_NAME." ct
							ON 			ct.id = u.id_contact
							WHERE 		ct.id_globalobject IN (".$db->getParamsFromArray($ids, 'id1', $params).")
							AND 		u.id IN (".$db->getParamsFromArray($lstU, 'id3',$params).")
							$more
							AND 		u.status = ".user::USER_ACTIF."
							ORDER BY 	u.firstname, u.lastname";
					$res = $db->query($sel,$params);
					if($db->numrows($res)){
						while($r = $db->fetchrow($res)){
							$user = new user();
							$user->openFromResultSet($r);
							$user->setLightAttribute('extended',false);
							$user->display(_DESKTOP_TPL_LOCAL_PATH.'/form_ct_tiers/user/display_mini_user.tpl.php');
						}
					}else{
						echo $_SESSION['cste']['NO_RESULT'];
					}
				}else
					echo $_SESSION['cste']['NO_RESULT'];
			}else
				echo $_SESSION['cste']['NO_RESULT'];
			die();
			break;
		case 'searchCity':
			ob_clean();
			$options = '<option value="dims_nan"></option>';
			$val = trim(dims_load_securvalue('val',dims_const::_DIMS_CHAR_INPUT,true,true,true));
			$id_country = dims_load_securvalue('id_country',dims_const::_DIMS_NUM_INPUT,true,true,true);
			if($val != '' && $id_country != '' && $id_country > 0){
				require_once(DIMS_APP_PATH . "/modules/system/class_city.php");
				$lst = city::searchStart($val,$id_country);
				$first = true;
				foreach($lst as $c){
					$sel = "";
					if($first){
						$sel = "selected=true";
						$first = false;
					}
					if($c->get('insee') != '')
						$options .= '<option '.$sel.' dims-data-value="'.$c->get('cp').'" value="'.$c->get('id').'">'.$c->get('label').' ('.substr($c->get('insee'),0,2).')</option>';
					else
						$options .= '<option '.$sel.' dims-data-value="'.$c->get('cp').'" value="'.$c->get('id').'">'.$c->get('label').'</option>';
				}
			}
			echo $options;
			die();
			break;
		case 'load_incomplete_records':
			ob_clean();
			$nb = intval(dims_load_securvalue('nb',dims_const::_DIMS_NUM_INPUT,true,true,true));
			require_once DIMS_APP_PATH.'modules/system/class_tiers_contact.php';
			require_once DIMS_APP_PATH.'modules/system/class_address_link.php';

			//$tiers = tiers::find_by(array('id_workspace'=>$_SESSION['dims']['workspaceid'],'telephone'=>'', ' ORDER BY date_creation '));
			$db = dims::getInstance()->getDb();
			$lst = array();

			$sel = "SELECT 		DISTINCT t.*
					FROM 		".tiers::TABLE_NAME." t
					LEFT JOIN 	".address_link::TABLE_NAME." al
					ON 			al.id_goobject = t.id_globalobject
					WHERE 		(t.telephone = ''
					OR 			al.id_goobject IS NULL)
					AND 		t.id_workspace = :idw
					ORDER BY 	date_creation DESC
					LIMIT 		0, :nb";
			$params = array(
				':idw' => array('type'=>PDO::PARAM_INT, 'value'=>$_SESSION['dims']['workspaceid']),
				':nb' => array('type'=>PDO::PARAM_INT, 'value'=>$nb),
			);
			$res = $db->query($sel,$params);
			$contacts = array();
			while($r = $db->fetchrow($res)){
				$t = new tiers();
				$t->openFromResultSet($r);
				$lst[] = $t;
			}

			$sel = "SELECT 		DISTINCT c.*
					FROM 		".contact::TABLE_NAME." c
					LEFT JOIN 	".tiersct::TABLE_NAME." lk
					ON 			lk.id_contact = c.id
					LEFT JOIN 	".address_link::TABLE_NAME." al
					ON 			al.id_goobject = c.id_globalobject
					WHERE 		(c.mobile = ''
					OR 			(lk.id_contact IS NULL
					AND 		al.id_goobject IS NULL))
					AND 		c.id_workspace = :idw
					ORDER BY 	date_create DESC
					LIMIT 		0, :nb";
			$params = array(
				':idw' => array('type'=>PDO::PARAM_INT, 'value'=>$_SESSION['dims']['workspaceid']),
				':nb' => array('type'=>PDO::PARAM_INT, 'value'=>$nb),
			);
			$res = $db->query($sel,$params);
			$contacts = array();
			while($r = $db->fetchrow($res)){
				$c = new contact();
				$c->openFromResultSet($r);
				$lst[] = $c;
			}

			usort($lst,'sortCtTiersByCreate');
			$lst = array_reverse($lst);
			$lst = array_slice($lst, $nb, 10);
			foreach($lst as $elem){
				switch ($elem->getid_object()) {
					case tiers::MY_GLOBALOBJECT_CODE:
						//$elem->display(_DESKTOP_TPL_LOCAL_PATH.'/form_ct_tiers/shared/missing_infos_tiers.tpl.php');
						$elem->display(_DESKTOP_TPL_LOCAL_PATH.'/companies_recently/companies_recently_details.tpl.php');
						break;
					case contact::MY_GLOBALOBJECT_CODE:
						//$elem->display(_DESKTOP_TPL_LOCAL_PATH.'/form_ct_tiers/shared/missing_infos_ct.tpl.php');
						$elem->display(_DESKTOP_TPL_LOCAL_PATH.'/contacts_recently/contacts_recently_details.tpl.php');
						break;
				}
			}
			die();
			break;
	}
}
