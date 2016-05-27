<?php
$motscles = '';
$a_finalres = array();

if(!isset($_SESSION['catalogue']['recherche']['notRatt'])) $_SESSION['catalogue']['recherche']['notRatt'] = false;

if (!empty($_POST['motscles'])) {
	$_SESSION['catalogue']['recherche']['motscles'] = $_POST['motscles'];
	$_SESSION['catalogue']['recherche']['notRatt'] = (isset($_POST['notRatt']));
}

if (!empty($_SESSION['catalogue']['recherche']['motscles'])) {
	$motscles = $_SESSION['catalogue']['recherche']['motscles'];
	$a_motscles = explode(' ', $_SESSION['catalogue']['recherche']['motscles']);

	$key = 0;
	$a_res = array();
	foreach ($a_motscles as $mot) {
		$a_res[$key] = array();
		$mot = addslashes(trim($mot));

		if (strlen($mot) > 1) {
			$rs = $db->query("
				SELECT	a.id_article
				FROM	dims_mod_cata_article a
				INNER JOIN	dims_mod_cata_article_lang al
				ON			al.id_article_1 = a.id_article
				LEFT JOIN	dims_mod_cata_marque m
				ON			m.id = a.marque
				WHERE	(
								a.reference LIKE '%$mot%'
							OR	al.label LIKE '%$mot%'
							OR	al.description LIKE '%$mot%'
							OR	m.libelle LIKE '%$mot%'
						)");
			while ($row = $db->fetchrow($rs)) {
				$a_res[$key][$row['id_article']] = $row['id_article'];
			}
			$key++;
		}
	}

	if (sizeof($a_res)) {
		$a_finalres = $a_res[0];
		for ($i = 1; $i < $key; $i++) {
			if (is_array($a_res[$i])) {
				$a_finalres = array_intersect($a_finalres, $a_res[$i]);
			}
		}
	}
}
?>

<?=$skin->open_simplebloc("Recherche d'articles", '100%');?>
	<form name="f_recherche" action="<?=$dims->getScriptEnv();?>" method="post">

	<table cellpadding="2" cellspacing="0">
	<tr>
		<td><label for="motscles">Recherche :</label></td>
		<td><input class="text" type="text" id="motscles" name="motscles" value="<?=$motscles;?>"/></td>
		<td><input class="checkbox" type="checkbox" id="notRatt" name="notRatt" value="1" <?php if ($_SESSION['catalogue']['recherche']['notRatt']) echo 'checked'; ?> /> <label for="notRatt">Uniquement les articles non rattach&eacute;s</label></td>
		<td><input class="button" type="submit" value="Chercher"/></td>
	</tr>
	</table>
	</form>

<?php
if (sizeof($a_finalres)) {
	$color = $skin->values['bgline2'];
	?>
	<form name="form_lst_art" action="<?=$dims->getScriptEnv();?>" method="Post">
	<input type="hidden" name="op" id="op" value="">

	<table cellpadding="2" cellspacing="1" width="100%" id="articlesList">
	<tr bgcolor="<?=$color;?>" valign="middle" class="nomark">
		<th class="Title" width=\"1%\" nowrap><input class="checkbox" type="checkbox" onclick="javascript:markAllRows('articlesList');" /></th>
		<th class="Title" width=\"1%\" nowrap>&nbsp;</th>
		<th class="Title" width=\"1%\" nowrap>&nbsp;</th>
		<th class="Title"><a class="Title" href="<?=$scriptenv;?>?order=reference"><? echo _CATA_ARTICLE_LABEL_REF; if($_SESSION['catalogue']['tri_article']['nom']=='reference') echo ' '.$_SESSION['catalogue']['tri_article']['img']; ?></a></th>
		<th class="Title"><a class="Title" href="<?=$scriptenv;?>?order=label"><? echo _CATA_ARTICLE_LABEL_DESIGNATION; if($_SESSION['catalogue']['tri_article']['nom']=='label') echo ' '.$_SESSION['catalogue']['tri_article']['img']; ?></a></th>
		<th class="Title" align="center">Edit.</th>
	</tr>
	<?php
	$i = 0;

	$sql = "
		SELECT	a.*, al.label AS designation, af.id_famille
		FROM	dims_mod_cata_article a

		INNER JOIN 	dims_mod_cata_article_lang al
		ON 			al.id_article_1 = a.id_article

		LEFT JOIN	dims_mod_cata_article_famille af
		ON			af.id_article = a.id_article

		WHERE	a.id_article IN (".implode(',', $a_finalres).")
		";
	if ($_SESSION['catalogue']['recherche']['notRatt']) {
		$sql .= "
			AND		ISNULL(af.id_famille)";
	}
	$sql .= "
		GROUP BY a.id_article
		ORDER BY a.reference";
	$rs = $db->query($sql);
	echo $db->numrows($rs)." RESULTAT(S)";
	while ($fields = $db->fetchrow($rs)) {
		$i++;
		$class = ($i % 2 == 0) ? 'odd' : 'even';

		echo "
			<tr class='{$class}' onclick=\"javascript: markRow(this, $i);\">
				<td align=\"center\">
					<a name=\"a{$fields['id_article']}\" />
					<input class=\"checkbox\" type=\"checkbox\" name=\"articles[]\" value=\"{$fields['id_article']}\" />
				</td>
				<td align=\"center\">";
		if ($fields['published']) {
			echo "<img src=\"./common/modules/catalogue/img/p_green.png\" alt=\"Publi&eacute;\" />";
		} else {
			echo "<img src=\"./common/modules/catalogue/img/p_red.png\" alt=\"Non Publi&eacute;\" />";
		}
		echo "</td>";
		if ( $fields['image'] != '' && file_exists('./photos/50x50/'.$fields['image']) ) {
			echo "<td align=\"center\" nowrap>&nbsp;<a href=\"javascript: dims_openwin('./popup_photo.php?img={$fields['image']}');\"><img src=\"./photos/50x50/{$fields['image']}\" alt=\"{$fields['image']}\" /></a>&nbsp;</td>";
		} else {
			echo "<td nowrap>&nbsp;</td>";
		}
		echo "
				<td nowrap>&nbsp;{$fields['reference']}&nbsp;</td>
				<td nowrap>{$fields['designation']}</td>
				<td align=\"center\">&nbsp;<a href=\"$scriptenv?dims_moduletabid="._ADMIN_TAB_CATA_ARTEDIT."&famId={$fields['id_famille']}&op=edit&id_article={$fields['id_article']}&retour=rech\"><img src=\"./common/modules/catalogue/img/crayon.gif\" alt=\"Ouvrir la fiche de cet article\" border=\"0\" /></a>&nbsp;</td>
			</tr>";
	}
	?>
	<tr class="nomark"><td colspan="6">&nbsp;</td></tr>
	<tr class="nomark">
		<td colspan="6" align="center" width="100%">
			<input class="Button" type="button" value="Coller dans la famille courante" onclick="javascript: document.form_lst_art.op.value='rattach_articles_sel'; document.form_lst_art.submit();" />
			<input class="Button" type="submit" value="Publier" onclick="javascript: document.form_lst_art.op.value='save_publish'; document.form_lst_art.submit();" />
		</td>
	</tr>
	</table>
	</form>
	<?
}
?>
<?=$skin->close_simplebloc();?>

<script type="text/javascript">
	document.f_recherche.motscles.focus();
</script>
