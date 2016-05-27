<?php
$_SESSION['desktopv2']['address_book']['ab_search'] = dims_load_securvalue('ab_search',dims_const::_DIMS_CHAR_INPUT,true,true,true,$_SESSION['desktopv2']['address_book']['ab_search']);

// initialisation des filtres
$init_ab_search = dims_load_securvalue('init_ab_search', dims_const::_DIMS_NUM_INPUT, true, true);
if ($init_ab_search) {
	$_SESSION['desktopv2']['address_book']['ab_search'] = '';
}

// texte du champ de recherche
if ($_SESSION['desktopv2']['address_book']['ab_search'] != '') {
	$text_ab_search = $_SESSION['desktopv2']['address_book']['ab_search'];
	$button['class'] = 'progressive close';
	$button['href'] = '/admin.php?init_ab_search=1';
	$button['onclick'] = '';
}
else {
	$text_ab_search = $_SESSION['cste']['LOOKING_FOR_A_CONTACT_OR_A_COMPANY']. ' ?';
	$button['class'] = 'progressive valid';
	$button['href'] = 'Javascript: void(0);';
	$button['onclick'] = 'Javascript: if($(\'input#editbox_search_contact\').val() != \''.$text_ab_search.'\') $(this).closest(\'form\').submit();';
}
?>

<div id="fiche_contact_search">
	<form action="admin.php" method="post" name="formsearch" id="bloc_formsearch_contact">
		<?
			// Sécurisation du formulaire par token
			require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
			$token = new FormToken\TokenField;
			$token->field("ab_search");
			$tokenHTML = $token->generate();
			echo $tokenHTML;
		?>
		<span>
			<input type="image" class="button_search" src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/search_gauche.png" name="button_search" style="float:left">
			<input style="width: 180px;" type="text" name="ab_search" class="editbox_search<? if ($button['onclick'] == '') echo ' working'; ?>" id="editbox_search_contact" maxlength="80" value="<?php echo htmlspecialchars($text_ab_search); ?>" <?php if ($button['onclick'] != '') echo htmlspecialchars('onfocus="Javascript:this.value=\'\'; $(this).addClass(\'working\');"'); ?> onblur="Javascript:if (this.value==''){ $(this).removeClass('working');this.value='<?php addslashes($text_ab_search); ?>'; }">
			<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/search_droite.png" style="float:left" />

			<a style="margin-top:6px;" class="<?php echo $button['class']; ?>" href="<?php echo $button['href']; ?>" onclick="<?php echo $button['onclick']; ?>"></a>
		</span>
	</form>
</div>

<div class="fiche_contact_container">
	<div class="fiche_contact_AB">
	    <?
		$lstContacts = array();
		switch($_SESSION['desktopv2']['adress_book']['group']){
			case _DESKTOP_V2_ADDRESS_BOOK_ALL_CONTACT :
				$lstContacts = $allContacts;
				break;
			case _DESKTOP_V2_ADDRESS_BOOK_LAST_LINKED :
				$lstContacts = $lastLinkedContacts;
				break;
			case _DESKTOP_V2_ADDRESS_BOOK_FAVORITES :
				$lstContacts = $favoriteContacts;
				break;
			case _DESKTOP_V2_ADDRESS_BOOK_MONITORED :
				$lstContacts = $monitoredContacts;
				break;
			default:
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
				$lstContacts = $lstGroups[$_SESSION['desktopv2']['adress_book']['group']]->contacts;
		}
		$lst = array();
		foreach($lstContacts as $contact){
			switch(get_class($contact)){
				case 'tiers':
					if ( $_SESSION['desktopv2']['address_book']['ab_search'] == '' || stristr($contact->fields['intitule'], $_SESSION['desktopv2']['address_book']['ab_search']) ) {
						$lst['tiers'][$contact->fields['id']] = $contact->fields['id'];
						$contact->display(_DESKTOP_TPL_LOCAL_PATH.'/address_book/fiche_address_book_list_tiers.tpl.php');
					}
					break;
				case 'contact':
					$employeur = current($contact->getCompaniesLinkedByType('_DIMS_LABEL_EMPLOYEUR'));
					if ( $_SESSION['desktopv2']['address_book']['ab_search'] == '' || stristr($contact->fields['firstname'].' '.$contact->fields['lastname'].' '.$employeur['intitule'], $_SESSION['desktopv2']['address_book']['ab_search']) ) {
						$contact->setLightAttribute('employeur', $employeur);
						$lst['contact'][$contact->fields['id']] = $contact->fields['id'];
						$contact->display(_DESKTOP_TPL_LOCAL_PATH.'/address_book/fiche_address_book_list_contact.tpl.php');
					}
					break;
			}
		}
		if (isset($_SESSION['desktopv2']['adress_book']['sel_type']) && isset($_SESSION['desktopv2']['adress_book']['sel_id'])){
			switch($_SESSION['desktopv2']['adress_book']['sel_type']){
				case dims_const::_SYSTEM_OBJECT_CONTACT :
					if (!isset($lst['contact'][$_SESSION['desktopv2']['adress_book']['sel_id']])){
						unset($_SESSION['desktopv2']['adress_book']['sel_type']);
						unset($_SESSION['desktopv2']['adress_book']['sel_id']);
					}
					break;
				case dims_const::_SYSTEM_OBJECT_TIERS :
					if (!isset($lst['tiers'][$_SESSION['desktopv2']['adress_book']['sel_id']])){
						unset($_SESSION['desktopv2']['adress_book']['sel_type']);
						unset($_SESSION['desktopv2']['adress_book']['sel_id']);
					}
					break;
			}
		}
		?>
	</div>
</div>
