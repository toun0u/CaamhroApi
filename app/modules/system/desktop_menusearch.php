<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
if (!isset($_SESSION['dims']['typesearch'])) {
	$_SESSION['dims']['typesearch']=0;
}
$style=($_SESSION['dims']['typesearch']==0) ? 'text-decoration:underline' : '';
echo  "<a href=\"javascript:void(0);\" onclick=\"javascript:updateTypeSearch(0)\"><font style=\"".$style."\">".$_DIMS['cste']['_SEARCH']."</font></a>&nbsp;|&nbsp;";

$style=($_SESSION['dims']['typesearch']==1) ? 'text-decoration:underline' : '';
echo  "<a href=\"javascript:void(0);\" onclick=\"javascript:updateTypeSearch(1)\"><font style=\"".$style."\">".$_DIMS['cste']['_DIMS_LABEL_SEARCH_BY_TAGS']."</a></font>";

// affichage de l'histo eventuel
if (isset($_SESSION['dims']['history_object']) && !empty($_SESSION['dims']['history_object'])) {
	echo "&nbsp;|&nbsp;";
	$style=($_SESSION['dims']['typesearch']==2) ? 'text-decoration:underline' : '';
	echo  "<a href=\"javascript:void(0);\" onclick=\"javascript:updateTypeSearch(2)\"><font style=\"".$style."\">".$_DIMS['cste']['_DIMS_LABEL_LAST_OPENED_OBJECT']."</a></font>";
}

switch ($_SESSION['dims']['typesearch']) {
	case 0:
		?>
		<div id="searchBar_obj" style="margin:2px auto;display:block;width:600px;clear:both;">
			<input style="" id="searchBar_obj_bar" name="searchBar_obj_bar" class="searchBar_obj_bar" type="text" onkeypress="javascript:dims_word_keyupExec(event);"
				   value="<?
				   mb_detect_encoding($expression_brute, "UTF-8") == "UTF-8" ? : $expression_brute = utf8_encode($expression_brute);
					$expr=stripslashes($expression_brute);
					echo str_replace('"','&quot;',$expr);
					echo str_replace('"','&quot;',stripslashes(utf8_encode($expression_brute)));
				   ?>"/>
			<input id="searchBar_obj_sub" class="searchBar_obj_sub" type="submit" value="" onclick="searchWord();" />
			Recherche avanc&eacute;
		</div>
		<?
		break;
	case 1:
		echo "Tags";
		break;

	case 2:
			echo '<div style="margin:0px auto;display:block;width:100%;clear:both;">';
			echo $dims->getHistoryObject();
			echo '</div>';

		break;
}

?>
