<?php
if (isset($_SESSION['desktopv2']['adress_book']['sel_type']) && isset($_SESSION['desktopv2']['adress_book']['sel_id'])
	&& $_SESSION['desktopv2']['adress_book']['sel_id'] != '' && $_SESSION['desktopv2']['adress_book']['sel_id'] > 0
	&& ($_SESSION['desktopv2']['adress_book']['sel_type'] == dims_const::_SYSTEM_OBJECT_CONTACT || $_SESSION['desktopv2']['adress_book']['sel_type'] == dims_const::_SYSTEM_OBJECT_TIERS)){

	$focus = $edit = dims::getInstance()->getScriptEnv();
	$confirm = '';
	switch($_SESSION['desktopv2']['adress_book']['sel_type']){
		case dims_const::_SYSTEM_OBJECT_CONTACT :
			$confirm = 'Êtes-vous sûr de vouloir détacher ce contact ?\nCela supprimera tous les liens avec celui-ci !';
			$focus .= "?submenu=1&mode=contact&action=show&id=".$_SESSION['desktopv2']['adress_book']['sel_id'];
			$edit .= "?submenu=1&mode=contact&action=edit&id=".$_SESSION['desktopv2']['adress_book']['sel_id'];
			//$focus .= '?dims_mainmenu=9&cat=0&action='._BUSINESS_TAB_CONTACT_FORM.'&part='._BUSINESS_TAB_CONTACT_IDENTITE.'&contact_id='.$_SESSION['desktopv2']['adress_book']['sel_id'];
			break;
		case dims_const::_SYSTEM_OBJECT_TIERS :
			$confirm = 'Êtes-vous sûr de vouloir détacher cette entreprise ?\nCela supprimera tous les liens avec celle-ci !';
			$focus .= "?submenu=1&mode=company&action=show&id=".$_SESSION['desktopv2']['adress_book']['sel_id'];
			$edit .= "?submenu=1&mode=company&action=edit&id=".$_SESSION['desktopv2']['adress_book']['sel_id'];
			//$focus .= '?dims_mainmenu=9&cat=0&action='._BUSINESS_TAB_ENT_FORM.'&part='._BUSINESS_TAB_ENT_IDENTITE.'&id_ent='.$_SESSION['desktopv2']['adress_book']['sel_id'];
			break;
	}
?>
<div class="filter_colonne_3_bas">
	<span style="float:left;">
		<a href="Javascript: void(0);" onclick="javascript: document.location.href='<? echo $edit; ?>';">
			<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/modify.png" border="0" />
			<span><? echo $_SESSION['cste']['_MODIFY']; ?></span>
		</a>
	</span>
	<span style="float:left;">
		<a href="javascript:void(0);" onclick="javascript: detachContactAB(<? echo $_SESSION['desktopv2']['adress_book']['sel_id']; ?>,<? echo $_SESSION['desktopv2']['adress_book']['sel_type']; ?>,'<? echo $confirm; ?>');">
			<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/detach.png" border="0" />
			<span><? echo $_SESSION['cste']['_DIMS_LABEL_DETACH']; ?></span>
		</a>
	</span>
</div>
<div class="new_contact_colonne_3_bas">
	<a href="Javascript: void(0);" onclick="javascript: document.location.href='<? echo $focus; ?>';">
		<span><?php echo $_SESSION['cste']['_DIMS_FOCUS_ON_ACTIVITY']; ?></span>
		<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/focus_on_activity16.png" border="0" />
	</a>
</div>
<?
}
?>
