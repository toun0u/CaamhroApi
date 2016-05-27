<?php

require_once DIMS_APP_PATH . '/modules/system/import/global.php';
require_once DIMS_APP_PATH . '/modules/doc/class_docfile.php';

class view_business_factory {

	public static function buildAccueil(){
		global $_DIMS;

		?>

		<div class="zone_title_address_book">
		<div class="title_adress_book"><h1><?php echo $_SESSION['cste']['_BUSINESS_PAGE_TITLE']; ?></h1></div>
		</div>
		<?
		// History
		echo '<div style="float:left;width:25%;text-align:center;margin:2px auto;">
			<img src="'.$_SESSION['dims']['template_path'].'/media/history32.png">
		<br><input type="button" onclick="document.location.href=\''.dims_urlencode("/admin.php?business_op=suivi").'\'" class="ui-button ui-state-default ui-corner-all activebutton" aria-disabled="false" value="Suivis"/>
		</div>';
		// new file import
		echo '<div style="float:left;width:25%;text-align:center;margin:2px auto;">
			<img src="./common/img/doc_add.png">
		<br><input type="button" onclick="document.location.href=\''.dims_urlencode("/admin.php?business_op=admin_business_param").'\'" class="ui-button ui-state-default ui-corner-all activebutton" aria-disabled="false" value="'.$_DIMS['cste']['_DIMS_LABEL_ADMIN'].'"/>
		</div>';
		 echo '<div style="float:left;width:25%;text-align:center;margin:2px auto;">
			<img src="./common/img/pdf32.png">
		<br><input type="button" onclick="document.location.href=\''.dims_urlencode("/admin.php?business_op=admin_models_param").'\'" class="ui-button ui-state-default ui-corner-all activebutton" aria-disabled="false" value="'.$_SESSION['cste']['PRINTING_MODELS'].'"/>
		</div>';
		// Opportunity type management
		echo '<div style="float:left;width:25%;text-align:center;margin:2px auto;">
			<img src="'.$_SESSION['dims']['template_path'].'/media/properties32.png">
		<br><input type="button" onclick="document.location.href=\''.dims_urlencode('/admin.php?business_op=activity_type_management').'\'" class="ui-button ui-state-default ui-corner-all activebutton" aria-disabled="false" value="'.$_DIMS['cste']['ACTIVITY_TYPE'].'"/>
		</div>';

	}

	public static function buildGetAdminParam() {
		global $dims;
		global $_DIMS;
		$scriptenv="/admin.php";
		$business_op=$_SESSION['desktopv2']['business']['business_op'];
		$db = $dims::getInstance()->getDb();
		require_once DIMS_APP_PATH.'/modules/system/desktopV2/include/class_gescom_param.php';
		// chargement des params
		$params = class_gescom_param::getAllParams();
		?>
		<form name="form_params" action="<? echo $scriptenv; ?>" method="POST">
		<?
			// Sécurisation du formulaire par token
			require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
			$token = new FormToken\TokenField;
			$token->field("business_op",	"save_business_param");
			$token->field("param_exercice");
			$token->field("param_datedeb");
			$token->field("param_datefin");
			$token->field("param_pays");
			$token->field("param_conditionpaiement");
			$tokenHTML = $token->generate();
			echo $tokenHTML;
		?>
		<input type="hidden" name="business_op" value="save_business_param">
		<table cellpadding="2" cellspacing="1">
		<tr>
			<td align="right">Exercice:&nbsp;</td>
			<td><input type="text" class="text" name="param_exercice" value="<? echo isset($params['exercice']) ? $params['exercice'] : ""; ?>" size="20" /></td>
		</tr>
		<tr>
			<td align="right">Début Exercice:&nbsp;</td>
			<td align="left">
				<input maxlength="10" name="param_datedeb" id="datedeb" size="20" class="text" value="<? echo isset($params['datedeb']) ? business_dateus2fr($params['datedeb']) : ""; ?>">&nbsp;<a href="#" onclick="javascript:dims_calendar_open('datedeb', event);"><img src="./common/img/calendar/calendar.gif" width="31" height="18" align="top" border="0"></a>
			</td>
		</tr>
		<tr>
			<td align="right">Fin Exercice:&nbsp;</td>
			<td align="left">
				<input maxlength="10" name="param_datefin" id="datefin" size="20" class="text" value="<? echo isset($params['datefin']) ? business_dateus2fr($params['datefin']) : ""; ?>">&nbsp;<a href="#" onclick="javascript:dims_calendar_open('datefin', event);"><img src="./common/img/calendar/calendar.gif" width="31" height="18" align="top" border="0"></a>
			</td>
		</tr>
		<tr>
			<td align="right">Pays:&nbsp;</td>
			<td>
				<select class="select" name="param_pays">
				<?
				$listenum = business_getlistenum('pays');
				foreach($listenum as $enum)
				{
					$sel = ($enum['libelle'] == (isset($params['pays']) ? $params['pays'] : "")) ? 'selected' : '';
					echo "<option $sel value=\"{$enum['libelle']}\">{$enum['libelle']}</option>";
				}
				?>
				</select>
			</td>
		</tr>
		<tr>
			<td align="right" valign="top">Conditions de Paiement:&nbsp;</td>
			<td><textarea class="text" name="param_conditionpaiement" cols="35" rows="5"><? echo isset($params['conditionpaiement']) ? stripslashes($params['conditionpaiement']) : ""; ?></textarea></td>
		</tr>
		<tr>
			<td align="right" colspan="2"><input type="submit" class="flatbutton" value="<? echo $_DIMS['cste']['_DIMS_SAVE']; ?>"></td>
		</tr>
		</table>
		</form>
		<?php
	}

