<?

$array_modules = array();
$array_size = array('','25'=>'25%','33'=>'33%','50'=>'50%','66'=>'66%','75'=>'75%', '100'=>'100%');
$array_modules['']['label'] = '';

foreach($_SESSION['dims']['workspaces'][dims_const::_DIMS_SYSTEMGROUP]['modules'] as $homepage_moduleid)
{
	$array_modules[$homepage_moduleid]['label'] = $_SESSION['dims']['modules'][$homepage_moduleid]['label'];
}

?>

<? echo $skin->open_simplebloc(_SYSTEM_LABEL_HOMEPAGECONTENT,'100%'); ?>
<?
	if ($op=='add_homepage_line')
	{
	?>
	<FORM NAME="form_new_line" ACTION="<? echo $scriptenv; ?>" METHOD="POST">
	<?
		// Sécurisation du formulaire par token
		require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
		$token = new FormToken\TokenField;
		$token->field("op", "save_homepage_line");
		$token->field("line_nbcolumns");
		$token->field("form_column");
		$tokenHTML = $token->generate();
		echo $tokenHTML;
	?>
	<INPUT TYPE="HIDDEN" NAME="op" VALUE="save_homepage_line">
	<TABLE CELLPADDING="2" CELLSPACING="1">
	<TR>
		<TD ALIGN=RIGHT WIDTH=180><? echo _SYSTEM_LABEL_NBCOLUMNS; ?>:&nbsp;</TD>
		<TD ALIGN=LEFT>
			<SELECT class="select" NAME="line_nbcolumns">
			<OPTION>1</OPTION>
			<OPTION>2</OPTION>
			<OPTION>3</OPTION>
			<OPTION>4</OPTION>
			</SELECT>
		</TD>
	</TR>
	<TR>
		<TD ALIGN=RIGHT COLSPAN=2>
			<INPUT TYPE="Button" class="flatbutton" OnClick="javascript:location.href='<? echo $scriptenv; ?>'" VALUE="<? echo _DIMS_CANCEL; ?>">
			<INPUT TYPE="Reset" class="flatbutton" VALUE="<? echo _DIMS_RESET; ?>">
			<INPUT TYPE="Submit" class="flatbutton" VALUE="<? echo _DIMS_ADD; ?>">
		</TD>
	</TR>
	</TABLE>
	</FORM>
	<?
	}
	else
	{
	?>
	<TABLE CELLPADDING="2" CELLSPACING="1" WIDTH="100%">
	<TR>
		<TD ALIGN=RIGHT>
			<INPUT TYPE="Button" CLASS="FlatButton" VALUE="<? echo _SYSTEM_LABEL_PREVIEW ?>" OnClick="javascript:document.location.href='<? echo "$scriptenv?dims_mainmenu=".dims_const::_DIMS_MENU_HOME; ?>'">
			<INPUT TYPE="Button" CLASS="FlatButton" VALUE="<? echo _SYSTEM_LABEL_ADDLINE ?>" OnClick="javascript:document.location.href='<? echo "$scriptenv?op=add_homepage_line" ?>'">
		</TD>
	</TR>
	</TABLE>
	<?
	}
?>
<? echo $skin->close_simplebloc(); ?>


<?

$select_line = 	"SELECT *
				FROM dims_homepage_line
				WHERE id_group = :idgroup
				AND id_user = :iduser
				ORDER BY position ";

$result_line = $db->query($select_line, array(':idgroup' => dims_const::_DIMS_UNIDENTIFIEDGROUP, ':iduser' => dims_const::_DIMS_UNIDENTIFIEDUSER) );

