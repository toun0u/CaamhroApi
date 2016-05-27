<div class="title_groups">
	<h2><? echo $_SESSION['cste']['YOUR_GROUPS_OF_CONTACTS']; ?></h2>
</div>
<table cellspacing="0" cellpadding="0">
	<tbody>
		<tr <? if ($_SESSION['desktopv2']['adress_book']['group'] == _DESKTOP_V2_ADDRESS_BOOK_ALL_CONTACT) echo 'class="ligne1_sel"'; else echo 'class="ligne1" onclick="javascipt:document.location.href=\'/admin.php?group='._DESKTOP_V2_ADDRESS_BOOK_ALL_CONTACT.'\';"'; ?>>
			<td class="icon_contacts_AB">
				<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/groupe20.png" border="0">
			</td>
			<td class="text_nav_AB">
				<span><? echo $_SESSION['cste']['ALL_YOUR_CONTACTS']; ?> (<? echo count($allContacts); ?>)</span>
			</td>
			<td class="icon_nav_AB">
				<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/<? if ($_SESSION['desktopv2']['adress_book']['group'] == _DESKTOP_V2_ADDRESS_BOOK_ALL_CONTACT) echo 'selected_'; ?>item_fleche.png" border="0">
			</td>
		</tr>
		<?
		foreach($lstGroups as $gr)
			$gr->display(_DESKTOP_TPL_LOCAL_PATH.'/address_book/address_book_display_group.tpl.php');
		?>
	</tbody>
</table>
