<?
if (!isset($_SESSION['desktopv2']['adress_book']['type']))
	$_SESSION['desktopv2']['adress_book']['type'] = 0;
if (!isset($_SESSION['desktopv2']['adress_book']['group']))
	$_SESSION['desktopv2']['adress_book']['group'] = _DESKTOP_V2_ADDRESS_BOOK_ALL_CONTACT;

$type = dims_load_securvalue('type',dims_const::_DIMS_NUM_INPUT,true,true,true,$_SESSION['desktopv2']['adress_book']['type']);
$_SESSION['desktopv2']['adress_book']['type'] = $type;
$group = dims_load_securvalue('group',dims_const::_DIMS_NUM_INPUT,true,true,true,$_SESSION['desktopv2']['adress_book']['group']);
$_SESSION['desktopv2']['adress_book']['group'] = $group;

$allContacts = $desktop->getAllContacts($_SESSION['desktopv2']['adress_book']['type']);
$lastLinkedContacts = $desktop->getLastLinkedContacts($_SESSION['desktopv2']['adress_book']['type']);
$favoriteContacts = $desktop->getFavoritesContacts($_SESSION['desktopv2']['adress_book']['type']);
$monitoredContacts = $desktop->getMonitoredContacts($_SESSION['desktopv2']['adress_book']['type']);
$lstGroups = $desktop->getGroupsUser();

?>
<div class="address_book">
<div class="zone_title_address_book">
    <div class="title_adress_book"><h1><?php echo $_SESSION['cste']['YOUR_ADDRESS_BOOK']; ?></h1></div>
    <!--div class="title_setpage"><span>Set this page your home page</span></div-->
</div>

