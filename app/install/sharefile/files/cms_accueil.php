<div style="width:100%">
	<table style="width:100%;">
		<tr>
			<td style="width:5%"><img src="/modules/sharefile/img/send.png"></td>
			<td style="width:30%"><a href="<? echo dims_urlencode("index.php?op=add_share&reset=1"); ?>">Nouveau partage</a></td>
			<td style="width:5%"><img src="/modules/sharefile/img/contacts.png"></td>
			<td style="width:30%"><a href="<? echo dims_urlencode("index.php?op=manage_contact"); ?>">Gestion de vos contacts</a></td>
		<?
		if (dims_isadmin()) {
			// acces en admin sur le serveur d'application
			echo '<td style="width:5%"><img src="/modules/sharefile/img/properties.png"></td>';
			echo '<td style="width:25%"><a href="'.dims_urlencode("index.php?op=admin_sharefile").'">Administration</a></td>';

		}

		?>
		</tr>
	</table>
</div>
