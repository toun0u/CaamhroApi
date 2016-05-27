<?php
if (defined('_ACTIVE_GESCOM') && _ACTIVE_GESCOM) {
	?>
	<h2 class="contact">
		<balise id="suivi">
			<img src="<?= _DESKTOP_TPL_PATH; ?>/gfx/contact/documents.png" />
			<span>
				<?= $_SESSION['cste']['_MONITORINGS']; ?>
			</span>
		</balise>
	</h2>
	<div id="add_suivi">
		<a class="add" href="javascript:void(0);">
			<?= $_SESSION['cste']['_DIMS_ADD_MONITORING']; ?>
		</a>
	</div>
	<div id="linked_suivi" class="bloc_contact">
		<?php
		$db = dims::getInstance()->getDb();

		if(isset($_GET['_dims_formtoken'])){
			unset($_GET['_dims_formtoken']);
		}

		$cols = suivi::$cols;
		$urlOrder = $_GET;
		if(isset($urlOrder['order'])){
			unset($urlOrder['order']);
		}else{
			$_GET['order'][5] = suivi::SUIVI_TRI_DESC;
		}
		$urlOrder = dims::getInstance()->getScriptEnv()."?".http_build_query($urlOrder);
		$asc = _DESKTOP_TPL_PATH."/gfx/common/img_tri_haut.png";
		$desc = _DESKTOP_TPL_PATH."/gfx/common/img_tri_bas.png";
		$non_trie = _DESKTOP_TPL_PATH."/gfx/common/img_tri_non_trie.png";

		$suivi = new suivi();
		$p = 1;
		$suivi->page_courant = dims_load_securvalue('p',dims_const::_DIMS_NUM_INPUT,true,true,true,$p,1);
		$suivi->isPageLimited = true;
		$suivi->setPaginationParams(10, 10, false, $_SESSION['cste']['_FIRST'], $_SESSION['cste']['_LAST'], $_SESSION['cste']['_PREVIOUS'], $_SESSION['cste']['_NEXT']);

		$paramsUrl = array(
			"submenu" 	=> 1,
			"mode" 		=> "",
			"action" 	=> "show",
			"id"		=> 0,
		);
		$addUrl = "";

		switch($this->getid_object()) {
			case dims_const::_SYSTEM_OBJECT_TIERS:
				$filters = "tiers_id = ".$this->get('id');
				$paramsUrl["mode"] = "company";
				$paramsUrl["id"] = $this->get('id');
				$addUrl = "&id_tiers=".$this->get('id');
				break;
			case dims_const::_SYSTEM_OBJECT_CONTACT:
				$filters = "contact_id = ".$this->get('id');
				$paramsUrl["mode"] = "contact";
				$paramsUrl["id"] = $this->get('id');
				$addUrl = "&id_contact=".$this->get('id');
				break;
			default:
				$filters = "0=1";
				break;
		}

		$filtres = dims_load_securvalue('s',dims_const::_DIMS_CHAR_INPUT,true,true,true);
		if(isset($filtres['exercice']) && $filtres['exercice'] !== suivi::SUIVI_TOUS){
			$filters .= " AND s.exercice = '".$filtres['exercice']."' ";
		}
		if(isset($filtres['type']) && $filtres['type'] !== suivi::TYPE_TOUS){
			$filters .= " AND s.type = '".$filtres['type']."' ";
		}
		if(isset($filtres['accepted']) && $filtres['accepted'] !== suivi::SUIVI_TOUS){
			$filters .= " AND s.valide = '".$filtres['accepted']."' ";
		}
		if(isset($filtres['cleared']) && $filtres['cleared'] !== suivi::SUIVI_TOUS){
			$filters .= " AND s.solde ".(($filtres['cleared'] == suivi::SUIVI_NON)?"!=":"=")." 0 ";
		}
		$search = (!empty($filtres['search']))?$filtres['search']:"";
		$order = dims_load_securvalue('order',dims_const::_DIMS_NUM_INPUT,true,true,true);

		$suivis = $suivi->getAll($search, false, $filters, $order);

		$form = new Dims\form(array(
			'name' 			=> uniqid(true),
			'method'		=> "GET",
			'action'		=> dims::getInstance()->getScriptEnv()."?action=show&id=2",
			'submit_value'	=> $_SESSION['cste']['_DIMS_FILTER'],
			'back_name'		=> $_SESSION['cste']['_DIMS_RESET'],
			'back_url'		=> dims::getInstance()->getScriptEnv()."?".http_build_query($paramsUrl),
		));
		foreach ($paramsUrl as $k => $v) {
			$form->add_hidden_field(array(
				'name'	=> $k,
				'value'	=> $v,
			));
		}
		$form->add_text_field(array(
			'name'		=> 's[search]',
			'label' 	=> $_SESSION['cste']['_DIMS_LABEL'],
			'row'		=> 1,
			'col'		=> 1,
			'value'		=> isset($_GET['s']['search'])?$_GET['s']['search']:"",
		));

		$exercices = array(
			suivi::SUIVI_TOUS => $_SESSION['cste']['_DIMS_ALLS'],
		);
		$query = "	SELECT 		DISTINCT exercice 
					FROM		".suivi::TABLE_NAME."
					ORDER BY 	exercice DESC";
		$res = $db->query($query);
		while($row = $db->fetchrow($res)){
			$exercices[$row['exercice']] = $row['exercice'];
		}
		$form->add_select_field(array(
			'name'		=> 's[exercice]',
			'label' 	=> $_SESSION['cste']['_DUTY'],
			'options'	=> $exercices,
			'row'		=> 2,
			'col'		=> 1,
			'value'		=> isset($_GET['s']['exercice'])?$_GET['s']['exercice']:suivi::SUIVI_TOUS,
		));

		$types = array(
			suivi::TYPE_TOUS => $_SESSION['cste']['_DIMS_ALLS'],
		);
		$query = "	SELECT 	DISTINCT type 
					FROM 	".suivi::TABLE_NAME;
		$res = $db->query($query);
		while($row = $db->fetchrow($res)) {
			switch($row['type']) {
				case suivi::TYPE_DEVIS:
					$types[suivi::TYPE_DEVIS] = $_SESSION['cste']['QUOTATION'];
					break;
				case suivi::TYPE_FACTURE:
					$types[suivi::TYPE_FACTURE] = $_SESSION['cste']['INVOICE'];
					break;
				case suivi::TYPE_AVOIR:
					$types[suivi::TYPE_AVOIR] = $_SESSION['cste']['ASSET'];
					break;
			}
		}
		$form->add_select_field(array(
			'name'		=> 's[type]',
			'label' 	=> $_SESSION['cste']['_TYPE'],
			'options'	=> $types,
			'row'		=> 2,
			'col'		=> 2,
			'value'		=> isset($_GET['s']['type'])?$_GET['s']['type']:suivi::TYPE_TOUS,
		));

		$accepteds = array(
			suivi::SUIVI_TOUS => $_SESSION['cste']['_DIMS_ALLS'],
			suivi::SUIVI_OUI => $_SESSION['cste']['_DIMS_YES'],
			suivi::SUIVI_NON => $_SESSION['cste']['_DIMS_NO'],
		);
		$form->add_select_field(array(
			'name'		=> 's[accepted]',
			'label' 	=> $_SESSION['cste']['_ACCEPTED'],
			'options'	=> $accepteds,
			'row'		=> 2,
			'col'		=> 3,
			'value'		=> isset($_GET['s']['accepted'])?$_GET['s']['accepted']:suivi::TYPE_TOUS,
		));

		$cleareds = array(
			suivi::SUIVI_TOUS => $_SESSION['cste']['_DIMS_ALLS'],
			suivi::SUIVI_OUI => $_SESSION['cste']['_DIMS_YES'],
			suivi::SUIVI_NON => $_SESSION['cste']['_DIMS_NO'],
		);
		$form->add_select_field(array(
			'name'		=> 's[cleared]',
			'label' 	=> $_SESSION['cste']['_CLEARED'],
			'options'	=> $cleareds,
			'row'		=> 2,
			'col'		=> 4,
			'value'		=> isset($_GET['s']['cleared'])?$_GET['s']['cleared']:suivi::TYPE_TOUS,
		));
		$form->build();

		?>
		<table style="width: 100%;">
			<thead>
				<tr>
					<td>
						<div style="display:inline-block;"><?= $_SESSION['cste']['_ACCEPTED']; ?></div>
						<a style="display:inline-block;float:right;" href="<?= $urlOrder."&order[0]=".((isset($_GET['order'][0]) && $_GET['order'][0] == suivi::SUIVI_TRI_ASC)?suivi::SUIVI_TRI_DESC:suivi::SUIVI_TRI_ASC); ?>">
							<img src="<?= isset($_GET['order'][0]) ? (($_GET['order'][0] == suivi::SUIVI_TRI_ASC) ? $asc : $desc) : $non_trie ?>" />
						</a>
					</td>
					<td>
						<div style="display:inline-block;"><?= $_SESSION['cste']['_TYPE']; ?></div>
						<a style="display:inline-block;float:right;" href="<?= $urlOrder."&order[1]=".((isset($_GET['order'][1]) && $_GET['order'][1] == suivi::SUIVI_TRI_ASC)?suivi::SUIVI_TRI_DESC:suivi::SUIVI_TRI_ASC); ?>">
							<img src="<?= isset($_GET['order'][1]) ? (($_GET['order'][1] == suivi::SUIVI_TRI_ASC) ? $asc : $desc) : $non_trie ?>" />
						</a>
					</td>
					<td>
						<div style="display:inline-block;"><?= $_SESSION['cste']['_NUMBER']; ?></div>
						<a style="display:inline-block;float:right;" href="<?= $urlOrder."&order[2]=".((isset($_GET['order'][2]) && $_GET['order'][2] == suivi::SUIVI_TRI_ASC)?suivi::SUIVI_TRI_DESC:suivi::SUIVI_TRI_ASC); ?>">
							<img src="<?= isset($_GET['order'][2]) ? (($_GET['order'][2] == suivi::SUIVI_TRI_ASC) ? $asc : $desc) : $non_trie ?>" />
						</a>
					</td>
					<td>
						<div style="display:inline-block;"><?= $_SESSION['cste']['_AGENDA_LABEL_LABEL']; ?></div>
						<a style="display:inline-block;float:right;" href="<?= $urlOrder."&order[3]=".((isset($_GET['order'][3]) && $_GET['order'][3] == suivi::SUIVI_TRI_ASC)?suivi::SUIVI_TRI_DESC:suivi::SUIVI_TRI_ASC); ?>">
							<img src="<?= isset($_GET['order'][3]) ? (($_GET['order'][3] == suivi::SUIVI_TRI_ASC) ? $asc : $desc) : $non_trie ?>" />
						</a>
					</td>
					<td>
						<div style="display:inline-block;"><?= $_SESSION['cste']['_DUTY']; ?></div>
						<a style="display:inline-block;float:right;" href="<?= $urlOrder."&order[4]=".((isset($_GET['order'][4]) && $_GET['order'][4] == suivi::SUIVI_TRI_ASC)?suivi::SUIVI_TRI_DESC:suivi::SUIVI_TRI_ASC); ?>">
							<img src="<?= isset($_GET['order'][4]) ? (($_GET['order'][4] == suivi::SUIVI_TRI_ASC) ? $asc : $desc) : $non_trie ?>" />
						</a>
					</td>
					<td>
						<div style="display:inline-block;"><?= $_SESSION['cste']['_DIMS_DATE']; ?></div>
						<a style="display:inline-block;float:right;" href="<?= $urlOrder."&order[5]=".((isset($_GET['order'][5]) && $_GET['order'][5] == suivi::SUIVI_TRI_ASC)?suivi::SUIVI_TRI_DESC:suivi::SUIVI_TRI_ASC); ?>">
							<img src="<?= isset($_GET['order'][5]) ? (($_GET['order'][5] == suivi::SUIVI_TRI_ASC) ? $asc : $desc) : $non_trie ?>" />
						</a>
					</td>
					<td>
						<div style="display:inline-block;"><?= $_SESSION['cste']['_DUTY_FREE_AMOUNT'];?></div>
						<a style="display:inline-block;float:right;" href="<?= $urlOrder."&order[6]=".((isset($_GET['order'][6]) && $_GET['order'][6] == suivi::SUIVI_TRI_ASC)?suivi::SUIVI_TRI_DESC:suivi::SUIVI_TRI_ASC); ?>">
							<img src="<?= isset($_GET['order'][6]) ? (($_GET['order'][6] == suivi::SUIVI_TRI_ASC) ? $asc : $desc) : $non_trie ?>" />
						</a>
					</td>
					<td>
						<div style="display:inline-block;"><?= $_SESSION['cste']['_DISCOUNT'];?></div>
						<a style="display:inline-block;float:right;" href="<?= $urlOrder."&order[7]=".((isset($_GET['order'][7]) && $_GET['order'][7] == suivi::SUIVI_TRI_ASC)?suivi::SUIVI_TRI_DESC:suivi::SUIVI_TRI_ASC); ?>">
							<img src="<?= isset($_GET['order'][7]) ? (($_GET['order'][7] == suivi::SUIVI_TRI_ASC) ? $asc : $desc) : $non_trie ?>" />
						</a>
					</td>
					<td>
						<div style="display:inline-block;"><?= $_SESSION['cste']['_TOTAL_DUTY_FREE_AMOUNT'];?></div>
						<a style="display:inline-block;float:right;" href="<?= $urlOrder."&order[8]=".((isset($_GET['order'][8]) && $_GET['order'][8] == suivi::SUIVI_TRI_ASC)?suivi::SUIVI_TRI_DESC:suivi::SUIVI_TRI_ASC); ?>">
							<img src="<?= isset($_GET['order'][8]) ? (($_GET['order'][8] == suivi::SUIVI_TRI_ASC) ? $asc : $desc) : $non_trie ?>" />
						</a>
					</td>
					<td>
						<div style="display:inline-block;"><?= $_SESSION['cste']['_TOTAL_AMOUNT_WITH_DUTY'];?></div>
						<a style="display:inline-block;float:right;" href="<?= $urlOrder."&order[9]=".((isset($_GET['order'][9]) && $_GET['order'][9] == suivi::SUIVI_TRI_ASC)?suivi::SUIVI_TRI_DESC:suivi::SUIVI_TRI_ASC); ?>">
							<img src="<?= isset($_GET['order'][9]) ? (($_GET['order'][9] == suivi::SUIVI_TRI_ASC) ? $asc : $desc) : $non_trie ?>" />
						</a>
					</td>
					<td>
						<div style="display:inline-block;"><?= $_SESSION['cste']['_CLEARED'];?></div>
						<a style="display:inline-block;float:right;" href="<?= $urlOrder."&order[10]=".((isset($_GET['order'][10]) && $_GET['order'][10] == suivi::SUIVI_TRI_ASC)?suivi::SUIVI_TRI_DESC:suivi::SUIVI_TRI_ASC); ?>">
							<img src="<?= isset($_GET['order'][10]) ? (($_GET['order'][10] == suivi::SUIVI_TRI_ASC) ? $asc : $desc) : $non_trie ?>" />
						</a>
					</td>
					<td>
						<div><?= $_SESSION['cste']['_DIMS_ACTIONS']; ?></div>
					</td>
				</tr>
			</thead>
			<tbody>
				<?php
				$orderTd = current(array_keys($_GET['order']));
				foreach($suivis as $res) {
					$res->setLightAttribute('order',$orderTd);
					$res->display(_DESKTOP_TPL_LOCAL_PATH.'/form_ct_tiers/suivi/_list.tpl.php');
				}
				?>
			</tbody>
		</table>
		<div class="pagination">
			<?php
			$suivi->rewrite = true;
			$pages = $suivi->getPagination();
			if(count($pages) > 1) {
				$urlPage = $_GET;
				if(isset($urlPage['p'])){
					unset($urlPage['p']);
				}
				$urlPage = dims::getInstance()->getScriptEnv()."?".http_build_query($urlPage);
				?>
				<span class="label"><?= $_SESSION['cste']['_DIMS_LABEL_PAGE']; ?> :</span>
				<?php
				foreach($pages as $k=>$p){
					if(!empty($p['url'])) {
						$p['url'] = $urlPage."&p=".($k+1);
						echo '<a href="'.$p['url'].'" title="'.$p['title'].'">'.$p['label'].'</a>';
					}
					else echo '<span class="current">'.$p['label'].'</span>';
				}
			} else {
				?>
				<span class="label"><?= $_SESSION['cste']['_DIMS_LABEL_PAGE']; ?> :</span><span class="current">1</span>
				<?php
			}
			?>
		</div>
	</div>
	<script type="text/javascript">
	$(document).ready(function(){
		$('#add_suivi .add').click(function(){
			document.getElementById('dims_popup').innerHTML="";
		    var idpopup = dims_openOverlayedPopup(950, 600);
			dims_xmlhttprequest_todiv('<?= dims::getInstance()->getScriptEnv(); ?>', 'submenu=1&mode=suivi&action=new<?= $addUrl; ?>&id_popup='+idpopup,'','p'+idpopup);
		});
		$('#linked_suivi .open').click(function(){
			document.getElementById('dims_popup').innerHTML="";
		    var idpopup = dims_openOverlayedPopup(950, 600);
			dims_xmlhttprequest_todiv('<?= dims::getInstance()->getScriptEnv(); ?>', 'submenu=1&mode=suivi&action=show&id='+$(this).attr('dims-data-value')+'&id_popup='+idpopup,'','p'+idpopup);
		});
		<?php if(isset($_SESSION['dims']['suivi']['reopen']) && $_SESSION['dims']['suivi']['reopen'] > 0){ ?>
			document.getElementById('dims_popup').innerHTML="";
		    var idpopup = dims_openOverlayedPopup(950, 600);
			dims_xmlhttprequest_todiv('<?= dims::getInstance()->getScriptEnv(); ?>', 'submenu=1&mode=suivi&action=show&id=<?= $_SESSION['dims']['suivi']['reopen']; ?>&id_popup='+idpopup,'','p'+idpopup);
		<?php unset($_SESSION['dims']['suivi']['reopen']); } ?>
	});
	</script>
	<?php
}
