<?
if (!empty($_POST['idmodule'])) {
    $idmodule = dims_load_securvalue('idmodule', dims_const::_DIMS_NUM_INPUT, false, true,true);
}

echo $skin->open_simplebloc($_DIMS['cste']['_SYSTEM_MODULESELECTED']);
?>
<form name="form_modparam" action="<? echo $scriptenv; ?>" method="post">
<?
	// Sécurisation du formulaire par token
	require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
	$token = new FormToken\TokenField;
	$token->field("idmodule");
	$tokenHTML = $token->generate();
	echo $tokenHTML;
?>
<TABLE CELLPADDING="2" CELLSPACING="1">
<TR>
	<TD>
	<SELECT class="select" NAME="idmodule" OnChange="form_modparam.submit()">
	<?
	foreach($_SESSION['dims']['modules'] as $idm => $mod) {
		if (empty($idmodule)) $idmodule = $idm;
		?>
                    <option <? if ($idmodule == $idm) echo 'selected'; ?> value="<? echo $idm; ?>"><? echo "{$mod['label']} ({$mod['moduletype']})"; ?></option>
		<?
	}
	?>
	</SELECT>
	</TD>
</TR>
</TABLE>
</form>
<?
echo $skin->close_simplebloc();

if (isset($idmodule) && $idmodule>0) {
	echo $skin->open_simplebloc($_DIMS['cste']['_SYSTEM_MODULEPARAM'],'100%');
	?>
	<TABLE CELLPADDING="2" CELLSPACING="1">
	<?
	$param_module->open($idmodule);

	if (isset($param_module->tabparam)) {
		//dims_print_r($param_module->tabparam);
		echo 	"
			<FORM  ACTION=\"$scriptenv\" METHOD=\"POST\">
			<INPUT TYPE=\"HIDDEN\" NAME=\"op\" VALUE=\"save\">
			<INPUT TYPE=\"HIDDEN\" NAME=\"idmodule\" VALUE=\"$idmodule\">
			";

		// Sécurisation du formulaire par token
		require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
		$token = new FormToken\TokenField;
		$token->field("op");
		$token->field("idmodule");

		foreach($param_module->tabparam as $name => $param) {
			?>
			<tr>
				<td align="right"><? echo $param['label']; ?>:&nbsp;</td>
				<?
				if (!empty($param['choices'])) {
					?>
					<td>
					<select class="select" NAME="<? echo $name; ?>">
					<?
					$token->field($name);
					foreach($param['choices'] as $value => $displayed_value) {
						?>
						<option <? if ($param['value'] == $value) echo 'selected'; ?> value="<? echo htmlspecialchars($value); ?>"><? echo $displayed_value; ?></option>
						<?
					}
					?>
					</select>
					</td>
					<?
				}
				else {
					?>
					<td><input class="text" type="text" name="<? echo $name; ?>" value="<? echo htmlspecialchars($param['value']); ?>" style="width:100%;"></td>
					<?
					$token->field($name);
				}
				?>
			</tr>
			<?
		}
		?>
		<tr>
			<td colspan="2" align="right">
				<input class="flatbutton" type="submit" value="<? echo $_DIMS['cste']['_DIMS_SAVE']; ?>">
				<? $token->field("submit"); ?>
			</td>
		</tr>

	<?
	}
	else echo '&nbsp;'.$_DIMS['cste']['_DIMS_LABEL_NOMODULEPARAM'];
	?>
	</TABLE>
	<?
	$tokenHTML = $token->generate();
	echo $tokenHTML;
	echo "</form>";
	echo $skin->close_simplebloc();
}
?>
