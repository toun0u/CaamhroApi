
<script language="javascript">
function changeAffectation(id_user) {
	// lecture du select
	var selec=document.getElementById("sel"+id_user);
	var value=selec.options[selec.selectedIndex].value;
	dims_xmlhttprequest("admin.php","op=changeaffectation&id_user="+id_user+"&value="+value+"&id_q="+<? echo $_SESSION['sharefile']['current_share']; ?>);
}

function sendFaqEmail(id_user) {
	if (confirm("<? echo _DIMS_FAQ_CONFIRM_SENDMAIL; ?>")) {
		dims_xmlhttprequest("admin.php","op=sendEmail&id_user="+id_user);
		alert("<? echo _FAQ_SEND_MESSAGE; ?>");
	}
}
</script>

<?php

$id_share= $_SESSION['sharefile']['current_share'];

$sql="	SELECT 		q.*, q.id as id_q, u.*, u.id as id_user,
					rub.label as labelrub,q.email as emailq,
					count(distinct r.id ) AS cpte
		FROM 		dims_mod_faq_question q
		left join 	dims_mod_faq_reponse as r on r.id_question=q.id
		left join 	dims_mod_faq_rubriques as rub
		on 			rub.id=q.id_rubrique
		LEFT JOIN 	dims_user u ON u.id = q.id_user
		where 		q.id= :idshare
		group by 	q.id";

$res_q = $db->query($sql,array(':idshare' => $_SESSION['sharefile']['current_share']));

