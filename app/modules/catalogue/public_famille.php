<script type="text/javascript">
function form_validate(form) {
	if (dims_validatefield("<? echo _CATA_MANAGE_FAMILY_FORM_LABEL; ?>",form.f_label,"string"))
		return true;
	return false;
}
</script>

<?php
$obj_famille = new cata_famille();
$obj_famille->open($famId);

// managing the $op variable
if (!isset($op)) $op = '';

$isallowed = 1;
// --------------------
// Operation MANAGEMENT
// --------------------

switch($op) {
	case 'save':
		//dims_print_r($_POST);
		//die();

		$ancien_pos = 0;
		//$obj_famille = new cata_famille();
		//$obj_famille->open($famId);

		$obj_famille->setvalues($_POST, 'f_');

		/*
		 * Bloc ne servant qu'a gerer l'ordre des familles
		 */
		$i = 1;
		//Pour eviter les 'trous' on renumérote tout
		$sup = $db->query("
			SELECT	id_famille
			FROM	dims_mod_cata_famille
			WHERE	id_parent = ".$obj_famille->fields['id_parent']."
			AND		id_module = {$_SESSION['dims']['moduleid']}
			ORDER BY position");
		while ($fi = $db->fetchrow($sup)) {
			if ($obj_famille->fields['id_famille'] == $fi['id_famille']) $ancien_pos = $i;
			$db->query("UPDATE dims_mod_cata_famille SET position = $i WHERE id_famille = ".$fi['id_famille']);
			$i++;
		}

		// verify position change
		if ($obj_famille->fields['position'] != $ancien_pos) {
			// must update concerning heading
			$pas = $obj_famille->fields['position'] - $ancien_pos;
			if ($ancien_pos != 0) {
				$pas = $pas / abs($pas);
				$dec = $pas * (-1);

				for ($i = $ancien_pos + $pas; $i != $obj_famille->fields['position'] + $pas; $i += $pas) {
					$select = "
						UPDATE	dims_mod_cata_famille
						SET		position = position+(".$dec.")
						WHERE	id_parent = ".$obj_famille->fields['id_parent']."
						AND		position =".$i."
						AND		id_module = {$_SESSION['dims']['moduleid']}";
					$db->query($select);
				}
			}
		}

		// cyril : gestion du rewrite -- il faudrait savoir si c'est activé par un param !
		if (_DIMS_URLREWRITE) {
			$toRewrite = $obj_famille->fields['label'];
		}

		$obj_famille->save();
		cata_updateparents($obj_famille->fields['id_famille'], $obj_famille->fields['parents'], $obj_famille->fields['depth']+1);

		dims_redirect("$scriptenv?famId=$famId");
		break;
	case 'child':
		$obj_famille_child = new cata_famille();
		$obj_famille_child->open($famId);
		$famId = $obj_famille_child->createchild();
		dims_redirect("$scriptenv?famId=$famId");
		break;
	case 'clone':
		$obj_famille_clone = new cata_famille();
		$obj_famille_clone->open($famId);
		$famId = $obj_famille_clone->createclone();
		dims_redirect("$scriptenv?famId=$famId");
		break;
	case 'confirm_delete':
		echo $skin->open_simplebloc(_CATA_LABEL_DELETE_FAMILY,'100%');
		?>
		<table cellpadding="5">
			<tr>
				<td rowspan="5"><?php echo _CATA_LABEL_CONFIRMDELFAMILY ?></td>
			</tr>
			<tr>
				<td><a href="<?php echo $dims->getScriptEnv(). "?famId=$famId&op=delete&art=0&child=0"; ?>">Oui</a></td>
				<td rowspan="5"><a href="<?php echo $dims->getScriptEnv(). "?famId=$famId"; ?>">Non</a></td>
			</tr>
			<tr>
				<td><a href="<?php echo $dims->getScriptEnv(). "?famId=$famId&op=delete&art=1&child=0"; ?>"><?php echo _CATA_LABEL_CONFIRMDELFAMILY_ALL; ?></a></td>
			</tr>
			<tr>
				<td><a href="<?php echo $dims->getScriptEnv()."?famId=$famId&op=delete&art=0&child=1"; ?>"><?php echo _CATA_LABEL_CONFIRMDELFAMILY_CHILD; ?></a></td>
			</tr>
			<tr>
				<td><a href="<?php echo $dims->getScriptEnv()."?famId=$famId&op=delete&art=1&child=1"; ?>"><?php echo _CATA_LABEL_CONFIRMDELFAMILY_CHILD_ALL; ?></a></td>
			</tr>
		</table>
		<?php
		echo $skin->close_simplebloc();
		break;
	case 'delete':
		$art = dims_load_securvalue('art', dims_const::_DIMS_NUM_INPUT, true, false);
		$child = dims_load_securvalue('child', dims_const::_DIMS_NUM_INPUT, true, false);

		if(empty($art)) $art = 0;
		if(empty($child)) $child = 0;

		$obj_del = new cata_famille();
		$obj_del->open($famId);
		$famId = $obj_del->fields['id_parent'];
		$obj_del->delete($child, $art);

		$parent = new cata_famille();
		$parent->open($famId);

		if (!empty($familys['tree'][$famId])) {
			foreach ($familys['tree'][$famId] as $children) {
				cata_updateparents($parent->fields['id_famille'], $parent->fields['parents'], $parent->fields['depth'] + 1);
			}
		}
		dims_redirect($dims->getScriptEnv().'?famId='.$famId);
		break;
	case 'reorganize_all_positions':
		function reorganize_all_positions($famId) {
			global $db;
			global $familys;

			if (!empty($familys['tree'][$famId])) {
				foreach ($familys['tree'][$famId] as $id => $fam) {
					$familys['list'][$fam]['position'] = $id + 1;

					for ($d = 1; $d < $familys['list'][$fam]['depth']; $d++) {
						echo '-';
					}
					echo $familys['list'][$fam]['label'].' -> '.($id + 1).'<br/>';
					$db->query('UPDATE dims_mod_cata_famille SET position = '.($id + 1).' WHERE id_famille = '.$fam);

					reorganize_all_positions($fam);
				}
			}
		}

		reorganize_all_positions(1);
		break;
	case 'reorganize_all_parents':
		$fam = new cata_famille();
		$fam->open(1);
		cata_updateparents($fam->fields['id_famille'], $fam->fields['parents'], $fam->fields['depth'] + 1);
		break;
	default:
		echo $skin->open_simplebloc(_CATA_MANAGE_FAMILY_MODIFY,'100%'); ?>
		<FORM NAME="manage" ACTION="<? echo "$scriptenv?famId=$famId&op=save" ?>" method="post" OnSubmit="javascript:return form_validate(this)">
		<TABLE CELLPADDING="3" CELLSPACING="2">

		<!-- label -->
		<TR>
			<TD ALIGN="RIGHT" VALIGN="MIDDLE">
				<? echo _CATA_MANAGE_FAMILY_FORM_LABEL ?>
			</TD>
			<TD>
				<INPUT TYPE="TEXT" NAME="f_label" SIZE="50" <? if (!$isallowed) echo " DISABLED "; ?> VALUE="<? echo $obj_famille->fields['label'] ?>">
			</TD>
		</TR>

		<!-- description -->
		<TR>
			<TD ALIGN="RIGHT" VALIGN="TOP">
				<? echo _CATA_MANAGE_FAMILY_FORM_DESCRIPTION ?>
			</TD>
			<TD>
				<?php echo dims_fckeditor("f_description", $obj_famille->fields['description'], '800', '350'); ?>
			</TD>
		</TR>
		<TR>
			<TD ALIGN="RIGHT" VALIGN="MIDDLE">
				<? echo _CATA_MANAGE_FAMILY_FORM_PARENT ?>
			</TD>
			<TD>
				<SELECT <? if (!$isallowed) echo " DISABLED "; ?>NAME="f_id_parent">
					<?
					echo "<OPTION VALUE=\"0\">"._CATA_MANAGE_FAMILY_ROOT."</OPTION>";

					foreach(getfamilystree($_SESSION['dims']['moduleid'],1) as $id => $fdg)
					{
						$lab= substr($fdg['label'],0,100);

						for($i=1;$i<$fdg['depth'];$i++)
						{
							$lab='- '.$lab;
						}
						($obj_famille->fields['id_parent'] == $fdg['id_famille']) ? $selected = ' SELECTED' : $selected = '';

						if($obj_famille->fields['id_famille'] != $fdg['id_famille']) //ne pas mettre lui-même
						echo "<OPTION VALUE=\"{$fdg['id_famille']}\"$selected>{$lab}</OPTION>";
					}
					?>
				</SELECT>
			</TD>
		</TR>
		<!-- position -->
		<TR>
			<TD ALIGN="RIGHT" VALIGN="MIDDLE">
				<? echo _CATA_MANAGE_FAMILY_POSITION ?>
			</TD>
			<TD>
				<SELECT <? if (!$isallowed) echo " DISABLED "; ?>NAME="f_position">
					<?php
					// buld dir sel for all modules available
					$selectheading = "
						SELECT	position
						FROM	dims_mod_cata_famille
						WHERE	id_module = {$_SESSION['dims']['moduleid']}
						AND		id_parent=".$obj_famille->fields['id_parent']."
						ORDER BY position";
					$answerheading = $db->query($selectheading);
					$pos=1;
					while ($fields_heading = $db->fetchrow($answerheading)) {
						$selected = '';
						if($obj_famille->fields['position'] == $fields_heading['position']) $selected = ' SELECTED';

						echo "<OPTION VALUE=\"$pos\"$selected>$pos</OPTION>";
						$pos++;
					}
					?>
				</SELECT>
			</TD>
		</TR>
		<!-- Visibilité -->
		<TR>
			<TD ALIGN="RIGHT" VALIGN="MIDDLE">
				<? echo _CATA_MANAGE_FAMILY_VISIBLE ?>
			</TD>
			<TD>
				<INPUT TYPE="hidden" NAME="f_visible" VALUE="0">
				<INPUT TYPE="checkbox" NAME="f_visible" <? if (!$isallowed) echo " DISABLED "; ?> VALUE="1" <? if($obj_famille->fields['visible']==1) echo 'checked'; ?>>
			</TD>
		</TR>
		<!-- validation -->
		<TR>
			<TD COLSPAN="2" ALIGN="CENTER" VALIGN="MIDDLE">
				<INPUT TYPE="SUBMIT"  <? if (!$isallowed) echo " DISABLED "; ?>VALUE="<? echo _CATA_MANAGE_FAMILY_FORM_SUBMIT ?>" CLASS="Button">
			</TD>
		</TR>

	</TABLE>
	</FORM>
	<?php
	echo $skin->close_simplebloc();
	break;
}

if ($op != "confirm_delete") {
	// on developpe la toolbar du bas
	unset($toolbar);
	$toolbar = array();

	$toolbar[_CATA_TOOLBAR_BTN_FAMILY_CHILD]['title'] = _CATA_MANAGE_FAMILYS_CHILD;
	$toolbar[_CATA_TOOLBAR_BTN_FAMILY_CHILD]['url'] = "$scriptenv?famId=$famId&op=child&dims_moduleicon="._CATA_TOOLBAR_BTN_FAMILY_CHILD;
	$toolbar[_CATA_TOOLBAR_BTN_FAMILY_CHILD]['icon'] = './common/modules/catalogue/img/managing/btn_family_child.png';
	$toolbar[_CATA_TOOLBAR_BTN_FAMILY_CHILD]['width'] = '100';
	$toolbar[_CATA_TOOLBAR_BTN_FAMILY_CHILD]['id_help'] = '';

	if ($obj_famille->fields['id_famille'] > 1) {
		$toolbar[_CATA_TOOLBAR_BTN_FAMILY_CLONE]['title'] = _CATA_MANAGE_FAMILYS_CLONE;
		$toolbar[_CATA_TOOLBAR_BTN_FAMILY_CLONE]['url'] = "$scriptenv?famId=$famId&op=clone&dims_moduleicon="._CATA_TOOLBAR_BTN_FAMILY_CLONE;
		$toolbar[_CATA_TOOLBAR_BTN_FAMILY_CLONE]['icon'] = './common/modules/catalogue/img/managing/btn_family_clone.png';
		$toolbar[_CATA_TOOLBAR_BTN_FAMILY_CLONE]['width'] = '100';
		$toolbar[_CATA_TOOLBAR_BTN_FAMILY_CLONE]['id_help'] = '';

		$toolbar[_CATA_TOOLBAR_BTN_FAMILY_DELETE]['title'] = _CATA_MANAGE_FAMILYS_DELETE;
		$toolbar[_CATA_TOOLBAR_BTN_FAMILY_DELETE]['url'] = "$scriptenv?famId=$famId&op=confirm_delete&dims_moduleicon="._CATA_TOOLBAR_BTN_FAMILY_DELETE;
		$toolbar[_CATA_TOOLBAR_BTN_FAMILY_DELETE]['icon'] = './common/modules/catalogue/img/managing/btn_family_delete.png';
		$toolbar[_CATA_TOOLBAR_BTN_FAMILY_DELETE]['width'] = '100';
		$toolbar[_CATA_TOOLBAR_BTN_FAMILY_DELETE]['id_help'] = '';
	}

	echo $skin->open_simplebloc('','100%');
	//echo $skin->create_toolbar('',$toolbar,$_SESSION['dims']['moduleicon']);
	echo $skin->create_toolbar($toolbar,$_SESSION['dims']['moduleicon']);
	echo $skin->close_simplebloc();
}
