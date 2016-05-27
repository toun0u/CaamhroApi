<?php
$contacts = $this->get('contacts');
?>
<table width="100%">
	<tr>
		<td colspan="4" align="righ\">
		</td>
	</tr>
	<tr style="background-color:#EDEDED;">
		<td>Nom</td>
		<td>Prénom</td>
		<td>Email</td>
		<td>Actions</td>
	</tr>
	<?php
	if(empty($contacts)) {
		?>
		<tr style="background-color: #B0DB78;">
			<td colspan="4" style="text-align:center;color:white;font-weight:bold;">
				Aucun contact
			</td>
		</tr>
		<?php
	}
	else {
		foreach($contacts as $contact) {
			?>
			<tr>
				<td>
					<?= $contact['lastname']; ?>
				</td>
				<td>
					<?= $contact['firstname']; ?>
				</td>
				<td>
					<?= $contact['email']; ?>
				</td>
				<td>
					<a alt="Modifier" href="<?= dims_urlencode($this->get('urlbase')->addParams(array('op' => 'contacts', 'modify' => 'add', 'contact_id' => $contact['id']))); ?>">
						<img title="Modifier" src="./common/img/edit.gif" style="border:0px;">
					</a>
					&nbsp;/&nbsp;
					<a alt="Supprimer" href="Javascript: void(0);" onclick="Javascript: dims_confirmlink('<?= dims_urlencode($this->get('urlbase')->addParams(array('op' => 'contacts', 'action' => 'delete', 'contact_id' => $contact['id']))); ?>','<?= dims_constant::getVal('_DIMS_CONFIRM'); ?>');">
						<img title="Supprimer" src="./common/img/del.png" style="border:0px;">
					</a>
				</td>
			</tr>
			<?php
		}
	}
	?>
</table>
