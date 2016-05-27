<?php
global $desktop;
$dims = dims::getInstance();
$lstBusiness = $this->getMyTags();

$mode = dims_load_securvalue('mode', dims_const::_DIMS_CHAR_INPUT, true, true);

$_SESSION['desktopv2']['concepts']['tag_search'] = dims_load_securvalue('tag_search',dims_const::_DIMS_CHAR_INPUT,true,true,true,$_SESSION['desktopv2']['concepts']['tag_search']);

// initialisation des filtres
$init_tag_search = dims_load_securvalue('init_tag_search', dims_const::_DIMS_NUM_INPUT, true, true);
if ($init_tag_search) {
	$_SESSION['desktopv2']['concepts']['tag_search'] = '';
}

// texte du champ de recherche
if ($_SESSION['desktopv2']['concepts']['tag_search'] != '') {
	$text_tag_search = $_SESSION['desktopv2']['concepts']['tag_search'];
	$button['class'] = 'searching';
	$button['href'] = '/admin.php?init_tag_search=1';
	$button['onclick'] = '';
}
else {
	$text_tag_search = $_SESSION['cste']['LOOKING_FOR_A_BUSINESS_TAG']. ' ?';
	$button['class'] = '';
	$button['href'] = 'Javascript: void(0);';
	$button['onclick'] = 'Javascript: if($(\'input#editbox_search_tag\').val() != \''.$text_tag_search.'\') $(this).closest(\'form\').submit();';
}

$id_fiche = $this->getId();

$type_fiche=0;
switch(get_class($this)){
	case 'tiers':
		$type_fiche = dims_const::_SYSTEM_OBJECT_TIERS;
		break;
	case 'contact' :
		$type_fiche = dims_const::_SYSTEM_OBJECT_CONTACT;
		break;
}

?>

<div class="title_concept_droite">
	<h2 class="title_period_activity">
		<?php echo $_SESSION['cste']['BUSINESS_TAGS_OF']; ?> <? echo dims_strcut(($_SESSION['desktopv2']['concepts']['sel_type'] == dims_const::_SYSTEM_OBJECT_CONTACT) ? substr($this->fields['firstname'],0,1).". ".$this->fields['lastname'] : (isset($this->title) ? $this->title : ""),25); ?>
    </h2>
</div>
<?php
if($mode == 'edit_tags') {
	$_SESSION['tags']['search'] = trim(dims_load_securvalue('label',dims_const::_DIMS_CHAR_INPUT,true,false,true,$_SESSION['tags']['search'],null,true));
	?>
	<div class="business_tag_modify">
		<a href="<?php echo $dims->getScriptEnv(); ?>">
			<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/icon_back.png" />
			<span><? echo $_SESSION['cste']['_DIMS_BACK']; ?></span>
		</a>
	</div>
	<div class="searchform_tags">
		<form action="#" method="post" name="formsearch" id="formsearch" onsubmit="Javascript: return false;">
			<?
				// Sécurisation du formulaire par token
				require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
				$token = new FormToken\TokenField;
				$token->field("button_search_x"); // Le nom des input de type image sont modifiés par les navigateur en ajoutant _x et _y
				$token->field("button_search_y");
				$token->field("tag_search");
				$tokenHTML = $token->generate();
				echo $tokenHTML;
			?>
			<span>
				<input type="image" class="button_search_tags" src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/search_gauche_petit.png" name="button_search" style="float:left">
				<!-- <input type="text" name="tag_search" id="editbox_search_tags" class="editbox_search_tags" maxlength="80" value="Looking for a business tag ?" onfocus="Javascript:this.value='';" onblur="Javascript:if (this.value=='')this.value='Looking for a business ?';"> -->
				<input type="text" name="tag_search" id="editbox_search_tags" class="editbox_search_tags<? if ($button['class'] == 'searching') echo ' working'; ?>" maxlength="80" value="<?php echo $text_tag_search; ?>" <? if ($button['class'] != 'searching') echo 'onfocus="Javascript:if(this.value==\''.$text_tag_search.'\') { this.value=\'\'; $(this).addClass(\'working\'); }"'; ?> onblur="Javascript:if (this.value==''){ $(this).removeClass('working'); this.value='<?php echo $text_tag_search; ?>'; }" onkeyup="javascript:searchTagsConcept(this.value, <?php echo $id_fiche;?>, <?php echo $type_fiche;?>);">
				<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/search_droite_petit.png" style="float:left">
			</span>
		</form>
	</div>
	<div id="zone_tags" class="searchform_tags_result">
	</div>
	<?php
}
else {
	?>
	<div class="business_tag_modify">
		<a href="<?php echo $dims->getScriptEnv(); ?>?mode=edit_tags">
			<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/modify.png" />
			<span><? echo $_SESSION['cste']['_MODIFY']; ?></span>
		</a>
	</div>
	<?php
}
?>
<div class="context_tag">
	<?php
	foreach($lstBusiness as $area) {
		//$area->display(_DESKTOP_TPL_LOCAL_PATH.'/concepts/context_tags/context_tags.tpl.php');
		$area->setLightAttribute('from_concept', true);
		$area->setLightAttribute('id_fiche',$id_fiche);
		$area->setLightAttribute('type_fiche',$type_fiche);
		if($mode == 'edit_tags') $area->setLightAttribute('detach_button', true);
		$area->display(_DESKTOP_TPL_LOCAL_PATH.'/tag/tag_elem.tpl.php');
	}
	?>
</div>
