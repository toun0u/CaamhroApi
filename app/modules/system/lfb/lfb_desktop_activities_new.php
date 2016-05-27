<div style="width:100%;">
	<table style="width:99%;" cellpadding="0" cellspacing="0">
	<tr>
		<td style="width:100%;height:60px;" valign="top">

			<div style="width:100%;height:160px;display:block;">
				<span style="text-align:center;width:100%;">
					<form name="form_view_date" action="" method="POST">
					<?php
						// Sécurisation du formulaire par token
						require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
						$token = new FormToken\TokenField;
						$token->field("desktop_view_date");
						$tokenHTML = $token->generate();
						echo $tokenHTML;
						// mode de vue cette semaine
						// 15 jours, 1 mois, 3 mois
						if (!isset($_SESSION['dims']['desktop_view_date'])) $_SESSION['dims']['desktop_view_date']=0;
						$desktop_view_date=dims_load_securvalue('desktop_view_date',dims_const::_DIMS_CHAR_INPUT,true,true,false,&$_SESSION['dims']['desktop_view_date'],0);
						$tab=array();
						$tab[]=$_DIMS['cste']['_DIMS_LABEL_THIS_WEEK'];
						$tab[]=$_DIMS['cste']['_DIMS_LABEL_15_DAYS'];
						$tab[]=$_DIMS['cste']['_DIMS_LABEL_THIS_MONTH'];
						$tab[]=$_DIMS['cste']['_DIMS_LABEL_3_MONTHS'];

						foreach($tab as $i=>$choiceview) {
							if ($i==$_SESSION['dims']['desktop_view_date']) $select="checked=\"checked\"";
							else $select="";

							echo "<input type=\"radio\" name=\"desktop_view_date\" value=\"$i\" onclick=\"javascript:document.form_view_date.submit();\" ".$select.">".$choiceview."&nbsp;";
						}
					?>
					</form>
				</span>
				<span style="width: 100%; display: block; float: left;text-align:center; font-size: 14px; color: rgb(186, 186, 186);font-weight: bold;">
				<?
				switch ($_SESSION['dims']['desktop_view_date']) {
						case 0:
							$date_since2 = date("Ymd",mktime(0, 0, 0, date("m"), date("d")-7, date("Y")));
							$date_since = date("d/m/Y",mktime(0, 0, 0, date("m"), date("d")-7, date("Y")));
							break;
						case 1:
							$date_since2 = date("Ymd",mktime(0, 0, 0, date("m"), date("d")-15, date("Y")));
							$date_since = date("d/m/Y",mktime(0, 0, 0, date("m"), date("d")-15, date("Y")));
							break;
						case 2:
							$date_since2 = date("Ymd",mktime(0, 0, 0, date("m"), date("d")-31, date("Y")));
							$date_since = date("d/m/Y",mktime(0, 0, 0, date("m"), date("d")-31, date("Y")));
							break;
						case 3:
							$date_since2 = date("Ymd",mktime(0, 0, 0, date("m"), date("d")-90, date("Y")));
							$date_since = date("d/m/Y",mktime(0, 0, 0, date("m"), date("d")-90, date("Y")));
							break;
					}

				echo $_DIMS['cste']['_DIMS_LABEL_SINCE']." ".$date_since;
				?>
				</span>
				<span style="width: 100%; display: block; float: left;">
					<table style="width:100%;">
						<tr>
							<td style="width:33%">
								<span style="width: 15%; display: block; float: left;">
									<img border="0" src="./common/templates/backoffice<? echo $_SESSION['dims']['template_name']; ?>/media/contact32.png"/>
								</span>
								<span style="width: 80%; display: block; float: left; font-size: 16px; color: rgb(186, 186, 186);font-weight: bold;margin-top:10px;margin-left:5px;">
								<? echo $_DIMS['cste']['_DIMS_LABEL_CONTACTS']; ?>
								</span>
							</td>
							<td style="width:33%">
								<span style="width: 15%; display: block; float: left;">
									<img border="0" src="./common/templates/backoffice<? echo $_SESSION['dims']['template_name']; ?>/media/factory_32.png"/>
								</span>
								<span style="width: 80%; display: block; float: left; font-size: 16px; color: rgb(186, 186, 186);font-weight: bold;margin-top:10px;margin-left:5px;">
								<? echo $_DIMS['cste']['_DIMS_LABEL_GROUP_LIST']; ?>
								</span>
							</td>
							<td style="width:33%" rowspan="3">
								<span style="width: 15%; display: block; float: left;">
									<img border="0" src="./common/templates/backoffice<? echo $_SESSION['dims']['template_name']; ?>/media/planning32.png"/>
								</span>
								<span style="width: 85%; display: block; float: left; font-size: 16px; color: rgb(186, 186, 186); font-weight: bold;margin-top:10px;">
									<?
									echo $_DIMS['cste']['_DIMS_LABEL_EVENTS'];
									?>
								</span>
								<span style="width: 100%; display: block; float: left;clear:both;">
									<?
										//Recherche des evenements a venir
										$sql = 'SELECT
													a.id AS id_evt,
													a.libelle,
													a.typeaction,
													a.datejour,
													a.heuredeb,
													a.heurefin,
													a.timestp_modify,
													a.description,
													u.id AS id_user,
													u.lastname,
													u.firstname,
													u.id_contact
												FROM
													dims_mod_business_action a
												INNER JOIN
													dims_user u
													ON
														u.id = a.id_user
												WHERE
													a.datejour > CURDATE()
												AND
													a.type = :type
												AND
													a.id_parent = 0';

										$ressource = $db->query($sql, array(
											':type' => dims_const::_PLANNING_ACTION_EVT
										));
										if ($db->numrows($ressource)>0) {
											echo "<a href=\"javascript:void(0);\" onclick=\"desktopViewDetailContact(events);\">".$db->numrows($ressource)." ".$_DIMS['cste']['_DIMS_LABEL_EVENTS']."</a>";
										}
										else {
											echo $_DIMS['cste']['_DIMS_LABEL_NO_EVENT'];
										}
									?>
								<?
									echo "<br><a href=\"admin.php?dims_mainmenu=".dims_const::_DIMS_MENU_HOME."&dims_desktop=block&dims_action=public&submenu=".dims_const::_DIMS_SUBMENU_EVENT."&action=view_admin_events&ssubmenu="._DIMS_ADMIN_EVENTS_INSCR."\"><img src=\"./common/img/configure.png\" alt=\"admin\">".$_DIMS['cste']['_DIMS_EVENT_ADMIN_SELF']."</a>";
									?>
								</span>

								<?
								// partie gestion de projets
								if (isset($currentworkspace['activeproject']) && $currentworkspace['activeproject']==1) {
								?>
								<span style="width: 15%; display: block; float: left;margin-top:10px;">
									<img border="0" src="./common/templates/backoffice<? echo $_SESSION['dims']['template_name']; ?>/media/project32.png"/>
								</span>
								<span style="width: 85%; display: block; float: left; font-size: 16px; color: rgb(186, 186, 186); font-weight: bold;margin-top:20px;">
									<?
									echo $_DIMS['cste']['_LABEL_PROJECTS'];
									?>
								</span>
								<span style="width: 100%; display: block; float: left;clear:both;">
									<?
										/* Requ�te SQL de selection */
										$sql = 	"select 	p.id,
															p.label,
															p.progress,
															u1.lastname,
															u1.firstname,
															concat(u2.lastname, ' ', u2.firstname) as resp,
															p.date_start,
															p.date_end,
															p.state
												from 	dims_project p,
														dims_user u1,
														dims_user u2
												where 	p.id_create=u1.id
												and 	p.id_resp=u2.id ".$where."".$orderby."";


										/* On execute la requete */

										$ressource = $db->query($sql);
										if ($db->numrows($ressource)>0) {
											echo "<a href=\"".$dims->getScriptEnv()."?dims_action=public&dims_desktop=block&dims_mainmenu=".dims_const::_DIMS_MENU_PROJECTS."&projectmenu=2\">".$db->numrows($ressource)." ".$_DIMS['cste']['_LABEL_PROJECTS']."</a>";
										}
										else {
											echo $_DIMS['cste']['_DIMS_LABEL_NO_PROJECT'];
										}
									?>
										</span>
									<?
									}
								?>
							</td>
						</tr>
					<?

					$sql_p = "	SELECT			c.firstname, c.lastname, c.id as id_pers, c.date_create,
												u.id as id_creator, u.lastname as name_creator, u.firstname as pren_creator
								FROM			dims_mod_business_contact c
								INNER JOIN		dims_mod_business_contact u
								ON				u.id = c.id_user_create
								WHERE			c.date_create >= :datesince2
								AND				c.inactif != 1
								ORDER BY		c.date_create DESC, c.lastname, c.firstname";

					$res_p = $db->query($sql_p, array(
						'datesince2' => $date_since2."000000"
					));
					$nb_resp = $db->numrows($res_p);

					//FICHES MODIFIEES : selection des personnes et de leurs entreprises
					$sql_pmod = "	SELECT			distinct c.firstname, c.lastname, c.id as id_pers, c.timestp_modify,
													u.id as id_creator, u.lastname as name_creator, u.firstname as pren_creator
									FROM			dims_mod_business_contact c
									LEFT JOIN		dims_mod_business_contact u
									ON				u.id = c.id_user_create
									WHERE			c.timestp_modify >= :datesince2
									AND				c.inactif != 1
									ORDER BY		c.timestp_modify DESC, c.lastname, c.firstname";

					$res_pmod = $db->query($sql_pmod, array(
						'datesince2' => $date_since2."000000"
					));
					$nb_respmod = $db->numrows($res_pmod);

					// fiches en veille
					$sql_f = "SELECT * FROM dims_mod_business_ct_watch WHERE id_user = :userid and id_personne>0";
					$res_f = $db->query($sql_f, array(
						':userid' => $_SESSION['dims']['userid']
					));
					$nb_veille = $db->numrows($res_f);

					/*******************************************************/
					// calcul entreprise

					//NOUVELLES FICHES : selection des personnes et de leurs entreprises
					$sql_p = "	SELECT			t.intitule, t.id as id_tiers, t.ville, t.date_creation,
												c.id as id_creator, c.lastname as name_creator, c.firstname as pren_creator
								FROM			dims_mod_business_tiers t
								INNER JOIN 		dims_user u
								ON 				u.id = t.id_user_create
								INNER JOIN		dims_mod_business_contact c
								ON				c.id = u.id_contact
								WHERE			t.date_creation >= :datesince2
								AND				t.inactif != 1
								ORDER BY		t.date_creation DESC, t.intitule";

					$res_p = $db->query($sql_p, array(
						'datesince2' => $date_since2."000000"
					));
					$nb_resp_ent = $db->numrows($res_p);

					//FICHES MODIFIEES : selection des personnes et de leurs entreprises
					$sql_pmod = "	SELECT			t.intitule, t.id as id_tiers, t.timestp_modify, t.ville,
													u.id as id_creator, u.lastname as name_creator, u.firstname as pren_creator
									FROM			dims_mod_business_tiers t
									INNER JOIN 		dims_user u
									ON 				u.id = t.id_user_create
									INNER JOIN		dims_mod_business_contact c
									ON				c.id = u.id_contact
									WHERE			t.timestp_modify >= :datesince2
									AND				t.inactif != 1
									ORDER BY		t.timestp_modify DESC, t.intitule";

					$res_pmod = $db->query($sql_pmod, array(
						'datesince2' => $date_since2."000000"
					));
					$nb_respmod_ent = $db->numrows($res_pmod);

					echo "<tr>";
					switch ($nb_resp) {
						case 0:
							echo "<td><span style=\"width:16px;margin-right:4px;display:block;float:left;\"><img src=\"./common/img/add_view.png\" alt=\"\"></span><span style=\"float:left;\"><a href=\"javascript:void(0);\" onclick=\"desktopViewDetailContact('contact_new');\">".$_DIMS['cste']['_DIMS_LABEL_NO_NEW_SHEET']."</a></span></td>";
							break;
						case 1:
							echo "<td><span style=\"width:16px;margin-right:4px;display:block;float:left;\"><img src=\"./common/img/add_view.png\" alt=\"\"></span><span style=\"float:left;\"><a href=\"javascript:void(0);\" onclick=\"desktopViewDetailContact('contact_new');\">".$nb_resp." ".$_DIMS['cste']['_DIMS_LABEL_NEW_SHEET_SINCE_ONCE']." </a></span></td>";
							break;
						default:
							echo "<td><span style=\"width:16px;margin-right:4px;display:block;float:left;\"><img src=\"./common/img/add_view.png\" alt=\"\"></span><span style=\"float:left;\"><a href=\"javascript:void(0);\" onclick=\"desktopViewDetailContact('contact_new');\">".$nb_resp." ".$_DIMS['cste']['_DIMS_LABEL_NEW_SHEET_SINCE']."</a></span></td>";
							break;
					}

					// affichage pour les entreprises
					switch ($nb_resp_ent) {
						case 0:
							echo "<td><span style=\"width:16px;margin-right:4px;display:block;float:left;\"><img src=\"./common/img/add_view.png\" alt=\"\"></span><span style=\"float:left;\"><a href=\"javascript:void(0);\" onclick=\"desktopViewDetailContact('ent_new');\">".$_DIMS['cste']['_DIMS_LABEL_NO_NEW_SHEET']."</a></span></td>";
							break;
						case 1:
							echo "<td><span style=\"width:16px;margin-right:4px;display:block;float:left;\"><img src=\"./common/img/add_view.png\" alt=\"\"></span><span style=\"float:left;\"><a href=\"javascript:void(0);\" onclick=\"desktopViewDetailContact('entact_new');\">".$nb_resp_ent." ".$_DIMS['cste']['_DIMS_LABEL_NEW_SHEET_SINCE_ONCE']." </a></span></td>";
							break;
						default:
							echo "<td><span style=\"width:16px;margin-right:4px;display:block;float:left;\"><img src=\"./common/img/add_view.png\" alt=\"\"></span><span style=\"float:left;\"><a href=\"javascript:void(0);\" onclick=\"desktopViewDetailContact('ent_new');\">".$nb_resp_ent." ".$_DIMS['cste']['_DIMS_LABEL_NEW_SHEET_SINCE']."</a></span></td>";
							break;
					}
					echo"</tr><tr>";

					switch ($nb_respmod) {
						case 0:
							echo "<td><span style=\"width:16px;margin-right:4px;display:block;float:left;\"><img src=\"./common/img/edity.gif\" alt=\"\"></span><span style=\"float:left;\"><a href=\"javascript:void(0);\" onclick=\"desktopViewDetailContact('contact_modify');\">".$_DIMS['cste']['_DIMS_LABEL_NO_MODIFY_SHEET']."</a></span></td>";
							break;
						case 1:
							echo "<td><span style=\"width:16px;margin-right:4px;display:block;float:left;\"><img src=\"./common/img/edit.gif\" alt=\"\"></span><span style=\"float:left;\"><a href=\"javascript:void(0);\" onclick=\"desktopViewDetailContact('contact_modify');\">".$nb_respmod." ".$_DIMS['cste']['_MODIFY_SHEET_SINCE_ONCE']." </a></span></td>";
							break;
						default:
							echo "<td><span style=\"width:16px;margin-right:4px;display:block;float:left;\"><img src=\"./common/img/edit.gif\" alt=\"\"></span><span style=\"float:left;\"><a href=\"javascript:void(0);\" onclick=\"desktopViewDetailContact('contact_modify');\">".$nb_respmod." ".$_DIMS['cste']['_MODIFY_SHEET_SINCE']."</a></span></td>";
							break;
					}

					// affichage pour les entreprises
					switch ($nb_respmod_ent) {
						case 0:
							echo "<td><span style=\"width:16px;margin-right:4px;display:block;float:left;\"><img src=\"./common/img/edit.gif\" alt=\"\"></span><span style=\"float:left;\"><a href=\"javascript:void(0);\" onclick=\"desktopViewDetailContact('ent_modify');\">".$_DIMS['cste']['_DIMS_LABEL_NO_MODIFY_SHEET']."</a></span></td>";
							break;
						case 1:
							echo "<td><span style=\"width:16px;margin-right:4px;display:block;float:left;\"><img src=\"./common/img/edit.gif\" alt=\"\"></span><span style=\"float:left;\"><a href=\"javascript:void(0);\" onclick=\"desktopViewDetailContact('ent_modify');\">".$nb_respmod_ent." ".$_DIMS['cste']['_MODIFY_SHEET_SINCE_ONCE']." </a></span></td>";
							break;
						default:
							echo "<td><span style=\"width:16px;margin-right:4px;display:block;float:left;\"><img src=\"./common/img/edit.gif\" alt=\"\"></span><span style=\"float:left;\"><a href=\"javascript:void(0);\" onclick=\"desktopViewDetailContact('ent_modify');\">".$nb_respmod_ent." ".$_DIMS['cste']['_MODIFY_SHEET_SINCE']."</a></span></td>";
							break;
					}
					echo"</tr><tr>";

					// fiches en veille
					$sql_f = "SELECT * FROM dims_mod_business_ct_watch WHERE id_user = :iduser and id_tiers>0";
					$res_f = $db->query($sql_f, array(
						':iduser' => $_SESSION['dims']['userid']
					));
					$nb_veille_ent = $db->numrows($res_f);

					// affichage en veille
					switch ($nb_veille) {
						case 0:
							echo "<td><span style=\"width:16px;margin-right:4px;display:block;float:left;\"><img src=\"./common/img/view.png\" alt=\"\"></span><span style=\"float:left;\"><a href=\"javascript:void(0);\" onclick=\"desktopViewDetailContact('contact_veille');\">".$_DIMS['cste']['_DIMS_LABEL_VEILLE_NO_FICH']."</a></span></td>";
							break;
						case 1:
							echo "<td><span style=\"width:16px;margin-right:4px;display:block;float:left;\"><img src=\"./common/img/view.png\" alt=\"\"></span><span style=\"float:left;\"><a href=\"javascript:void(0);\" onclick=\"desktopViewDetailContact('contact_veille');\">".$nb_veille." ".$_DIMS['cste']['_DIMS_LABEL_VEILLE_FICH_ONCE']." </a></span></td>";
							break;
						default:
							echo "<td><span style=\"width:16px;margin-right:4px;display:block;float:left;\"><img src=\"./common/img/view.png\" alt=\"\"></span><span style=\"float:left;\"><a href=\"javascript:void(0);\" onclick=\"desktopViewDetailContact('contact_veille');\">".$nb_veille." ".$_DIMS['cste']['_DIMS_LABEL_VEILLE_FICH']."</a></span></td>";
							break;
					}

					// affichage pour les entreprises en veille
					switch ($nb_veille_ent) {
						case 0:
							echo "<td><span style=\"width:16px;margin-right:4px;display:block;float:left;\"><img src=\"./common/img/view.png\" alt=\"\"></span><span style=\"float:left;\"><a href=\"".$dims->getScriptEnv()."?dims_action=public&dims_mainmenu=0&submenu=".dims_const::_DIMS_SUBMENU_ACTIVITIES."&action=ent_veille\">".$_DIMS['cste']['_DIMS_LABEL_VEILLE_NO_FICH']."</a></span></td>";
							break;
						case 1:
							echo "<td><span style=\"width:16px;margin-right:4px;display:block;float:left;\"><img src=\"./common/img/view.png\" alt=\"\"></span><span style=\"float:left;\"><a href=\"".$dims->getScriptEnv()."?dims_action=public&dims_mainmenu=0&submenu=".dims_const::_DIMS_SUBMENU_ACTIVITIES."&action=ent_veille\">".$nb_veille." ".$_DIMS['cste']['_DIMS_LABEL_VEILLE_FICH_ONCE']." </a></span></td>";
							break;
						default:
							echo "<td><span style=\"width:16px;margin-right:4px;display:block;float:left;\"><img src=\"./common/img/view.png\" alt=\"\"></span><span style=\"float:left;\"><a href=\"".$dims->getScriptEnv()."?dims_action=public&dims_mainmenu=0&submenu=".dims_const::_DIMS_SUBMENU_ACTIVITIES."&action=ent_veille\">".$nb_veille." ".$_DIMS['cste']['_DIMS_LABEL_VEILLE_FICH']."</a></span></td>";
							break;
					}

					?>
					</tr>
					</table>
				</span>
			</div>

			<?php
				$title='';
				if (isset($currentworkspace['planning']) && $currentworkspace['planning']==1) {
					$title=$_DIMS['cste']['_DIMS_LABEL_EVENTS'];
				}
				if (isset($currentworkspace['activeproject']) && $currentworkspace['activeproject']==1) {
					if ($title=='') {
						$title=$_DIMS['cste']['_LABEL_PROJECTS'];
					}
					else {
						$title.=" / ".$_DIMS['cste']['_LABEL_PROJECTS'];
					}
				}
				if (isset($currentworkspace['activenewsletter']) && $currentworkspace['activenewsletter']==1) {
					if ($title=='') {
						$title=$_DIMS['cste']['_DIMS_LABEL_NEWSLETTER'];
					}
					else {
						$title.=" / ".$_DIMS['cste']['_DIMS_LABEL_NEWSLETTER'];
					}
				}
			?>
		</td>
	</tr>
	</table>
</div>
