<?php
$dims = dims::getInstance();
global $skin;

$suivi = $this->getLightAttribute('suivi');

$lstcata=$dims->getModuleByType('catalogue');
if (!empty($lstcata)) {
	require_once(DIMS_APP_PATH . '/modules/catalogue/include/class_catalogue.php');
	$cata = new catalogue();
	$searchArttext=' onkeyup="javascript:searchArticleCatalogueKey();" ';
}
else {
	$searchArttext='';
}
?>

<FORM ACTION="<? echo $dims->getScriptEnv(); ?>" METHOD="POST" NAME="form_detail_suivi">
<?
	// Sécurisation du formulaire par token
	require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
	$token = new FormToken\TokenField;
	$token->field("dims_op",	"desktopv2");
	$token->field("action",		"suivi_detail_enregistrer");
	$token->field("id_suivi",	$suivi->getIdSuivi());
	$token->field("suivi_detail_id",$this->fields['id']);
	$token->field("suivi_detail_code");
	$token->field("suivi_detail_libelle");
	$token->field("suivi_detail_pu");
	$token->field("suivi_detail_description");
	$token->field("suivi_detail_qte");
	$token->field("suivi_detail_tauxtva");
	$tokenHTML = $token->generate();
	echo $tokenHTML;
?>
<INPUT TYPE="HIDDEN" NAME="dims_op" VALUE="desktopv2" />
<INPUT TYPE="HIDDEN" NAME="action" VALUE="suivi_detail_enregistrer" />
<INPUT TYPE="HIDDEN" NAME="id_suivi" VALUE="<?php echo $suivi->getIdSuivi(); ?>" />

<INPUT TYPE="HIDDEN" NAME="suivi_detail_id" VALUE="<?php echo $this->fields['id']; ?>" />

<TABLE CELLPADDING="2" CELLSPACING="1">
<tr>
	<td align="right">Code:&nbsp;</td>
	<td><input type="text" <? echo $searchArttext;?> class="text" size="32" id="suivi_detail_code" name="suivi_detail_code" value="<?php echo $this->fields['code']; ?>"></td>
		<td>&nbsp;</td>
		<td  rowspan="2">
			<div id="dyncontentArticle" name="dyncontentArticle" style="height:80px;">

			</div>
		</td>
</tr>
<tr>
	<td align="right" valign="top">Libellé:&nbsp;</td>
			<td><textarea class="text" rows="2" cols="30" id="suivi_detail_libelle" name="suivi_detail_libelle"><?php echo $this->fields['libelle']; ?></textarea></td>
</tr>
<tr>
	<td align="right">Prix Unitaire:&nbsp;</td>
	<td><input type="text" class="text" size="10" id="suivi_detail_pu" name="suivi_detail_pu" value="<?php echo $this->fields['pu']; ?>"></td>
		<td align="right" valign="top" rowspan="3">Description:&nbsp;</td>
	<td align="left" valign="top" rowspan="3">
			<textarea name="suivi_detail_description" rows="6" cols="60" class="text"><?php echo $this->fields['description']; ?></textarea>
	</td>
</tr>
<tr>
	<td align="right">Quantité:&nbsp;</td>
	<td><input type="text" class="text" size="5" id="suivi_detail_qte" name="suivi_detail_qte" value="<?php echo $this->fields['qte']; ?>"></td>
</tr>
<tr>
	<td align="right">Taux TVA:&nbsp;</td>
	<td>
			<?
			// selection du tva

				if (!empty($lstcata)) {

					$lsttva=$cata->getTva();

					echo '<select  id="suivi_detail_tauxtva" name="suivi_detail_tauxtva">';
					foreach ($lsttva as $tva) {
						echo '<option value="'.$tva.'">'.$tva.'</option>';
					}
					echo '</select>';
				}
				else {
			?>
				<input type="text" class="text" size="10" name="suivi_detail_tauxtva" value="<?php echo $this->fields['tauxtva']; ?>">
			<?
				}
			?>
		</td>
</tr>
</table>

<TABLE WIDTH="100%" CELLPADDING="0" CELLSPACING="0"><TR BGCOLOR="<?php echo $skin->values['colprim']; ?>"><TD HEIGHT="1"></TD></TR></TABLE>

<TABLE WIDTH="100%" CELLPADDING="2" CELLSPACING="1" BGCOLOR="<?php echo $skin->values['bgline2']; ?>">
<tr>
	<td ALIGN="RIGHT">
		<?php
		echo dims_create_button('Enregistrer la ligne', 'check', 'document.form_detail_suivi.submit()');
		echo dims_create_button('Annuler', 'cancel', 'document.getElementById(\'suiviDetail\').innerHTML=\'\'');
		?>
	</TD>
</tr>
</TABLE>

</FORM>

<script language="javascript">
	$(document).ready(function() {
		document.form_detail_suivi.suivi_detail_code.focus();
	});
</script>
<?php
