<?php /* MODIFICATION NICOLAS POUR LISTE DES USERS -> DEBUT */ ?>

<script type="text/javascript">
	function deplacer(dep_id, ar_id) {
		liste_dep = document.getElementById(dep_id)
		liste_ar = document.getElementById(ar_id);
		nbre_option = document.getElementById('liste_users').length;

		for(a=0; a<nbre_option; a++){
			if(liste_dep.options[a].selected == true) {
				var text_option = liste_dep.options[a].text;
				var value_option = liste_dep.options[a].value;
				<?php /* document.getElementById('liste_users').options[a].selected = true; */ ?>

				var text_option = liste_dep.options[a].text;
				var value_option = liste_dep.options[a].value;
				var not_present = true;
				for (i=0; i < liste_ar.length; i++) {
					if (liste_ar.options[i].value == value_option) {
						not_present = false;
						break;
					}
				}

				if (not_present){
					var o = document.createElement('option');
					var t = document.createTextNode(text_option);
					o.setAttribute('value',value_option);
					o.setAttribute('id',value_option);
					o.appendChild(t);
					liste_ar.appendChild(o);
				}
				<?php
				/*
				else {
					alert('Cet �l�ment a d�j� �t� s�lectionn�...');
				}*/ ?>
			}
		}
	}

	function annuler(id_liste) {
		nbre_option = document.getElementById(id_liste).length;
		for(a=(nbre_option-1); a>=0; a--){
			<?php /* document.getElementById(id_liste).options[a].selected = true; */ ?>
			if(document.getElementById(id_liste).options[a].selected == true) {
				id = document.getElementById(id_liste).options[a].id;

				var optionToDel = document.getElementById(id);
				optionToDel.parentNode.removeChild(optionToDel);
			}
		}
	}

	function deplacer_all(dep_id, ar_id) {
		liste_dep = document.getElementById(dep_id)
		liste_ar = document.getElementById(ar_id);
		nbre_option = document.getElementById('liste_users').length;

		for(a=0; a<nbre_option; a++){
			<?php /* document.getElementById('liste_users').options[a].selected = true; */ ?>

			var text_option = liste_dep.options[a].text;
			var value_option = liste_dep.options[a].value;
			var not_present = true;
			for (i=0; i < liste_ar.length; i++) {
				if (liste_ar.options[i].value == value_option) {
					not_present = false;
					break;
				}
			}

			if (not_present){
				var o = document.createElement('option');
				var t = document.createTextNode(text_option);
				o.setAttribute('value',value_option);
				o.setAttribute('id',value_option);
				o.appendChild(t);
				liste_ar.appendChild(o);
			}
		}
	}

	function annuler_all(id_liste) {
		nbre_option = document.getElementById(id_liste).length;
		for(a=(nbre_option-1); a>=0; a--){
			<?php /* document.getElementById(id_liste).options[a].selected = true; */ ?>
			id = document.getElementById(id_liste).options[a].id;

			var optionToDel = document.getElementById(id);
			optionToDel.parentNode.removeChild(optionToDel);
		}
	}
</script>

<?php /* MODIFICATION NICOLAS POUR LISTE DES USERS -> DEBUT */ ?>

<div style="padding:4px;">
	<p class="dims_va" style="font-weight:bold;">
		<a href="<? echo "$scriptenv?dims_moduletabid="._BUSINESS_TAB_TIERSADD."&cat=1"; ?>"><img border="0" src="./common/modules/business/img/ico_client.gif"><span style="margin-left:4px;"><? echo $_DIMS['cste']['_DIMS_LABEL_LABELTAB_TIERSADD']; ?></span></a>
		<a href="<? echo "$scriptenv?dims_moduletabid="._BUSINESS_TAB_INTERLOCUTEURSADD."&cat=1"; ?>" style="margin-left:4px;"><img border="0" src="./common/modules/business/img/ico_user.gif"><span style="margin-left:4px;"><? echo $_DIMS['cste']['_DIMS_LABEL_LABELTAB_CONTACTADD']; ?></span></a>
	</p>

	<form name="search" action="<? echo "$scriptenv?cat="._BUSINESS_CAT_ACCUEIL; ?>" method="post">
	<?
		// Sécurisation du formulaire par token
		require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
		$token = new FormToken\TokenField;
		$token->field("op", "recherche_rapide");
		$token->field("recherche");
		$tokenHTML = $token->generate();
		echo $tokenHTML;
	?>
	<p class="dims_va" style="padding-top:4px;">
		<input type="hidden" name="op" value="recherche_rapide">
		<span style="font-weight:bold;">Recherche Rapide :</span>
		<input type="text" class="text" name="recherche" style="width:100px;margin-left:4px;">
		<input type="image" src="./common/modules/business/img/ico_loupe.png" style="margin-left:4px;">
	</p>
	</form>

	<p class="dims_va" style="padding-top:4px;">
		<span style="font-weight:bold;">Pr�c�dentes recherches :</span>
		<span><?
		$select =	"
				SELECT	max(timestp) as timestp, id_action, id_record
				FROM	dims_user_action_log
				WHERE	id_user = :iduser
				AND		id_module = :idmodule
				AND		id_action = :idaction
				GROUP BY id_record
				ORDER BY timestp DESC
				LIMIT 0,5
				";

		$rs = $db->query($select, array(
			':iduser' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['userid']),
			':idmodule' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['moduleid']),
			':idaction' => array('type' => PDO::PARAM_INT, 'value' => _BUSINESS_ACTION_RECHERCHERAPIDE)
		));

		$search_list = '';
		while ($fields = $db->fetchrow($rs)) {
			if ($search_list) $search_list .= ', ';
			$search_list .= "<a href=\"$scriptenv?cat="._BUSINESS_CAT_ACCUEIL."&op=recherche_rapide&recherche={$fields['id_record']}&recherche_tiers=on&recherche_interlocuteurs=on&recherche_dossiers=on\">{$fields['id_record']}</a>";
		}

		echo $search_list;
		?>
		</span>
	</p>

