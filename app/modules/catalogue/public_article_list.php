<?php
if (!isset($_SESSION['catalogue']['tri_article'])) {
	$_SESSION['catalogue']['tri_article']['nom']	= 'reference';
	$_SESSION['catalogue']['tri_article']['sens']	= 'ASC';
	$_SESSION['catalogue']['tri_article']['img']	= '<img src="./common/modules/catalogue/img/ico_asc.png" alt="" border="0" />';
}

$order = dims_load_securvalue('order', dims_const::_DIMS_CHAR_INPUT, true, false);
if (!empty($order)) {
	if ($_SESSION['catalogue']['tri_article']['nom']	== $order) {
		if ($_SESSION['catalogue']['tri_article']['sens'] == 'ASC') {
			$_SESSION['catalogue']['tri_article']['sens']	= 'DESC';
			$_SESSION['catalogue']['tri_article']['img']	= '<img src="./common/modules/catalogue/img/ico_desc.png" alt="" border="0" />';
		} else {
			$_SESSION['catalogue']['tri_article']['sens']	= 'ASC';
			$_SESSION['catalogue']['tri_article']['img']	= '<img src="./common/modules/catalogue/img/ico_asc.png" alt="" border="0" />';
		}
	}
	$_SESSION['catalogue']['tri_article']['nom'] = $order;
}

$req_tri = $_SESSION['catalogue']['tri_article']['nom'].' '.$_SESSION['catalogue']['tri_article']['sens'];

$sql = "
	SELECT	a.*,
			al.label,
			af.position
	FROM	dims_mod_cata_article_famille af

	INNER JOIN	dims_mod_cata_article a
	ON			a.id_article = af.id_article

	INNER JOIN	dims_mod_cata_article_lang al
	ON			al.id_article_1 = a.id_article

	WHERE	af.id_famille = $famId

	ORDER BY $req_tri";
$result = $db->query($sql);

