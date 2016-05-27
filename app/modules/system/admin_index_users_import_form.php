<form action="<? echo $scriptenv; ?>" method="Post" enctype="multipart/form-data" name="form_import_users>
<?
	// S�curisation du formulaire par token
	require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
	$token = new FormToken\TokenField;
	$token->field("op", "import");
	$token->field("srcfile");
	$token->field("md5passwd");
	$tokenHTML = $token->generate();
	echo $tokenHTML;
?>
<input type="Hidden" name="op" value="import">
<table cellpadding="2" cellspacing="1" align="center" width="100%">
	<tr>
		<td align="right"><? echo $_DIMS['cste']['_DIMS_LABEL_IMPORTSRC']; ?>*:&nbsp;</td>
		<td align="left"><input class="text" type="File" name="srcfile"></td>
	</tr>
	<tr>
		<td align="right">Mots de passe MD5** ?</td>
		<td align="left"><input class="Checkbox" type="Checkbox" name="md5passwd"></td>
	</tr>
	<tr>
		<td align="center" colspan="2">
            <?php
            echo dims_create_button($_DIMS['cste']['_SYSTEM_LABELTAB_USERIMPORT'],"import","javascript:forms.form_import_users.submit();");
            ?>
        </td>
	</tr>
	<tr>
		<td colspan="2">
			<br>
			(*) La source d'import doit �tre un fichier texte, dont les champs sont s�par�s par des points-virgule.<br>
			La premi�re ligne doit �tre une ligne de description.<br>
			Cela signifie qu'elle contient certains ou tous les champs de la liste ci-dessous.<br>
			Elle sert � d�crire la structure du fichier.<br>
			Elle doit au moins contenir les champs "login" et "password".<br><br>
			Liste des champs :<br>
			- adminlevel : Niveau du compte. S'il n'est pas renseign�, il sera affect� au niveau le plus bas.<br>
			- lastname : Nom de famille.<br>
			- firstname : Pr�nom.<br>
			- login : Login. Il doit �tre unique. Il est n�cessaire � la cr�ation d'un compte.<br>
			- password : Mot de passe. Il est n�cessaire � la cr�ation d'un compte.<br>
			- email : Email.<br>
			- phone : Num�ro de t�l�phone.<br>
			- fax : Num�ro de fax.<br>
			- address : Adresse.<br>
			- comments : Commentaires.<br><br>
			Liste des valeurs possibles pour le champ "adminlevel" :<br>
			- 10 : Utilisateur<br>
			- 15 : Gestionnaire de groupe<br>
			- 20 : Administrateur de groupe<br>
			- 99 : Administrateur<br><br>

			(**)Si la case "Mots de passe MD5" est coch�e, les mots de passe seront sauvegard�s tels quels.<br>
			Dans le cas contraire, ils seront encrypt�s en MD5.
		</td>
	</tr>
</table>
</form>
