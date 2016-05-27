<?php

/*
* To change this template, choose Tools | Templates
* and open the template in the editor.
*/

echo '<div style="display:block;width:100%;float:left;">';
echo '<div class="ui-widget-header">
		<span class="ui-icon ui-icon-search" style="float:left;"></span>
		<span>'.$_DIMS['cste']['_DIMS_SEARCH_RESULT'].'</span>

	</div>';

echo "<span style=\"float:left;width:100%;\">";
$textresult=$_DIMS['cste']['_DIMS_LABEL_EXPRESSION_RESULTAT'];
$textresult=str_replace(array("{EXPR}","{NBRESULT}"),array("<b>'".stripslashes(utf8_encode($dimsearch->expression_brut))."'</b>","<b>".$nbresult."</b>"),$textresult);
echo $textresult;
//echo "</span>";
echo '
<button class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-secondary" onclick="deleteSelected()" title="'.$_DIMS['cste']['_LABEL_DELETE_QUERY'].'">
			<span class="ui-button-text">'.$_DIMS['cste']['_DIMS_LABEL_CANCEL'].'</span>
			<span class="ui-button-icon-secondary ui-icon ui-icon-cancel"></span>
		</button>';
echo "</span><span style=\"float:left;width:100%;\">";

if (!empty($dimsearch->expression)) {
	foreach ($dimsearch->expression as $k => $elems) {
		//echo "<br>".$dimsearch->expression[$k]['word']." : ";

	}
}

echo "</span>";
echo "</div>";
echo "<table style=\"width:100%\" cellspacing=\"4\">";

$nbrest=0;
$taille=count($dimsearch->expression);
if ($taille==0) $taille=1;
if ($taille <=2) $nbrest=2-$taille;

$taille=80/($taille+$nbrest);

$nbkeywords=sizeof($dimsearch->expression);

// construction de la premiere ligne
echo "<tr><td style=\"color:#AAAAAA;\">".$_DIMS['cste']['_DIMS_LABEL_KEYWORDS'].'</td>';
foreach ($dimsearch->expression as $k=>$elemword) {
	echo "<td style=\"text-align:center;;font-weight:bold;width:".$taille."%;\">";
	echo $dimsearch->expression[$k]['word'];
	// ajout de la fonction de suppression du mot courant
	echo "<a href=\"javascript:void(0);\" onclick=\"javascript:deleteWordSearch(".$k.");\" title=\"".$_DIMS['cste']['_DELETE']."\"><img src=\"./common/img/delete.png\" alt=\"".$_DIMS['cste']['_DELETE']."\">";
	echo "</td>";

	if ($k<$nbkeywords-1) {
		if ($dimsearch->tabfiltre[$k]['op']=='AND') {
			$title='AND';
			$changeop="OR";
		}
		else {
			$title='OR';
			$changeop="AND";
		}
		echo "<td><a href=\"javascript:void(0);\" onclick=\"javascript:updateOperatorWordSearch(".$k.",'".$changeop."');\" title=\"".$title."\">".$dimsearch->tabfiltre[$k]['op']."</a></td>";

	}
}
echo "<td style=\"width:".($taille*$nbrest)."%\"></td>";
echo "</tr><tr><td style=\"color:#AAAAAA;\">".$_DIMS['cste']['_TYPE'].'</td>';

