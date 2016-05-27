<?

//dans le cas o� on est dans l'ajout (action = _BUSINESS_TAB_CONTACTSTIERS), on affiche pas les infobulles
//$ajout = 0;
$url_action = dims_load_securvalue('action', dims_const::_DIMS_CHAR_INPUT, true, true);
$id_cont = dims_load_securvalue('id_cont', dims_const::_DIMS_CHAR_INPUT, true, true); //l'id contact n'est rempli que si on vient de valider le formulaire d'un nouveau contact dans le cas o� l'on cr�e une entreprise, va servir � savoir si on affiche ou non la zone contact rattach�
//echo "url_action : ".$url_action;
if($url_action != "" || $url_action != _BUSINESS_TAB_CONTACTSTIERS) {
	$ajout = 0;
}
else {
	$ajout = 1;
}

// construction des cat�gories et champs dynamiques
$categcour=0;
// 1ere requete pour les champs attaches aux rubriques g�n�riques
$sql =	"
			SELECT		mf.*,mc.label as categlabel, mc.id as id_cat
			FROM		dims_mod_business_meta_field as mf
			RIGHT JOIN	dims_mod_business_meta_categ as mc
			ON			mf.id_metacateg=mc.id
			AND			mf.id_object = :idobject
			AND			mc.admin=1
			WHERE		mc.id=3
			ORDER BY	mc.position, mf.position
			";
$rubgen=array();
$rubgen_cpteworkspace=0;
$lstworkspace=array();
$rs_fields=$db->query($sql, array(
	':idobject' => dims_const::_SYSTEM_OBJECT_TIERS
));
$color="";
while ($fields = $db->fetchrow($rs_fields)) {
	if (!isset($rubgen[$fields['id_cat']]))  {
		$rubgen[$fields['id_cat']]=array();
		$rubgen[$fields['id_cat']]['label']=$fields['categlabel'];
		if($fields['id'] != '') $rubgen[$fields['id_cat']]['list']=array();
	}

	// on ajoute maintenant les champs dans la liste
	$fields['use']=0;// par defaut non utilise
	$fields['enabled']=array();
	if($fields['id'] != '') $rubgen[$fields['id_cat']]['list'][$fields['id']]=$fields;
}
//dims_print_r($rubgen);
// construction de la liste des usages
$sql= " select		mu.*,
					mf.id_metacateg,
					w.label as labelworkspace
		from		dims_mod_business_meta_use as mu
		INNER JOIN	dims_mod_business_meta_field as mf
		ON			mf.id=mu.id_metafield
		AND			mu.id_object= :idobject
		LEFT JOIN	dims_workspace as w
		ON			w.id=mu.id_workspace
		WHERE		mf.id_metacateg = 3";

$res=$db->query($sql, array(
	':idobject' => dims_const::_SYSTEM_OBJECT_TIERS
));

if ($db->numrows($res)>0) {
	while ($f=$db->fetchrow($res)) {

		if ($f['sharemode']==1) {
			// on doit verifier si le currentworkspace fait parti de la liste
			if ($_SESSION['dims']['workspaceid']==$f['id_workspace']) {
				// on est dans la selection
				$rubgen[$f['id_metacateg']]['list'][$f['id_metafield']]['use']=1;
			}

			// on stocke les workspaces qui l'utilisent
			$rubgen[$f['id_metacateg']]['list'][$f['id_metafield']]['enabled'][$f['id_workspace']]=$f['id_workspace'];
		}
		else {
			// on est a 2 on met le flag a 2
			$rubgen[$f['id_metacateg']]['list'][$f['id_metafield']]['use']=2;
		}

		// construciton de la liste des workspaces
		if (!isset($lstworkspace[$f['id_workspace']])) $lstworkspace[$f['id_workspace']]=$f['labelworkspace'];

		//on veut sacoir s'il y a des workspace pour le partage
		if(!empty($f['id_workspace'])) {
			$rubgen_cpteworkspace++;
		}
	}
}
//dims_print_r($rubgen);
?>
<form action="" method="post">
<?
	// Sécurisation du formulaire par token
	require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
	$token = new FormToken\TokenField;
	$token->field("op",		"save_ent");
	$token->field("action",	_BUSINESS_TAB_CONTACTSTIERS);
	$token->field("ent_intitule");
	$token->field("ent_dirigeant");
	$token->field("ent_presentation");
	$tokenHTML = $token->generate();
	echo $tokenHTML;
