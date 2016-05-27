<?php
// chargement des metiers
$a_works = array();
$db->query("SELECT id, label_works FROM dims_works ORDER BY label_works");
while ($row = $db->fetchrow($res)) {
	$a_works[$row['id']] = $row['label_works'];
}

// recuperation des metiers de l'utilisateur
$user->works = $user->getWorks();

// affichage sur 4 colonnes
$nbCol = 4;
?>

<form name="form_modify_works" action="<?=$scriptenv;?>" method="post">
<?
	// Sécurisation du formulaire par token
	require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
	$token = new FormToken\TokenField;
	$token->field("op", "save_user_works");
	$token->field("user_works");
	$tokenHTML = $token->generate();
	echo $tokenHTML;
?>
<input type="hidden" name="op" value="save_user_works">
	<div class="dims_form" style="float:left;width:50%;">
		<table>
		<tr>
			<?php
			$i = 0;
			foreach ($a_works as $id => $label) {
				$checked = (in_array($id, $user->works)) ? ' checked' : '';
				?>
				<td width="<?=(100/$nbCol);?>%"><input class="checkbox" type="checkbox" name="user_works[]" value="<?=$id;?>" <?=$checked;?>/> <?=$label;?></td>
				<?
				$i++;
				if ($i % $nbCol == 0) {
					echo "</tr><tr>";
				}
			}
			?>
		</tr>
		</table>
	</div>

	<div style="clear:both;float:right;padding:4px;">
		<?php
		echo dims_create_button($_DIMS['cste']['_DIMS_SAVE'],"./common/img/save.gif","javascript:form_modify_works.submit();","enreg","");
		?>
	</div>
</form>
