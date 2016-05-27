<?php
$page = dims_load_securvalue('page',dims_const::_DIMS_NUM_INPUT,true,true,true);
if (empty($page) || $page < 0)
	$page = 0;
$db = dims::getInstance()->db;

$nb_max = 0;
$sql = "SELECT	COUNT(id) as nb_elem
	FROM	".$this->getRefTmpTable()."
	WHERE	status < :status ";
$res = $db->query($sql, array(
	':status' => _STATUS_IMPORT_OK_CT
));
if ($r = $db->fetchrow($res)){
	$nb_max = $r['nb_elem'];
}

$sql = "SELECT	*
	FROM	".$this->getRefTmpTable()."
	WHERE	status < :status
	LIMIT	".$page.", 1";
$res = $db->query($sql, array(
	':status' => array('type' => PDO::PARAM_INT, 'value' => _STATUS_IMPORT_OK_CT),
));
if ($r = $db->fetchrow($res)){
	$r2 = array_change_key_case($r, CASE_LOWER);
	$r3 = array_keys($r2);
	foreach($r3 as $k => $v)
		$r3[$k] = str_replace(array(" ","-","."),"_",$v);
	$r = array_combine($r3,$r);
	?>
	<h3>
		<? echo $_SESSION['cste']['_ERROR']; ?> : <? echo $page+1; ?> / <? echo $nb_max; ?>
	</h3>
	<form id="save_merge" name="save_merge" method="POST" action="<? echo dims::getInstance()->getScriptEnv()."?import_op="._OP_MERGE_IMPORT_SAVE."&id_import=".$this->fields['id']; ?>">
		<?
			// Sécurisation du formulaire par token
			require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
			$token = new FormToken\TokenField;
			$token->field("page",		$page);
			$token->field("id_contact",	"0");
			$token->field("id_column",	$r['id']);
			$token->field("action_save");
		?>
		<input type="hidden" name="page" value="<? echo $page; ?>" />
		<input type="hidden" name="id_contact" id="id_contact" value="0" />
		<input type="hidden" name="id_column" value="<? echo $r['id']; ?>" />
		<input type="hidden" name="action_save" id="action_save" value="" />
		<table cellpadding="0" cellspacing="0" style="width: 100%;border:1px solid #D6D6D6;">
			<tr>
				<th style="width:275px;padding:5px;border-bottom:1px solid #D6D6D6;border-right:1px solid #D6D6D6;">
				<? echo $_SESSION['cste']['_EXCEL_FILE']; ?>
				</th>
				<? if ($r['status'] != _STATUS_IMPORT_ERR_TIERS  && $r['status']!=_STATUS_IMPORT_OK_CT){ ?>
				<th style="width:200px;padding:5px;border-bottom:1px solid #D6D6D6;border-right:1px solid #D6D6D6;">
					<? echo $_SESSION['cste']['_DIMS_LABEL_CONTACT']; ?>
					<input name="type_<? echo dims_const::_SYSTEM_OBJECT_CONTACT; ?>" checked=true type="checkbox" value="<? echo dims_const::_SYSTEM_OBJECT_CONTACT; ?>" class="active_type" />
					<?
						$token->field("type_".dims_const::_SYSTEM_OBJECT_CONTACT);
					?>
				</th>
				<? }else{ ?>
				<th style="width:200px;padding:5px;border-bottom:1px solid #D6D6D6;border-right:1px solid #D6D6D6;">
					<? echo $_SESSION['cste']['_DIMS_LABEL_COMPANY']; ?>
					<input name="type_<? echo dims_const::_SYSTEM_OBJECT_TIERS; ?>" checked=true type="checkbox" value="<? echo dims_const::_SYSTEM_OBJECT_TIERS; ?>" class="active_type" />
					<?
						$token->field("type_".dims_const::_SYSTEM_OBJECT_TIERS);
					?>
				</th>
				<? } ?>
				<th style="padding:5px;border-bottom:1px solid #D6D6D6;border-right:1px solid #D6D6D6;">
					<?
					if ($r['status'] != _STATUS_IMPORT_ERR_TIERS  && $r['status']!=_STATUS_IMPORT_OK_CT)
						echo $_SESSION['cste']['_LIST_OF_CONTACTS'];
					else
						echo $_SESSION['cste']['_LABEL_LIST_ALREADY_KNOWN'];
					?>
				</th>
				<th style="min-width:40%;padding:5px;border-bottom:1px solid #D6D6D6;">

				</th>
			</tr>
			<?
			$liste_column_table_temp = $r;
			unset($liste_column_table_temp['id']);
			unset($liste_column_table_temp['status']);
			unset($liste_column_table_temp['id_tiers']);
			unset($liste_column_table_temp['id_contact']);

			$liste_column_table_tempBis = array();
			foreach($liste_column_table_temp as $key => $val)
				$liste_column_table_tempBis[strtolower($key)] = array();

			$params = array();
			$sql =	"
				SELECT		mf.*,mc.label as categlabel, mc.id as id_cat, mb.protected,mb.name as namefield,mb.label as titlefield
				FROM		dims_mod_business_meta_field as mf
				INNER JOIN	dims_mb_field as mb
				ON		mb.id=mf.id_mbfield
				RIGHT JOIN	dims_mod_business_meta_categ as mc
				ON		mf.id_metacateg=mc.id
				WHERE		mf.id_object IN (".
												$this->db->getParamsFromArray(explode(',', dims_const::_SYSTEM_OBJECT_TIERS ), 'objecttiers', $params)
											.",".
												$this->db->getParamsFromArray(explode(',', dims_const::_SYSTEM_OBJECT_CONTACT), 'objectcontact', $params)
											.")
				AND		mf.used=1
				ORDER BY	mc.position, mf.position
				";
			$res2 = $db->query($sql, $params);
			$lstChamps = array();
			$lstCateg = array();
			$oblFieldsCtTiers = array();
			$oblFieldsCtTiers[dims_const::_SYSTEM_OBJECT_TIERS]['intitule'] = 0;
			$oblFieldsCtTiers[dims_const::_SYSTEM_OBJECT_CONTACT]['firstname'] = 0;
			$oblFieldsCtTiers[dims_const::_SYSTEM_OBJECT_CONTACT]['lastname'] = 0;
			while ($r2 = $db->fetchrow($res2)){
				$ch = array();
				$ch['id_mtf'] = $r2['id'];
				$ch['namefield'] = $r2['namefield'];
				$ch['titlefield'] = $r2['titlefield'];
				$ch['name'] = $r2['name'];
				$ch['type'] = $r2['type'];
				$ch['format'] = $r2['format'];
				$ch['values'] = $r2['values'];
				$ch['maxlength'] = $r2['maxlength'];
				$ch['protected'] = $r2['protected'];
				$lstChamps[$r2['id_object']][$r2['id']] = $ch;

				if ($ch['namefield'] == 'intitule' && $r2['id_object'] == dims_const::_SYSTEM_OBJECT_TIERS){
					$oblFieldsCtTiers[dims_const::_SYSTEM_OBJECT_TIERS][$ch['namefield']] = $ch['id_mtf'];
				}elseif (($ch['namefield'] == 'firstname' || $ch['namefield'] == 'lastname') && $r2['id_object'] == dims_const::_SYSTEM_OBJECT_CONTACT){
					$oblFieldsCtTiers[dims_const::_SYSTEM_OBJECT_CONTACT][$ch['namefield']] = $ch['id_mtf'];
				}

				if (isset($liste_column_table_tempBis[strtolower($r2['namefield'])])){
					$liste_column_table_tempBis[strtolower($r2['namefield'])][$r2['id_object']] = $r2['id'];
				}elseif (isset($liste_column_table_tempBis[strtolower(((isset($_SESSION['cste'][$r2['titlefield']]))?$_SESSION['cste'][$r2['titlefield']]:$r2['titlefield']))])){
					$liste_column_table_tempBis[strtolower(((isset($_SESSION['cste'][$r2['titlefield']]))?$_SESSION['cste'][$r2['titlefield']]:$r2['titlefield']))][$r2['id_object']] = $r2['id'];
				}

			}
			$_SESSION['dims']['import']['fields_ct_tiers'] = $lstChamps;

			require_once DIMS_APP_PATH."modules/system/import/class_check_fields.php";
			$lstTiers = import_check_fields::getListForType(dims_const::_SYSTEM_OBJECT_TIERS);
			$lstCt = import_check_fields::getListForType(dims_const::_SYSTEM_OBJECT_CONTACT);
			$firstname = $lastname = $intitule = "";
			foreach($liste_column_table_tempBis as $key => $val){
				if (isset($lstTiers[$key])){
					$liste_column_table_tempBis[$key][dims_const::_SYSTEM_OBJECT_TIERS] = $lstTiers[$key];
					if ($lstTiers[$key] == $oblFieldsCtTiers[dims_const::_SYSTEM_OBJECT_TIERS]['intitule'])
						$intitule = $r[$key];
				}
				if (isset($lstCt[$key])){
					$liste_column_table_tempBis[$key][dims_const::_SYSTEM_OBJECT_CONTACT] = $lstCt[$key];
					if ($lstCt[$key] == $oblFieldsCtTiers[dims_const::_SYSTEM_OBJECT_CONTACT]['firstname'])
						$firstname = $r[$key];
					elseif ($lstCt[$key] == $oblFieldsCtTiers[dims_const::_SYSTEM_OBJECT_CONTACT]['lastname'])
						$lastname = $r[$key];
				}
			}
			$listCtTiers = false;
			foreach($liste_column_table_tempBis as $key => $val){
				?>
				<tr>
					<td style="border-right:1px solid #D6D6D6;">
						<? echo $key; ?>
					</td>
					<? if ($r['status'] != _STATUS_IMPORT_ERR_TIERS  && $r['status']!=_STATUS_IMPORT_OK_CT){ ?>
					<td style="border-right:1px solid #D6D6D6;">
						<?
						if (isset($_SESSION['dims']['import']['fields_ct_tiers'][dims_const::_SYSTEM_OBJECT_CONTACT])){
							?>
							<select name="field_ct_<? echo $key; ?>" rel="<? echo dims_const::_SYSTEM_OBJECT_CONTACT; ?>">
								<?
									$token->field("field_ct_".$key);
								?>
								<option value="dims_nan" rel="<? echo dims_const::_SYSTEM_OBJECT_CONTACT; ?>">--</option>
							<?
							foreach($_SESSION['dims']['import']['fields_ct_tiers'][dims_const::_SYSTEM_OBJECT_CONTACT] as $val2){
								$sel = "";
								if (isset($val[dims_const::_SYSTEM_OBJECT_CONTACT]) && $val[dims_const::_SYSTEM_OBJECT_CONTACT] == $val2['id_mtf'])
									$sel = " selected=true ";
								?>
								<option <? echo $sel; ?> ref="<? echo $val2['namefield']; ?>" value="<? echo $val2['id_mtf']; ?>"><? echo (isset($_SESSION['cste'][$val2['titlefield']])?$_SESSION['cste'][$val2['titlefield']]:$val2['titlefield']).(($val2['protected'])?" *":""); ?></option>
								<?
							}
							?>
							</select>
							<?
						}
						?>
					</td>
					<? }else{ ?>
					<td style="border-right:1px solid #D6D6D6;">
						<?
						if (isset($_SESSION['dims']['import']['fields_ct_tiers'][dims_const::_SYSTEM_OBJECT_TIERS])){
							?>
							<select name="field_tiers_<? echo $key; ?>" rel="<? echo dims_const::_SYSTEM_OBJECT_TIERS; ?>">
								<?
									$token->field("field_tiers_".$key);
								?>
								<option value="dims_nan" rel="<? echo dims_const::_SYSTEM_OBJECT_TIERS; ?>">--</option>
							<?
							foreach($_SESSION['dims']['import']['fields_ct_tiers'][dims_const::_SYSTEM_OBJECT_TIERS] as $val2){
								$sel = "";
								if (isset($val[dims_const::_SYSTEM_OBJECT_TIERS]) && $val[dims_const::_SYSTEM_OBJECT_TIERS] == $val2['id_mtf'])
									$sel = " selected=true ";
								?>
								<option <? echo $sel; ?> ref="<? echo $val2['namefield']; ?>" value="<? echo $val2['id_mtf']; ?>"><? echo (isset($_SESSION['cste'][$val2['titlefield']])?$_SESSION['cste'][$val2['titlefield']]:$val2['titlefield']).(($val2['protected'])?" *":""); ?></option>
								<?
							}
							?>
							</select>
							<?
						}
						?>
					</td>
					<? } ?>
					<?
					if (!$listCtTiers){
						?>
						<td rowspan="<? echo count($liste_column_table_tempBis); ?>" class="list_ct_tiers" style="vertical-align: top;border-right:1px solid #D6D6D6;">
							<table cellpadding="0" cellspacing="0" style="width: 100%;">
								<?
								if ($r['status'] != _STATUS_IMPORT_ERR_TIERS && $r['status']!=_STATUS_IMPORT_OK_CT){
									foreach(dims::getInstance()->dims_levenshtein($firstname,$lastname,1) as $ct){
										?>
										<tr style="cursor: pointer;" ref="<? echo dims_const::_SYSTEM_OBJECT_CONTACT; ?>" rel="<? echo $ct['id_contact']; ?>">
											<td class="contacts" style="padding:5px;">
												<? echo $ct['firstname']." ".$ct['lastname']; ?>
											</td>
										</tr>
										<?
									}
								}else{

									foreach(dims::getInstance()->dims_levenshteinTiers($intitule,1) as $ct){
										?>
										<tr style="cursor: pointer;" ref="<? echo dims_const::_SYSTEM_OBJECT_TIERS; ?>" rel="<? echo $ct['id_tiers']; ?>">
											<td class="contacts" style="padding:5px;">
												<? echo $ct['intitule']; ?>
											</td>
										</tr>
										<?
									}
								}
								?>
							</table>
						</td>
						<?
						$listCtTiers = true;
					}
					?>
					<td>
					</td>
				</tr>
				<?
			}
			?>
		</table>
		<?
			$tokenHTML = $token->generate();
			echo $tokenHTML;
		?>
	</form>
	<div class="paginationsearch" style="text-align: right;margin-bottom:10px;margin-top:10px;">
		<input type="button" onclick="javascript:document.location.href='<? echo dims::getInstance()->getScriptEnv()."?import_op="._OP_SAVE_HISTORY; ?>';" value="<? echo $_SESSION['cste']['_DIMS_BACK']; ?>" />
		<input type="button" onclick="javascript:$('input#action_save').val('new');document.save_merge.submit();" value="<? echo $_SESSION['cste']['_DIMS_IMPORT_CT_NO_SAME']; ?>" />
		<input type="button" onclick="javascript:$('input#action_save').val('import');document.save_merge.submit();" value="<? echo$_SESSION['cste']['_USE_DATA_OF_IMPORT']; ?>" />
		<input type="button" onclick="javascript:$('input#action_save').val('origin');document.save_merge.submit();" value="<? echo $_SESSION['cste']['_KEEP_DATA_ORIGIN']; ?>" style="margin-right:15px;" />
		<span class="label"><? echo $_SESSION['cste']['_LABEL_IMPORT']; ?> : </span>
		<?
		if ($page > 0){
			?>
			<a href="<? echo dims::getInstance()->getScriptEnv()."?import_op="._OP_MERGE_IMPORT."&id_import=".$this->fields['id']."&page=".($page-1); ?>">
				<? echo $_SESSION['cste']['_PREVIOUS']; ?>
			</a>
			<?
		}
		if ($page < $nb_max){
			?>
			<a href="<? echo dims::getInstance()->getScriptEnv()."?import_op="._OP_MERGE_IMPORT."&id_import=".$this->fields['id']."&page=".($page+1); ?>">
				<? echo $_SESSION['cste']['_NEXT']; ?>
			</a>
			<?
		}
		?>
	</div>
	<script type="text/javascript">
	/* TODO : Effectuer un chargement de la liste lors du changement du firstname/lastname/intitule */
		var currentData = null;
		var currentType = null;
		var dataFromExcel = <? echo json_encode($r); ?>;
		$(document).ready(function(){
			$('td.list_ct_tiers table tr').click(function(){
				if (!$(this).hasClass('selected')){
					$('td.list_ct_tiers table tr').removeClass('selected');
					$(this).addClass('selected');
					$('input#id_contact').val($(this).attr('rel'));
					currentType = $(this).attr('ref');
					$.ajax({
						type: "POST",
						url: 'admin.php',
						data: {
							'dims_op' : 'desktopv2',
							'action': 'get_infos_ct_tiers',
							'type': currentType,
							'id': $(this).attr('rel')
						},
						dataType: "json",
						success: function(data) {
							currentData = data;
							getInfosCt(data);
						}
					});
				}
			});
			$('form#save_merge select').change(function(){
				if ($(this).attr('rel') == currentType && currentData != null)
					getInfosCt(currentData);
			});
		});
		function getInfosCt(data){
			$('form#save_merge table:first tr').each(function(){
				if ($(this).is(':first-child')){
					if(currentType == <? echo dims_const::_SYSTEM_OBJECT_CONTACT; ?>)
						$('th:last',$(this)).html('<div style="width:49%;float:left;border-right:1px solid;">Excel</div><div style="width:49%;float:right;"><? echo $_SESSION['cste']['_DIMS_LABEL_CONTACT']; ?></div>');
					else if(currentType == <? echo dims_const::_SYSTEM_OBJECT_TIERS; ?>)
						$('th:last',$(this)).html('<div style="width:49%;float:left;border-right:1px solid;">Excel</div><div style="width:49%;float:right;"><? echo $_SESSION['cste']['_DIMS_LABEL_COMPANY']; ?></div>');
				}else if ($('select[rel="'+currentType+'"] option:selected',$(this)).val() != 'dims_nan'){
					var key = $('select[rel="'+currentType+'"] option:selected',$(this)).attr('ref');
					$('td:last[class!="contacts"]',$(this)).html('<div style="width:49%;min-height:13px;float:left;border-right:1px solid;">'+dataFromExcel[jQuery.trim($('td:first',$(this)).html())]+'</div><div style="width:49%;float:right;">'+data[key]+'</div>');
				}
			});
		}
	</script>
	<?
}else
	dims_redirect('/admin.php?import_op='._OP_NEW_IMPORT);
?>