	public static function buildGetSuivis() {
		global $dims;
		$scriptenv=$dims->getScriptEnv();
		$business_op=$_SESSION['desktopv2']['business']['business_op'];
		$db = $dims::getInstance()->getDb();

		$select = "
		SELECT		distinct(exercice)
		FROM		dims_mod_business_suivi
		WHERE		exercice <> ''
		AND		id_workspace = :idworkspace
		ORDER BY	exercice DESC";
		$lastexervice='';
		$res=$db->query($select, array(
			':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['workspaceid']),
		));
		if ($fields = $db->fetchrow($res))
		{
		$lastexervice=$fields['exercice'];
		}

		$business_reset=dims_load_securvalue('business_reset',dims_const::_DIMS_NUM_INPUT,true,true,false);

		if ($business_reset) {
		   unset($_SESSION['desktopv2']['business']);
		   $_SESSION['desktopv2']['business']['business_op']=$business_op;
		}

		if (!isset($_SESSION['desktopv2']['business']['suivi_tiers'])) $_SESSION['desktopv2']['business']['suivi_tiers']='';
		if (!isset($_SESSION['desktopv2']['business']['suivi_numero'])) $_SESSION['desktopv2']['business']['suivi_numero']='';
		if (!isset($_SESSION['desktopv2']['business']['suivi_dossier'])) $_SESSION['desktopv2']['business']['suivi_dossier']='';
		if (!isset($_SESSION['desktopv2']['business']['suivi_exercice'])) $_SESSION['desktopv2']['business']['suivi_exercice']=$lastexervice;
		if (!isset($_SESSION['desktopv2']['business']['suivi_type'])) $_SESSION['desktopv2']['business']['suivi_type']='';
		if (!isset($_SESSION['desktopv2']['business']['suivi_datecreation1'])) $_SESSION['desktopv2']['business']['suivi_datecreation1']='';
		if (!isset($_SESSION['desktopv2']['business']['suivi_datecreation2'])) $_SESSION['desktopv2']['business']['suivi_datecreation2']='';
		if (!isset($_SESSION['desktopv2']['business']['suivi_solde'])) $_SESSION['desktopv2']['business']['suivi_solde']='';

		$suivi_tiers=dims_load_securvalue('suivi_tiers',dims_const::_DIMS_CHAR_INPUT,true,true,false,$_SESSION['desktopv2']['business']['suivi_tiers'],$_SESSION['desktopv2']['business']['suivi_tiers']);
		$suivi_numero=dims_load_securvalue('suivi_numero',dims_const::_DIMS_CHAR_INPUT,true,true,false,$_SESSION['desktopv2']['business']['suivi_numero'],$_SESSION['desktopv2']['business']['suivi_numero']);
		$suivi_dossier=dims_load_securvalue('suivi_dossier',dims_const::_DIMS_CHAR_INPUT,true,true,false,$_SESSION['desktopv2']['business']['suivi_dossier'],$_SESSION['desktopv2']['business']['suivi_dossier']);
		$suivi_exercice=dims_load_securvalue('suivi_exercice',dims_const::_DIMS_CHAR_INPUT,true,true,false,$_SESSION['desktopv2']['business']['suivi_exercice'],$_SESSION['desktopv2']['business']['suivi_exercice']);
		$suivi_type=dims_load_securvalue('suivi_type',dims_const::_DIMS_CHAR_INPUT,true,true,false,$_SESSION['desktopv2']['business']['suivi_type'],$_SESSION['desktopv2']['business']['suivi_type']);
		$suivi_datecreation1=dims_load_securvalue('suivi_datecreation1',dims_const::_DIMS_CHAR_INPUT,true,true,false,$_SESSION['desktopv2']['business']['suivi_datecreation1'],$_SESSION['desktopv2']['business']['suivi_datecreation1']);
		$suivi_datecreation2=dims_load_securvalue('suivi_datecreation2',dims_const::_DIMS_CHAR_INPUT,true,true,false,$_SESSION['desktopv2']['business']['suivi_datecreation2'],$_SESSION['desktopv2']['business']['suivi_datecreation2']);
		$suivi_solde=dims_load_securvalue('suivi_solde',dims_const::_DIMS_CHAR_INPUT,true,true,false,$_SESSION['desktopv2']['business']['suivi_solde'],$_SESSION['desktopv2']['business']['suivi_solde']);

		if (!isset($suivi_tiers)) $suivi_tiers = '';
		if (!isset($suivi_numero)) $suivi_numero = '';
		if (!isset($suivi_dossier)) $suivi_dossier = '';
		if (!isset($suivi_exercice)) $suivi_exercice = '';
		if (!isset($suivi_type)) $suivi_type = '';
		if (!isset($suivi_datecreation1)) $suivi_datecreation1 = '';
		if (!isset($suivi_datecreation2)) $suivi_datecreation2 = '';
		if (!isset($suivi_solde)) $suivi_solde = '';

		echo 'TYPE='.$suivi_type;
		?>

		<FORM NAME="form_suivi" ACTION="<? echo $scriptenv; ?>" METHOD="POST">
		<?
			// Sécurisation du formulaire par token
			require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
			$token = new FormToken\TokenField;
			$token->field("business_op",	"suivis_recherche");
			$token->field("suivi_numero");
			$token->field("suivi_tiers");
			$token->field("suivi_type");
			$token->field("suivi_exercice");
			$token->field("suivi_datecreation1");
			$token->field("suivi_datecreation2");
			$token->field("suivi_solde");
			$token->field("suivi_modele");
			$tokenHTML = $token->generate();
			echo $tokenHTML;
		?>

		<INPUT TYPE="HIDDEN" NAME="business_op" VALUE="suivis_recherche">

		<TABLE CELLPADDING="2" CELLSPACING="1">
		<tr>
		<td align="RIGHT">Numéro:&nbsp;</td>
		<td colspan="3"><input type="text" name="suivi_numero" class="text" value="<? echo $suivi_numero; ?>" size="25"></td>
		</tr>
		<tr>
		<td align="RIGHT">Client:&nbsp;</td>
		<td colspan="3"><input type="text" name="suivi_tiers" class="text" value="<? echo $suivi_tiers; ?>" size="25"></td>
		</tr>
		<tr>
		<td align="RIGHT">Type:&nbsp;</td>
		<td colspan="3">
			<select class="select" name="suivi_type">
			<option value="">(tous)</option>
			<?
			$listenum = business_getlistenum('typesuivi', false);
			foreach($listenum as $id_enum => $enum)
			{
				$sel = ($enum['libelle'] == $suivi_type) ? 'selected' : '';
				echo "<option $sel value=\"{$enum['libelle']}\">{$enum['libelle']}</option>";
			}
			?>
			</select>
		</td>
		</tr>
		<tr>
		<td align="RIGHT">Exercice:&nbsp;</td>
		<td colspan="3">
			<select class="select" name="suivi_exercice">
			<option value="">(tous)</option>
			<?
			$select = "
				SELECT		distinct(exercice)
				FROM		dims_mod_business_suivi
				where		id_workspace = :idworkspace
				ORDER BY	exercice DESC";

			$res=$db->query($select, array(
				':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['workspaceid']),
			));
			while ($fields = $db->fetchrow($res)) {
				$sel = ($fields['exercice'] == $suivi_exercice) ? 'selected' : '';
				echo "<option $sel value=\"{$fields['exercice']}\">{$fields['exercice']}</option>";
			}
			?>
			</select>
		</td>
		</tr>
		<tr>
		<td align="RIGHT">Créé entre le:&nbsp;</td>
		<td colspan="3"><input type="text" name="suivi_datecreation1" class="text" value="<? echo $suivi_datecreation1; ?>" size="15">&nbsp;Et le:&nbsp;<input type="text" name="suivi_datecreation2" class="text" value="<? echo $suivi_datecreation2; ?>" size="15"></td>
		</tr>
		<tr>
		<TD ALIGN=RIGHT>Soldé:&nbsp;</TD>
		<TD ALIGN=LEFT>
			<SELECT CLASS="Select" NAME="suivi_solde">
			<option value="">(tous)</option>
			<option value="1" <? if ($suivi_solde === '1') echo 'selected'; ?>>Oui</option>
			<option value="0" <? if ($suivi_solde === '0') echo 'selected'; ?>>Non</option>
			</SELECT>
		</TD>
		</TR>
		<tr>
		<TD ALIGN=RIGHT>Modèle:&nbsp;</TD>
		<TD ALIGN=LEFT>
			<select class="Select" name="suivi_modele">
			<?php
			$listenum = business_getlistenum('modele_suivi',false);
			foreach($listenum as $enum) {
				if ($suivi_modele === $enum['libelle'] || $suivi_modele=='') {
				$sele= 'selected';
				$suivi_modele=$enum['libelle'];
				}
				else $sele='';
				echo "<option ".$sele." value=\"{$enum['libelle']}\">{$enum['libelle']}</option>";
			}
			?>
			</select>
		</td>
		</tr>
		<tr>
		<td ALIGN="RIGHT" COLSPAN="2">
			<input type="button" onclick="javascript:document.location.href='/admin.php?business_reset=1'" Value="<? echo $_SESSION['cste']['RESET_ADVANCED_SEARCH']; ?>">
			<INPUT TYPE="Submit" CLASS="FlatButton" VALUE="<? echo $_SESSION['cste']['_SEARCH']; ?>">
		</TD>
		</tr>
		</TABLE>
		</FORM>

		<?
		if (!isset($_SESSION['business']['suivi_search'])) $_SESSION['business']['suivi_search'] = '';

		//Selection et affichage du tiers sÈlectionnÈ
		if ($business_op == "suivis_recherche" || $_SESSION['business']['suivi_search'])
		{
			echo "<TABLE CELLPADDING=\"2\" CELLSPACING=\"1\" WIDTH=\"100%\"><tr><td>";


			if ($business_op == 'suivis_recherche') // construction de la requete
			{
				$where = 'WHERE 1=1';
				$from = '';

				if ($suivi_tiers != '') {
					$where .= " AND t.id = s.tiers_id AND (t.intitule LIKE :suivitier OR t.intitule_search LIKE :suivitier) ";
					$params[':suivitier'] = array('type' => PDO::PARAM_STR, 'value' => '%'.$suivi_type.'%');
				}
				if ($suivi_dossier != '') {
					$where .= " AND d.id = s.dossier_id AND (d.objet_dossier LIKE :suividossier OR d.objet_dossier_search LIKE :suividossier) ";
					$params[':suividossier'] = array('type' => PDO::PARAM_STR, 'value' => '%'.$suivi_dossier.'%');
				}
				if ($suivi_numero != '') {
					$where .= " AND s.id = suivinumero";
					$params[':suivinumero'] = array('type' => PDO::PARAM_STR, 'value' => $suivi_numero);
				}
				if ($suivi_exercice != '') {
					$where .= " AND s.exercice = :exercicesuivi";
					$params[':exercicesuivi'] = array('type' => PDO::PARAM_STR, 'value' => $suivi_exercice);
				}
				if ($suivi_solde === '1') {
					$where .= " AND s.type = 'Facture' AND s.solde = 0";
				}
				if ($suivi_solde === '0') {
					$where .= " AND s.type = 'Facture' AND s.solde > 0";
				}
				if ($suivi_type != '') {
					$where .= " AND s.type = :typesuivi";
					$params[':typesuivi'] = array('type' => PDO::PARAM_STR, 'value' => $suivi_type);
				}
				if ($suivi_datecreation1 != '') {
					$where .= " AND s.datejour >= :datejour1 ";
					$params[':datejour1'] = array('type' => PDO::PARAM_STR, 'value' => business_datefr2us($suivi_datecreation1));
				}
				if ($suivi_datecreation2 != '') {
					$where .= " AND s.datejour <= :datejour2 ";
					$params[':datejour2'] = array('type' => PDO::PARAM_STR, 'value' => business_datefr2us($suivi_datecreation2));
				}

				$select =	"
					SELECT		s.*,
							t.intitule,
							c.firstname,c.lastname,
							t.id as tiers_id,
							s.contact_id
					FROM		dims_mod_business_suivi s
					LEFT JOIN	dims_mod_business_tiers as t
					ON		t.id = s.tiers_id
					LEFT JOIN	dims_mod_business_contact as c
					ON		c.id = s.contact_id
					$where
					and		s.id_workspace = :idworkspace
					ORDER BY	s.id DESC";
					$params[':idworkspace'] = array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['workspaceid']);
			}
			else {
				$select = $_SESSION['business']['suivi_search'];
				$params = $_SESSION['business']['suivi_params'];
			}

			$result = $db->query($select, $params);
			$_SESSION['business']['suivi_search'] = $select;
			$_SESSION['business']['suivi_params'] = $params;

			$opened=false;

			$color = "#FFFFFFF";

			?>
			<TABLE WIDTH="100%" CELLPADDING="2" CELLSPACING="1">
			<TR BGCOLOR="<? echo $color; ?>" CLASS="Title">
			<td>&nbsp;</td>
			<TD ALIGN="left">Numéro</TD>
			<TD ALIGN="left">Client</TD>
			<TD ALIGN="left">Type</TD>
			<TD ALIGN="left">Exercice</TD>
			<TD ALIGN="left">Date</TD>
			<TD ALIGN="left">Libellé</TD>
			<TD ALIGN="left">Accepté</TD>
			<TD ALIGN="right" nowrap>Montant HT</TD>
			<TD ALIGN="right" nowrap>Solde HT</TD>
			<TD></TD>
			</TR>
			<?
			$array_email = array();
			$total=0;
			$totalfacture=0;
			$solde=0;
			$totalttc=0;
			if ($db->numrows($result)>0 && $db->numrows($result) < 1000) {
				while ($fields = $db->fetchrow($result)) {
					$color = ($color == "#FFFFFF") ? "#EFEFEF" : "#FFFFFF";
					$ouvrir = dims_urlencode("$scriptenv?dims_moduletabid="._BUSINESS_TAB_SUIVISINFORMATIONS."&op=suivi_modifier&suivi_id={$fields['id']}&suivi_type={$fields['type']}&suivi_exercice={$fields['exercice']}");

					?>
					<TR bgcolor="<? echo $color; ?>">
						<TD width="170" align="center">
							<a href="<? echo dims_urlencode("$scriptenv?dims_op=desktopv2&action=imprimer_suivi&id_suivi={$fields['id']}&suivi_modele=".$suivi_modele."&suivi_type={$fields['type']}&suivi_exercice={$fields['exercice']}"); ?>"><img src="./common/modules/business/img/download_odt.gif" border="0" alt="imprimer (ODT)"></a>
							<a href="<? echo dims_urlencode("$scriptenv?dims_op=desktopv2&action=imprimer_suivi&id_suivi={$fields['id']}&suivi_modele=".$suivi_modele."&suivi_type={$fields['type']}&suivi_exercice={$fields['exercice']}&format=PDF"); ?>"><img src="./common/modules/business/img/download_pdf.gif" border="0" alt="imprimer (PDF)"></a>
							<a href="<? echo dims_urlencode("$scriptenv?dims_op=desktopv2&action=imprimer_suivi&id_suivi={$fields['id']}&suivi_modele=".$suivi_modele."&suivi_type={$fields['type']}&suivi_exercice={$fields['exercice']}&format=DOC"); ?>"><img src="./common/modules/business/img/download_doc.gif" border="0" alt="imprimer (DOC)"></a>
						</TD>

						<TD><? printf("%s%s%06d",$fields['type'][0],$fields['exercice'],$fields['id']); ?></TD>
						<TD>
						<?
						if ($fields['tiers_id']>0) {
						?>
							<a href="/admin.php?submenu=2&id=<?php echo $fields['tiers_id'];?>&type=6&init_filters=1&from=business">
							<?
							echo $fields['intitule'];?>
							</a>
						<?
						}
						else {
							?>
							<a href="/admin.php?submenu=2&id=<?php echo $fields['contact_id'];?>&type=7&init_filters=1&from=business">
							<?
							echo $fields['firstname']." ".$fields['lastname'];?>
							</a>
							<?
						}

						?>
						</TD>

						<td>
							<?
							$filename = './common/modules/business/img/ico_'.strtolower($fields['type']).'.gif';
							if (file_exists($filename)) echo "<img src=\"$filename\">";
							else echo "<strong>{$fields['type']}</strong>";
							?>
						</td>
						<TD><? echo $fields['exercice'];?></TD>
						<TD><? echo business_dateus2fr($fields['datejour']);?></TD>
						<TD><? echo $fields['libelle'];?></TD>
						<TD><?
						if ($fields['valide']) {
						echo "<img src=\"./common/img/publish.png\">";
						}

						?></TD>
						<TD ALIGN="right"><?
						$total+=$fields['montantht'];
						$totalttc+=$fields['montantttc'];

						echo business_format_price($fields['montantht']);?></TD>
						<?
						if ($fields['type'] == 'Facture') //  && $suivi->fields['solde'] > 0
						{
							$totalfacture+=$fields['montantht'];
							if ($fields['montanttva']>0)
							$tva=($fields['montanttva']/$fields['montantht'])+1;
							else
							$tva=1;

							if ($fields['solde'] > 0) {
							$solde+=$fields['solde']/$tva;
							?>
							<TD nowrap align="right"><? echo business_format_price($fields['solde']/$tva); ?><br />
							<a href="javascript:dims_confirmlink('<? echo "$scriptenv?op=suivi_solder&suivi_id={$fields['id']}&suivi_type={$fields['type']}&suivi_exercice={$fields['exercice']}','".$_SESSION['cste']['_DIMS_VALID']."');"; ?>">Solder</a>
							<?
							}
							else
							{
							?>
							<TD nowrap align="right"><? echo business_format_price($fields['solde']/$tva); ?></TD>

							<?
							}
						}
						else echo '<td align="right"> - - - - </td>';
						?>

						<TD width="100" align="center">
						<!--<a class="progressive previsu" href="javascript:void(0);" onclick="javascript:openSuivi(<?php echo $fields['id_suivi']; ?>);" title="<?php echo $_SESSION['cste']['_DIMS_LABEL_EDIT']; ?>">-->

						<?php
						/*
						 *
							<A HREF="<? echo $ouvrir; ?>"><img src="./common/modules/business/img/ico_modify.gif" border="0" ALT="<? echo $_SESSION['cste']['_MODIFY']; ?>"></A>
							<A HREF="javascript:dims_confirmlink('<? echo dims_urlencode("$scriptenv?op=suivi_supprimer&suivi_id={$fields['id']}&suivi_type={$fields['type']}&suivi_exercice={$fields['exercice']}"); ?>','<? echo str_replace('<VALUE>',addslashes("{$fields['type']} - ".sprintf("%s%s%06d",$fields['type'][0],$fields['exercice'],$fields['id'])." - {$fields['libelle']}"),$_SESSION['cste']['_DIMS_VALID']); ?>')"><img src="./common/modules/business/img/ico_delete.gif" border="0" ALT="<? echo $_SESSION['cste']['_DELETE']; ?>"></A>

							<a class="progressive close" href="javascript:void(0);" onclick="javascript:dims_confirmlink('/admin.php?dims_op=desktopv2&action=supprimer_suivi&id_suivi=<?php echo $fields['id_suivi']; ?>', 'Êtes-vous sûr(e) de vouloir supprimer ce suivi ?');" title="<?php echo $_SESSION['cste']['_DELETE']; ?>">
						 */
						?>
						</TD>
					</TR>
					<?
				}
				?>
				<tr>
				<td colspan="3" style="text-align: right">Total complet :
				<?php echo business_format_price($total);?>HT
				</td>
				<td colspan="4" style="text-align: right">Total facture :
					<?php echo business_format_price($totalfacture);?>HT
				</td>
				<td colspan="4" style="text-align: right">Solde facture :
				<?php echo business_format_price($solde);?>HT
				</td>
				</tr>

				<?php
			}
			else
			{
				if ($db->numrows($result)>0) echo '<tr><td colspan="10" align="center">'.$db->numrows($result).' résultats trouvés. Vous devez préciser votre recherche.</td></tr>';
				else echo '<tr><td colspan="10" align="center">Aucun résultat</td></tr>';
			}
			echo "</TABLE>";
			echo "</td></tr></TABLE>";

		}

	}

	public static function buildActivityTypesList() {
		require_once DIMS_APP_PATH."modules/system/activity/class_type.php";
		$types = activity_type::getAllTypes();

		?>
		<a href="<?php echo dims_urlencode('/admin.php?business_op=activity_type_management_edit'); ?>">
		<?php echo $_SESSION['cste']['NEW_ACTIVITY_TYPE']; ?>
		</a>
		<table>
		<?php
		foreach($types as $type) {
			?>
			<tr>
			<td><?php echo $type->fields['label']; ?></td>
			<td><span class="color" style="color:<?php echo $type->fields['color']; ?>;">&nbsp;</span></td>
			<td>
				<a style="text-decoration:none;" href="<?php echo dims_urlencode('/admin.php?business_op=activity_type_management_edit&id_type='.$type->getId()); ?>">
				<img src="/common/modules/system/desktopV2/templates/gfx/common/editer20.png" />
				</a>
				<a style="text-decoration:none;" href="<?php echo dims_urlencode('/admin.php?business_op=activity_type_management_delete&id_type='.$type->getId()); ?>">
				<img src="/common/modules/system/desktopV2/templates/gfx/common/close.png" />
				</a>
			</td>
			</tr>
			<?php
		}
		?>
		</table>

		<?php

	}

	public static function buildActivityTypesEdit($id_type = 0) {
		require_once DIMS_APP_PATH."modules/system/activity/class_type.php";

		$type = new activity_type();
		if(!empty($id_type)) {
		$type->open($id_type);
		}
		else {
		$type->init_description();
		}
		?>
		<form method="post" action="<?php echo dims_urlencode('/admin.php?business_op=activity_type_management_save'); ?>">
		<?
			// Sécurisation du formulaire par token
			require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
			$token = new FormToken\TokenField;
			$token->field("id_type",	$id_type);
			$token->field("type_label");
			$token->field("type_color");
			$tokenHTML = $token->generate();
			echo $tokenHTML;
		?>
		<input type="hidden" name="id_type" value="<?php echo $id_type; ?>" />
		<div>
			<label for="type_label">
			<?php echo $_SESSION['cste']['_DIMS_LABEL']; ?>
			</label>
			<input type="text" id="type_label" name="type_label" value="<?php echo $type->fields['label']; ?>" />
		</div>
		<div>
			<label for="type_color">
			<?php echo $_SESSION['cste']['_DIMS_LABEL_COLOR']; ?>
			</label>
			<input type="text" id="type_color" name="type_color" value="<?php echo $type->fields['color']; ?>" />
		</div>
		<div>
			<input type="submit" value="<?php echo $_SESSION['cste']['_SUBMIT']; ?>" />
		</div>
		<link type="text/css" rel="stylesheet" href="./common/js/colorpicker/css/colorpicker.css" />
		<script type="text/javascript" language="javascript" src="./common/js/colorpicker/js/colorpicker.js"></script>
		<script type="text/javascript" language="javascript">
			$(document).ready(function() {
			$('#type_color').ColorPicker({
				onSubmit: function(hsb, hex, rgb, el) {
				$(el).val('#'+hex);
				$(el).ColorPickerHide();
				},
				onBeforeShow: function () {
				$(this).ColorPickerSetColor(this.value);
				}
			})
			.bind('keyup', function(){
				$(this).ColorPickerSetColor(this.value);
			});
			});
		</script>
		</form>

		<?php
	}
}
?>