?>
<input type="hidden" name="op" value="save_ent">
<input type="hidden" name="action" value="<? echo _BUSINESS_TAB_CONTACTSTIERS;?>">
<table style="width:100%">
	<tr>
		<td align="right" width="30%"><? echo $_DIMS['cste']['_DIMS_LABEL_ENT_NAME']; ?> : </td>
		<td align="left" width="25%">
			<input type="text" id="ent_intitule" size="35" name="ent_intitule" value="<? echo $ent->fields['intitule']; ?>"/>
		</td>
		<td align="left">
		<? if($ajout) { ?>
			<div style="float:left;position:relative;"><a href="#" onclick="affich_blocinfo('inf_titre1');"><img src="./common/img/properties.png"/></a>
			<div id="inf_titre1" style="position:absolute;width:250px;height:100px;left:-10px;top:-10px;display:none;z-index:25;">
				<? echo $skin->open_infobloc('', 'width:100%;', '', './common/img/no.png','21px', '21px', '-7px', '-5px', '#', 'javascript:close_blocinfo(\'inf_titre1\');', ''); ?>
				<div style="padding:2px;color:#cccccc;">Cr&eacute;&eacute; le 14/02/09 par Patrick Nourrissier (LFB)</div>
				<? echo $skin->close_infobloc(); ?>
			</div>
			</div>
		<? } ?>
		</td>
	</tr>
	<tr>
		<td align="right"><? echo $_DIMS['cste']['_DIMS_LABEL_ENT_DIR']; ?> : </td>
		<td align="left">
			<input type="text" id="ent_dirigeant" size="35" name="ent_dirigeant" value="<? echo $ent->fields['dirigeant']; ?>"/>
		</td>
		<td align="left">
		<? if($ajout) { ?>
			<div style="float:left;position:relative;"><a href="#" onclick="affich_blocinfo('inf_titre1');"><img src="./common/img/properties.png"/></a>
			<div id="inf_titre1" style="position:absolute;width:250px;height:100px;left:-10px;top:-10px;display:none;z-index:25;">
				<? echo $skin->open_infobloc('', 'width:100%;', '', './common/img/no.png','21px', '21px', '-7px', '-5px', '#', 'javascript:close_blocinfo(\'inf_titre1\');', ''); ?>
				<div style="padding:2px;color:#cccccc;">Cr&eacute;&eacute; le 14/02/09 par Patrick Nourrissier (LFB)</div>
				<? echo $skin->close_infobloc(); ?>
			</div>
			</div>
		<? } ?>
		</td>
	</tr>
	<tr>
		<td align="right"><? echo $_DIMS['cste']['_DIMS_LABEL_ENT_PRES']; ?> : </td>
		<td align="left">
			<textarea id="ent_presentation" cols="33" rows="5" name="ent_presentation"><? echo $ent->fields['presentation']; ?></textarea>
		</td>
		<td align="left">
		<? if($ajout) { ?>
			<div style="float:left;position:relative;"><a href="#" onclick="affich_blocinfo('inf_titre1');"><img src="./common/img/properties.png"/></a>
			<div id="inf_titre1" style="position:absolute;width:250px;height:100px;left:-10px;top:-10px;display:none;z-index:25;">
				<? echo $skin->open_infobloc('', 'width:100%;', '', './common/img/no.png','21px', '21px', '-7px', '-5px', '#', 'javascript:close_blocinfo(\'inf_titre1\');', ''); ?>
				<div style="padding:2px;color:#cccccc;">Cr&eacute;&eacute; le 14/02/09 par Patrick Nourrissier (LFB)</div>
				<? echo $skin->close_infobloc(); ?>
			</div>
			</div>
		<? } ?>
		</td>
	</tr>
	<?
		if (isset($rubcour['list']) && !empty($rubcour['list']) && $rubgen_cpteworkspace>0) {
	?>
	<tr>
		<td colspan="3" style="border:1px dotted #40567E;">
			<table>
				<?
				// construction des champs dynamiques pour cette rubrique generique

					echo '<tr><td><b><u>'.$_DIMS['cste']['_MOBILE'].'</u> : </b></td></tr>';
					foreach ($rubcour['list'] as $fields) {
						if ($fields['option_needed']) $oblig=" *";
						else $oblig="";

						echo "<tr><td align=\"right\" width=\"30%\">".$fields['name'].$oblig."</td>";
						echo "<td width=\"20%\">";

						// construction du reply eventuel
						if (isset($ent->fields['field'.$fields['fieldname']]) ) {
							$replies[$fields['id']] = explode('||',$ent->fields['field'.$fields['fieldname']]);
						}

						// appel du rendu
						include DIMS_APP_PATH . '/modules/system/crm_business_model_metafield.php';

						echo "</td><td></td></tr>";
					}

				?>
			</table>
		</td>
	</tr>
	<?
		}
	?>
	<tr>
		<td colspan="3">
			<table width="100%">
				<tr>
					<td align="center"><input type="submit" value="<? echo $_DIMS['cste']['_DIMS_SAVE']; ?>"/></td>
				</tr>
			</table>
		</td>
	</tr>
</table>
