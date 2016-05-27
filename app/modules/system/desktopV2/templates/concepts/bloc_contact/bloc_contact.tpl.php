<?php
//if (!isset($_SESSION['desktopv2']['concepts']['rech_type'])) $_SESSION['desktopv2']['concepts']['rech_type'] = 0;
$_SESSION['desktopv2']['concepts']['rech_type'] = dims_load_securvalue('typerech',dims_const::_DIMS_NUM_INPUT,true,true,true,$_SESSION['desktopv2']['concepts']['rech_type']);
$_SESSION['desktopv2']['concepts']['contact_search'] = dims_load_securvalue('contact_search',dims_const::_DIMS_CHAR_INPUT,true,true,true,$_SESSION['desktopv2']['concepts']['contact_search']);

// initialisation des filtres
$init_contact_search = dims_load_securvalue('init_contact_search', dims_const::_DIMS_NUM_INPUT, true, true);
if ($init_contact_search) {
	$_SESSION['desktopv2']['concepts']['contact_search'] = '';
}

// texte du champ de recherche
if ($_SESSION['desktopv2']['concepts']['contact_search'] != '') {
	$text_contact_search = $_SESSION['desktopv2']['concepts']['contact_search'];
	$button['class'] = 'searching';
	$button['href'] = '/admin.php?init_contact_search=1';
	$button['onclick'] = '';
}
else {
	$text_contact_search = $_SESSION['cste']['LOOKING_FOR_A_CONTACT_OR_A_COMPANY']. ' ?';
	$button['class'] = '';
	$button['href'] = 'Javascript: void(0);';
	$button['onclick'] = 'Javascript: if($(\'input#editbox_search_contact\').val() != \''.$text_contact_search.'\') $(this).closest(\'form\').submit();';
}

global $lstObj;
global $desktop;
$_SESSION['desktopv2']['concepts']['contacts'] = 0;

// affichage a gauche ou a droite en fonction de la présence du bloc suivis
if (defined('_ACTIVE_GESCOM') && _ACTIVE_GESCOM) {
	$style = 'float: left; clear: left;';
}
else {
	$style = 'float: right;';
}
?>

