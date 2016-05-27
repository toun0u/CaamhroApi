<div class="title_groups">
		<h2><? echo $_SESSION['cste']['LAST_LINKED_CONTACTS']; ?></h2>
	</div>
	<table cellspacing="0" cellpadding="0">
		<tbody>
			<tr <? if ($_SESSION['desktopv2']['adress_book']['group'] == _DESKTOP_V2_ADDRESS_BOOK_LAST_LINKED) echo 'class="ligne1_sel"'; else echo 'class="ligne1" onclick="javascipt:document.location.href=\'/admin.php?group='._DESKTOP_V2_ADDRESS_BOOK_LAST_LINKED.'\';"'; ?>>
				<td class="icon_contacts_AB">
					<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/groupe20.png" border="0">
				</td>
				<td class="text_nav_AB">
					<span><? echo $_SESSION['cste']['LAST_LINKED_CONTACTS']; ?> (<? echo count($lastLinkedContacts); ?>)</span>
				</td>
				<td class="icon_nav_AB">
					<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/<? if ($_SESSION['desktopv2']['adress_book']['group'] == _DESKTOP_V2_ADDRESS_BOOK_LAST_LINKED) echo 'selected_'; ?>item_fleche.png" border="0">
				</td>
			</tr>
			<tr <? if ($_SESSION['desktopv2']['adress_book']['group'] == _DESKTOP_V2_ADDRESS_BOOK_FAVORITES) echo 'class="ligne1_sel"'; else echo 'class="ligne1" onclick="javascipt:document.location.href=\'/admin.php?group='._DESKTOP_V2_ADDRESS_BOOK_FAVORITES.'\';"'; ?>>
				<td class="icon_contacts_AB">
					<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/groupe20.png" border="0">
				</td>
				<td class="text_nav_AB">
					<span><? echo $_SESSION['cste']['_FAVORITES']; ?> (<? echo count($favoriteContacts); ?>)</span>
				</td>
				<td class="icon_nav_AB">
					<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/<? if ($_SESSION['desktopv2']['adress_book']['group'] == _DESKTOP_V2_ADDRESS_BOOK_FAVORITES) echo 'selected_'; ?>item_fleche.png" border="0">
				</td>
			</tr>
			<tr <? if ($_SESSION['desktopv2']['adress_book']['group'] == _DESKTOP_V2_ADDRESS_BOOK_MONITORED) echo 'class="ligne1_sel"'; else echo 'class="ligne1" onclick="javascipt:document.location.href=\'/admin.php?group='._DESKTOP_V2_ADDRESS_BOOK_MONITORED.'\';"'; ?>>
				<td class="icon_contacts_AB">
					<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/groupe20.png" border="0">
				</td>
				<td class="text_nav_AB">
					<span><? echo $_SESSION['cste']['MONITORED_CONTACTS']; ?> <font style="color:#df1d31;">(<? echo count($monitoredContacts); ?> new)</font></span>
				</td>
				<td class="icon_nav_AB">
					<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/<? if ($_SESSION['desktopv2']['adress_book']['group'] == _DESKTOP_V2_ADDRESS_BOOK_MONITORED) echo 'selected_'; ?>item_fleche.png" border="0">
				</td>
			</tr>
		</tbody>
	</table>
</div>
