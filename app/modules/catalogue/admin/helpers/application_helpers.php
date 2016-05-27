<?php
function &store_sessparam(&$tab, $value){
	return $tab = $value;
}

function &get_sessparam(&$tab, $default_value = null){
	if( isset($tab) ) return $tab;
	else if ( ! is_null($default_value) ){
		return store_sessparam($tab, $default_value);
	}
	else return null;
}

function get_path($c, $a, $params = array()){
	if (!is_array($params)) {
		$params = array();
	}

	return dims::getInstance()->getScriptEnv().'?'.http_build_query(
		array(
			'c' => $c,
			'a' => $a,
		) + $params
	);
}

function get_formated_countries(){
	include_once DIMS_APP_PATH."modules/system/class_country.php";
	$countries = country::getAllCountries();
	$tab = array();
	foreach($countries as $country) {
		$tab[$country->get('id')] = $country->getLabel($_SESSION['dims']['currentlang']);
	}
	return $tab;
}

function show_guide($text){
	$view = view::getInstance();
	echo '<div class="guide">
				<table>
					<tr>
						<td>
							<img src="'.$view->getTemplateWebPath('gfx/info32.png').'" title="Info" alt="info"/>
						</td>
						<td>'.$text.'</td>
					</tr>
				</table>
			</div>';
}

function gettemplatename() {
	include_once DIMS_APP_PATH . 'modules/wce/include/global.php';
	$db = dims::getInstance()->getDb();

	$template_name = 'default';

	// recherche du template
	$lstwcemods = dims::getInstance()->getWceModulesFromDomain();
	$wce_module_id = (!empty($lstwcemods)) ? current($lstwcemods) : 0;

	$rs = $db->query('SELECT * FROM `dims_mod_wce_heading` WHERE `type` = 0 AND `id_module` = '.$wce_module_id.' ORDER BY `depth` LIMIT 1');
	if ($db->numrows($rs)) {
		$row = $db->fetchrow($rs);
		if ($row['template'] != '') {
			$template_name = $row['template'];
		}
	}

	return $template_name;
}

function image_tag($path, $attributes = array()){
	$img = '<img src="'.dims::getInstance()->getRootPath().'/'.view::getInstance()->getTemplateWebPath($path).'" ';
	foreach($attributes as $attr => $val){
		$img .= $attr.'="'.$val.'" ';
	}
	$img .= '/>';
	return $img;
}


function generator_tickets($id_cmde) {
	require_once DIMS_APP_PATH.'modules/catalogue/include/class_commande.php';
	require_once(DIMS_APP_PATH . '/include/class_opendocument.php');

	//if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected']) {

		// test si l'id de commande est bien > 0
		if ($id_cmde>0) {
			$commande = commande::find_by(array('id_client' => $_SESSION['catalogue']['client_id']), null, 1);

			if(!empty($commande) && !$commande->isNew() && $commande->fields['id_client']==$_SESSION['catalogue']['client_id']){
				$client = client::find_by(array('id_client' => $_SESSION['catalogue']['client_id']), null, 1);
				if(!empty($client) && !$client->isNew()){
					$od = new dims_opendocument(realpath('.').'/data/modele_zoo.odt');
					$od->setFormat("ODT");

					$data = array();
					$dateachat=dims_timestamp2local($commande->fields['date_validation']);
					$data['(TITULAIRE)'] = $client->fields['nom']." ".$client->fields['prenom'];
					$data['(DATE_ACHAT)'] = $dateachat['date'];
					$data['(DATE_ENTREE)'] = "";

					// on va boucler sur toutes les lignes de la commande
					$indice_page=1;

					$lignesdetail=$commande->getlignes();
					if (!empty($lignesdetail)) {

						foreach ($lignesdetail as $detail) {
							$data['(TYPE_ENTREE)'] = $detail->fields['label'];
							$data['(MONTANT)'] = $detail->fields['pu_ttc']. " euros";

							$od->setData($data);

							// on s'occupe des images maintenant
							$images=array();

							// repartition sectorielle
							$elem=array();
							$elem["tag"]="svg:title";
							$elem["title"]="qrcode";
							$elem["image"]="";
							$images[]=$elem;

							$od->setImages($images);
							$od->createOpenDocument('ebillet-'.$indice_page.'.odt',realpath('.').'/data/',$images,true);
						}

					}
					die();
				}
			}
			return false;
		}
		else return false;
	//}
	//else return false;

}
