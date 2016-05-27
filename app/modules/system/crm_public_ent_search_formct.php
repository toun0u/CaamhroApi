<?php
echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_LABEL_CT_SEARCH_ENT'], "", "padding-left:15px;font-size:12px;", "./common/img/factory.gif", "26", "26", "-15px", "0px", "javascript:void(0);", "javascript:document.location.href='".$dims->getScriptEnv()."?disp=ent';", "");
?>
<div id="lkp" style="float:left;width:100%;display:<? echo $disp_ent; ?>;">
	<div style="float:left;width:45%;">
		<form id="ent_search_ent" name="ent_search_ent" method="post" action="admin.php?cat=<? echo _BUSINESS_CAT_CONTACT; ?>&action=<? echo _BUSINESS_TAB_CONTACTSSEEK; ?>&part=<? echo _BUSINESS_TAB_CONTACTSSEEK; ?>">
		<?
			// Sécurisation du formulaire par token
			require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
			$token = new FormToken\TokenField;
			$token->field("op",		"exec_search_ent");
			$token->field("nb_line","0");
			$tokenHTML = $token->generate();
			echo $tokenHTML;
		?>
		<input type="submit" style="width:0px;height:0px;border:none;display:none;">
		<input type="hidden" name="op" value="exec_search_ent"/>
		<input type="hidden" name="nb_line" value="0"/>
		<table width="100%">
			<tr>
				<td width="100%" align="center" style="vertical-align:top;">
		<?
		$categcour=0;
		$categlabel="";

		$sql =	"
					SELECT		mf.*,mc.label as categlabel, mc.id as id_cat,
								mb.protected,mb.name as namefield,mb.label as titlefield
					FROM		dims_mod_business_meta_field as mf
					INNER JOIN	dims_mb_field as mb
					ON			mb.id=mf.id_mbfield
					AND			(mf.option_search=1
					OR			mb.name='intitule')
					LEFT JOIN	dims_mod_business_meta_categ as mc
					ON			mf.id_metacateg=mc.id
					WHERE		mf.id_object = ".dims_const::_SYSTEM_OBJECT_TIERS."
					ORDER BY	mc.position, mf.position
					";

		$rs_fields=$db->query($sql);

		while ($fields = $db->fetchrow($rs_fields)) {
			// test si utilise ou non
			if ($categcour!=$fields['id_metacateg']) {
				if ($categcour>0) {
					echo "</table>".$skin->close_widgetbloc();
				}

				$categcour=$fields['id_metacateg'];
				if(substr($fields['categlabel'], 0,3) == "IDE") {
					$categlabel=$_DIMS['cste']['_DIMS_PERS_IDENTITY'];
				}
				elseif(substr($fields['categlabel'], 0,3) == "COO") {
					$categlabel=$_DIMS['cste']['_DIMS_PERS_COORD'];
				}
				else
					$categlabel=$fields['categlabel'];

				echo $skin->open_widgetbloc($categlabel, 'width:100%;', 'padding-bottom:0px;padding-left:10px;vertical-align:bottom;font-size:12px;', '','', '', '', '', '', '', '')."
							<table width=\"100%\">";

			}

			// on affiche maintenant les champs attach�s � la rubrique
			if (isset($_DIMS['cste'][$fields['titlefield']])) $namevalue= $_DIMS['cste'][$fields['titlefield']];
			else $namevalue=$fields['name'];

			$namefield=$fields['namefield'];

			// alimentation de la structure de filtre
			$arrayfield_control[]=$namefield;
			?>
			<tr>
				<td align="right" width="30%"><? echo ucfirst(strtolower($namevalue)); ?> </td>
				<td align="left" width="25%">
			<?
			$curvalue="";
			switch($fields['type']) {
				case 'select':
					// v�rification si description = <nace ou country
					if ($fields['enum']!='') {
						$sql_sa = "SELECT * FROM dims_mod_business_enum WHERE type LIKE '".dims_sql_filter($fields['enum'])."' AND lang =".$_SESSION['dims']['currentlang'];

						$res_sa = $db->query($sql_sa);
						while($tab = $db->fetchrow($res_sa)) {
							if ($tab['libelle']!="") {
								$code = $tab['code'];
								$elem=array();
								if ($code!="") $elem['value']=$code.' - '.dims_strcut(html_entity_decode(strtolower($tab['libelle'])),100);
								else $elem['value']=dims_strcut(($tab['libelle']),100);
								$elem['key']=$tab['libelle'];
								$values[]=$elem;
							}
						}
						$fields['values']=$values;
					}

					if(!empty($_SESSION['business']['ent_search_ent'][$namefield])) {
						$replies[$fields['id']][0]=$_SESSION['business']['ent_search_ent'][$namefield];
					}

					if (!is_array($fields['values'])) {
						$values = explode('||',$fields['values']);
					}
					?>
					<select class="select" name="ct_<? echo $namefield; ?>" id="ct_<? echo $namefield; ?>" class="select">
					<?
						$token->field("ct_$namefield");
					?>
					<option></option>
					<?
					if (!is_array($fields['values'])) {

						foreach($values as $value) {
							$selected = (isset($replies[$fields['id']]) && $value == $replies[$fields['id']][0])? 'selected' : '';
							?>
							<option <? echo $selected; ?> value="<? echo $value; ?>"><? echo $value; ?></option>
							<?
						}
					}
					else {
						// afichage specifique pour code nace, pays, etc.
						foreach($values as $k=> $elem) {
							if (isset($elem['value']) && isset($elem['key'])) {
								$value=$elem['value'];
								$key=$elem['key'];
								$selected = (isset($replies[$fields['id']]) && $key == $replies[$fields['id']][0])? 'selected' : '';
								?>
								<option <? echo $selected; ?> value="<? echo $key; ?>"><? echo $value; ?></option>
								<?
							}
						}
					}
					?>
					</select>
					<?
					break;
				default:
					if(!empty($_SESSION['business']['ent_search_ent'][$namefield])) $curvalue=$_SESSION['business']['ent_search_ent'][$namefield];
					echo "<input type=\"text\" id=\"ent_".$namefield."\" size=\"35\" name=\"ent_".$namefield."\" value=\"".$curvalue."\"/>";
					$token->field("ent_".$namefield);
					break;
			}
			?>
				</td>
				<td align="left">
				</td>
			</tr>
			<?
		}

		// construction de la liste des workspaces disponibles
		$array_workspace=array();

		foreach ($dims->getAdminWorkspaces() as $key => $value) {
			$array_workspace[$key]=$value['label']; //ucfirst(strtolower(trim($value['label'])));
		}

		asort($array_workspace);
		echo '	<tr>
			<td width="30%" align="right">'.$_DIMS['cste']['_WORKSPACE'].'
			<td align="left">';
		echo "<select name=\"id_workspace_from\" style=\"width:220px;\">";
		$token->field("id_workspace_from");
		echo "<option value=\"\">".$_DIMS['cste']['_DIMS_ALL_WORKSPACES']."</option>";
		foreach ($array_workspace as $key => $label) {
			$select=(isset($_SESSION['business']['ent_search_ent']['id_workspace_from']) && $_SESSION['business']['ent_search_ent']['id_workspace_from']==$key) ? "selected" : "";
			echo "<option value=\"".$key."\" $select>".$label."</option>";
		}
		echo "</select>";
		echo '</tr>';

		echo "</table>".$skin->close_widgetbloc();
		?>
				</td>
			</tr>
			<tr>
				<td align="center">
				<?
				echo dims_create_button($_DIMS['cste']['_SEARCH'],"./common/img/search.png","javascript:document.ent_search_ent.submit();","search2",'','');
				echo dims_create_button($_DIMS['cste']['_DIMS_LABEL_SUPPR_SEARCH_VAL'],'./common/img/undo.gif','javascript:delete_search(\'ent\')','','');
				?>
				</td>
			</tr>
		</table>
		<?
			$tokenHTML = $token->generate();
			echo $tokenHTML;
		?>
		</form>
	</div>

	<div style="float:left;width:55%">
	<?php
		if(isset($_POST) && $op=="exec_search_ent") {
			require_once(DIMS_APP_PATH . '/modules/system/crm_public_ent_execsearchct.php');
		}
	?>
	</div>
</div>
	<?php	echo $skin->close_simplebloc() ?>