</div>
<?php /* MODIFICATION NICOLAS POUR RECHERCHE -> DEBUT */ ?>
<div id="div_recherche_dispo" style="display: none;">
	<table cellpadding="0" cellspacing="0" border="0">
		<tr>
			<td style="padding: 5px 5px 5px 20px; ">
				<form name="form_recherche_dispo" method="post" action="" onsubmit="return false;">
				<?
					// Sécurisation du formulaire par token
					require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
					$token = new FormToken\TokenField;
					$token->field("affiche_users");
					$token->field("recherche_dispo_users");
					$token->field("recherche_dispo_duree");
					$token->field("recherche_dispo_avap_heure_1");
					$token->field("recherche_dispo_avap_minute_1");
					$token->field("recherche_dispo_avap_heure_2");
					$token->field("recherche_dispo_avap_minute_2");
					$token->field("recherche_dispo_nombre_date");
					$token->field("recherche_dispo_jour");
					$tokenHTML = $token->generate();
					echo $tokenHTML;
				?>
					<?php echo $skin->open_simplebloc('','padding:5px;'); ?>
					<TABLE CELLPADDING="2" CELLSPACING="1" STYLE="padding: 0px;">
						<TR>
							<TD ALIGN="right"><? echo $_DIMS['cste']['_DIMS_LABEL_LABEL_USERS']; ?> :&nbsp;</TD>
							<td colspan="3">
								<table cellpadding="0" cellspacing="0">
									<tr>
										<td align="left">
											<select multiple name='affiche_users[]' size="10" style="width:150px;" ondblclick="deplacer('liste_users', 'liste_conserve_users')" id='liste_users'>
											<?php
												$select = " SELECT	distinct (u.id), u.login, u.lastname, u.firstname
															FROM	dims_user u,
																	dims_group_user gu
															WHERE	u.id = gu.id_user
															";

												$result = $db->query($select);

												while ($fields = $db->fetchrow($result)) {
													echo "<option value=\"{$fields['id']}\">".strtoupper($fields['firstname'])." ".ucfirst(strtolower($fields['lastname']))."</option>";
												}
											?>
											</select>
										</td>
										<td align="center" style="width:70px;text-align:center">
											<input type="button" value=">" onclick="deplacer('liste_users', 'liste_conserve_users')"/><br/>
											<input type="button" value=">>" onclick="deplacer_all('liste_users', 'liste_conserve_users')"/><br/>
											<input type="button" value="<<" onclick="annuler_all('liste_conserve_users')"/><br/>
											<input type="button" value="<" onclick="annuler('liste_conserve_users')"/>
										</td>
										<td align="center" style="text-align:center">
											<select multiple name='recherche_dispo_users[]' style="width:150px;" size="10" id='liste_conserve_users' ondblclick="annuler('liste_conserve_users')">
											</select>
										</td>
										<TD ALIGN="right" VALIGN="top" style="width:50px;" ><img height="20px" src="./common/modules/business/img/ok/button_cancel.png" onclick="document.getElementById('div_recherche_dispo').style.display='none';"/></TD>
									</tr>
								</table>
							</td>
						</TR>
						<TR>
							<TD ALIGN="right" valign="middle" colspan="3">&nbsp;</TD>
						</TR>
						<TR>
							<TD ALIGN="right"><? echo $_DIMS['cste']['_DIMS_LABEL_LABEL_TIME']; ?> :&nbsp;</TD>
							<TD ALIGN="LEFT" colspan="2">
								<?php
								$array_heure = array(
											'30' => '30 min',
											'45' => '45 min',
											'60' => '1h00',
											'90' => '1h30',
											'120' => '2h00',
											'180' => '3h00',
											'240' => '4h00',
											'300' => '5h00',
											'360' => '6h00',
											'420' => '7h00',
											'480' => '8h00'
											);
								?>
								<select class="select" name="recherche_dispo_duree">
									<?
									foreach($array_heure as $value => $text)
									{
										if(empty($action->fields['temps_prevu'])) {
											$sel = ($value == '60') ? 'selected' : '';
											echo "<option $sel value=\"$value\">$text</option>
											";
										}
										else {
											$sel = ($action->fields['temps_prevu'] == $value) ? 'selected' : '';
											echo "<option $sel value=\"$value\">$text</option>
											";
										}
									}
									?>
								</select>
							</TD>
						</TR>
						<TR>
							<TD ALIGN="right"><?php echo $_DIMS['cste']['_DIMS_LABEL_LABEL_BETWEEN']; ?> :&nbsp;</TD>
							<TD ALIGN="left" colspan="2">
								<TABLE ALIGN="left">
									<TR>
										<td align="left">
											<select class="select" name="recherche_dispo_avap_heure_1">
												<?php

												for ($h=_PLANNING_H_START;$h<=_PLANNING_H_END;$h++)
												{
													$sel = (_PLANNING_H_START==$h) ? 'selected' : '';
													printf("<option %s value=\"%02d\">%02d</option>",$sel,$h,$h);
												}
												?>
											</select> h
											<select class="select" name="recherche_dispo_avap_minute_1">
												<?php
												for ($m=0;$m<4;$m++)
												{
													$sel = ('0'==$m*15) ? 'selected' : '';
													printf("<option %s value=\"%02d\">%02d</option>",$sel, $m*15, $m*15);
												}
												?>
											</select>
										</td>
										<TD ALIGN="right">&nbsp;<?php echo $_DIMS['cste']['_DIMS_LABEL_LABEL_AND']; ?>&nbsp;</TD>
										<td align="left">
											<select class="select" name="recherche_dispo_avap_heure_2">
												<?php

												for ($h=_PLANNING_H_START;$h<=_PLANNING_H_END;$h++)
												{
													$sel = (_PLANNING_H_END==$h) ? 'selected' : '';
													printf("<option %s value=\"%02d\">%02d</option>",$sel,$h,$h);
												}
												?>
											</select> h
											<select class="select" name="recherche_dispo_avap_minute_2">
												<?php
												for ($m=0;$m<4;$m++)
												{
													$sel = ('0'==$m*15) ? 'selected' : '';
													printf("<option %s value=\"%02d\">%02d</option>",$sel, $m*15, $m*15);
												}
												?>
											</select>
										</td>
									</TR>
								</TABLE>
							</TD>
						<TR>
						<TR>
							<TD ALIGN="RIGHT" ><? echo $_DIMS['cste']['_DIMS_LABEL_LABEL_NB_DATE']; ?> :&nbsp;</TD>
							<TD ALIGN="LEFT">
								<input type="text" class="text" name="recherche_dispo_nombre_date" size="3" value="5">
							</TD>
							<TD ALIGN="RIGHT">
								<INPUT TYPE="image" SRC="./common/modules/business/img/ok/ico_loupe.gif" style="width:30px;margin-left:4px;"
								onclick="dims_xmlhttprequest_formtodiv('index-light.php', 'op=xml_planning_recherche_dispo', document.forms['form_recherche_dispo'], 'result_search', 'recherche_dispo_users[]');">
							</TD>
						</TR>
						<TR>
							<TD ALIGN="right" valign="middle"><? echo $_DIMS['cste']['_DIMS_LABEL_LABEL_DAY']; ?> :&nbsp;</TD>
							<TD ALIGN="LEFT" COLSPAN="2">
								<table>
									<tr>
										<td>
											Lundi
										</td>
										<td>
											<INPUT TYPE="checkbox" NAME="recherche_dispo_jour[]" VALUE="1" checked />
										</td>

										<td>
											Mardi
										</td>
										<td>
											<INPUT TYPE="checkbox" NAME="recherche_dispo_jour[]" VALUE="2" checked />
										</td>

										<td>
											Mercredi
										</td>
										<td>
											<INPUT TYPE="checkbox" NAME="recherche_dispo_jour[]" VALUE="3" checked />
										</td>

										<td>
											Jeudi
										</td>
										<td>
											<INPUT TYPE="checkbox" NAME="recherche_dispo_jour[]" VALUE="4" checked />
										</td>

										<td>
											Vendredi
										</td>
										<td>
											<INPUT TYPE="checkbox" NAME="recherche_dispo_jour[]" VALUE="5" checked />
										</td>
									</tr>
								</table>
							</TD>
						</TR>
					</TABLE>

					<?php echo $skin->close_simplebloc(); ?>
				</form>
			</td>
			<td style="padding: 5px; valign: top;">
				<div id="result_search"></div>
			</td>
		</tr>
	</table>
	<br />
</div>
<?php /* MODIFICATION NICOLAS POUR RECHERCHE -> FIN */ ?>