if($value = $db->fetchrow($res_q)) {
	$var=dims_timestamp2local($value['date_create']);
	$datecreate=$var['date'];
	$timecreate=$var['time'];

	if ($value['date_modify']>0) {
		$var=dims_timestamp2local($value['date_modify']);
		$datemodify=$var['date'];
	}
	else $datemodify="";

	if ($value['firstname']!="" && $value['lastname']!="") {
       	$user= "{$value['firstname']} {$value['lastname']}";
    }
    elseif ($value['emailq']!="") {
    	$user=$value['emailq'];
    }
    else $user=" - ";

    if ($value['labelrub']=="") $value['labelrub']="<font style=\"font-style:italic;\">Non renseign&eacute;</font>";
    echo "<form  class=\"dims_form\">";
	echo "<p><label style=\"font-weight:bold;\">Question pos&eacute;e par </label><span>".$user." le ".$datecreate." &agrave; ".$timecreate."</span></p>";
	echo "<p><label style=\"font-weight:bold;\">Rubrique concern&eacute;e </label><span>".$value['labelrub']."</span></p>";
	echo "<p><label style=\"font-weight:bold;\">Publi&eacute;e</label><span>";
	   		if ($value['published']) {
	   			if ($value['cpte']==0) echo "<img src=\"./common/modules/faq/img/wait.png\" alt=\""._DIMS_FAQ_WAIT."\">";
	   			else echo "<img src=\"./common/modules/faq/img/valid.png\" alt=\""._DIMS_FAQ_PUBLISHED."\">";
	   		}
	   		else echo "<img src=\"./common/modules/faq/img/notvalid.png\" alt=\""._DIMS_FAQ_NOTPUBLISHED."\">";
	echo "</span></p>";
	echo "<p><label style=\"font-weight:bold;\">Contenu de la question</label><span>".$value['question']."</span></p>";
	echo "</form>";
	if ((dims_isactionallowed(0) || dims_isadmin())) {
    	echo "<div id=\"modif_question\" name=\"modif_question\" style=\"display:none;\">";
    	echo $skin->open_simplebloc("Modifier la question",'100%','','',false);
    		echo "<form  class=\"dims_form\" name=\"addquestion\" method=\"POST\" action=\"".$scriptenv."?op=save_question\">";
	   		echo "<input type=\"hidden\" name=\"id_q\" value=\"".$_SESSION['sharefile']['current_share']."\">";
	    	echo "<p><label>Question modifi&eacute;e le </label>".$datemodify."</p>";
	    	echo "<p><label>Contenu de la question :</label>
	    	<textarea id=\"question\" class=\"form_textarea\" name=\"question\" cols=\"60\" rows=\"10\">".$value['question']."</textarea></p>";
	   		echo "<p><label>Rubrique</label> : <select name=\"id_rubrique\">";
		    // on affiche le choix de la rubrique
		    $res=$db->query("SELECT *
		    				FROM dims_mod_faq_rubriques
		    				WHERE id_module= :idmodule",
		    				array(':idmodule' => $_SESSION['dims']['moduleid'])
		    				);

		    echo "<option value=\"0\">-</option>";
		    if ($db->numrows($res)>0) {
		    	while ($rub=$db->fetchrow($res)) {
		    		if ($rub['id']==$value['id_rubrique']) $selected="selected";
		    		else $selected="";

		    		echo "<option value=\"".$rub['id']."\" $selected>".$rub['label']."</option>";
		    	}
		    }
		    echo "</select></p>";
		    if ($value['published']) $check="checked";
		    else $check="";
		    echo "<p><label>Publi&eacute;e :</label><input type=\"checkbox\" name=\"published\" ".$check."></p>";

		    // construction de la liste des personnes pouvant �tre rattach�es
		    // collecte des roles qui peuvent effectuer l'action 1
		    $select = 	"SELECT 	*
						FROM 	dims_role_action
						WHERE 	id_action = 1
						AND		id_module_type = :idmoduletype";

			$answer = $db->query($select,array(':idmoduletype' => $_SESSION['dims']['modules'][$_SESSION['dims']['moduleid']]['id_module_type']));
			$tabroles = array();

			while ($f = $db->fetchrow($answer)) {
				$tabroles[]=$f['id_role'];
			}
		    // on a maintenant la liste des roles, on recherche les personnes ayant directement ce role soit :
		    // sur une association de users => roles
		    // sur un groupe
		    // sur un profil
		    $tabusers = array();

		    if (sizeof($tabroles)>0) {
			    // on commence par les rattachements directs d'un user sur un workspace avec un role
			    $params = array();
				$select = 	"SELECT 		distinct u.*
							FROM		dims_user as u
							INNER JOIN 	dims_workspace_user_role
							on			u.id=dims_workspace_user_role.id_user
							AND 		id_role in (".$db->getParamsFromArray($tabroles, 'roles', $params).")";

				$answer = $db->query($select, $params);

				while ($f = $db->fetchrow($answer)) {
					$tabusers[$f['id']]=$f;
				}

				// on poursuit sur les users rattache sur les workspaces
				$params = array();
				$select = 	"
							SELECT 		distinct u.*
							FROM		dims_user as u
							INNER JOIN 	dims_workspace_user as wu
							ON			u.id_user=wu.id
							inner join	dims_role_profile as p
							on			p.id_profile=u.id_profile
							AND 		id_role in (".$db->getParamsFromArray($tabroles, 'roles', $params).")";
				$answer = $db->query($select, $params);

				while ($f = $db->fetchrow($answer)) {
					$tabusers[$f['id']]=$f;
				}

				// on regarde pour les personnes li�es au groupe
				$params = array();
				$select = 	"
							SELECT 		distinct u.*
							FROM		dims_user as u
							inner join	dims_group_user as g
							on			g.id_user=u.id
							inner join	dims_workspace_group as wg
							on			wg.id_group=g.id_group
							inner join	dims_role_profile as p
							on			p.id_profile=wg.id_profile
							AND 		id_role in (".$db->getParamsFromArray($tabroles, 'roles', $params).")";


				$answer = $db->query($select, $params);

				while ($f = $db->fetchrow($answer)) {
					$tabusers[$f['id']]=$f;
				}

				// on affiche l'ensemble des contributeurs et on regarde si ils sont r�f�rents ou secondaires
				$select = 	"
							SELECT 		*
							FROM		dims_mod_faq_affectation where id_question = :idquestion";

				$answer = $db->query($select,array(':idquestion' => $_SESSION['sharefile']['current_share'] ));

				while ($f = $db->fetchrow($answer)) {
					$tabsaffect[$f['id_user']]=$f['type'];
				}

				// on parcours toutes les personnes et on regarde ce que cela donne
				echo "<table width=\"80%\" align=\"center\"><tr><td>"._DIMS_FAQ_CONTRIB."</td><td>Attribution</td><td>Email</td></tr>";
				foreach ($tabusers as $id_user => $user) {
					if($id_user%2==0) $color="trl1";
					else $color="trl2";

					echo "<tr class=\"$color\"><td>M. ".$user['firstname']." ".$user['lastname']."</td>";
					// on regarde le type
					if (isset($tabsaffect[$id_user])) $type=$tabsaffect[$id_user];
					else $type=0;
					echo "<td><select id=\"sel".$id_user."\" name=\"sel".$id_user."\" onchange=\"javascript:changeAffectation(".$id_user.")\">";

					if ($type==0) $selec="selected";
					else $selec="";
					echo "<option value=\"0\" $selec>-</option>";

					if ($type==1) $selec="selected";
					else $selec="";
					echo "<option value=\"1\" $selec>Contributeur r&eacute;f&eacute;rent</option>";

					if ($type==2) $selec="selected";
					else $selec="";
					echo "<option value=\"2\" $selec>Contributeur secondaire</option>";

					echo "</select></td>";

					// partie email
					echo "<td><a href=\"javascript:void(0)\" onclick=\"sendFaqEmail(".$id_user.");\"><img src=\"./common/img/icon_tickets.gif\" border=\"0\"></td>";
					echo "</tr>";
				}
				echo "</table>";
		    }

		    echo "<p><label>&nbsp;</label><span style=\"tex-align:right;\">";
		    echo dims_create_button(_DIMS_SAVE,"./common/img/save.gif","javascript:document.addquestion.submit();");
		    echo "</span></p></form>";
			echo $skin->close_simplebloc();
		    echo "</div>";
    	// on affiche l'afffectation des utilisateurs
    }
}

    $sql_r = "	SELECT r.*, r.id as id_r, u.lastname,u.firstname, u.id as id_user
				FROM dims_mod_faq_reponse r
				INNER JOIN dims_user u
				ON u.id = r.id_user
				WHERE r.id_question = :idshare";
    $res_r = $db->query($sql_r, array(':idshare' => $id_share));
    $tab_r =array();
    while($value = $db->fetchrow($res_r)) {
        $tab_r[$value['id_r']] = $value;
    }
    $_SESSION['admin_faq'][$id_share]['reponse'] = $tab_r;

