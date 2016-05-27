<?php
// chargement des centres d'interet
$a_int = array();
$db->query("SELECT id, nom_interest FROM dims_interest ORDER BY nom_interest");
while ($row = $db->fetchrow($res)) {
	$a_int[$row['id']] = $row['nom_interest'];
}

// recuperation des centres d'interet de l'utilisateur
$user->interests = $user->getInterests();

// affichage sur 4 colonnes
$nbCol = 4;
?>

<form name="form_modify_interests" action="<?=$scriptenv;?>" method="post">
<?
	// Sécurisation du formulaire par token
	require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
	$token = new FormToken\TokenField;
	$token->field("op", "save_user_interests");
	$token->field("user_interests");
	$tokenHTML = $token->generate();
	echo $tokenHTML;
?>
<input type="hidden" name="op" value="save_user_interests">
	<div class="dims_form" style="float:left;width:50%;">
		<table>
		<tr>
			<?php
			$i = 0;
			foreach ($a_int as $id => $label) {
				$checked = (in_array($id, $user->interests)) ? ' checked' : '';
				?>
				<td width="<?=(100/$nbCol);?>%"><input class="checkbox" type="checkbox" name="user_interests[]" value="<?=$id;?>" <?=$checked;?>/> <?=$label;?></td>
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
		echo dims_create_button($_DIMS['cste']['_DIMS_SAVE'],"./common/img/save.gif","javascript:form_modify_interests.submit();","enreg","");
		?>
	</div>
</form>
