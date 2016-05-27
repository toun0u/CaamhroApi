<?php
require_once(DIMS_APP_PATH . '/modules/system/class_tag_index.php');

echo "<div id=\"detail_contenttag\" style=\"clear:both;\">";

$content = '';
$add_g = '';
$private = '';

$typetag = dims_load_securvalue('typetag', dims_const::_DIMS_NUM_INPUT, true, true);

if ($typetag>=0) {
	$_SESSION['dims']['current_typetag']=$typetag;
}
if (!isset($_SESSION['dims']['current_typetag'])) $_SESSION['dims']['current_typetag']=0; // generic

if (isset($_SESSION['dims']['save_typetag'])){
	$_SESSION['dims']['current_typetag'] = $_SESSION['dims']['save_typetag'];
	unset($_SESSION['dims']['save_typetag']);
}

$typetag=$_SESSION['dims']['current_typetag'];
// construction des types de tags
$arraytypetag=array();

$arraytypetag[0]['label']=$_DIMS['cste']['_DIMS_LABEL_LFB_GEN']; // generique
$arraytypetag[0]['nb']=0;
if (isset($_SESSION['dims']['temp_tag'][0])) $arraytypetag[0]['nb']+=sizeof($_SESSION['dims']['temp_tag'][0]);
$arraytypetag[0]['selected']=($_SESSION['dims']['current_typetag']==0) ? true : false;
$arraytypetag[0]['icon']='./common/img/tag.png';

//if ($_SESSION['dims']['current_object']['id_object'] == dims_const::_SYSTEM_OBJECT_CONTACT) {
/*
	$arraytypetag[1]['label']=$_DIMS['cste']['_DIMS_LABEL_CONTACT_GOUPS']; // groupe de contacts
	$arraytypetag[1]['nb']=0;
	if (isset($_SESSION['dims']['temp_tag'][1])) $arraytypetag[1]['nb']+=sizeof($_SESSION['dims']['temp_tag'][1]);
	$arraytypetag[1]['selected']=($_SESSION['dims']['current_typetag']==1) ? true : false;;
	$arraytypetag[1]['icon']='./common/img/social.png';
  //}
 */

$arraytypetag[2]['label']=$_DIMS['cste']['_DIMS_LABEL_ENT_SECTACT'];
$arraytypetag[2]['nb']=0;
if (isset($_SESSION['dims']['temp_tag'][2])) $arraytypetag[2]['nb']+=sizeof($_SESSION['dims']['temp_tag'][2]);
$arraytypetag[2]['selected']=($_SESSION['dims']['current_typetag']==2) ? true : false;;
$arraytypetag[2]['icon']='./common/img/stats.png';
/*
$arraytypetag[3]['label']=$_DIMS['cste']['_DIMS_LABEL_COUNTRY'];
$arraytypetag[3]['nb']=0;
if (isset($_SESSION['dims']['temp_tag'][3])) $arraytypetag[3]['nb']+=sizeof($_SESSION['dims']['temp_tag'][3]);
$arraytypetag[3]['selected']=($_SESSION['dims']['current_typetag']==3) ? true : false;
$arraytypetag[3]['icon']='./common/img/workspace.png';

$arraytypetag[4]['label']=$_DIMS['cste']['_DIMS_LABEL_YEAR'];
$arraytypetag[4]['nb']=0;
if (isset($_SESSION['dims']['temp_tag'][4])) $arraytypetag[4]['nb']+=sizeof($_SESSION['dims']['temp_tag'][4]);
$arraytypetag[4]['selected']=($_SESSION['dims']['current_typetag']==4) ? true : false;
$arraytypetag[4]['icon']='./common/img/date.png';
*/
$tag=date("Y");
$idtag=0;
$res=$db->query("SELECT id from dims_tag where tag like :tag and type=4", array(
	':tag' => $tag
));

$valuefiltertag = dims_load_securvalue('filternametag', dims_const::_DIMS_CHAR_INPUT, true, true);

