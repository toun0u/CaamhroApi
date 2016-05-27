<?php
$dims = dims::getInstance();
$db = $dims->getDb();
global $skin;
global $business_periode;

$id_popup = $this->getLightAttribute('id_popup');

// on est sur la fiche du tiers
if(isset($_SESSION['desktopv2']['concepts']['filters']['stack'][0][1]))
	$tiers_go = $_SESSION['desktopv2']['concepts']['filters']['stack'][0][1];

// chargement des paramètres
$res=$db->query("SELECT * FROM dims_mod_business_params WHERE id_workspace = :idworkspace", array(
	':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $_SESSION['dims']['workspaceid']),
));
$params = array();
while( $fields = $db->fetchrow($res) ) $params[$fields['param']] = $fields['value'];
?>

<div>
	<div class="actions">
		<a href="Javascript: void(0);" onclick="Javascript: dims_closeOverlayedPopup('<?php echo $id_popup; ?>');">
			<img src="modules/courrier/templates/backoffice/img/icon_close.gif" />
		</a>
	</div>
	<h2><?php echo $_SESSION['cste']['_DIMS_ADD_MONITORING']; ?></h2>

	<FORM ACTION="<? echo $dims->getScriptEnv(); ?>" METHOD="POST" NAME="form_suivi" onsubmit="return(suivi_validate(this));">
	<?
		// Sécurisation du formulaire par token
		require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
		$token = new FormToken\TokenField;
		$token->field("dims_op",		"desktopv2");
		$token->field("action",			"suivi_enregistrer");
		$token->field("suivi_exercice", (isset($params['exercice']) ? $params['exercice'] : ""));
		$token->field("suivi_type");
		$token->field("suivi_datejour");
		$token->field("suivi_libelle");
		$token->field("suivi_dossier_id");
		$token->field("suivi_description");
		$tokenHTML = $token->generate();
		echo $tokenHTML;
	?>
	<input type="hidden" name="dims_op" value="desktopv2" />
	<input type="hidden" name="action" value="suivi_enregistrer" />
	<input type="hidden" name="suivi_exercice" READONLY value="<? echo (isset($params['exercice']) ? $params['exercice'] : ""); ?>" />

	<TABLE CELLPADDING="2" CELLSPACING="1">
	<TR>
		<!-- colonne 1 -->
		<TD valign="top" width="50%">
			<TABLE CELLPADDING="2" CELLSPACING="1">
			<tr>
				<td align="right"><?php echo $_SESSION['cste']['_DUTY']; ?>:&nbsp;</td>
				<td align="left"><? echo (isset($params['exercice']) ? $params['exercice'] : "") ?></td>
			</tr>
			<tr>
				<td align="right"><?php echo $_SESSION['cste']['_TYPE']; ?>:&nbsp;</td>
				<td align="left">
				<select class="select" name="suivi_type">
				<?
				$listenum = business_getlistenum('typesuivi', false);
				foreach($listenum as $id_enum => $enum) {
					if ($enum['libelle']=='Facture') $sele='selected';
					else $sele='';
					echo "<option $sele value=\"{$enum['libelle']}\">{$enum['libelle']}</option>";
				}
				?>
				</select>
				</td>
			</tr>
			<TR>
				<TD ALIGN="right"><?php echo $_SESSION['cste']['_DIMS_DATE']; ?>:&nbsp;</TD>
				<td align="left">
					<input maxlength="10" name="suivi_datejour" id="suivi_datejour" size="20" class="text" value="<? echo date('d/m/Y'); ?>">
				</td>
			</TR>
			<tr>
				<td align="right"><?php echo $_SESSION['cste']['_AGENDA_LABEL_LABEL']; ?>:&nbsp;</td>
				<td align="left">
					<input type="text" class="text" size="30" name="suivi_libelle" value="">
				</td>
			</tr>
			<tr>
				<td align="right" valign="top"><?php echo $_SESSION['cste']['_DOC_FOLDER']; ?>:&nbsp;</td>
				<td align="left">
				<select class="select" name="suivi_dossier_id" style="width:250px">
				<?php
				// recherche de tous les dossiers du client
				require_once(DIMS_APP_PATH . "/modules/system/class_search.php");
				$matrix = new search();
				$linkedObjectsIds = $matrix->exploreMatrice(
					null,
					null,
					null,
					isset($_SESSION['desktopv2']['concepts']['filters']['stack'][0][1]) ? array($_SESSION['desktopv2']['concepts']['filters']['stack'][0][1]) : array(),
					null,
					null,
					null,
					array(0),
					null,
					null
					);
				$desktop = new desktopv2();
				$lstObj = $desktop->getLinkedObjects($linkedObjectsIds, $_SESSION['desktop']['concept']['tags']);

				foreach ($lstObj['dossiers'] as $dossier) {
					echo '<option value="'.$dossier->fields['id_globalobject'].'">'.$dossier->fields['label'].'</option>';
				}
				?>
				</select>
				</td>
			</tr>
			</TABLE>
		</TD>

		<!-- colonne 2 -->
		<TD valign="top" width="50%">
			<TABLE CELLPADDING="2" CELLSPACING="1">
			<tr>
				<td align="right" valign="top"><?php echo $_SESSION['cste']['_DIMS_COMMENTS']; ?>:&nbsp;</td>
				<td align="left">
					<textarea name="suivi_description" rows="6" cols="40" class="text"></textarea>
				</td>
			</tr>
			</TABLE>
		</TD>
	</TR>

	</TABLE>
	<TABLE WIDTH="100%" CELLPADDING="0" CELLSPACING="0"><TR BGCOLOR="<? echo $skin->values['colprim']; ?>"><TD HEIGHT="1"></TD></TR></TABLE>

	<TABLE WIDTH="100%" CELLPADDING="2" CELLSPACING="1" BGCOLOR="<? echo $skin->values['bgline2']; ?>">
	<tr>
		<td ALIGN="RIGHT" COLSPAN="2">
			<INPUT TYPE="Submit" CLASS="FlatButton" VALUE="<? echo $_SESSION['cste']['_DIMS_SAVE']; ?>">
			ou <a href="Javascript: void(0);" onclick="Javascript: dims_closeOverlayedPopup('<?php echo $id_popup; ?>');"><?php echo $_SESSION['cste']['_DIMS_CANCEL']; ?></a>
		</td>
	</tr>
	</TABLE>

	</FORM>
</div>

<script language="javascript" type="text/javascript">
function suivi_validate(form) {
	if (dims_validatefield("Type",form.suivi_type,"string"))
	if (dims_validatefield("Libellé",form.suivi_libelle,"string"))
		return true;

	return false;
}
</script>

<?php
