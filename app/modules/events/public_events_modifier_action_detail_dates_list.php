<table>
	<tr>
		<td><?php echo $_DIMS['cste']['_DIMS_DATE']; ?></td>
		<td><?php echo $_DIMS['cste']['_DIMS_LABEL_REGISTRATION_S']; ?></td>
		<td><?php echo $_DIMS['cste']['_DIMS_LABEL_CLOSE_INSCRIPTION']; ?></td>
		<td><?php echo $_DIMS['cste']['_DIMS_LABEL_VALIDATE_REGISTRATION']; ?></td>

		<td><?php echo $_DIMS['cste']['_DELETE']; ?></td>
	</tr>
	<?
	// construction de la liste des dates disponibles
	$listaction=$action->getExtendedActions();
	foreach ($listaction as $id_act =>$actelem) {
		$datej=business_datefr2us($action->fields['datejour']);
		echo "<tr><td>".$datej."</td><td></td><td>";
	}
	?>
</table>
