<?php
$mode = dims_load_securvalue('mode', dims_const::_DIMS_CHAR_INPUT, true, true);
if ($mode == "edit"){
	?>
	<!--<link type="text/css" rel="stylesheet" href="<?php echo _DESKTOP_TPL_PATH; ?>/opportunity/css/styles.css" media="screen" />
	<link type="text/css" rel="stylesheet" href="./common/js/chosen/chosen.css" media="screen" />
	<script language="JavaScript" type="text/JavaScript" src="./common/js/chosen/chosen.jquery.js"></script>-->
	<form method="POST" action="<?php echo dims::getInstance()->getScriptEnv(); ?>" enctype="multipart/form-data" name="save_opportunity">
		<?
			// Sécurisation du formulaire par token
			require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
			$token = new FormToken\TokenField;
			$token->field("action",	"save_object");
			$token->field("type",	dims_const::_SYSTEM_OBJECT_OPPORTUNITY);
			$token->field("id",		$this->fields['id']);
			$tokenHTML = $token->generate();
			echo $tokenHTML;
		?>
		<input type="hidden" name="action" value="save_object" />
		<input type="hidden" name="type" value="<?php echo dims_const::_SYSTEM_OBJECT_OPPORTUNITY; ?>" />
		<input type="hidden" name="id" value="<?php echo $this->fields['id']; ?>" />
		<?
		require_once DIMS_APP_PATH.'modules/system/class_country.php';
		require_once DIMS_APP_PATH.'modules/system/opportunity/class_sector.php';
		require_once DIMS_APP_PATH.'modules/system/opportunity/class_type.php';

		$db = dims::getInstance()->db;
		$datestart = explode('-', $this->fields['datejour']);
		$dateend = explode('-', $this->fields['datefin']);
		$location = explode(',',$this->fields['lieu']);
		$this->setLightAttribute('location',$location);
		$this->setLightAttribute('datestart',$datestart);
		$this->setLightAttribute('dateend',$dateend);
		$event = 0;
		$sel = "SELECT		DISTINCT dims_mod_business_action.id
				FROM		dims_mod_business_action
				INNER JOIN	dims_matrix
				ON			dims_matrix.id_opportunity = :idglobalobject
				AND			dims_matrix.id_action > 0
				AND			dims_matrix.id_action = dims_mod_business_action.id_globalobject
				GROUP BY	dims_matrix.id_action";
		$res = $db->query($sel, array(
			':idglobalobject' => array('type' => PDO::PARAM_INT, 'value' => $this->fields['id_globalobject']),
		));
		if ($r = $db->fetchrow($res))
			$event = $r['id'];
		$this->setLightAttribute('event',$event);

		// chargement des pays
		$this->setLightAttribute('a_countries', country::getAllCountries());

		// chargement des secteurs
		$this->setLightAttribute('a_sectors', opportunity_sector::getAllSectors());

		// chargement des types
		$this->setLightAttribute('a_types', opportunity_type::getAllTypes());

		// chargement des events
		$a_events = array();
		$rs = $db->query('	SELECT		id, libelle
							FROM		dims_mod_business_action
							WHERE		type = :type
							AND			id_parent = 0
							ORDER BY	libelle', array(
				':type' => dims_const::_PLANNING_ACTION_EVT
		));
		while ($row = $db->fetchrow($rs)) {
			$a_events[$row['id']] = $row['libelle'];
		}
		$this->setLightAttribute('a_events',$a_events);
		$this->display(_DESKTOP_TPL_LOCAL_PATH.'/opportunity/edit_opportunity_desc.tpl.php');
		?>
	</form>
	<div id="global_error" class="todo_form_error"></div>
	<?
	echo dims_create_button($_SESSION['cste']['_DIMS_LABEL_CANCEL'],'cancel','void(0);','',"float:right;margin:10px;",dims::getInstance()->getScriptEnv()."?mode=1");
	echo dims_create_button($_SESSION['cste']['_DIMS_SAVE'],'disk','if(dims_controlform(\'save_opportunity\', \'global_error\', \'champ obligatoire non saisi\'))document.save_opportunity.submit(); else return false;"','',"float:right;margin:10px;");
}else{
?>
<div class="desc_picture_mini">
	<?
	if ($this->fields['banner_path'] != '' && file_exists($this->fields['banner_path']))
		echo '<img class="conc_img_event" src="'.$this->fields['banner_path'].'" />';
	else
		echo '<img class="conc_img_event" src="'._DESKTOP_TPL_PATH.'/gfx/common/event_default_search.png" />';
	?>
</div>
<div class="desc_content" style="width:75%;">
	<table cellspacing="0" cellpadding="3">
		<tbody>
			<tr>
				<td colspan="2">
					<h1><? echo $this->fields['libelle']; ?></h1>
				</td>
			</tr>
			<tr>
				<td>
					<!-- On redirige sur la même page pour l'édition -->
					<div>
						<a href="Javascript: void(0);" onclick="javascript:document.location.href='/<?php echo dims::getInstance()->getScriptEnv() ?>?mode=edit';">
							<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/modify.png" />
							<span><? echo $_SESSION['cste']['_DIMS_LABEL_EDIT']; ?></span>
						</a>
					</div>
				</td>
				<td>
					<div>
						<a href="Javascript: void(0);" onclick="javascript:dims_confirmlink('/<?php echo dims::getInstance()->getScriptEnv() ?>?action=delete_opportunity&opp_id=<?php echo $this->getId(); ?>', '<?php echo $_SESSION['cste']['_DIMS_CONFIRM']; ?>');">
							<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/close.png" />
							<span><? echo $_SESSION['cste']['_DELETE']; ?></span>
						</a>
					</div>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					&nbsp;
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<?php echo stripslashes(str_replace('\r\n', '<br/>', $this->fields['description'])); ?>
				</td>
			</tr>
		</tbody>
	</table>
</div>
<div class="desc_date">
	<?php
if($this->fields['datejour'] == '0000-00-00'){
	require_once DIMS_APP_PATH.'modules/system/class_search.php';
	$matrix = new search();
	$my_context = $matrix->exploreMatrice($_SESSION['dims']['workspaceid'], array(), array($this->fields['id_globalobject']), array(), array(),array(), array(), array(), array(), array());

	$distrib = $my_context['distribution'];
	if(isset($distrib)){
		if(isset($distrib['opportunities'][$this->fields['id_globalobject']]['ref']) && !empty($distrib['opportunities'][$this->fields['id_globalobject']]['ref'])){
			$ref = new action();
			$ref->openWithGB($distrib['opportunities'][$this->fields['id_globalobject']]['ref']);
			if(!$ref->isNew()){
				$this->fields['datejour'] = $ref->fields['datejour'];
				$this->save();//à priori c'est un hack pour corriger un truc improbable, autant qu'on n'y passe rarement en sauvegardant tout ça
			}
		}
	}
}

	$datedeb = explode('-',$this->fields['datejour']);
	$datefin = explode('-',$this->fields['datefin']);
		?>
		<table class="desc_date_calendar">
			<tr>
				<td class="desc_date_bloc_calendar">
					<div class="bloc_ligne calendar">
						<table class="ro_calendar">
							<tbody>
								<tr>
									<td class="bloc_calendar">
										<table cellspacing="0" cellpadding="0" width="100%">
											<tbody>
												<tr>
													<td align="center" class="calendar_top"><? if ($datedeb[1] > 0) { echo date('M',mktime(00,00,00,$datedeb[1]))?>. <? } echo $datedeb[0]; ?></td>
												</tr>
												<tr>
													<td align="center" class="calendar_bot"><? if ($datedeb[2] > 0) echo $datedeb[2]; else echo '-'; ?></td>
												</tr>
											</tbody>
										</table>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</td>
			</tr>
		</table>

		<?php
		if($this->fields['datejour'] != $this->fields['datefin'] && $this->fields['datefin']!= '0000-00-00'){
			?>
			<table class="desc_date_calendar">
				<tr>
					<td align="center"><img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/separateur_date.png" /></td>
				</tr>
				<tr>
					<td class="desc_date_bloc_calendar">
						<div class="bloc_ligne calendar">
							<table class="ro_calendar">
								<tbody>
									<tr>
										<td class="bloc_calendar">
											<table cellspacing="0" cellpadding="0" width="100%">
												<tbody>
													<tr>
														<td align="center" class="calendar_top"><? if ($datefin[1] > 0) { echo date('M',mktime(00,00,00,$datefin[1]))?>. <? } echo $datefin[0]; ?></td>
													</tr>
													<tr>
														<td align="center" class="calendar_bot"><? if ($datefin[2] > 0) echo $datefin[2]; else echo '-'; ?></td>
													</tr>
												</tbody>
											</table>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
					</td>
				</tr>
			</table>
			<?php
		}
	?>
</div>
<? } ?>