echo "<table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\">
	<tr><td>";

if ($_SESSION['sharefile']['current_reponse']>0) {
	$rep = new reponse();
	$rep->open($_SESSION['sharefile']['current_reponse']);
	if (!isset($rep->fields['reponse'])) $_SESSION['sharefile']['current_reponse']=0;
}

if ($_SESSION['sharefile']['current_reponse']==0)
	echo dims_create_button(_DIMS_ADD." une r&eacute;ponse","./common/img/add.gif","javascript:dims_switchdisplay('new_reponse');document.addreponse.fck_new_reponse.focus();","enreg1",$displaycontent);

	if (dims_isactionallowed(0) || dims_isadmin()) {
		echo dims_create_button(_DIMS_MODIFY,"./common/img/edit.gif","javascript:dims_switchdisplay('modif_question');document.addquestion.id_rubrique.focus();","enreg1",$displaycontent);
	}
?>
    	<tr>
    		<td>
                <?
                    echo "<div id=\"new_reponse\" name=\"new_reponse\" style=\"display:none;\">";
    				echo "<form class=\"dims_form\" name=\"addreponse\" method=\"POST\" action=\"".$scriptenv."\">";
    				echo "<input type=\"hidden\" name=\"op\" value=\"add_reponse\">";
                ?>
    				<p>
    					<label style="font-weight:bold;">R&eacute;ponse :</label>
    				</p>
    				<p style="width:100%;">
                        <?
                        if ($_SESSION['sharefile']['current_reponse']==0) {
	                        include_once('./FCKeditor/fckeditor.php') ;

							$oFCKeditor = new FCKeditor('fck_new_reponse');

							$basepath = dirname($_SERVER['HTTP_REFERER']); // compatible with proxy rewrite

							if ($basepath == '/') $basepath = '';

							$oFCKeditor->BasePath	= "{$basepath}/FCKeditor/";

							// default value
							$oFCKeditor->Value = "";
							//$oFCKeditor->Value= $article->fields['content'];

							// width & height
							$oFCKeditor->Width='100%';
							$oFCKeditor->Height='400';

							$oFCKeditor->Config['CustomConfigurationsPath'] = "{$basepath}/FCKeditor/fckconfigrestrict.js"  ;
							//$oFCKeditor->Config['ToolbarLocation'] = 'Out:xToolbar' ;
							$oFCKeditor->Config['SkinPath'] = "{$basepath}/modules/system/fckeditor/skins/default/" ;
							$oFCKeditor->Config['EditorAreaCSS'] = "{$basepath}/modules/system/fckeditor/fck_editorarea.css" ;
							$oFCKeditor->Config['BaseHref'] = "http://{$_SERVER['HTTP_HOST']}{$basepath}/";
							$oFCKeditor->Create('FCKeditor_1') ;
                        }
						?>
					</p>
					<p>
						<label>&nbsp;</label><span style="text-align:right">
                                <input type="submit" value="<? echo _DIMS_SAVE; ?>">
                        </span>
					</p>
                </form>
           </td>
    	</tr>
