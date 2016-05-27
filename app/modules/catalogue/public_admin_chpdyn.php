<?php
include_once './common/modules/catalogue/include/class_champ.php';

$a_types = array(
	'texte' => 'Texte simple',
	'liste' => 'Liste de choix'
	);

switch ($op) {
	case 'edit':
		$champ = new cata_champ();

		$id_field = dims_load_securvalue('id', dims_const::_DIMS_NUM_INPUT, true, true,true);

		if(!empty($id_field)) {
			$title = 'Modifier un champ';
			$champ->open($id_field);
			$champ->valeurs = $champ->getvaleurs();
		} else {
			$title = 'Ajouter un champ';
			$champ->init_description();
			$champ->valeurs = array();
		}

		echo $skin->open_simplebloc($title, '100%');
			?>
                        <script type="text/javascript" >
                            var verifcolor = false;

                            function field_validate(form) {
                                    form.valeurs.value = '';

                                    t = form.chp_type;

                                    if (t.value == 'liste' || t.value == 'select' || t.value == 'radio' || t.value == 'checkbox')
                                    {
                                            for (i=0;i<form.c_valeurs.length;i++)
                                            {
                                                    if (form.valeurs.value != '') form.valeurs.value += '||';
                                                    form.valeurs.value += form.c_valeurs[i].value;
                                            }
                                    }
                                    else if (t.value == 'tablelink') form.valeurs.value = form.f_formfield.value;

                                    if (dims_validatefield('<? echo $_DIMS['cste']['_FORMS_FIELD_NAME']; ?>',form.chp_libelle,"string"))
                                            return(true);

                                    return(false);
                            }

                            function add_value(lst,val)
                            {
                                    if (val.value != '')
                                    {
                                            if ((verifcolor && dims_validatefield('couleur', val, 'color')) || !verifcolor)
                                            {
                                                    if (verifcolor)
                                                    {
                                                            color = new dims_rgbcolor(val.value);
                                                            rgbcolor = color.toHex();
                                                            lst.options[lst.length] = new Option('', rgbcolor);
                                                            lst.options[lst.length-1].style.backgroundColor = rgbcolor;
                                                    }
                                                    else lst.options[lst.length] = new Option(val.value, val.value);

                                            }
                                    }
                                    val.value = '';
                                    val.focus();
                            }

                            function modify_value(lst,val)
                            {
                                    if ((verifcolor && dims_validatefield('couleur', val, 'color')) || !verifcolor)
                                    {
                                            sel = lst.selectedIndex;
                                            if (sel>-1)
                                            {
                                                    if (verifcolor)
                                                    {
                                                            color = new dims_rgbcolor(val.value);
                                                            rgbcolor = color.toHex();
                                                            lst.options[sel].value = rgbcolor;
                                                            lst.options[sel].text = '';
                                                            lst.options[sel].style.backgroundColor = color.toHex();
                                                    }
                                                    else
                                                    {
                                                            lst.options[sel].value = val.value;
                                                            lst.options[sel].text = val.value;
                                                    }
                                            }
                                    }
                                    val.focus();
                            }

                            function delete_value(lst)
                            {
                                    sel = lst.selectedIndex;

                                    if (sel < lst.length-1)
                                    {
                                            lst[sel] = lst[sel+1];
                                            lst.selectedIndex = sel;
                                    }
                                    else lst.length--;
                            }

                            function move_value(lst,mv)
                            {
                                    sel = lst.selectedIndex;
                                    if (sel-mv>=0 && sel-mv< lst.length)
                                    {
                                            var tmp;
                                            tmp = lst[sel-mv].value;

                                            if (verifcolor)
                                            {
                                                    lst[sel-mv].value = lst[sel].value;
                                                    lst[sel-mv].style.backgroundColor = lst[sel-mv].value;

                                                    lst[sel].value = tmp;
                                                    lst[sel].style.backgroundColor = lst[sel].value;
                                            }
                                            else
                                            {
                                                    lst[sel-mv].text = lst[sel].value;
                                                    lst[sel-mv].value = lst[sel].value;

                                                    lst[sel].text = tmp;
                                                    lst[sel].value = tmp;
                                            }
                                            lst.selectedIndex=lst.selectedIndex-mv;
                                    }
                            }
                    </script>
			<form name="f_chp" action="<? echo $dims->getScriptEnv(); ?>" method="post" onsubmit="javascript:return field_validate(this);">
			<input type="hidden" name="op" value="save">
			<input type="hidden" name="id" value="<? echo $champ->fields['id']; ?>">
			<input type="hidden" name="valeurs" value="<? echo htmlentities(implode('||', $champ->valeurs)); ?>">

			<?
			foreach ($champ->valeurs as $key => $value) {
				?><input type="hidden" name="id_<? echo $value; ?>" value="<? echo $key; ?>"><?
			}
			?>

			<table cellpadding="2" cellspacing="1">
			<tr>
				<th>Intitulé :</th>
				<td><input class="text" type="text" name="chp_libelle" value="<? echo $champ->fields['libelle']; ?>"></td>
			</tr>
			<tr>
				<th>Type :</th>
				<td>
					<select class="select" name="chp_type" onchange="javascript:display_fieldvalues();">
						<?
						foreach ($a_types as $key => $value) {
							$sel = ($champ->fields['type'] == $key) ? ' selected' : '';
							echo "<option value=\"{$key}\"{$sel}>{$value}</option>";
						}
						?>
					</select>
				</td>
			</tr>
			</table>

			<table cellpadding="2" cellspacing="1" id="fieldvalues" style="display: none">
			<tr>
				<th valign="top">Valeurs :</th>
				<td>
					<select name="c_valeurs" class="select" size="12" style="width:250px" onclick="document.f_chp.newvalue.value=this.value;document.f_chp.newvalue.focus();">
					<?
					if ($champ->fields['type'] == 'liste') {
						$a_vals = array();
						foreach ($champ->valeurs as $value) {
							if ($value != '') {
								$a_vals[$value] = $value;
							}
						}
						foreach ($a_vals as $value) {
							echo "<option value=\"$value\">$value</option>";
						}
					}
					?>
					</select>
				</td>
				<td valign="top">
					<input style="width:25px;" type="button" class="button" value="+" onclick="javascript:move_value(document.f_chp.c_valeurs,1)">
					<br />
					<input style="width:25px;margin-top:4px;" type="button" class="button" value="-" onclick="javascript:move_value(document.f_chp.c_valeurs,-1)">
				</td>
			</tr>
			<tr>
				<td></td>
				<td>
					<input style="width:250px;" name="newvalue" type="text" class="text">
				</td>
				<td></td>
			</tr>
			<tr>
				<td></td>
				<td>
					<input type="button" class="button" value="Ajouter" onclick="javascript:add_value(document.f_chp.c_valeurs, document.f_chp.newvalue)">
					<input type="button" class="button" value="Supprimer" onclick="javascript:delete_value(document.f_chp.c_valeurs)">
				</td>
				<td></td>
			</tr>
			</table>

			<br/>
			<table cellpadding="2" cellspacing="1">
			<tr>
				<td><input class="button" type="button" value="Retour" onclick="javascript:document.location.href='<? echo $scriptenv; ?>';"></td>
				<td align="right"><input class="button" type="submit" value="Enregistrer"></td>
			</tr>
			</form>
			</table>

			<script language="javascript">
				function display_fieldvalues() {
					t = document.f_chp.chp_type;
					if (t.value == 'texte') {
						document.getElementById('fieldvalues').style.display='none';
					} else {
						document.getElementById('fieldvalues').style.display='block';
					}
				}

				// temp -> saisie
				function Nenterevent(e) {
					switch(e.keyCode) {
						case 13:
							add_value(document.f_chp.c_valeurs, document.f_chp.newvalue);
							return false;
						break;
					}
				}

				if (window.attachEvent) {
					window.attachEvent('onload', display_fieldvalues);
				} else {
					window.onload = display_fieldvalues();

					// temp -> saisie
					document.captureEvents( Event.KEYDOWN );
					document.onkeydown = Nenterevent;
				}
			</script>
			<?
		echo $skin->close_simplebloc();
		break;

	case 'save':
                //dims_print_r($_POST);die();
		$champ = new cata_champ();
		if (!empty($_POST['id']) && is_numeric($_POST['id'])) {
			$champ->open($_POST['id']);
			$champ->orig_valeurs = $champ->getvaleurs();
		}
		$champ->setvalues($_POST, 'chp_');
		$champ->valeurs = $_POST['valeurs'];
		$champ->save();

		dims_redirect($scriptenv);
		break;

	case 'delete':
		if (!empty($_GET['id']) && is_numeric($_GET['id'])) {
			$champ = new cata_champ();
			$champ->open($_GET['id']);
			$champ->delete();
		}

		dims_redirect($scriptenv);
		break;

	default:
		$a_champs = array();
		$db->query("
			SELECT	*
			FROM	dims_mod_cata_champ
			ORDER BY id");
		while ($row = $db->fetchrow()) {
			$a_champs[] = $row;
		}

		$color = $skin->values['bgline2'];

		echo $skin->open_simplebloc('Liste des champs', '100%');
			?>
			<table cellpadding="2" cellspacing="1" style="width: 100%">
			<tr>
				<td colspan="4"><input class="button" type="button" value="Ajouter un champ" onclick="javascript:document.location.href='<? echo $scriptenv ?>?op=edit';"></td>
			</tr>
			<tr bgcolor="<? echo $color; ?>">
				<th align="right">&nbsp;Id&nbsp;</th>
				<th>&nbsp;Intitulé&nbsp;</th>
				<th>&nbsp;Type&nbsp;</th>
				<th>&nbsp;</th>
			</tr>
			<?
			if (sizeof($a_champs)) {
				foreach ($a_champs as $chp) {
					$color = ($color == $skin->values['bgline2']) ? $skin->values['bgline1'] : $skin->values['bgline2'];

					echo "
						<tr bgcolor=\"$color\">
							<td align=\"right\">&nbsp;{$chp['id']}&nbsp;</td>
							<td>&nbsp;{$chp['libelle']}&nbsp;</td>
							<td>&nbsp;{$a_types[$chp['type']]}&nbsp;</td>
							<td>&nbsp;<a href=\"$scriptenv?op=edit&id={$chp['id']}\">Modifier</a> | <a href=\"javascript:dims_confirmlink('$scriptenv?op=delete&id={$chp['id']}','Etes-vous sûr(e) ?');\">Supprimer</a>&nbsp;</td>
						</tr>";
				}
			}
			?>
			</table>
			<?
		echo $skin->close_simplebloc();
		break;
}
?>