<div class="browser_address_book">
    <div class="colonne_1">
		<div id="ab_your_groups">
			<? include _DESKTOP_TPL_LOCAL_PATH."/address_book/address_book_your_group.tpl.php"; ?>
		</div>
		<div id="ab_dynamic_groups">
			<? include _DESKTOP_TPL_LOCAL_PATH."/address_book/address_book_dynamic_group.tpl.php"; ?>
		</div>
	    <div class="colonne_2" id="ab_colonne_2">
	        <?php include _DESKTOP_TPL_LOCAL_PATH.'/address_book/fiche_address_book.tpl.php'; ?>
	    </div>
	    <div class="colonne_3" id="ab_colonne_3">
	        <?php
			if (isset($_SESSION['desktopv2']['adress_book']['sel_type']) && isset($_SESSION['desktopv2']['adress_book']['sel_id']) && $_SESSION['desktopv2']['adress_book']['sel_id'] != '' && $_SESSION['desktopv2']['adress_book']['sel_id'] > 0){
				switch($_SESSION['desktopv2']['adress_book']['sel_type']){
					case dims_const::_SYSTEM_OBJECT_TIERS :
						$ct = new tiers();
						if($ct->open($_SESSION['desktopv2']['adress_book']['sel_id']))
							$ct->display(_DESKTOP_TPL_LOCAL_PATH.'/address_book/fiche_desc_tiers_address_book.tpl.php');
						break;
					case dims_const::_SYSTEM_OBJECT_CONTACT :
						$ct = new contact();
						if($ct->open($_SESSION['desktopv2']['adress_book']['sel_id']))
							$ct->display(_DESKTOP_TPL_LOCAL_PATH.'/address_book/fiche_desc_contact_address_book.tpl.php');
						break;
				}
			}
			?>
	    </div>
	</div>
	<div class="browser_address_book_bas">
	    <div class="colonne_1_bas">
			<?php
			if (isset($_SESSION['desktopv2']['adress_book']['group']) && $_SESSION['desktopv2']['adress_book']['group']> 0) {
				?>
				<div style="float: left;">
					<a href="Javascript: void(0);" onclick="javascript:dims_confirmlink('/admin.php?action=delete_contacts_gr', 'Are you sure you want to delete this group ?');">
						<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/close.png" border="0" />
						<span><? echo $_SESSION['cste']['_DIMS_LABEL_DELETE_GROUP']; ?></span>
					</a>
				</div>
				<?php
			}
			else {
				?>
				<div style="float: left;">
					<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/delete_filter_kw.png" border="0"/>
					<span><? echo $_SESSION['cste']['_DIMS_LABEL_DELETE_GROUP']; ?></span>
				</div>
				<?php
			}
			?>
			<div style="float: right;">
				<a href="Javascript: void(0);" onclick="javascript:addNewContactsGroup(event);">
					<span><? echo $_SESSION['cste']['_DIMS_LABEL_CREATE_GROUP']; ?></span>
					<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/plus_newtype.png" border="0" />
				</a>
			</div>
	    </div>
	    <div class="colonne_2_bas">
	        <div class="filter_colonne_2_bas">
				<span <? if ($_SESSION['desktopv2']['adress_book']['type'] != dims_const::_SYSTEM_OBJECT_TIERS && $_SESSION['desktopv2']['adress_book']['type'] != dims_const::_SYSTEM_OBJECT_CONTACT) echo 'class="selected"'; ?>>
					<a onclick="javascript:document.location.href='/admin.php?type=0';" href="javascript:void(0);">
						<? echo $_SESSION['cste']['_DIMS_ALLS']; ?>
					</a>
				</span>
				<span <? if ($_SESSION['desktopv2']['adress_book']['type'] == dims_const::_SYSTEM_OBJECT_TIERS) echo 'class="selected"'; ?>>
					<img onclick="javascript:document.location.href='/admin.php?type=<? echo dims_const::_SYSTEM_OBJECT_TIERS; ?>';" src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/company_picto.png" title="<?php echo $_SESSION['cste']['_BUSINESS_TIER']; ?>" border="0">
				</span>
				<span <? if ($_SESSION['desktopv2']['adress_book']['type'] == dims_const::_SYSTEM_OBJECT_CONTACT) echo 'class="selected"'; ?>>
					<img onclick="javascript:document.location.href='/admin.php?type=<? echo dims_const::_SYSTEM_OBJECT_CONTACT; ?>';" src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/human_picto.png" title="<?php echo $_SESSION['cste']['_DIMS_LABEL_CONTACTS']; ?>" border="0">
				</span>
			</div>
			<div class="new_contact_colonne_2_bas">
				<a href="Javascript: void(0);" onclick="javascript:$('div.getLinkAdditionalContent').slideToggle('fast',flip_flop($('div.getLinkAdditionalContent'),$(this),'<?php echo _DESKTOP_TPL_PATH; ?>'));">
					<span><? echo $_SESSION['cste']['_IMPORT_TAB_NEW_CONTACT']; ?></span>
					<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/plus_newtype.png" title="<?php echo $_SESSION['cste']['_IMPORT_TAB_NEW_CONTACT']; ?>" border="0" />
				</a>
			</div>
			<div class="new_contact_colonne_2_bas">
				<a href="Javascript: void(0);" onclick="javascript: document.location.href='/admin.php?dims_op=desktopv2&action=ab_export_contacts';">
					<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/export_excel.png" title="<?php echo $_SESSION['cste']['_FORMS_DATA_EXPORT']; ?>" border="0" />
				</a>
			</div>
	    </div>
	    <div class="colonne_3_bas" id="ab_colonne_3_bas">
	        <? include _DESKTOP_TPL_LOCAL_PATH."/address_book/address_book_menu_bas_desc.tpl.php"; ?>
	    </div>
	</div>
</div>

<?php
$currentCt = new contact();
$currentCt->open($_SESSION['dims']['user']['id_contact']);
$currentCt->display(_DESKTOP_TPL_LOCAL_PATH.'/concepts/bloc_contact/link_entity.tpl.php');
?>
</div>
