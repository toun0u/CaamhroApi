<link type="text/css" rel="stylesheet" href="./common/js/chosen/chosen.css" media="screen" />
<?php
$db = dims::getInstance()->getDb();
$type = 0;

$mode = dims_load_securvalue('mode', dims_const::_DIMS_CHAR_INPUT, true, true);
$edit_more = 1;
$edit_more = dims_load_securvalue('edit_more', dims_const::_DIMS_NUM_INPUT, true, true, false, $edit_more);

switch(get_class($this)){
	case 'tiers':
		$type = dims_const::_SYSTEM_OBJECT_TIERS;
		break;
	case 'contact' :
		$type = dims_const::_SYSTEM_OBJECT_CONTACT;
		break;
}
if ($type > 0){
	$sql =	"
		SELECT		mf.*,mc.label as categlabel, mc.id as id_cat, mb.protected,mb.name as namefield,mb.label as titlefield
		FROM		dims_mod_business_meta_field as mf
		INNER JOIN	dims_mb_field as mb
		ON			mb.id=mf.id_mbfield
		RIGHT JOIN	dims_mod_business_meta_categ as mc
		ON			mf.id_metacateg=mc.id
		WHERE		mf.id_object = :idobject
		AND			mf.used=1
		ORDER BY	mc.position, mf.position
		";
	$res = $db->query($sql, array(
		':idobject' => $type
	));
	$lstChamps = array();
	$lstCateg = array();
	while ($r = $db->fetchrow($res)){
		$ch = array();
		$ch['id_mtf'] = $r['id'];
		$ch['namefield'] = $r['namefield'];
		$ch['titlefield'] = $r['titlefield'];
		$ch['name'] = $r['name'];
		$ch['type'] = $r['type'];
		$ch['format'] = $r['format'];
		$ch['values'] = $r['values'];
		$ch['maxlength'] = $r['maxlength'];
		$lstChamps[$r['id_cat']][] = $ch;
		$lstCateg[$r['id_cat']] = $r['categlabel'];
	}

	$length = 2;

	require_once DIMS_APP_PATH.'modules/system/class_country.php';
	$country = new country();
	$country->open($this->fields['id_country']);

	foreach(array('telephone', 'telecopie') as $field) {
		if(!empty($this->fields[$field]) && $mode != "edit") {
			if(substr($this->fields[$field], 0, 1) != '+')
				$this->fields[$field] = '+'.$country->fields['phoneprefix'].$this->fields[$field];

			$this->fields[$field] = dims_format_phone($this->fields[$field]);
		}
	}

	if($mode == 'edit') {
		$_SESSION['dims']['crm_newent_saveredirect'] = dims::getInstance()->getScriptEnv().'?submenu='._DESKTOP_V2_CONCEPTS.'&id='.$this->getId().'&type='.$type;
		?>
		<form name="form_modify_user" method="post" action="<?php echo dims::getInstance()->getScriptEnv(); ?>" enctype="multipart/form-data">
		<?
			// Sécurisation du formulaire par token
			require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
			$token = new FormToken\TokenField;
			$token->field("action",	"save_object");
			$token->field("type",	$type);
			$token->field("id",		$this->getId());
			$tokenHTML = $token->generate();
			echo $tokenHTML;
		?>
		<input type="hidden" name="action" value="save_object" />
		<input type="hidden" name="type" value="<?php echo $type; ?>" />
		<input type="hidden" name="id" value="<?php echo $this->getId(); ?>" />
		<?php
	}
	?>
	<table cellpadding="3" cellspacing="0">
	<?
	$inputMailName=""; // Variable contenant le name de l'input contenant l'email (pour vérifier sa syntaxe)
	$inputMailName2=""; // Variable contenant le name de l'input contenant l'email2 (pour vérifier sa syntaxe)
	$inputMailName3=""; // Variable contenant le name de l'input contenant l'email3 (pour vérifier sa syntaxe)
	foreach($lstCateg as $idCateg => $categ){
		if (isset($lstChamps[$idCateg])){
			$values = '';
			foreach($lstChamps[$idCateg] as $champ){
				switch($champ['namefield']){
					case 'pays':
					case 'country':
					case 'address':
					case 'address2':
					case 'address3':
					case 'postalcode':
					case 'city':
					case 'adresse':
					case 'adresse2':
					case 'adresse3':
					case 'codepostal':
					case 'ville':
						/*if ($this->fields[$champ['namefield']] > 0 || $mode == 'edit') {
							if (isset($_SESSION['cste'][$champ['titlefield']]))
									$values .= '<td class="title_desc">'.$_SESSION['cste'][$champ['titlefield']].'</td>';
								else
									$values .= '<td class="title_desc">'.$champ['name'].'</td>';
							if($mode == 'edit') {
								$values .= '<td><select style="width: 260px;" name="id_country" class="crm_country" id="crm_country" data-placeholder="'.$_SESSION['cste']['_DIMS_SELECT_COUNTRY'].'">
									<option value=""></option>';
								$token->field("id_country");

									require_once DIMS_APP_PATH."modules/system/class_country.php";
									if ($this->fields['id_country'] == 0 || $this->fields['id_country'] == ''){
										$this->updateIdCountry();
									}
									$sel_Country = null;
									foreach (country::getAllCountries() as $country) {
										$sel = '';
										if ($country->fields['id'] == $this->fields['id_country']){
											$sel = "selected=true";
											$sel_Country = $country;
										}
										$values .= '<option value="'.$country->fields['id'].'"'.$sel.'>'.stripslashes($country->fields['printable_name']).'</option>';
									}
								$values .= '</select></td>';
							}else{
								$values .= '<td>'.$this->fields[$champ['namefield']].'</td>';
							}
						}*/
						break;
					case 'photo' :
						if($mode == 'edit') {
							if (isset($_SESSION['cste'][$champ['titlefield']]))
								$values .= '<td class="title_desc">'.$_SESSION['cste'][$champ['titlefield']].'</td>';
							else
								$values .= '<td class="title_desc">'.$champ['name'].'</td>';
							$values .= '<td><input type="file" id="photo" name="photo" /></td>';
							$token->field("photo");
						}
						break;
					case 'civilite':
					case 'lastname':
					case 'firstname':
					case 'intitule':
						if($mode == 'edit') { // Edition
							$values .= '<tr>';
							if (isset($_SESSION['cste'][$champ['titlefield']]))
								$values .= '<td class="title_desc">'.$_SESSION['cste'][$champ['titlefield']].'</td>';
							else
								$values .= '<td class="title_desc">'.$champ['name'].'</td>';

							$values .= '<td>';

							if (!is_array($champ['values'])) {
								$val = explode('||',$champ['values']);
							}

							switch($champ['type']) {
								case 'textarea':
									$values .= '<textarea class="text" name="fck_field'.$champ['id_mtf'].'" id="field'.$champ['id_mtf'].'" rows="5">'.$this->fields[$champ['namefield']].'</textarea>';
									$token->field("fck_field".$champ['id_mtf']);
									break;
								case 'text':
									$maxlength = ($champ['maxlength'] > 0 && $champ['maxlength'] != '') ? $champ['maxlength'] : '50';

									$values .= '<input style="width:250px;" type="text" id="field'.$champ['id_mtf'].'" name="fck_field'.$champ['id_mtf'].'" value="'.htmlspecialchars($this->fields[$champ['namefield']]).'" class="text" maxlength="'.$maxlength.'" />';
									$token->field("fck_field".$champ['id_mtf']);
									break;
								case 'select':
									$values .= '<select class="select" name="fck_field'.$champ['id_mtf'].'" id="field'.$champ['id_mtf'].'" class="select">
									<option></option>';
									$token->field("fck_field".$champ['id_mtf']);
										foreach($val as $elem) {
											$selected = ($elem == $this->fields[$champ['namefield']]) ? 'selected' : '';
											$values .= '<option '.$selected.' value="'.htmlspecialchars($elem).'">'.$elem.'</option>';
										}
									$values .= '</select>';
									break;
								case 'radio':
									$chec = '';
									if ($this->fields[$champ['namefield']] == 1)
										$chec = 'checked=true';
									$values .= '<input '.$chec.' type="radio" class="text" name="fck_field'.$champ['id_mtf'].'" id="field'.$champ['id_mtf'].'" />';
									$token->field("fck_field".$champ['id_mtf']);
									break;

							}
							$values .= '</td>';
							$values .= '</tr>';
						}
						break;
					default :
						if ((isset($this->fields[$champ['namefield']]) && trim($this->fields[$champ['namefield']]) != '') || $edit_more){
							$values .= '<tr>';

							// Label
							if (isset($_SESSION['cste'][$champ['titlefield']]))
								$values .= '<td class="title_desc">'.$_SESSION['cste'][$champ['titlefield']].'</td>';
							else
								$values .= '<td class="title_desc">'.$champ['name'].'</td>';

							// Value
							if($mode == 'edit') { // Edition

								$values .= '<td>';

								if (!is_array($champ['values'])) {
									$val = explode('||',$champ['values']);
								}

								switch($champ['type']) {
									case 'textarea':
										$values .= '<textarea class="text" name="fck_field'.$champ['id_mtf'].'" id="field'.$champ['id_mtf'].'" class="text" rows="5">'.$this->fields[$champ['namefield']].'</textarea>';
										$token->field("fck_field".$champ['id_mtf']);
										break;
									case 'text':
										$maxlength = ($champ['maxlength'] > 0 && $champ['maxlength'] != '') ? $champ['maxlength'] : '50';
										if ($champ['namefield']=="email"){
											$inputMailName = 'fck_field'.$champ['id_mtf']; // On donne le name de l'input à la variable $inputMailName pour l'utiliser après dans la vérification
										}elseif($champ['namefield']=="email2"){
											$inputMailName2 = 'fck_field'.$champ['id_mtf'];
										}elseif($champ['namefield']=="email3"){
											$inputMailName3 = 'fck_field'.$champ['id_mtf'];
										}
										$values .= '<input type="text" style="width:250px;" id="field'.$champ['id_mtf'].'" name="fck_field'.$champ['id_mtf'].'" value="'.htmlspecialchars($this->fields[$champ['namefield']]).'" class="text" maxlength="'.$maxlength.'" />';
										$token->field("fck_field".$champ['id_mtf']);
										break;
									case 'select':
										$values .= '<select class="select" name="fck_field'.$champ['id_mtf'].'" id="field'.$champ['id_mtf'].'" class="select">
										<option></option>';
										$token->field("fck_field".$champ['id_mtf']);
											foreach($val as $elem) {
												$selected = ($elem == $this->fields[$champ['namefield']]) ? 'selected' : '';
												$values .= '<option '.$selected.' value="'.htmlspecialchars($elem).'">'.$elem.'</option>';
											}
										$values .= '</select>';
										break;
									case 'radio':
										$chec = '';
										if ($this->fields[$champ['namefield']] == 'on')
											$chec = 'checked=true';
										$values .= '<input '.$chec.' type="checkbox" class="text" name="fck_field'.$champ['id_mtf'].'" id="field'.$champ['id_mtf'].'" />';
										$token->field("fck_field".$champ['id_mtf']);
										break;

								}
								$values .= '</td>';
							}
							else {
								$values .= '<td>';
								switch($champ['type']) {
									case 'radio':
										// echo dims_print_r($this->fields);
										if ($this->fields[$champ['namefield']] == 'on')
											$values .= '<img src="./common/img/checkdo.png" alt="'.$this->fields[$champ['namefield']].'" title="'.$this->fields[$champ['namefield']].'" />';
										else
											$values .= '<img src="./common/img/check.png" alt="'.$this->fields[$champ['namefield']].'" title="'.$this->fields[$champ['namefield']].'" />';
										break;
									default:
										$values .= $this->fields[$champ['namefield']];
										break;
								}
								$values .= '</td>';

							}

							$values .= '</tr>';
						}
						break;
				}
			}
			if ($values != ''){
				?>
				<tr>
					<th colspan="2"><? echo $categ; ?></th>
				</tr>
				<?
				echo $values;
			}
		}
	}
	$values = "";
	$lstAddress = $this->getAllAdresses();
	require_once DIMS_APP_PATH."modules/system/class_country.php";
	require_once DIMS_APP_PATH."modules/system/class_city.php";
	if($mode == 'edit') {
		$typeAdd = address_type::all("WHERE is_active=1 AND id_workspace = :idwork", array(':idwork'=>$_SESSION['dims']['workspaceid']));
		$a_countries = country::getAllCountries();
		foreach($lstAddress as $type){
			if(isset($type['add']) && !empty($type['add'])){
				$t = $type['obj'];
				$values .= "<tr>";
					$values .= '<th colspan="2">'.$_SESSION['cste']['_DIMS_LABEL_ADDRESS']." : ".$t->getLabel().'</th>';
				$values .= '</tr>';
				foreach($type['add'] as $address){
					$values .= '<tr><td class="title_desc"><input type="hidden" name="addr_list[]" value="'.$address->get('id').'" />'.$_SESSION['cste']['_TYPE'].'</td>';
					$values .= '<td><select name="type_address_'.$address->get('id').'" style="width:250px;">';
					foreach($typeAdd as $tt){
						if($t->get('id') == $tt->get('id')){
							$values .= '<option value="'.$tt->get('id').'" selected="true">'.$tt->getLabel().'</option>';
						}else{
							$values .= '<option value="'.$tt->get('id').'">'.$tt->getLabel().'</option>';
						}
					}
					$values .= '</select></td></tr>';

					$values .= '<tr><td class="title_desc">'.$_SESSION['cste']['_DIMS_LABEL_ADDRESS'].'</td>';
					$values .= '<td><input name="adr_'.$address->get('id').'_address" style="width:250px;" type="text" value="'.$address->get('address').'" /></td></tr>';

					$values .= '<tr><td class="title_desc">'.$_SESSION['cste']['_DIMS_LABEL_CP'].'</td>';
					$values .= '<td><input name="adr_'.$address->get('id').'_postalcode" style="width:250px;" type="text" value="'.$address->get('postalcode').'" /></td></tr>';

					$values .= '<tr><td class="title_desc">'.$_SESSION['cste']['_DIMS_LABEL_COUNTRY'].'</td>';
					$values .= '<td><select class="sel_country" dims-data-value="'.$address->get('id').'" name="adr_'.$address->get('id').'_id_country" id="adr_'.$address->get('id').'_id_country" style="width:250px;">';
					$sel_Country = null;
					foreach($a_countries as $c){
						if($c->get('id') == $address->get('id_country')){
							$values .= '<option value="'.$c->get('id').'" selected="true">'.$c->get('printable_name').'</option>';
							$sel_Country = $c;
						}else{
							$values .= '<option value="'.$c->get('id').'">'.$c->get('printable_name').'</option>';
						}
					}
					$values .= '</select></td></tr>';

					$values .= '<tr class="end_add_desc"><td class="title_desc">'.$_SESSION['cste']['_DIMS_LABEL_CITY'].'</td>';
					$values .= '<td id="chosen_city_'.$address->get('id').'"><select class="sel_city" dims-data-value="'.$address->get('id').'" id="adr_'.$address->get('id').'_id_city" name="adr_'.$address->get('id').'_id_city" style="width:250px;">';
					if(!is_null($sel_Country)){
						$citys = $sel_Country->getAllCity();
						foreach($citys as $city){
							if ($address->get('id_city') == $city->get('id'))
								$values .= '<option value="'.$city->get('id').'" selected="true">'.$city->get('label').'</option>';
							else
								$values .= '<option value="'.$city->get('id').'">'.$city->get('label').'</option>';
						}
					}
					$values .= '</select></td></tr>';
				}
			}
		}
		?>
		<script type="text/javascript">
			$(document).ready(function(){
				$("select.sel_city").chosen({
					allow_single_deselect:true,
					no_results_text: "<div class=\"button_add_city\" style=\"float:right;color:#690;cursor:pointer;\"><img style=\"float:left;\" src=\"<? echo _DESKTOP_TPL_PATH; ?>/gfx/common/add.png\" /><div style=\"float:right;margin-top:3px;\"><?php echo addslashes($_SESSION['cste']['ADD_IT_LA']); ?></div></div><?php echo addslashes($_SESSION['cste']['NO_RESULT']);?>"
				});
				$("select.sel_country")
					.chosen({no_results_text: "<?php echo addslashes($_SESSION['cste']['NO_RESULT']);?>"})
					.change(function(){
						var id = $('select.sel_country',$(this).parents('td:first')).attr('dims-data-value'),
							val = $(this).val();
						if($(this).val() != '') {
							$('#adr_'+id+'_id_city').removeAttr('disabled');
						}
						else {
							$('#adr_'+id+'_id_city').attr('disabled','disabled');
						}
						refreshCityOfCountry(val,'adr_'+id+'_id_city');
				});
				//$("select#type_address").chosen({no_results_text: "<?php echo addslashes($_SESSION['cste']['NO_RESULT']);?>"});

				$('div.button_add_city').live('click',function(){
					$(this).die('click');
					var id = $('select.sel_city',$(this).parents('td:first')).attr('dims-data-value');
					addNewCity('chosen_city_'+id,'adr_'+id+'_id_country');
					$("select.sel_country").each(function(){
						if($(this).val() == $('select#adr_'+id+'_id_country').val() && id != $(this).attr('dims-data-value')){
							refreshCityOfCountry($(this).val(),'adr_'+$(this).attr('dims-data-value')+'_id_city',$('select#adr_'+$(this).attr('dims-data-value')+'_id_city').val());
						}
					});
				});
			});
		</script>
		<?php
	}else{
		foreach($lstAddress as $type){
			if(isset($type['add']) && !empty($type['add'])){
				$t = $type['obj'];
				$values .= "<tr>";
					$values .= '<th colspan="2">'.$_SESSION['cste']['_DIMS_LABEL_ADDRESS']." : ".$t->getLabel().'</th>';
				$values .= '</tr><tr><td colspan="2"><ul>';
				foreach($type['add'] as $address){
					$values .= '<li>';
					$address->setLightAttribute('type',$t->get('id'));
					$values .= $address->get('address');
					if($address->get('address2') != '') $values .= " ".$address->get('address2');
					if($address->get('address3') != '') $values .= " ".$address->get('address3');
					$values .= " ".$address->get('postalcode');
					if($address->get('id_city') != '' && $address->get('id_city') > 0){
						$city = new city();
						$city->open($address->get('id_city'));
						$values .= " ".$city->get('label');
					}
					$country = new country();
					$country->open($address->get('id_country'));
					$values .= " ".$country->get('printable_name');
					$values .= '</li>';
				}
				$values .= "</ul></td></tr>";
			}
		}
	}
	echo $values;
	?>
	</table>
	<?php
	if($mode == 'edit') {
		?>
		<div class="buttons">
			<?php
			if($edit_more) {
				?>
				<input type="button" onclick="Javascript: location.href='<?php echo dims::getInstance()->getScriptEnv().'?mode=edit&edit_more=0'; ?>'" value="<?php echo $_DIMS['cste']['_LABEL_EDIT_LESS']; ?>" />
				<?php
			}
			else {
				?>
				<input type="button" onclick="Javascript: location.href='<?php echo dims::getInstance()->getScriptEnv().'?mode=edit&edit_more=1'; ?>'" value="<?php echo $_DIMS['cste']['_LABEL_EDIT_MORE']; ?>" />
				<?php
			}
			?>
			<input type="button" value="<?php echo $_DIMS['cste']['_DIMS_SAVE']; ?>" onclick="javascript:valideFormUser();"  />
			<input type="button" onclick="Javascript: location.href='<?php echo dims::getInstance()->getScriptEnv().'?submenu='._DESKTOP_V2_CONCEPTS.'&id='.$this->getId().'&type='.$type; ?>'" value="<?php echo $_DIMS['cste']['_DIMS_BACK']; ?>" />
		</div>
		<?
			$tokenHTML = $token->generate();
			echo $tokenHTML;
		?>
		</form>
		<script type="text/javascript">
			function valideFormUser(){
				form=document.form_modify_user;
				regexp_mail = new RegExp("^[a-z0-9._-]+@[a-z0-9._-]{2,}\\.[a-z]{2,4}$","i");
				<? if (!empty($inputMailName)) { ?>
					if (form.<? echo $inputMailName ?>.value.length==0 || regexp_mail.test(form.<? echo $inputMailName ?>.value)){
						// L'email est valide
					} else {
						// L'email est invalide
						alert('<? echo addslashes($_SESSION['cste']['_DIMS_JS_EMAIL_ERROR_0'])." (".$_SESSION['cste']['_DIMS_LABEL_EMAIL']." n°1)"; ?>');
						return false;
					}
				<? } ?>
				<? if (!empty($inputMailName2)) { ?>
					if (form.<? echo $inputMailName2 ?>.value.length==0 || regexp_mail.test(form.<? echo $inputMailName2 ?>.value)){
						// L'email est valide
					} else {
						// L'email est invalide
						alert('<? echo addslashes($_SESSION['cste']['_DIMS_JS_EMAIL_ERROR_0'])." (".$_SESSION['cste']['_DIMS_LABEL_EMAIL']." n°2)"; ?>');
						return false;
					}
				<? } ?>
				<? if (!empty($inputMailName3)) { ?>
					if (form.<? echo $inputMailName3 ?>.value.length==0 || regexp_mail.test(form.<? echo $inputMailName3 ?>.value)){
						// L'email est valide
					} else {
						// L'email est invalide
						alert('<? echo addslashes($_SESSION['cste']['_DIMS_JS_EMAIL_ERROR_0'])." (".$_SESSION['cste']['_DIMS_LABEL_EMAIL']."  n°3)"; ?>');
						return false;
					}
				<? } ?>
				<? $_SESSION['dims']['fail_email_redirect_url']="./admin.php?mode=edit&error=email"; ?>
				document.form_modify_user.submit();
			}


			$(document).ready(function(){
				$("select.crm_country").chosen({no_results_text: "No results matched"});
			});
		</script>
		<?php
	}
	?>
<?
}
?>