while ($fields_line = $db->fetchrow($result_line))
{
	$icons = "
		<A HREF=\"<LINK_ADD>\"><IMG BORDER=0 SRC=\"".$skin->get_image('icon_add')."\"></A>
		<A HREF=\"<LINK_DOWN>\"><IMG BORDER=0 SRC=\"".$skin->get_image('arrow_bottom')."\"></A>
		<A HREF=\"<LINK_UP>\"><IMG BORDER=0 SRC=\"".$skin->get_image('arrow_top')."\"></A>
		<A HREF=\"<LINK_CLOSE>\"><IMG BORDER=0 SRC=\"".$skin->get_image('icon_close')."\"></A>
		";

	$link = "$scriptenv?op=move_homepage_line&homepage_line_id=$fields_line[id]";

	$icons_link = $icons;
	$icons_link = str_replace('<LINK_ADD>',"$scriptenv?op=add_homepage_column&homepage_line_id=$fields_line[id]",$icons_link);
	$icons_link = str_replace('<LINK_DOWN>',"$link&move=down",$icons_link);
	$icons_link = str_replace('<LINK_UP>',"$link&move=up",$icons_link);
	$icons_link = str_replace('<LINK_CLOSE>',"$link&move=close",$icons_link);

	echo $skin->open_simplebloc(' ','100%',$icons_link);

	$select_column =	"SELECT 	dims_homepage_column.*,
									dims_module.public,
									dims_module.active,
									dims_module.viewmode,
									dims_module.transverseview
						FROM 		dims_homepage_column
						LEFT JOIN	dims_module ON dims_module.id = dims_homepage_column.id_module
						WHERE 		dims_homepage_column.id_line = :idline
						ORDER BY 	dims_homepage_column.position ";

	$result_column = $db->query($select_column, array(':idline' => $fields_line['id']) );


	echo 	"
		<TABLE WIDTH=100% CELLPADDING=1 CELLSPACING=1>
		<TR>
			<TD>
			<TABLE WIDTH=100% CELLPADDING=0 CELLSPACING=0>
			<TR>
		";
	$numrows = $db->numrows($result_column);
	$c=1;


	$icons = "
		<A HREF=\"<LINK_LEFT>\"><IMG BORDER=0 SRC=\"".$skin->get_image('arrow_left')."\"></A>
		<A HREF=\"<LINK_RIGHT>\"><IMG BORDER=0 SRC=\"".$skin->get_image('arrow_right')."\"></A>
		<A HREF=\"<LINK_CLOSE>\"><IMG BORDER=0 SRC=\"".$skin->get_image('icon_close')."\"></A>
		";

	while ($fields_column = $db->fetchrow($result_column))
	{
		$size = $fields_column['size']-1;

		$link = "$scriptenv?op=move_homepage_column&homepage_column_id=$fields_column[id]";

		$icons_link = $icons;
		$icons_link = str_replace('<LINK_LEFT>',"$link&move=left",$icons_link);
		$icons_link = str_replace('<LINK_RIGHT>',"$link&move=right",$icons_link);
		$icons_link = str_replace('<LINK_CLOSE>',"$link&move=close",$icons_link);

		$list_size = '';
		foreach($array_size as $key => $value)
		{
			if ($fields_column['size'] == $key) $sel = 'selected';
			else $sel = '';
			$list_size .= "<OPTION $sel VALUE=\"$key\">$value</OPTION>";
		}

		$list_modules = '';
		foreach($array_modules as $key => $value)
		{
			if ($fields_column['id_module'] == $key) $sel = 'selected';
			else $sel = '';
			$list_modules .= "<OPTION $sel VALUE=\"$key\">$value[label]</OPTION>";
		}


		echo "<TD ALIGN=CENTER VALIGN=TOP xyz WIDTH=\"".$size."%\">";
		echo $skin->open_simplebloc(dims_strcut($fields_column['title'],20),'100%',$icons_link);
		echo 	"
			<TABLE CELLPADDING=2 CELLSPACING=1 ALIGN=CENTER>
			<FORM NAME=\"form_column".$fields_line['id']."-".$fields_column['id']."\" ACTION=\"$scriptenv\" METHOD=\"POST\">"

		// Sécurisation du formulaire par token
		require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
		$token = new FormToken\TokenField;
		$token->field("op");
		$token->field("homepage_column_id");
		$token->field("homepage_column_id_module");
		$token->field("homepage_column_title");
		$token->field("homepage_column_size");
		$tokenHTML = $token->generate();
		echo $tokenHTML;

		echo 	"
			<INPUT TYPE=\"HIDDEN\" NAME=\"op\" VALUE=\"save_homepage_column\">
			<INPUT TYPE=\"HIDDEN\" NAME=\"homepage_column_id\" VALUE=\"$fields_column[id]\">
			<TR>
				<TD ALIGN=\"RIGHT\">"._SYSTEM_LABEL_MODULE.":&nbsp;</TD>
				<TD ALIGN=\"LEFT\">
				<SELECT style=\"width:100px\" class=\"select\" NAME=\"homepage_column_id_module\">
				$list_modules
				</SELECT>
				</TD>
			</TR>
			<TR>
				<TD ALIGN=\"RIGHT\">"._SYSTEM_LABEL_TITLE.":&nbsp;</TD>
				<TD ALIGN=\"LEFT\">
				<INPUT class=\"text\" TYPE=\"Text\" style=\"width:100px\" MAXLENGTH=100 NAME=\"homepage_column_title\" VALUE=\"$fields_column[title]\">
				</TD>
			</TR>
			<TR>
				<TD ALIGN=\"RIGHT\">"._SYSTEM_LABEL_SIZE.":&nbsp;</TD>
				<TD ALIGN=\"LEFT\">
				<SELECT style=\"width:100px\" class=\"select\" NAME=\"homepage_column_size\">
				$list_size
				</SELECT>
				</TD>
			</TR>
			";


		if (!is_null($fields_column['active']))
		{
			$active = $public = $shared = $herited = '<img src="./common/modules/system/img/ico_point_red.gif" align="middle">';
			$viewmode = '';

			if ($fields_column['active'])
			{
				$active = '<img src="./common/modules/system/img/ico_point_green.gif" align="middle">';
			}

			if ($fields_column['public'])
			{
				$public = '<img src="./common/modules/system/img/ico_point_green.gif" align="middle">';
			}

			$viewmode = $dims_viewmodes[$fields_column['viewmode']];

			if ($fields_column['transverseview']) $viewmode .= ' '._SYSTEM_LABEL_TRANSVERSE;

			echo	"
				<TR>
					<TD ALIGN=\"CENTER\" COLSPAN=\"2\">
					<TABLE CELLPADDING=\"2\" CELLSPACING=\"1\" WIDTH=\"100%\" CLASS=\"Skin\">
					<TR CLASS=\"Title\" BGCOLOR=\"".$skin->values['bgline2']."\">
						<TD ALIGN=\"CENTER\">Vue</TD>
						<TD ALIGN=\"CENTER\">Actif</TD>
						<TD ALIGN=\"CENTER\">Public</TD>
					</TR>
					<TR BGCOLOR=\"".$skin->values['bgline1']."\">
						<TD ALIGN=\"CENTER\">$viewmode</TD>
						<TD ALIGN=\"CENTER\">$active</TD>
						<TD ALIGN=\"CENTER\">$public</TD>
					</TR>
					</TABLE>
					</TD>
				</TR>
				";
		}

		echo	"


			<TR>
				<TD COLSPAN=\"2\" ALIGN=\"RIGHT\">
				<INPUT TYPE=\"Submit\" CLASS=\"FlatButton\" VALUE=\""._DIMS_MODIFY."\">
				</TD>
			</TR>
			</FORM>
			</TABLE>
			";

		echo $skin->close_simplebloc();
		echo "</TD>";
		if ($c++ < $numrows) echo "<TD WIDTH=1%></TD>";
	}
	echo 	"
			</TR>
			</TABLE>
			</TD>
		</TR>
		</TABLE>
		";

	echo $skin->close_simplebloc();

}
?>