foreach ($dimsearch->expression as $k=>$elemword) {
	echo "<td style=\"text-align:center;\">";
	if (isset($dimsearch->tabfiltre[$k]['type']) && !empty($dimsearch->tabfiltre[$k]['type'])) {
		for ($j=1;$j<=2;$j++) {
			$selected = ($j==$dimsearch->tabfiltre[$k]['type']) ? 'checked' : '';

			echo '<span style="margin-right:20px;">';

			if ($j==1) echo "<img src=\"./common/img/kword.png\">&nbsp;".$_DIMS['cste']['_LABEL_WORD'];
			else echo "<img src=\"./common/img/tag.png\">&nbsp;".$_DIMS['cste']['_DIMS_LABEL_TAG'];
			echo '<input type="radio" value="'.$j.'" onclick="javascript:updateTypeWordSearch('.$k.','.$j.');" name="kword_type_'.$k.'" '.$selected.'></span>';


		}
	}
	echo "</td><td></td>";
}
echo "<td></td></tr><tr><td style=\"color:#AAAAAA;vertical-align:top;\">".$_DIMS['cste']['_LABEL_SEE_ALSO'].'</td>';
foreach ($dimsearch->expression as $k=>$elemword) {
	$filter_isactive	= false;
	echo "<td  style=\"vertical-align:top;\">";
	$sizeelem=0;
	// on propose des tags
	if (isset($dimsearch->tabfiltre[$k]['type']) && !empty($dimsearch->tabfiltre[$k]['type']) && $dimsearch->tabfiltre[$k]['type']==2) {

		if (isset($dimsearch->tabtag[$k])) {
			echo "<div id=\"search_word_res".$k."\" style=\"display:block;\">";
			if (isset($dimsearch->tabtag[$k]) && !empty($dimsearch->tabtag[$k])) {
				foreach ($dimsearch->tabtag[$k] as $kk => $word) {
					if (isset($_DIMS['cste'][$word])) $word=$_DIMS['cste'][$word];
					echo "<a href='javascript:void(0);' onclick=\"actualizeSearch(".$k.",'".$kk. "',2);\">".$word."</a> - ";
				}
			}
			echo "</div></td><td></td>";
		}

		echo "<td></td>";
	}
	else {
		if (isset($dimsearch->tabpossible[$k]) && !empty($dimsearch->tabpossible[$k])) {
			$sizeelem=sizeof($dimsearch->tabpossible[$k]);
		}
		//echo "<tr><td><a href=\"javascript:void(0)\" onclick=\"javascript:dims_switchdisplay('search_word_res".$k."');\">".$_DIMS['cste']['_LABEL_SEE_ALSO']." (".$sizeelem.")</a></td></tr>";
		echo "<div id=\"search_word_res".$k."\" style=\"display:block;\">";
		if (isset($dimsearch->tabpossible[$k]) && !empty($dimsearch->tabpossible[$k])) {
			foreach ($dimsearch->tabpossible[$k] as $kk => $word) {
				echo "<a href='javascript:void(0);' onclick=\"actualizeSearch(".$k.",'".$word. "',1);\">".$word."</a> - ";
			}
		}
		echo "</div></td><td></td>";
	}
}

echo "<td></td></tr><tr><td style=\"color:#AAAAAA;vertical-align:top;\">".$_DIMS['cste']['_FORMS_FIELDLIST'].'</td>';
foreach ($dimsearch->expression as $k=>$elemword) {

	echo "<td>";
	if (!empty($dimsearch->tabpotentiel[$k]) && !isset($dimsearch->tabfiltre[$k]['type']) || $dimsearch->tabfiltre[$k]['type']!=2)  {
		foreach ($dimsearch->tabpotentiel[$k] as $id_module => $objects) {
			foreach($objects as $id_object=>$object) {
				echo "<ul>";
				$total=0;
				foreach ($object as $metaf=>$cpte) {
					$total+=$cpte;
				}
				//dims_print_r($dimsearch->tablemetafield);
				foreach ($object as $metaf=>$cpte) {
					if ($cpte>0 && (($cpte*100)/$total)>2) {
						if (isset($dimsearch->tabfiltre[$k]['metafield'][$id_module][$id_object]) && $dimsearch->tabfiltre[$k]['metafield'][$id_module][$id_object]==$metaf) {
							echo "<li><a href=\"javascript:void(0);\" onclick=\"javascript:updateDimsSearch(".$k.",".$id_module.",".$id_object.",".$metaf.",0);\">".$dimsearch->tablemetafield[$metaf]['name']."<img src=\"./common/img/delete.png\"></a></li>";
						}
						elseif(isset($dimsearch->tablemetafield[$metaf]['name'])) {
							echo "<li><a href=\"javascript:void(0);\" onclick=\"javascript:updateDimsSearch(".$k.",".$id_module.",".$id_object.",".$metaf.",1);\">".$dimsearch->tablemetafield[$metaf]['name']." (".$cpte.")</a></li>";
						}
					}
				}
				echo "</ul>";
			}
		}
	}
	else {
		if (isset($dimsearch->tabfiltre[$k][1])) {
			echo "<li><a href=\"javascript:void(0);\" onclick=\"javascript:updateDimsSearch(".$k.",1,".$dimsearch->tabfiltre[$k][1].",0);\">".$dimsearch->tablemetafield[$dimsearch->tabfiltre[$k][1]]['name']." (".$cpte.")<img src=\"./common/img/delete.png\"></a></li>";
		}
	}
	echo "</td><td></td>";
}
echo "</tr></table>";
?>