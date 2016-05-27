<?php
echo $skin->open_simplebloc(_CATA_SUBTITLE_ART_RATTACH,"100%");
	?>
	<form name="rattach_form" action="<?php echo $dims->getScriptEnv(); ?>" method="post">
	<input type="hidden" name="op" value="rattach" />

	<table cellpadding="2" cellspacing="1">
	<tr>
		<td><?php echo _CATA_ARTICLE_LABEL_RECH; ?> :</td>
		<td><input class="text" type="text" name="recherche" value="<?php if (!empty($recherche)) echo $recherche; ?>" maxlength="20"></td>
		<td><input class="button" type="submit" value="Rechercher"></td>
		<td><input class="button" type="button" value="Annuler" onclick="javascript:document.location.href='<?php echo $dims->getScriptEnv(); ?>';"></td>
	</tr>
	</table>

	<?php
	if (!empty($recherche)) {
		$sql = "
			SELECT	a.id_article, a.reference, al.label, al.description
			FROM	dims_mod_cata_article a
			INNER JOIN	dims_mod_cata_article_lang al
			ON			al.id_article_1 = a.id_article
			WHERE	(
					a.reference LIKE '$recherche%'
					|| al.label LIKE '%$recherche%'
					|| al.description LIKE '%$recherche%'
					)";
		$rs = $db->query($sql);
		if ($db->numrows($rs)) {
			$color = $skin->values['bgline2'];
			?>
			<br>
			<table cellpadding="2" cellspacing="1">
			<tr bgcolor="<?php echo $color; ?>">
				<th class="title">&nbsp;<input type="checkbox" name="masterbox" checked onclick="javascript:dims_checkall(document.rattach_form,'articles',this.checked);">&nbsp;</th>
				<th class="title">&nbsp;<?php echo _CATA_ARTICLE_LABEL_REF; ?>&nbsp;</th>
				<th class="title">&nbsp;<?php echo _CATA_ARTICLE_LABEL_DESIGNATION; ?>&nbsp;</th>
			</tr>
			<?php
			while ($row = $db->fetchrow($rs)) {
				$color = ($color == $skin->values['bgline2']) ? $skin->values['bgline1'] : $skin->values['bgline2'];
				echo "
					<tr bgcolor=\"$color\">
						<td>&nbsp;<input type=\"checkbox\" name=\"articles[]\" value=\"{$row['id_article']}\" checked>&nbsp;</td>
						<td>&nbsp;{$row['reference']}&nbsp;</td>
						<td>&nbsp;{$row['label']}&nbsp;</td>
					</tr>";
			}
			?>
			<tr>
				<td colspan="3" align="center">
					<input class="button" type="button" value="<?php echo _CATA_ARTICLE_LABEL_ATTACH_SELECTED; ?>" onclick="javascript:document.rattach_form.op.value='save_rattach';document.rattach_form.submit();">
				</td>
			</tr>
			</table>
			<?php
		}
	}
	echo "</form>";
echo $skin->close_simplebloc();
?>

<script language="JavaScript">
	document.rattach_form.recherche.focus();
</script>