if ($db->numrows($res)==0) {
	// on cr√©√© le tag
	require_once(DIMS_APP_PATH . "/modules/system/class_tag.php");
	$objtag = new tag();
	$objtag->fields['type']=4; // date
	$objtag->fields['id_workspace']=$_SESSION['dims']['workspaceid'];
	$objtag->fields['tag']=$tag;
	$objtag->save();
	$idtag=$objtag->fields['id'];
}



	//selection des groupes appartenant au user en vu d'un rattachement
	$sqlu = "SELECT		t.tag as label, t.id, t.private
						FROM		dims_tag as t";
	$params = array();
	if ($typetag==0) {
		$sqlu .= "		WHERE			((t.id_workspace = :idworkspace and private=0 and type=0) OR (t.id_user = :iduser and private=1  and type=0))";
		$params[':idworkspace']	= $_SESSION['dims']['workspaceid'];
		$params[':iduser']		= $_SESSION['dims']['userid'];
	}
	else {
		$sqlu .= "		WHERE t.type= :typetag ";
		$params[':typetag']	= $typetag;
	}

	if ($typetag==3 && $valuefiltertag!='') {
		// recherche des constantes
		$listid="0";
		$sqlbis=$db->query("SELECT t.id from dims_tag as t inner join dims_constant as c on t.type=3 and t.tag=c.phpvalue and c.value like :value", array(
			':value' => "%".$valuefiltertag."%"
		));

		if ($db->numrows($sqlbis)>0) {
			while ($d=$db->fetchrow($sqlbis)) {
				$listid.=",".$d['id'];
			}
		}
		$sqlu .= " AND t.id in (".$db->getParamsFromArray(explode(',', $listid), 'listid', $params).")";
	}

	$sqlu.=" order by t.tag";

	$resu = $db->query($sqlu, $params);
	$nbelem=$db->numrows($resu);

	// on ajoute le code pour faire plusieurs onglets sinon ne tient pas
	$maxelem=36;
	$nbblock=0;

	if ($nbelem>48) {
		$add_g .= "<div style=\"width:100%;text-align:center;margin-top:4px;\">";
		$nbblock=$nbelem/$maxelem;
		if ($nbelem%$maxelem>0) $nbblock++;
	}

	// on boucle sur les blocs pour afficher le multi page
	for($b=1;$b<=$nbblock;$b++) {
		$add_g .= "<a style=\"border:dotted 1px #6E6E6E;padding:2px;\" href=\"javascript:void(0);\" onclick=\"javascript:switchMultiDiv('blocktag',".$b.",".$nbblock.");\">".$b."</a>&nbsp;";
	}

	if ($nbelem>48) {
		$add_g .= "</div>";
	}

	$listselpays='';
	$url=$_SERVER['QUERY_STRING'];

	if (strpos($url,'refreshDesktop')>0) $url='';

	// affichage des filtres
	$add_g .=	'<form name="search_tag_objet" method="post" action="/admin.php">';
	// Sécurisation du formulaire par token
	require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
	$token = new FormToken\TokenField;
	$tokenHTML = $token->generate();
	$add_g .= $tokenHTML;
	//$add_g .=   '<span style="width:100%"><input type="text" id="search_desktop_tag_name" name="search_desktop_tag_name" value="" onkeyup="javascript:updateSearchTagName();"></span>';
	$add_g .=	'</form>';

	if($db->numrows($resu) > 0) {

		$add_g .=	'<form id="add_ct_g" name="add_ct_g" method="post" action="/admin.php?">';
		// Sécurisation du formulaire par token
		require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
		$token = new FormToken\TokenField;
		$token->field("dims_op",	"add_tag");
		$token->field("typetag",	$typetag);
		$token->field("id_record",	$_SESSION['dims']['current_object']['id_record']);
		$token->field("id_object",	$_SESSION['dims']['current_object']['id_object']);
		$token->field("id_module",	$_SESSION['dims']['current_object']['id_module']);
		$token->field("add_contact","1");
		$token->field("filtercountrytag");
		$add_g .=	'<input type="hidden" name="dims_op" value="add_tag">
					<input type="hidden" name="typetag" value="'.$typetag.'">
					<input type="hidden" name="id_record" value="'.$_SESSION['dims']['current_object']['id_record'].'">
					<input type="hidden" name="id_object" value="'.$_SESSION['dims']['current_object']['id_object'].'">
					<input type="hidden" name="id_module" value="'.$_SESSION['dims']['current_object']['id_module'].'">
					<input type="hidden" name="add_contact" value="1">';

		$pos=0;
		$bcourant=1;
		if ($typetag==3) {

			$add_g .=	$_DIMS['cste']['_DIMS_FILTER'].' : <input type="text" value="'.$valuefiltertag.'" name="filtercountrytag" id="filtercountrytag">';
			$add_g .= '<img src="./common/img/search.png" style="mouse:pointer;" onclick="javascript:updateTypeTag(3);">';
		}

		$maxcount=$db->numrows($resu)/3;
		$col=0;
		if ($maxcount>$maxelem/3) $maxcount=$maxelem/3;

		while($tab_u = $db->fetchrow($resu)) {
			if ($pos%$maxelem==0) {
				if ($pos>0) $add_g .='</tr></table></div>';

				$select=($pos==0) ? 'display:block;visibility:visible;' : 'display:none;visibility:hidden;';
				$add_g .='<div id="blocktag'.$bcourant.'" style="'.$select.'">
								<span style="float:left;"><b>'.$_DIMS['cste']['_WORKSPACE'].'</b></span>
								<div style="clear:both;float:left;width:33%;"><ul style="">';
				$col=0;
				$bcourant++;
			}

			if ($col>0 && $col%$maxcount==0) {
				$add_g.= '</ul></div><div style="float:left;"><ul style="">';
				$col=0;
			}

			/*if ($pos%3==0) {
				if ($pos>0) $add_g.=  "</tr>";
				$add_g.=  "<tr>";
			}*/

			if (isset($selectedtags[$tab_u['id']]) || isset($_SESSION['dims']['temp_tag'][$typetag][$tab_u['id']]) && $_SESSION['dims']['temp_tag'][$typetag][$tab_u['id']]>0) {
							$check="checked='checked' ";
							$boolcheck=1;

							if (isset($_DIMS['cste'][$tab_u['label']])) {
								// on a une valeur selectionne
								$prefix=substr($tab_u['label'],11);
								if ($listselpays=="") {
										$listselpays=$prefix;
								}
								else {
										$listselpays.=",".$prefix;
								}
							}
			}
			else {
							$check='';
							$boolcheck=0;
			}

			// traduction en langue courante
			if (isset($_DIMS['cste'][$tab_u['label']])) {
				$tab_u['label'] = $_DIMS['cste'][$tab_u['label']];
			}

			if ($tab_u['private'] == 0){
				$add_g.= "	<li style='margin:1px'>".$tab_u['label'];
					if ($typetag <3)
						$add_g.="<img src=\"./common/img/tags-icon.png\" onclick=\"javascript:displayEditTag(event,'".$tab_u['id']."');\">";
					$add_g.="</li>";
			}else{
				$private.= "<li style='margin:1px'>".$tab_u['label'];
				if ($typetag <3)
						$private.="<img src=\"./common/img/tags-icon.png\" onclick=\"javascript:displayEditTag(event,'".$tab_u['id']."');\">";
				$private.="</li>";
			}
			$pos++;
			$col++;
		}

		if ($private == '')
			$add_g .= "</table></div>";
		else
			$add_g .= '	</table>
						<b>'.$_DIMS['cste']['_PRIVATE'].'</b>
						<table width="100%">
							<tr>
								'.$private.'
							</tr>
						</table>
					</div>';

		$add_g .= "<div style=\"clear:both;\">";
		//if ($_SESSION['dims']['current_object']['id_record']>0) {
			if ($typetag==3) {
				$add_g .= '<div style="width:49%;float:left;text-align:center;">'.dims_create_button($_DIMS['cste']['_DIMS_LABEL_MAP'],'./common/img/all.png','renderGeographic(\''.$listselpays.'\');').'</div>';
			}
			$add_g .= '<div style="width:49%;float:left;text-align:center;">'.dims_create_button($_DIMS['cste']['_DIMS_SAVE'],'./common/img/save.gif','document.add_ct_g.submit();').'</div>';
		//}
		$add_g .= '</form>';

	}

	// ajouter des test pour enlever : geographic area (3) et year (4)
		if ($typetag < 3)
	$add_g .= '<div style="width:49%;float:left;text-align:center;">'.dims_create_button($_DIMS['cste']['_DIMS_ADD'],'./common/img/add.gif','javascript:displayCreateTags(event,\''.$typetag.'\');').'</div>';


	?>
	<table style="width:100%;">
		<tr>
			<td style="text-align:center;">
				<?php
				// affichage des trois types de tags
				$toolbar=array();
				foreach ($arraytypetag as $id => $value) {
					$toolbar[$id] = array(
										'title'		=> $value['label'],
										'url'		=> "javascript:updateTypeTag(".$id.")",
										'icon'	=> $value['icon']
									);
				}
				echo $skin->create_toolbar($toolbar,$typetag);

				echo "<div style='position:relative;overflow:auto;height:100%;text-align:left;'>";
	echo $add_g;
	echo "</div>";
				?>
			</td>
		</tr>

	</table>
	<?php



echo "</div>";
?>

