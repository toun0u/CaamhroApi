<?php
$function = '';
$lstCompanies = $this->getCompaniesLinkedByType('_DIMS_LABEL_EMPLOYEUR');
foreach ($lstCompanies as $company) {
	if ($company['function'] != '') {
		$function = $company['function'];
		break;
	}
}
?>

<div class="desc_picture_mini">
	<?
	if ($this->getPhotoWebPath(60) != '' && file_exists($this->getPhotoPath(60)))
		echo '<img class="conc_img_ct" src="'.$this->getPhotoWebPath(60).'" border="0" />';
	else
		echo '<img class="conc_img_ct" src="'._DESKTOP_TPL_PATH.'/gfx/common/contact_default_search.png" border="0" />';
	?>
</div>
<div class="desc_content">
	<div class="desc_content_title">
		<h1><? echo $this->fields['civilite']." ".$this->fields['firstname']." ".$this->fields['lastname']; ?></h1>
	</div>
	<?php
	if ($function != '') {
		?>
		<div class="desc_content_function">
			<h2>
				<font style="font-weight: normal; color: #656565;">
					<? echo $_SESSION['cste']['_DIMS_LABEL_FUNCTION']; ?> :&nbsp;
				</font>
				<?php echo $function; ?>
			</h2>
		</div>
	<?php
	}
	?>
	<div class="desc_content_link">
		<div>
			<a href="<?php echo dims::getInstance()->getScriptEnv(); ?>?mode=edit">
				<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/modify.png" />
				<span><? echo $_SESSION['cste']['_MODIFY']; ?></span>
			</a>
		</div>
		<div>
			<a href="<?php echo dims::getInstance()->getScriptEnv(); ?>?submenu=1&mode=contact&action=edit&id=<?= $this->get('id'); ?>">
				<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/crayon16.png" />
				<span><? echo $_SESSION['cste']['_DIMS_LABEL_ADDRESS']; ?></span>
			</a>
		</div>
		<?php
		if ($this->fields['id'] != $_SESSION['dims']['user']['id_contact']) {
			require_once DIMS_APP_PATH.'modules/system/class_ct_link.php';
			$isLinked = ctlink::isLinked($this->fields['id'], dims_const::_SYSTEM_OBJECT_CONTACT);

			if ($isLinked) {
				?>
				<div>
					<a href="javascript:void(0);" onclick="javascript:dims_confirmlink('/admin.php?op=desktopv2&action=remove_from_ab&id_go=<?php echo $this->fields['id_globalobject']; ?>&type=<?php echo dims_const::_SYSTEM_OBJECT_CONTACT; ?>', '<?php echo addslashes($_SESSION['cste']['CONFIRM_REMOVE_FROM_ADDRESS_BOOK']); ?>');" title="<?php echo $_SESSION['cste']['REMOVE_FROM_MY_ADDRESS_BOOK']; ?>">
						<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/detach.png" />
						<span><?php echo $_SESSION['cste']['REMOVE_FROM_MY_ADDRESS_BOOK']; ?></span>
					</a>
				</div>
				<?php
			}
			else {
				// si il y a des groupes dans le carnet d'adresses,
				// on ouvre un popup pour les sélectionner
				// if ($_SESSION['desktopv2']['ab_groups']) {
				//	   $onclick='displayContactsGroups(event,'.$this->fields['id_globalobject'].','.dims_const::_SYSTEM_OBJECT_CONTACT.')';
				// }
				// sinon, on ajoute simplement le contact
				// else {
					$onclick = 'document.location.href=\'/admin.php?op=desktopv2&action=add_to_ab&id_go='.$this->fields['id_globalobject'].'&type='.dims_const::_SYSTEM_OBJECT_CONTACT.'\';';
				// }
				?>
				<div>
					<a href="javascript:void(0);" onclick="javascript:<?php echo $onclick; ?>" title="<?php echo $_SESSION['cste']['ADD_TO_MY_ADDRESS_BOOK']; ?>">
						<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/attach.png" />
						<span><?php echo $_SESSION['cste']['ADD_TO_MY_ADDRESS_BOOK']; ?></span>
					</a>
				</div>
				<?php
			}
		}
		?>
		<div>
			<a href="Javascript:void(0);" onclick="javascript:exportVcard(<? echo $this->fields['id']; ?>,<? echo dims_const::_SYSTEM_OBJECT_CONTACT; ?>);" title="<?php echo $_SESSION['cste']['EXPORT_VCARD']; ?>">
				<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/export_vcard.png" />
				<span><?php echo $_SESSION['cste']['EXPORT_VCARD']; ?></span>
			</a>
		</div>
		<div>
			<a href="Javascript:void(0);" onclick="javascript:sendVcard(<? echo $this->fields['id']; ?>,<? echo dims_const::_SYSTEM_OBJECT_CONTACT; ?>);" title="<?php echo $_SESSION['cste']['_INET_SEND_VCARD_BY_EMAIL']; ?>">
				<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/export_vcard.png" />
				<span><?php echo $_SESSION['cste']['_INET_SEND_VCARD_BY_EMAIL']; ?></span>
			</a>
		</div>
		<div>
			<a href="Javascript:void(0);" onclick="javascript:chooseCategSelection(<?php echo $this->fields['id_globalobject']; ?>);" title="<?php echo $_SESSION['cste']['_ADD_TO_THE_SELECTION']; ?>">
				<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/icon_add.png" />
				<span><?php echo $_SESSION['cste']['_ADD_TO_THE_SELECTION']; ?></span>
			</a>
		</div>
		<div>
		<?php
		if(!$this->hasAccount()){
			?>
			<a href="javascript:void(0);" onclick="javascript:if(confirm('<?php echo addslashes($_SESSION['cste']['_DIRECTORY_CONFIRM_DELETECONTACT']);?>')) document.location.href = '?dims_op=desktopv2&action=delete_concept&type=contact&go=<?php echo $this->fields['id_globalobject']; ?>&from=concept&desktop=1';">
				<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/close.png" border="0" />
				<span><?php echo $_SESSION['cste']['DELETE_CONTACT']; ?></span>
			</a>
			<?php
		}
		else{
			?>
			<span class="is_dims_user"><?php echo $_SESSION['cste']['CONTACT_IS_DIMS_USER']; ?></span>
			<?php
		}
		?>
		</div>
	</div>
