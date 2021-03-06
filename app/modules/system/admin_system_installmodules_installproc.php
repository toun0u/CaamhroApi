<?php
ini_set('max_execution_time', 0);

require_once DIMS_APP_PATH . '/modules/system/xmlparser_mod.php';
require_once DIMS_APP_PATH . '/modules/system/xmlparser_mb.php';
require_once DIMS_APP_PATH . '/include/class_dims_xml2array.php';

global $idmoduletype;
$idmoduletype = -1;

$installmoduletype=dims_load_securvalue('installmoduletype',dims_const::_DIMS_CHAR_INPUT,true,false,false);

echo $skin->open_simplebloc($_DIMS['cste']['_DIMS_LABEL_INSTALLREPORT'],'100%');
?>
<FORM ACTION="<?php echo $scriptenv; ?>" METHOD="POST">
<TABLE CELLPADDING="2" CELLSPACING="1" WIDTH="100%">

<?php
$select = "SELECT * FROM dims_module_type WHERE label = :label ";
$res=$db->query($select, array(':label' => addslashes($installmoduletype)) );
if ($db->numrows($res)) {
	echo "<TR BGCOLOR=\"{$skin->values['bgline1']}\"><TD>Module déjà installé !</TD></TR>";
}
else {
	$sqlfile = DIMS_APP_PATH . "/install/$installmoduletype/structure.sql";
	$xmlfile_desc = DIMS_APP_PATH . "/install/$installmoduletype/description.xml";
	$xmlfile_data = DIMS_APP_PATH . "/install/$installmoduletype/data.xml";
	$mbfile = DIMS_APP_PATH . "/install/$installmoduletype/mb.xml";
	$srcfiles = DIMS_APP_PATH . "/install/$installmoduletype/files";
	$destfiles = DIMS_APP_PATH . "/modules/$installmoduletype";

	$ok = '<td align="center"><img border="0" src="'.$_SESSION['dims']['template_path'].'./common/img/system/p_green.png"></td>';
	$error = '<td align="center"><img border="0" src="'.$_SESSION['dims']['template_path'].'./common/img/system/p_red.png"></td>';

	$critical_error = false;

	?>
	<TR BGCOLOR="<?php echo $skin->values['bgline1']; ?>">
		<TD><b>Copie des fichiers</b></TD>
		<?php
		// COPY FILES
		if (file_exists($srcfiles)) {
			if (is_writable(realpath(DIMS_APP_PATH . "/modules/"))) {
				dims_copydir($srcfiles , $destfiles);
				echo "<TD>Fichiers copiés</TD>$ok";
			}
			else {
				echo "<TD>Impossible de copier les fichiers dans '$destfiles'</TD>$error";
				$critical_error = true;
			}
		}
		else echo "<TD>Aucun fichier à copier</TD>$ok";
		?>
	</TR>
	<TR BGCOLOR="<?php echo $skin->values['bgline2']; ?>">
		<TD><b>Chargement des paramètres/actions</b></TD>
		<?php
		 if (file_exists($xmlfile_desc)) {
			$fp = fopen($xmlfile_desc, 'r');
			$data = fread ($fp, filesize ($xmlfile_desc));
			fclose($fp);

			$x2a = new dims_xml2array();
			$xmlarray = $x2a->parse($data);
			$pt = &$xmlarray['root']['dims'][0]['moduletype'][0];

			require_once DIMS_APP_PATH . '/modules/system/class_module_type.php';
			require_once DIMS_APP_PATH . '/modules/system/class_param_type.php';
			require_once DIMS_APP_PATH . '/modules/system/class_param_choice.php';
			require_once DIMS_APP_PATH . '/modules/system/class_mb_action.php';
			require_once DIMS_APP_PATH . '/modules/system/class_mb_cms_object.php';


			//dims_print_r($pt);
			$module_type = new module_type();
			$module_type->fields = array(	'label'			=> $pt['label'][0],
											'version'		=> $pt['version'][0],
											'author'		=> $pt['author'][0],
											'date'			=> $pt['date'][0],
											'publicparam'	=> $pt['publicparam'][0],
											'description'	=> $pt['description'][0],
											'contenttype'	=> $pt['contenttype'][0]
										);

			$idmoduletype = $module_type->save();

			if (!empty($pt['paramtype'])) {
				foreach($pt['paramtype'] as $key => $value) {
					$param_type = new param_type();
					$param_type->fields = array(	'id_module_type'	=> $module_type->fields['id'],
													'name'				=> $value['name'][0],
													'label'				=> $value['label'][0],
													'default_value'		=> $value['default_value'][0],
													'public'			=> $value['public'][0],
													'description'		=> $value['description'][0]
												);

					$param_type->save();

					if (!empty($value['paramchoice'])) {
						foreach($value['paramchoice'] as $ckey => $cvalue) {
							$param_choice = new param_choice();
							$param_choice->fields = array(	'id_module_type'	=> $module_type->fields['id'],
															'name'				=> $param_type->fields['name'],
															'value'				=> $cvalue['value'][0],
															'displayed_value'	=> $cvalue['displayed_value'][0]
														);
							$param_choice->save();
						}
					}
				}
			}

			if (!empty($pt['cms_object']))
			{
				foreach($pt['cms_object'] as $key => $value)
				{
					$mb_cms_object = new mb_cms_object();
					$mb_cms_object->fields = array(	'id_module_type'	=> $module_type->fields['id'],
													'label' => $value['label'][0],
													'script' => $value['script'][0],
													'select_id' => $value['select_id'][0],
													'select_label' => $value['select_label'][0],
													'select_table' => $value['select_table'][0]
												);
					$mb_cms_object->save();
				}
			}

			if (!empty($pt['action']))
			{
				foreach($pt['action'] as $key => $value)
				{
					$mb_action = new mb_action();
					$mb_action->fields = array(	'id_module_type'	=> $module_type->fields['id'],
												'id_action' => $value['id_action'][0],
												'label' => $value['label'][0],
												'id_object' => (!empty($value['id_object'][0])) ? $value['id_object'][0] : 0
											);
					$mb_action->save();
				}
			}

			echo "<TD>Fichier '$xmlfile_desc' importé</TD>$ok";

		}
		else echo "<TD>Fichier '$xmlfile_desc' non trouvé</TD>$ok";
		?>
	</TR>
	<?php
	if (!$critical_error)
	{
		dims_create_user_action_log(_SYSTEM_ACTION_INSTALLMODULE, $installmoduletype);
		?>
		<TR BGCOLOR="<?php echo $skin->values['bgline1']; ?>">
			<TD><b>Création des tables/champs</b></TD>
			<?php
			// CREATE TABLES / FIELDS
			if (file_exists($sqlfile))
			{
				$tabsql = array();
				$sql = '';

				$fd = fopen ($sqlfile, "r");
				while (!feof($fd))
				{
					$sql .= fgets($fd, 4096);
				}
				fclose ($fd);

				$sql = trim($sql);
				$tabsql = dims_parsesql($sql);

				foreach ($tabsql AS $key => $value)
				{
					$value = trim($value);
					if ($value!='')
					{
						$res=$db->query($value);
					}
				}
				echo "<TD>Fichier '$sqlfile' importé</TD>$ok";
			}
			else echo "<TD>Fichier '$sqlfile' non trouvé</TD>$ok";
			?>
		</TR>
		<TR BGCOLOR="<?php echo $skin->values['bgline1']; ?>">
			<TD><b>Chargement des données spécifiques</b></TD>
			<?php
			// INSERT MODULE DATA FROM XML
			if (file_exists($xmlfile_data))
			{
				if (!(list($xml_parser, $fp) = xmlparser_mod($xmlfile_data)))
				{
					echo "<TD>Erreur de lecture du fichier XML ($xmlfile_data)</TD>$error";
				}
				else
				{
					$stop = '';
					while (($data = fread($fp, 4096)) && $stop == '')
					{
						if (!xml_parse($xml_parser, $data, feof($fp)))
						{
							$stop = sprintf("Erreur XML: %s à la ligne %d dans '$xmlfile_data'\n", xml_error_string(xml_get_error_code($xml_parser)), xml_get_current_line_number($xml_parser));
						}
					}

					if ($stop != '') echo "<TD>$stop</TD>$error";
					else echo "<TD>Fichier '$xmlfile_data' importé</TD>$ok";

					//xmlparser_mod_free($xml_parser);
					xml_parser_free($xml_parser);
				}
			}
			else echo "<TD>Fichier '$xmlfile_data' non trouvé</TD>$ok";
			?>
		</TR>
		<TR BGCOLOR="<?php echo $skin->values['bgline2']; ?>">
			<TD><b>Chargement de la métabase</b></TD>
			<?php

			// INSERT MODULE DATA FROM XML
			if (file_exists($mbfile))
			{
				if (!(list($xml_parser, $fp) = xmlparser_mb($mbfile)))
				{
					echo "<TD>Erreur de lecture du fichier XML ($mbfile)</TD>$error";
				}
				else
				{
					$stop = '';
					while (($data = fread($fp, 4096)) && $stop == '')
					{
						if (!xml_parse($xml_parser, $data, feof($fp)))
						{
							$stop = sprintf("Erreur XML: %s à la ligne %d dans '$mbfile'\n", xml_error_string(xml_get_error_code($xml_parser)), xml_get_current_line_number($xml_parser));
						}
					}

					if ($stop != '') echo "<TD>$stop</TD>$error";
					else echo "<TD>Fichier '$mbfile' importé</TD>$ok";

					xml_parser_free($xml_parser);
					//xmlparser_mod_free($xml_parser);
				}
			}
			else echo "<TD>Fichier '$mbfile' non trouvé</TD>$ok";
			?>
		</TR>
		<?php
	}
}
?>
<TR>
	<TD COLSPAN="3" ALIGN="right"><INPUT TYPE="SUBMIT" CLASS="flatbutton" VALUE="<?php echo $_DIMS['cste']['_DIMS_CONTINUE']; ?>"></TD>
</TR>
</TABLE>
</FORM>
<?php echo $skin->close_simplebloc(); ?>
