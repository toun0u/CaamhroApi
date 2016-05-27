<?php
header('Content-type: text/html; charset='._DIMS_ENCODING);
header('Cache-Control: no-cache, must-revalidate'); // HTTP/1.1
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // Date dans le passé
ob_start();

$pref = dims_load_securvalue('pref', dims_const::_DIMS_CHAR_INPUT, true, true, true);

if (isset($pref) && $pref != "") {
	include_once DIMS_APP_PATH.'/modules/catalogue/include/class_article.php';
	$article = new article();
	$article->open($pref);
	$article->getDegressif();

	if (sizeof($article->degressifs)) {
		echo '
			<table style="font-size: 10px;" cellpadding="2" cellspacing="0" width="100%">
			<tr><td nowrap colspan="2"><font color="#ed0000">&nbsp;<b><u>Tarif d&eacute;gressif :</u></b>&nbsp;</font></td></tr>';
		$i = 1;
		foreach ($article->degressifs as $degr) {
			if ($degr['seuil_'.$i] > 0) {
				echo '
					<tr>
						<td nowrap><font color="#ed0000">&nbsp;- Pour <b>'.intval($degr['seuil_'.$i]).'</b> :&nbsp;</font></td>
						<td nowrap align="right"><font color="#ed0000">&nbsp;<b>'. catalogue_formateprix($degr['pv_'.$i]) .'</b> &euro;&nbsp;</font></td>
					</tr>';
			}
			else {
				break;
			}
			$i++;
		}
		echo '</table>';
	}
}

@ob_end_flush();
die();
