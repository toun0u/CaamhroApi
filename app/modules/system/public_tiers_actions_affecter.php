<?
echo $skin->open_simplebloc("Affectation d'une Action",'100%');

$action = new action();
$action->open($action_id);

?>

<SCRIPT LANGUAGE=javascript>
	var previous_dossier = '';
	var previous_tiers = '';

	var dossiers_sel_array = new Array();
	var tiers_sel_array = new Array();

	function rechercher_tiers(rech,dossier_id)
	{
		dims_getxmlhttp("index-light.php","xml_planning_rechercher_tiers","&rech="+rech+"&dossier_id="+dossier_id,1,"tiers");
	}

	function rechercher_tiers_delayed(rech,dossier_id)
	{
		if (rech != previous_tiers  || rech == '')
		{
			previous_tiers = rech;

			if (rech.length>=2 || dossier_id != 0)
			{
				xmlfinished=0;
				setTimeout("rechercher_tiers('"+rech+"','"+dossier_id+"')",50);
				setTimeout(is_xmlfinished_tiers,100);
			}
			//else traitement_tiers('',0);
		}

		return(true);
	}


	function rechercher_dossiers(rech,tiers_id)
	{
		dims_getxmlhttp("index-light.php","xml_planning_rechercher_dossiers","&rech="+rech+"&tiers_id="+tiers_id,1,"dossiers");
	}

	function rechercher_dossiers_delayed(rech, tiers_id)
	{
		if (rech != previous_dossier || rech == '')
		{
			previous_dossier = rech;

			if (rech.length>=2 || tiers_id != 0)
			{
				//alert(tiers_id);
				xmlfinished=0;
				setTimeout("rechercher_dossiers('"+rech+"','"+tiers_id+"')",50);
				setTimeout(is_xmlfinished_dossiers,100);
			}
		}
		//else traitement_dossiers('',0);
		return(true);
	}



	function is_xmlfinished_tiers()
	{
		if (xmlfinished) traitement_tiers(tabxmlresult[0]);
		else setTimeout(is_xmlfinished_tiers,50);
	}

	function is_xmlfinished_dossiers()
	{
		if (xmlfinished) traitement_dossiers(tabxmlresult[0]);
		else setTimeout(is_xmlfinished_dossiers,50);
	}

	var rechercher_tiers_closure;
	var rechercher_dossiers_closure;

	function createClosures()
	{
		rechercher_tiers_closure = rechercher_tiers_delayed;
		rechercher_dossiers_closure = rechercher_dossiers_delayed;
	}

	function traitement_tiers(result)
	{
		//alert(result);
		document.form_action.actiondetail_tiers_id.length = 0;
		var tiers = result.split(";;");
		for (i=0;i<tiers.length;i++)
		{
			var tiersdetail = tiers[i].split(",,");
			var newOpt  = new Option(tiersdetail[1],tiersdetail[0]);
			document.form_action.actiondetail_tiers_id.options[i] = newOpt;

			for (t=0;t<tiers_sel_array.length;t++)
			{
				if (tiers_sel_array[t] == tiersdetail[0]) document.form_action.actiondetail_tiers_id.selectedIndex = i;
			}
		}

		if (document.form_action.actiondetail_tiers_id[0].value == 0)
		{
			document.form_action.nouveau_tiers.checked = true;
		}
		else
		{
			document.form_action.nouveau_tiers.checked = false;
		}

		if (document.form_action.actiondetail_tiers_id.length == 1)
		{
			document.form_action.actiondetail_tiers_id.selectedIndex = 0;
			update_dossiers('');
		}
	}

	function traitement_dossiers(result)
	{
		//alert(result);
		document.form_action.actiondetail_dossier_id.length = 0;
		var dossiers = result.split(";;");
		for (i=0;i<dossiers.length;i++)
		{
			var dossierdetail = dossiers[i].split(",,");
			var newOpt  = new Option(dossierdetail[1],dossierdetail[0]);
			document.form_action.actiondetail_dossier_id.options[i] = newOpt;

			for (d=0;d<dossiers_sel_array.length;d++)
			{
				if (dossiers_sel_array[d] == dossierdetail[0]) document.form_action.actiondetail_dossier_id.selectedIndex = i;
			}
		}

		if (document.form_action.actiondetail_dossier_id[0].value == 0)
		{
			document.form_action.nouveau_dossier.checked = true;
		}
		else
		{
			document.form_action.nouveau_dossier.checked = false;
		}

		if (document.form_action.actiondetail_dossier_id.length == 1)
		{
			document.form_action.actiondetail_dossier_id.selectedIndex = 0;
			update_tiers('');
		}
	}

	createClosures();


	function update_dossiers(dossiers_sel)
	{
		dossiers_sel_array = dossiers_sel.split(",");
		if (document.form_action.choix[0].checked && document.form_action.actiondetail_tiers_id.selectedIndex!=-1) // clients
		{
			id_tiers = document.form_action.actiondetail_tiers_id.value
			document.form_action.actiondetail_dossier_id.length = 0;
			rechercher_dossiers_delayed('',id_tiers);

			document.form_action.rech_dossier.disabled = false;
			document.form_action.rech_dossier.value = '';
			//document.form_action.rech_dossier.focus();
		}

	}

	function update_tiers(tiers_sel)
	{
		tiers_sel_array = tiers_sel.split(",");
		if (document.form_action.choix[1].checked && document.form_action.actiondetail_dossier_id.selectedIndex!=-1) // dossiers
		{
			id_dossier = document.form_action.actiondetail_dossier_id.value
			document.form_action.actiondetail_tiers_id.length = 0;
			rechercher_tiers_delayed('',id_dossier);

			document.form_action.rech_tiers.disabled = false;
			document.form_action.rech_tiers.value = '';
			//document.form_action.rech_tiers.focus();
		}
	}

	function verif_action(form)
	{
		return (form.actiondetail_tiers_id.selectedIndex != -1 && form.actiondetail_dossier_id.selectedIndex != -1 && (!form.nouveau_tiers.checked || (form.nouveau_tiers.checked && !form.rech_tiers.value == '')) && (!form.nouveau_dossier.checked || (form.nouveau_dossier.checked && !form.rech_dossier.value == '')));
	}