echo $skin->open_simplebloc(_CATA_SUBTITLE_ART_LIST,'100%');
	if ($db->numrows($result)) {
		$color = $skin->values['bgline2'];
		?>
		<form name="form_lst_art" action="<?php echo $dims->getScriptEnv(); ?>" method="Post">
		<input type="hidden" name="op" id="op" value="" />

		<table cellpadding="2" cellspacing="1" width="100%" id="articlesList">
			<tr class="nomark">
				<td colspan="6">
					<table cellpadding="2" cellspacing="0">
					<tr class="nomark">
						<?php /*
						<td>[</td>
						<td><img src="./common/modules/catalogue/img/ico_green_cross.gif" border="0" /></td>
						<td><a href="<?php echo $dims->getScriptEnv(); ?>?op=create"><?=_CATA_ARTICLE_LABEL_ADD_ART;?></a></td>
						<td>]</td>
						<td>&nbsp;&nbsp;</td>
							  */ ?>

						<td>[</td>
						<td><img src="./common/modules/catalogue/img/attach.gif" border="0" /></td>
						<td><a href="<?php echo $dims->getScriptEnv(); ?>?op=rattach"><?=_CATA_ARTICLE_LABEL_ATTACH_ART;?></a></td>
						<td>]</td>
						<td>&nbsp;&nbsp;</td>

						<td>[</td>
						<td><img src="./common/modules/catalogue/img/ico_cube.gif" border="0" /></td>
						<td><a href="#" onclick="javascript:document.form_lst_art.op.value='affect_group';document.form_lst_art.submit();">Affectation group&eacute;e</a></td>
						<td>]</td>
						<td>&nbsp;&nbsp;</td>

						<?php if (!empty($_SESSION['catalogue']['selArticles'])): ?>
						<td>[</td>
						<td><img src="./common/modules/catalogue/img/paste.png" border="0" /></td>
						<td><a href="#" onclick="javascript:document.location.href='<?=$scriptenv;?>?op=rattach_articles';">Coller les articles du presse-papier</a></td>
						<td>]</td>
						<?php endif ?>
					</tr>
					</table>
				</td>
			</tr>
			<tr class="nomark" bgcolor="<?=$color;?>" valign="middle">
				<th class="Title" width=\"1%\" nowrap><input class="checkbox" type="checkbox" onclick="javascript:markAllRows('articlesList');" /></th>
				<th class="Title" width=\"1%\" nowrap>&nbsp;</th>
				<th class="Title" width=\"1%\" nowrap>&nbsp;</th>
				<th class="Title"><a class="Title" href="<? echo $dims->getScriptEnv(); ?>?order=reference"><? echo _CATA_ARTICLE_LABEL_REF; if($_SESSION['catalogue']['tri_article']['nom']=='reference') echo ' '.$_SESSION['catalogue']['tri_article']['img']; ?></a></th>
				<th class="Title"><a class="Title" href="<? echo $dims->getScriptEnv(); ?>?order=label"><? echo _CATA_ARTICLE_LABEL_DESIGNATION; if($_SESSION['catalogue']['tri_article']['nom']=='label') echo ' '.$_SESSION['catalogue']['tri_article']['img']; ?></a></th>
				<th class="Title" align="center">Edit.</th>
				<th class="Title" align="center">Dét.</th>
				<th class="Title" align="center">Suppr.</th>
			</tr>
			<?php
			$i = 0;
			while ($fields = $db->fetchrow($result)) {
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
						<td nowrap>{$fields['label']}</td>
						<td align=\"center\">&nbsp;<a href=\"$scriptenv?op=edit&id_article={$fields['id_article']}\"><img src=\"./common/modules/catalogue/img/crayon.gif\" alt=\"Ouvrir la fiche de cet article\" border=\"0\"></a>&nbsp;</td>
						<td align=\"center\">&nbsp;<a href=\"javascript:dims_confirmlink('$scriptenv?op=detach&id_article={$fields['id_article']}','Etes-vous sûr(e) de vouloir détacher cet article ?');\"><img src=\"./common/modules/catalogue/img/ico_cut.gif\" alt=\"Détacher cet article\" border=\"0\"></a>&nbsp;</td>
						<td align=\"center\">&nbsp;<a href=\"javascript:dims_confirmlink('$scriptenv?op=delete&id_article={$fields['id_article']}','Etes-vous sûr(e) de vouloir supprimer cet article ?');\"><img src=\"./common/modules/catalogue/img/supprimer.gif\" alt=\"Supprimer cet article\" border=\"0\"></a>&nbsp;</td>
					</tr>";
			}
			?>
			<tr class="nomark"><td colspan="6">&nbsp;</td></tr>
			<tr class="nomark">
				<td colspan="6" align="center" width="100%">
					<input class="Button" type="button" value="Couper dans le presse-papier" onclick="javascript: document.form_lst_art.op.value='cut_articles'; document.form_lst_art.submit();" />
					<input class="Button" type="button" value="Copier dans le presse-papier" onclick="javascript: document.form_lst_art.op.value='copy_articles'; document.form_lst_art.submit();" />
					<input class="Button" type="submit" value="Inverser la publication" onclick="javascript: document.form_lst_art.op.value='save_publish'; document.form_lst_art.submit();" />
				</td>
			</tr>
		</table>
		</form>
		<!-- FORM POUR LE CHANGEMENT DE POSITION -->
		<form name="form_chg_posit" action="<? echo $scriptenv; ?>" method="Post">
			<input type="Hidden" name="op" value="chg_posit">
			<input type="Hidden" name="chg_posit_id_famille" id="chg_posit_id_famille" value="<? echo $famId; ?>">
			<input type="Hidden" name="chg_posit_id_article" id="chg_posit_id_article" value="">
			<input type="Hidden" name="chg_posit_new_posit" id="chg_posit_new_posit" value="">
		</form>
		<?
	} else {
		?>
		<table cellpadding="2" cellspacing="0">
			<tr>
				<td>
					<table cellpadding="2" cellspacing="0">
					<tr>

						<td><? echo _CATA_ARTICLE_LABEL_NO_ART; ?></td>
						<td>&nbsp;&nbsp;</td>

						<?php /*
						<td>[</td>
						<td><img src="<?=_CATA_ICO_ADD;?>" border="0"></td>
						<td><a href="<?=$scriptenv;?>?op=create"><? echo _CATA_ARTICLE_LABEL_ADD_ART; ?></a></td>
						<td>]</td>
						<td>&nbsp;&nbsp;</td>
						 */ ?>

						<td>[</td>
						<td><img src="<?=_CATA_ICO_ATTACH;?>" border="0"></td>
						<td><a href="<?=$scriptenv;?>?op=rattach"><? echo _CATA_ARTICLE_LABEL_ATTACH_ART; ?></a></td>
						<td>]</td>
						<td>&nbsp;&nbsp;</td>

						<?php if (!empty($_SESSION['catalogue']['selArticles'])): ?>
						<td>[</td>
						<td><img src="./common/modules/catalogue/img/paste.png" border="0" /></td>
						<td><a href="#" onclick="javascript:document.location.href='<?=$scriptenv;?>?op=rattach_articles';">Coller les articles du presse-papier</a></td>
						<td>]</td>
						<?php endif ?>
					</tr>
					</table>
				</td>
			</tr>
		</table>
		<?
	}
echo $skin->close_simplebloc();
?>