<div class="bloc_contact" style="<?php echo $style; ?>">
	<div class="title_bloc_contact"><h2>Contacts / Companies</h2></div>
	<div class="bloc_zone_search_contact bloc_zone_search">
		<div class="bloc_searchform_contact">
			<form action="admin.php" method="post" name="formsearch" id="bloc_formsearch_contact">
            	<?
					// Sécurisation du formulaire par token
					require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
					$token = new FormToken\TokenField;
					$token->field("button_search_x"); // Le nom des input de type image sont modifiés par les navigateur en ajoutant _x et _y
					$token->field("button_search_y");
					$token->field("contact_search");
					$tokenHTML = $token->generate();
					echo $tokenHTML;
            	?>
				<span>
					<input type="image" class="button_search" src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/search_gauche.png" name="button_search" style="float:left">
					<input type="text" name="contact_search" class="editbox_search<? if ($button['class'] == 'searching') echo ' working'; ?>" id="editbox_search_contact" maxlength="80" value="<?php echo htmlspecialchars($text_contact_search); ?>" <? if ($button['class'] != 'searching') echo 'onfocus="Javascript:this.value=\'\'; $(this).addClass(\'working\');"'; ?> onblur="Javascript:if (this.value==''){ $(this).removeClass('working');this.value='<?php echo htmlspecialchars(addslashes($text_contact_search)); ?>'; }">
					<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/search_droite.png" style="float:left">

					<a class="<?php echo $button['class']; ?>" href="<?php echo $button['href']; ?>" onclick="<?php echo $button['onclick']; ?>"></a>
				</span>
			</form>
		</div>
	</div>
	<div class="bloc_filtre_contact">
		<table>
			<tbody>
				<tr>
					<td>
						<a href="Javascript: void(0);" onclick="javascript:document.location.href='/admin.php?typerech=0&init_contact_search=1';">
							<span <? if ($_SESSION['desktopv2']['concepts']['rech_type'] == 0) echo 'class="selected"'; ?>>All</span>
						</a>
					</td>
					<td>
						<a href="Javascript: void(0);" onclick="javascript:document.location.href='/admin.php?typerech=<? echo dims_const::_SYSTEM_OBJECT_TIERS; ?>';">
							<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/company_picto.png" />
							<span <? if ($_SESSION['desktopv2']['concepts']['rech_type'] == dims_const::_SYSTEM_OBJECT_TIERS) echo 'class="selected"'; ?>>Companies</span>
						</a>
					</td>
					<td>
						<a href="Javascript: void(0);" onclick="javascript:document.location.href='/admin.php?typerech=<? echo dims_const::_SYSTEM_OBJECT_CONTACT; ?>';">
							<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/human_picto.png" />
							<span <? if ($_SESSION['desktopv2']['concepts']['rech_type'] == dims_const::_SYSTEM_OBJECT_CONTACT) echo 'class="selected"'; ?>>Contacts</span>
						</a>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div id="cadre_bloc_contact" class="cadre_bloc_contact">
		<?php

		$_SESSION['desktopv2']['concepts']['exportcontacts']=array();
		$_SESSION['desktopv2']['concepts']['exporttiers']=array();

		if (isset($lstObj['ct'])) {
			$desktop->loadCompaniesFromContacts($lstObj['ct'],'_DIMS_LABEL_EMPLOYEUR',$lstObj['ctlistid']);
			// chargement du lot en memoire
			$contact= new contact();
			$contact->init_description();

			$arraylstctid=array();

			foreach($lstObj['ct'] as $ct){
				$contact->fields=$ct;

				if (isset($ct['links'][$_SESSION['desktopv2']['concepts']['sel_id']])) {
					$contact->fields['idlink']= $ct['links'][$_SESSION['desktopv2']['concepts']['sel_id']]['idlink'];
					$contact->fields['date_fin']=$ct['links'][$_SESSION['desktopv2']['concepts']['sel_id']]['date_fin'];
					$contact->fields['type_lien']=$ct['links'][$_SESSION['desktopv2']['concepts']['sel_id']]['type_lien'];
				}

				if(!isset($_SESSION['desktopv2']['concepts']['sel_type']) ||
					$_SESSION['desktopv2']['concepts']['sel_type']!=dims_const::_SYSTEM_OBJECT_CONTACT ||
					$_SESSION['desktopv2']['concepts']['sel_id']!=$ct['id']) {
				$contact->setLightAttribute('concept_not_event', $this->getLightAttribute('concept_not_event'));
				$contact->display(_DESKTOP_TPL_LOCAL_PATH.'/concepts/bloc_contact/contacts.tpl.php');
				$arraylstctid[]=$ct['id'];
			}
			}
			$_SESSION['desktopv2']['concepts']['exportcontacts']=$arraylstctid;
		}

		if (isset($lstObj['tiers'])) {

			if (isset($_SESSION['desktopv2']['concepts']['sel_type']) &&
				$_SESSION['desktopv2']['concepts']['sel_type']==dims_const::_SYSTEM_OBJECT_CONTACT)
				$desktop->loadLinksFromtiers($lstObj['tiers'],$lstObj['tierslistid'],$_SESSION['desktopv2']['concepts']['sel_id']);
			//$desktop->loadCompaniesFromContacts($lstObj['tiers'],'_DIMS_LABEL_EMPLOYEUR');
			// chargement du lot en memoire
			$tiers = new tiers();
			$tiers->init_description();

			$arraylsttiersid=array();

			foreach($lstObj['tiers'] as $t){
				$tiers->fields=$t;

				if (isset($t['links'][$_SESSION['desktopv2']['concepts']['sel_id']])) {
					$tiers->fields['idlink']= $t['links'][$_SESSION['desktopv2']['concepts']['sel_id']]['idlink'];
					$tiers->fields['date_fin']=$t['links'][$_SESSION['desktopv2']['concepts']['sel_id']]['date_fin'];
					$tiers->fields['type_lien']=$t['links'][$_SESSION['desktopv2']['concepts']['sel_id']]['type_lien'];
				}

				//if ($tiers->fields['inactif']== tiers::TIERS_ACTIF) {
				if(!isset($_SESSION['desktopv2']['concepts']['sel_type']) ||
					$_SESSION['desktopv2']['concepts']['sel_type']!=dims_const::_SYSTEM_OBJECT_TIERS ||
					$_SESSION['desktopv2']['concepts']['sel_id']!=$t['id']) {
				$tiers->setLightAttribute('concept_not_event', $this->getLightAttribute('concept_not_event'));
				$tiers->display(_DESKTOP_TPL_LOCAL_PATH.'/concepts/bloc_contact/tierss.tpl.php');
					//filtre sur actif ou non
				$arraylsttiersid[]=$t['id'];
			}
			}
            $_SESSION['desktopv2']['concepts']['exporttiers']=$arraylsttiersid;
		}
		?>
	</div>
	<div class="cadre_bloc_contact_bas">
		<table class="bloc_contact_bas">
			<tbody>
				<tr>
					<td>
						<div class="selection_actions" style="margin-left:5px;">
							<img style="height:16px;" src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/arrow_ltr.png" alt="<?php echo $_SESSION['cste']['_FOR_SELECTED_ITEM']; ?>" />
							<a href="Javascript: void(0);" onclick="Javascript: if($('.bloc_contact .selection_form input:checked').length > 0) selectionFormToSelection($('.bloc_contact .selection_form input:checked'));">
								<span><?php echo $_SESSION['cste']['_ADD_TO_THE_SELECTION']; ?></span>
							</a>
						</div>
					</td>
                                        <td style="color:#df1d31">
						<span style="margin-left:25px;"><?php echo pluralize('relationship', $_SESSION['desktopv2']['concepts']['contacts']); ?></span>
					</td>

					<?php
/*
					<td class="add_contact">
						<a href="Javascript: void(0);" onclick="javascript:popupChooseTypeContact(<?php echo dims_const_desktopv2::DESKTOP_V2_CONCEPTS; ?>, <?php echo $this->getId(); ?>, <?php echo $this->id_globalobject; ?>);">
							<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/plus_newtype.png" />
							<span><? echo $_SESSION['cste']['_DIMS_LABEL_ADDLINK']; ?></span>
						</a>
					</td>
*/
					?>
					<td class="add_contact">
						<a href="/admin.php?dims_op=desktopv2&action=exportContactsFromObject">
							<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/export_excel.png" />
							<span><? echo $_SESSION['cste']['EXPORT_CONTACTS_DATA']; ?></span>
						</a>
					</td>
				</tr>

			</tbody>
		</table>
	</div>
</div>

<script type="text/javascript">
function selectionFormToSelection(elemsSet) {
	var elemArray = elemsSet.map(function(i,n) {
		return $(n).val();
	}).get();

	chooseCategSelection(elemArray);
}
</script>