</SCRIPT>

<FORM ACTION="<? echo $scriptenv; ?>" METHOD="POST">
<?
	// Sécurisation du formulaire par token
	require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
	$token = new FormToken\TokenField;
	$tokenHTML = $token->generate();
	echo $tokenHTML;
?>
<TABLE WIDTH="100%" CELLPADDING="4" CELLSPACING="0" BORDER="0" BGCOLOR="<? echo $skin->values['bgline2']; ?>">
<TR>
	<td>&nbsp;</td>
	<TD ALIGN="right" width="100">
		<INPUT TYPE="submit" CLASS="FlatButton" VALUE="Retour à la liste des Actions">
	</TD>
</TR>
</TABLE>
</FORM>

<TABLE WIDTH="100%" CELLPADDING="0" CELLSPACING="0"><TR BGCOLOR="<? echo $skin->values['colprim']; ?>"><TD HEIGHT="1"></TD></TR></TABLE>
<TABLE WIDTH="100%" CELLPADDING="0" CELLSPACING="0"><TR><TD HEIGHT="5"></TD></TR></TABLE>

<div id="div_xmlplanning">
</div>

<FORM ACTION="<? echo $scriptenv; ?>" METHOD="POST" NAME="form_action">
<?
	// Sécurisation du formulaire par token
	require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
	$token = new FormToken\TokenField;
	$token->field("op", 		"action_affecter_suite");
	$token->field("action_id",	$action->fields['id']);
	$token->field("choix");
	$token->field("rech_tiers");
	$token->field("nouveau_tiers");
	$token->field("rech_dossier");
	$token->field("nouveau_dossier");
	$token->field("actiondetail_dossier_id");
	$tokenHTML = $token->generate();
	echo $tokenHTML;