</div>
<?php
if(count($lstCompanies)) {
	?>
	<div class="desc_member">
		<span><? echo $_SESSION['cste']['MEMBER_OF']; ?> (<? echo count($lstCompanies); ?>) :</span>
	</div>
	<?php
}
?>
<div class="desc_member_content">
	<? if (count($lstCompanies) > 2) { ?>
	<table cellspacing="0" cellpadding="3" class="fleche_defilement" style="display:none;">
		<tbody>
			<tr>
				<td class="fleche_defilement">
					<img onclick="javascript:switchLstCompanie('up');" src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/img_fleche_defilement_haut.png" />
				</td>
			</tr>
		</tbody>
	</table>
	<?
	}
	$nbComp = 0;
	foreach ($lstCompanies as $comp){
		$tiers = new tiers();
		$nbComp ++;
		if ($tiers->open($comp['id'])){
			if ($nbComp <= 2)
				echo '<div style="display:block;" class="display_companie">';
			else
				echo '<div style="display:none;" class="display_companie">';
			$tiers->display(_DESKTOP_TPL_LOCAL_PATH.'/concepts/description/contact/bloc_companies_link.tpl.php');
			echo '</div>';
		}
	}
	if (count($lstCompanies) > 2) { ?>
	<table cellspacing="0" cellpadding="3" class="fleche_defilement">
		<tbody>
			<tr>
				<td class="fleche_defilement">
					<img onclick="javascript:switchLstCompanie('down');" src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/img_fleche_defilement_bas.png" />
				</td>
			</tr>
		</tbody>
	</table>
	<? } ?>
	<table cellspacing="0" cellpadding="3" class="link_member">
		<tbody>
			<tr>
				<td class="link_member">
					<?php echo $_SESSION['cste']['HOW_TO_BE_INTRODUCED_TO_HIM']; ?> ?
					<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/activity_red_picto.png" />
				</td>
			</tr>
			<tr>
				<td class="link_member">
					<?php echo $_SESSION['cste']['DON_T_FOLLOW_HIS_ACTIVITY_ANYMORE']; ?>
					<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/monitored_cible.png" />
				</td>
			</tr>
		</tbody>
	</table>
</div>
<div class="desc_member_content">
	<?

	$contentvcard=$this->getVcard();
	require DIMS_APP_PATH . '/lib/qrcode/phpqrcode/qrlib.php';
	$imageFile=realpath('.')."/data/users/qrcodevcard-".$this->fields['id'].".png";
	//die($imageFile);
	//QRcode::png(implode("\n", $contentvcard), $imageFile, QR_ECLEVEL_L, 10);
	?>
</div>
<script type="text/javascript">
	function switchLstCompanie(sens){
		if (sens == 'down'){
			$("div.desc_member_content div.display_companie").not(":hidden").first().hide();
			$("div.desc_member_content div.display_companie").not(":hidden").last().next("div.display_companie").first().show();
		}else if(sens == 'up'){
			$("div.desc_member_content div.display_companie").not(":hidden").last().hide();
			$("div.desc_member_content div.display_companie").not(":hidden").first().prev("div.display_companie").first().show();
		}

		if ($("div.desc_member_content div.display_companie:first").is(":hidden"))
			$("div.desc_member_content table.fleche_defilement:first").show();
		else
			$("div.desc_member_content table.fleche_defilement:first").hide();

		if ($("div.desc_member_content div.display_companie:last").is(":hidden"))
			$("div.desc_member_content table.fleche_defilement:last").show();
		else
			$("div.desc_member_content table.fleche_defilement:last").hide();
	}
</script>