<?php
if(!empty($tab_r)) {
    echo "
    	<tr><td style=\"font-weight:bold;font-size:12px;\" align=\"center\">Liste des r&eacute;ponses</td></tr>
        <tr>
            <td>
                <div id=\"div_rep_$id_share\" style=\"border:#fff 1px solid\">
                    <table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\">
                    <tr style=\"background-color:#CBCBCB\"><td><b>Par</b></td><td align=\"center\"><b>R&eacute;ponse</b></td><td align=\"center\"><b>Actions</b></td></tr>
                    ";
    				$c=0;
                    foreach($tab_r as $id_r => $tab_rep) {
                    	$var=dims_timestamp2local($tab_rep['date_create']);
						$datecreate=$var['date'];
						$timecreate=$var['time'];
                    	$c++;
                    	if ($c%2==0) $class="class=\"trl1\"";
    					else $class="class=\"trl2\"";

                        echo "
                            <tr $class>
                                <td width=\"20%\">{$tab_rep['firstname']} {$tab_rep['lastname']}<br>
                                le ".$datecreate." &agrave; ".$timecreate."
                                </td>
                                <td width=\"50%\">{$tab_rep['reponse']}
                                </td>
                                <td >";

                        if ($tab_rep['id_user']==$_SESSION['dims']['userid']) {
                        	echo dims_create_button(_DIMS_DELETE,"./common/img/delete.gif","javascript:dims_confirmlink('".$scriptenv."?op=delete_reponse&id_r=".$id_r."','�tes-vous certain de vouloir supprimer cette r&eacute;ponse ?');","","","","enreg1");
                        	echo dims_create_button(_DIMS_MODIFY,"./common/img/edit.gif","","","",$scriptenv."?op=view_question&id_q=".$id_share."&id_r=".$id_r,"");
                        }
                        echo "   </td>
                            </tr>
                            ";

                    	if ($_SESSION['sharefile']['current_reponse']>0 && $_SESSION['sharefile']['current_reponse']==$id_r) {
	    					echo "<tr>
	                            <td colspan=\"2\">
	                            <div id=\"modif_reponse\" name=\"modif_reponse\" style=\"display:block;visibility:visible;\">";
	    					echo "<form class=\"dims_form\" name=\"modifreponse\" method=\"POST\" action=\"".$scriptenv."\">";
	    					echo "<input type=\"hidden\" name=\"id_r\" value=\"".$_SESSION['sharefile']['current_reponse']."\">";
							echo "<input type=\"hidden\" name=\"op\" value=\"add_reponse\">";

							 include_once('./FCKeditor/fckeditor.php') ;

							$oFCKeditor = new FCKeditor('fck_new_reponse');

							$basepath = dirname($_SERVER['HTTP_REFERER']); // compatible with proxy rewrite
							if ($basepath == '/') $basepath = '';

							$oFCKeditor->BasePath	= "{$basepath}/FCKeditor/";

							// default value
							$oFCKeditor->Value = $tab_rep['reponse']	;
							//$oFCKeditor->Value= $article->fields['content'];

							// width & height
							$oFCKeditor->Width='100%';
							$oFCKeditor->Height='400';

							$oFCKeditor->Config['CustomConfigurationsPath'] = "{$basepath}/FCKeditor/fckconfigrestrict.js"  ;
							//$oFCKeditor->Config['ToolbarLocation'] = 'Out:xToolbar' ;
							$oFCKeditor->Config['SkinPath'] = "{$basepath}/modules/system/fckeditor/skins/default/" ;
							$oFCKeditor->Config['EditorAreaCSS'] = "{$basepath}/modules/system/fckeditor/fck_editorarea.css" ;
							$oFCKeditor->Config['BaseHref'] = "http://{$_SERVER['HTTP_HOST']}{$basepath}/";
							$oFCKeditor->Create('FCKeditor_1') ;

							echo "<input type=\"submit\" value=\""._DIMS_SAVE."\"></form>";
							echo "	</td>
	                            </tr>
	                            ";
	    				}


                    }

    echo "          </table>
                </div>
            </td>
        </tr>

    ";
}
else  echo "<tr><td style=\"width:100%;text-align:center;\">Aucune r&eacute;ponse</td></tr>";

echo "</table>";
?>