?>
<INPUT TYPE="HIDDEN" NAME="op" VALUE="action_affecter_suite">
<INPUT TYPE="HIDDEN" NAME="action_id" VALUE="<? echo $action->fields['id']; ?>">
<TABLE CELLPADDING="2" CELLSPACING="1">
<TR>
	<!-- colonne 1 -->
	<TD valign="top" width="50%">
		<TABLE CELLPADDING="2" CELLSPACING="1">
		<TR>
			<TD ALIGN="right"><? echo _BUSINESS_LABEL_DATEJOUR; ?>:&nbsp;</TD>
			<td align="left"><? echo business_dateus2fr($action->fields['datejour']); ?>&nbsp;de&nbsp;<? printf("%s à %s",$action->fields['heuredeb'],$action->fields['heurefin']); ?></td>
		</TR>
		<tr>
			<td align="right">Type:&nbsp;</td>
			<td align="left"><? echo $action->fields['typeaction']; ?></td>
		</tr>
		<tr>
			<td align="right">Libellé:&nbsp;</td>
			<td align="left"><? echo htmlentities($action->fields['libelle']); ?></td>
		</tr>
		<tr>
			<td align="right" valign="top">Description:&nbsp;</td>
			<td align="left"><? echo nl2br($action->fields['description']); ?></td>
		</tr>

		<tr>
			<td align="right" valign="top">Filtrer par:&nbsp;</td>
			<td align="left">
				<table cellpadding="0" cellspacing="0">
				<tr>
					<td valign="middle"><input type="radio" name="choix" value="tiers" checked onchange="javascript:ChoixTiersDossiers(document.form_action.choix)"></td>
					<td valign="middle">Clients</td>
					<td valign="middle"><input type="radio" name="choix" value="dossier" onchange="javascript:ChoixTiersDossiers(document.form_action.choix)"></td>
					<td valign="middle">Dossiers</td>
				</tr>
				</table>
			</td>
		</tr>


		<!--tr>
			<td align="left" colspan="2">
				<div id="tiers" style="visibility:visible;display:block;">Filtre Client:&nbsp;<input type="text" class="text" size="30" name="rech_tiers" onblur="javascript:rechercher_tiers_delayed(this.value,0)"></div>
			</td>
		</tr-->
		<tr>
			<td align="right" valign="top">Filtre Client:&nbsp;</td>
			<td align="left">
				<table cellpadding="0" cellspacing="0">
				<tr>
					<td><input type="text" class="text" size="30" name="rech_tiers" onblur="javascript:rechercher_tiers_delayed(this.value,document.form_action.actiondetail_dossier_id.value)"></td>
					<td><input type="checkbox" name="nouveau_tiers" value="1"></td>
					<td>Nouveau</td>
				</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td align="right" valign="top">Clients:&nbsp;</td>
			<td align="left">
			<select name="actiondetail_tiers_id" size="4" style="width:270px" onclick="update_dossiers('')">
			</select>
			</td>
		</tr>

		<!--tr>
			<td align="left" colspan="2">
				<div id="dossiers" style="visibility:hidden;display:none;">Filtre Dossier:&nbsp;<input type="text" class="text" size="30" name="rech_dossier" onblur="javascript:rechercher_dossiers_delayed(this.value,'0')"></div>
			</td>
		</tr-->
		<tr>
			<td align="right" valign="top">Filtre Dossier:&nbsp;</td>
			<td align="left">
				<table cellpadding="0" cellspacing="0">
				<tr>
					<td><input type="text" class="text" size="30" name="rech_dossier" onblur="javascript:rechercher_dossiers_delayed(this.value,document.form_action.actiondetail_tiers_id.value)"></td>
					<td><input type="checkbox" name="nouveau_dossier" value="1"></td>
					<td>Nouveau</td>
				</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td align="right" valign="top">Dossiers:&nbsp;</td>
			<td align="left">
			<select name="actiondetail_dossier_id" size="4" style="width:270px" onclick="update_tiers('')">
			</select>
			</td>
		</tr>
		</TABLE>

		<script language="javascript">
		ChoixTiersDossiers(document.form_action.choix)
		</script>
	</TD>
</TR>
</TABLE>

<TABLE WIDTH="100%" CELLPADDING="0" CELLSPACING="0"><TR BGCOLOR="<? echo $skin->values['colprim']; ?>"><TD HEIGHT="1"></TD></TR></TABLE>

<TABLE WIDTH="100%" CELLPADDING="2" CELLSPACING="1" BGCOLOR="<? echo $skin->values['bgline2']; ?>">
<tr>
	<td ALIGN="RIGHT"><INPUT TYPE="Submit" CLASS="FlatButton" VALUE="<? echo _DIMS_SAVE; ?>" OnClick="javascript:if (!verif_action(document.form_action)) {alert('Vous devez sélectionner un client et un dossier');return(false)}"></TD>
</tr>
</TABLE>
</FORM>

<? echo $skin->close_simplebloc(); ?>