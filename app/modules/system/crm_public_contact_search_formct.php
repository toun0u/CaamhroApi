<?php
echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_LABEL_CT_SEARCH_PERS'], "", "padding-left:15px;font-size:12px;", "./common/img/contact.png", "26", "26", "-15px", "0px", "javascript:void(0);", "javascript:document.location.href='".$dims->getScriptEnv()."?disp=pers';", "");
?>
<div id="lkp" style="float:left;width:100%;display:<? echo $disp; ?>;">
	<div style="float:left;width:45%;">
		<form id="form_search_ct" name="form_search_ct" method="post" action="admin.php?cat=<? echo _BUSINESS_CAT_CONTACT; ?>&action=<? echo _BUSINESS_TAB_CONTACTSSEEK; ?>&part=<? echo _BUSINESS_TAB_CONTACTSSEEK; ?>">
		<?
			// Sécurisation du formulaire par token
			require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
			$token = new FormToken\TokenField;
			$token->field("op",	"exec_search");
			$token->field("nb_line");
			$tokenHTML = $token->generate();
			echo $tokenHTML;
		?>
		<input type="submit" style="width:0px;height:0px;border:none;display:none;">
		<input type="hidden" name="op" value="exec_search"/>
		<input type="hidden" name="nb_line" value="0"/>
		<table width="100%">
			<tr>
				<td width="100%" align="center" style="vertical-align:top;">
		<?
		$categcour=0;
		$categlabel="";

		$sql =	"	SELECT		mf.*,mc.label as categlabel, mc.id as id_cat,
								mb.protected,mb.name as namefield,mb.label as titlefield
					FROM		dims_mod_business_meta_field as mf
					INNER JOIN	dims_mb_field as mb
					ON			mb.id=mf.id_mbfield
					AND			(mf.option_search=1
					OR			mb.name='firstname'
					OR			mb.name='lastname')
					LEFT JOIN	dims_mod_business_meta_categ as mc
					ON			mf.id_metacateg=mc.id
					WHERE		mf.id_object = :idobject
					ORDER BY	mc.position, mf.position
					";

		$rs_fields=$db->query($sql, array(
			':idobject' => dims_const::_SYSTEM_OBJECT_CONTACT
		));

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
						$sql_sa = "SELECT * FROM dims_mod_business_enum WHERE type LIKE :enum AND lang = :lang ";

						$res_sa = $db->query($sql_sa, array(
							':enum' => $fields['enum'],
							':lang' => $_SESSION['dims']['currentlang']
						));
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

					if(!empty($_SESSION['business']['search_ct'][$namefield])) {
						$replies[$fields['id']][0]=$_SESSION['business']['search_ct'][$namefield];
					}

					if (!is_array($fields['values'])) {
						$values = explode('||',$fields['values']);
					}
					?>
					<select class="select" name="ct_<? echo $namefield; ?>" id="ct_<? echo $namefield; ?>" class="select">
					<? $token->field($namefield); ?>
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
					if(!empty($_SESSION['business']['search_ct'][$namefield])) $curvalue=$_SESSION['business']['search_ct'][$namefield];
					echo "<input type=\"text\" id=\"ct_".$namefield."\" size=\"35\" name=\"ct_".$namefield."\" value=\"".$curvalue."\"/>";
					$token->field("ct_".$namefield);
					break;
			}
			?>
				</td>
				<td align="left">
				</td>
			</tr>
			<?
		}
		echo "</table>".$skin->close_widgetbloc();
		if ($workspace->fields['contact_activeent']==1 && $disp_t!='ent') {
			echo $skin->open_widgetbloc(strtoupper($_DIMS['cste']['_DIMS_LABEL_LINK_ENT']), 'width:100%;', 'padding-bottom:0px;padding-left:10px;vertical-align:bottom;font-size:12px;', '','', '', '', '', '', '', '');
			?>
				<table width="100%">
					<tr>
						<td align="right" width="30%"><? echo ucfirst($_DIMS['cste']['_DIMS_LABEL_ENT_NAME']); ?> </td>
						<td align="left" width="25%">
							<input type="text" id="ent_intitule" size="35" name="ent_intitule" value="<? if(!empty($_SESSION['business']['search_ent']['intitule'])) echo $_SESSION['business']['search_ent']['intitule']; ?>"/>
							<? $token->field("ent_intitule"); ?>
						</td>
						<td align="left">
						</td>
					</tr>
					<tr>
						<td align="right"><? echo $_DIMS['cste']['_DIMS_LABEL_CITY']; ?> </td>
						<td align="left">
							<input type="text" id="ent_ville" size="35" name="ent_ville" value="<? if(!empty($_SESSION['business']['search_ent']['ville'])) echo $_SESSION['business']['search_ent']['ville']; ?>"/>
							<? $token->field("ent_ville"); ?>
						</td>
						<td align="left">
						</td>
					</tr>
					<tr>
						<td align="right"><? echo $_DIMS['cste']['_DIMS_LABEL_COUNTRY']; ?> </td>
						<td align="left">
							<input type="text" id="ent_pays" size="35" name="ent_pays" value="<? if(!empty($_SESSION['business']['search_ent']['pays'])) echo $_SESSION['business']['search_ent']['pays']; ?>"/>
							<? $token->field("ent_pays"); ?>
						</td>
						<td align="left">
						</td>
					</tr>
					<tr>
						<td align="right"><? echo $_DIMS['cste']['_DIMS_LABEL_FUNCTION']; ?> </td>
						<td align="left">
							<select style="width:100%;" id="lke_function" name="lke_function" value="<? if(!empty($_SESSION['business']['search_lkent']['function'])) echo $_SESSION['business']['search_lkent']['function']; ?>">
							<? $token->field("lke_function"); ?>
							<?
							// on va construire la liste des fonctions disponibles
							$sqlf = "SELECT DISTINCT function FROM dims_mod_business_tiers_contact WHERE function NOT LIKE '' ORDER BY function";
							$resf = $db->query($sqlf);
							echo '<option value="">--</option>';
							while($tab_f = $db->fetchrow($resf)) {
								$sel = '';
								if(!empty($_SESSION['business']['search_lkent']['function']) && $tab_f['function'] == $_SESSION['business']['search_lkent']['function']) $sel = 'selected="selected"';
								echo '<option value="'.$tab_f['function'].'" '.$sel.'>'.$tab_f['function'].'</option>';
							}
							?>
							</select>
						</td>
						<td align="left">
						</td>
					</tr>
				</table>
			<?
			echo $skin->close_widgetbloc();
		}

		echo $skin->open_widgetbloc(strtoupper($_DIMS['cste']['_DIMS_MORE_OPTIONS']), 'width:100%;', 'padding-bottom:0px;padding-left:10px;vertical-align:bottom;font-size:12px;', '','', '', '', '', '', '', '');
		echo '<table style="width:100%;">';
		// test si presence de groupe de personnes

		$sqlu = "SELECT			t.id,
						t.tag as label,
						t.private,
						t.id_user as id_user_create,
						t.id_workspace
			FROM		dims_tag as t
			WHERE			t.type=1 and ((t.id_workspace = :workspaceid and private=0) OR (t.id_user = :userid and private=1))";

		$resu = $db->query($sqlu, array(
			':workspaceid'	=> $_SESSION['dims']['workspaceid'],
			':userid'		=> $_SESSION['dims']['userid']
		));
		if($db->numrows($resu) > 0) {
			echo '	<tr>
						<td width="30%" align="right">'.$_DIMS['cste']['_DIMS_LABEL_CT_GROUP'].' </td>
						<td align="left">
							<select id="id_tag" name="id_tag"  style="width:220px;">
							<option value=""></option>';
						$token->field("id_tag");
						while($tab_u = $db->fetchrow($resu)) {
							$selected = (isset($_POST['id_tag']) && $_POST['id_tag']==$tab_u['id']) ? 'selected' : '';
							echo '<option '.$selected.' value="'.$tab_u['id'].'">'.$tab_u['label'].'</option>';
						}
						echo '</select>';
			echo '</td></tr>';
		}
		/*
		$sql= "SELECT count(ti.id) as cpte,t.id,t.tag FROM `dims_tag` as t
		inner join dims_tag_index as ti on ti.id_tag=t.id and ti.id_workspace=".$_SESSION['dims']['workspaceid']."
		and ti.id_object=".dims_const::_SYSTEM_OBJECT_CONTACT."
		where t.private = 0 and t.id_workspace=".$_SESSION['dims']['workspaceid']."
		group by tag order by cpte desc";

		$res=$db->query($sql);
		$listags='';
		if ($db->numrows($res)>0) {
			echo '	<tr>
				<td width="40%">'.$_DIMS['cste']['_DIMS_LABEL_TAGS'].' :</td>
				<td>
					<select id="id_tag" name="id_tag"  style="width:250px;">
					<option value=""></option>';
				while ($f=$db->fetchrow($res)) {
					$selected = (isset($_POST['id_tag']) && $_POST['id_tag']==$f['id']) ? 'selected' : '';
					echo '<option '.$selected.' value="'.$f['id'].'">'.$f['tag'].'</option>';
				}
				echo '</select>';
			echo '</td></tr>';
		}
		*/
		// affichage du filtre par personne
		// construction de la liste des contacts
		$work=new workspace();
		$work->open($_SESSION['dims']['workspaceid']);
		$lstusers=$work->getusers();
		$check=(isset($choice_from) && $choice_from==0) ? 'checked' : '';

		if (!isset($_SESSION['business']['search_ct']['id_user_from'])) $_SESSION['dims']['search_ct']['id_user_from']='';

		echo '	<tr>
			<td width="30%" align="right">'.$_DIMS['cste']['_DIMS_LABEL_CONTACTS'].'
			<td align="left">';
		echo "<select name=\"id_user_from\" style=\"width:220px;\">";
		$token->field("id_user_from");
		echo "<option value=\"\"></option>";
		foreach ($lstusers as $id=>$user) {
			$select=(isset($_SESSION['business']['search_ct']['id_user_from']) && $_SESSION['business']['search_ct']['id_user_from']==$user['id_contact']) ? "selected" : "";
			echo "<option value=\"".$user['id_contact']."\" $select>".strtoupper(substr($user['firstname'],0,1)).". ".$user['lastname']."</option>";
		}
		echo "</select>";


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
			$select=(isset($_SESSION['business']['search_ct']['id_workspace_from']) && $_SESSION['business']['search_ct']['id_workspace_from']==$key) ? "selected" : "";
			echo "<option value=\"".$key."\" $select>".$label."</option>";
		}
		echo "</select>";
		echo '</tr></table>';

		echo $skin->close_widgetbloc();
		?>
					</td>
			</tr>
			<tr>
					<td align="center">
					<?
					echo dims_create_button($_DIMS['cste']['_SEARCH'],"./common/img/search.png","javascript:document.form_search_ct.submit();","search2",'','');
					echo dims_create_button($_DIMS['cste']['_DIMS_LABEL_SUPPR_SEARCH_VAL'],'./common/img/undo.gif','javascript:delete_search(\'pers\')','','');
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
		if(isset($_POST) && $op=="exec_search") {
			require_once(DIMS_APP_PATH . '/modules/system/crm_public_contact_execsearchct.php');
		}
	?>
	</div>
</div>
	<?php	echo $skin->close_simplebloc() ?>
