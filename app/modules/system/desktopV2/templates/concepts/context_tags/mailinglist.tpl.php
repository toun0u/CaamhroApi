<?php
global $desktop;
$dims = dims::getInstance();
$lstNewsletter = $this->getMyNewsletters();

$modenewsletter = dims_load_securvalue('modenewsletter', dims_const::_DIMS_CHAR_INPUT, true, true);


// initialisation des filtres
$init_tag_search = dims_load_securvalue('init_tag_search', dims_const::_DIMS_NUM_INPUT, true, true);
if ($init_tag_search) {
	$_SESSION['desktopv2']['concepts']['tag_search'] = '';
}

// texte du champ de recherche
if (isset($_SESSION['desktopv2']['concepts']['tag_search']) && $_SESSION['desktopv2']['concepts']['tag_search'] != '') {
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
$_SESSION['dims']['currentobject_newsletter']=$id_fiche;
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


<?php
if($modenewsletter == 'edit_newsletter') {
	?>
	<div class="business_tag_modify">
		<a href="<?php echo $dims->getScriptEnv()."?action=show&id=".$id_fiche; ?>">
			<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/icon_back.png" />
			<span><? echo $_SESSION['cste']['_DIMS_BACK']; ?></span>
		</a>
	</div>
	<script type="text/javascript">
        window.onload = function() {
            displayMoreNewsletter();
        }
        </script>
	<div id="zone_newsletters" class="searchform_tags_result">
	</div>
	<?php
}
else {
	?>
	<div class="business_tag_modify">
		<a href="<?php echo $dims->getScriptEnv()."?action=show&id=".$id_fiche."&modenewsletter=edit_newsletter"; ?>">
			<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/modify.png" />
			<span><? echo $_SESSION['cste']['_MODIFY']; ?></span>
		</a>
	</div>
	<?php
}
?>
<div class="context_tag">
	<?php
	foreach($lstNewsletter as $area) {
		$area->setLightAttribute('from_concept', true);
		$area->setLightAttribute('id_fiche',$id_fiche);
		$area->setLightAttribute('type_fiche',$type_fiche);
		if($modenewsletter == 'edit_newsletter') $area->setLightAttribute('detach_button', true);
		$area->display(_DESKTOP_TPL_LOCAL_PATH.'/newsletters/newsletter_elem.tpl.php');
	}
	?>
</div>
