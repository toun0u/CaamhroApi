<?
include_once DIMS_APP_PATH . "/include/class_skin_common.php";

class skin extends skin_common {
	function skin() {
		parent::skin_common('dims');
	}

	function displayOnglet($list,$nametab,$block_id=0,$selectedvalue='',$fonctionJavascript='refreshDesktop') {
		echo '<ul class="items-tabs" id="'.$nametab.'">';

		foreach ($list as $id => $elem) {
			$sel = ($selectedvalue==$elem['id']) ? "actif" : "inactif";
			$classsel = ($selectedvalue==$elem['id']) ? "selected" : "";
			if (isset($elem['link']) && $elem['link']!='') {
				echo '<li class="'.$classsel.'" id="'.$nametab.$id.'" onclick="javascript:document.location.href=\''.$elem['link'].'\';">';
			}
			else {
				if (isset($elem['onclick'])) {
					echo '<li class="'.$classsel.'" id="'.$nametab.$id.'" onclick="javascript:'.$elem['onclick'].');">';
				}
				else {
					echo '<li class="'.$classsel.'" id="'.$nametab.$id.'" onclick="javascript:'.$fonctionJavascript.'('.$block_id.','.$elem['id'].');">';
				}
			}
			echo $elem['name']."</li>";
		}

		echo '</ul>';
	}

	function close_toolbar() {
		return '</div>';
	}

	function open_simplebloc($title = '', $style = '', $styletitle = '', $image='', $height='', $width='', $top='', $left='', $href="#", $onclick="", $a_style="") {
		$res = '<div style="'.$style.'" class="ui-widget ui-widget-content ui-corner-all ui-helper-clearfix">';
		if ($onclick!='') {
			$res.= '<div class="ui-widget-header ui-corner-top" style="cursor:pointer;" onclick="'.$onclick.'">'.$title.'</div>';
		}
		else {
			$res.= '<div class="ui-widget-header ui-corner-top">'.$title.'</div>';
		}
		$res.=	'<div class="ui-helper-clearfix" style="overflow:hidden;">';
		return $res;
	}

	function close_simplebloc() {
		return '</div></div>';
	}

	function open_simplebloc_classic($title = '', $style = '', $styletitle = '', $additionnal_title = '',$displayaccess=false) {
		$res="<div class=\"NSFrame\" style=\"".$style."\">
			<table class=\"main\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" summary=\"#\">
			<tbody>
				<tr class=\"top_title_mini\">
					<td class=\"left\"><img src=\"./common/img/1.gif\"/></td>
					<td class=\"center\" style=\"font-size:10px;height:18px\">";

		if ($title!="") $res.="<span style=\"float:left;\">$title</span>";

		if ($displayaccess) {

			// test pour l'administration du module minotiee

			if (dims_isactionallowed(0) || dims_isadmin() || dims_isactionallowed(-1,$_SESSION['dims']['workspaceid'],$_SESSION['dims']['moduleid'])) {
					//$urladmin="<a href=\"#\" title=\"Acc�s en administration\" onclick=\"\"><img src=\"./common/img/configure.png\"	alt=\"Acc�s en administration\" /></a>";
					$urladmin= dims_create_button(_DIMS_LABEL_ADMIN,"./common/img/configure.png","document.location.href='".dims_urlencode("admin.php?dims_moduleid=".$_SESSION['dims']['moduleid']."&dims_desktop=block&dims_action=admin")."'","","");
			}
			else
				$urladmin="";

			//$urlpublic="<a href=\"#\" title=\"Acc�s public\" onclick=\"document.location.href='".dims_urlencode("admin.php?dims_moduleid=".$_SESSION['dims']['moduleid']."&dims_desktop=block&dims_action=public")."'\"><img src=\"./common/img/public.png\" alt=\"Acc�s public\"/></a>";
			if (isset($urladmin))
				$res.="<span style=\"float:right;display:inline;width:350px;\">".$urladmin."</div>"	;
		}

		if ($additionnal_title!="") {
			$res.="<span >".$urlpublic."</span><span style=\"float:right;display:inline;\">".$additionnal_title."</span>"	;
		}

		$res .= "</td><td class=\"right\"><img src=\"./common/img/1.gif\"/></td></tr>
				<tr class=\"body\">
					<td class=\"left\"></td>
					<td class=\"center\" style=\"border: 0px solid red; margin: 0px; padding: 0px;\">";

		return $res;
	}

	function close_simplebloc_classic() {
		$res = "</td><td class=\"right\"><img src=\"./common/img/1.gif\"/></td></tr>";
		$res .= "<tr class=\"bottom\">
						<td class=\"left\"></td>
						<td class=\"center\"></td>
						<td class=\"right\"></td;
				</tr>
				</tbody>
			</table></div>";
		return $res;
	}

	function open_backgroundbloc($title = '', $style = '', $styletitle = '') {
		$res = '<div class="ui-widget ui-widget-content ui-corner-all ui-helper-clearfix NSFrame_blue" style="'.$style.'">
				<div class="ui-widget-header ui-corner-top" id="btitle"><div id="title" style="'.$styletitle.'">'.$title.'</div></div>
				<div class="ui-helper-clearfix" id="corps">';
		return $res;
	}

	function close_backgroundbloc() {
		$fin = "</div></div>";
		return $fin;
	}

	function open_widgetbloc($title = '', $style = '', $styletitle = '', $image='', $height='', $width='', $top='', $left='', $href="#", $onclick="", $a_style="") {
		$res = '<div class="ui-widget ui-widget-content" style="'.$style.'">';
		$res.='<div class="ui-widget-header ui-corner-top">';
		if($image != '') {
			$res .= '<a href="'.$href.'" onclick="'.$onclick.'">
						<img src="'.$image.'"/>
					</a>';
		}
		$res .= $title.'</div><div>';
		return $res;
	}

	function close_widgetbloc() {
		$fin = '</div></div>';
		return $fin;
	}

	function open_infobloc($title = '', $style = '', $styletitle = '', $image='', $height='', $width='', $top='', $left='', $href="#", $onclick="", $a_style="") {
		$res = '<table cellpadding="0" cellspacing="0" border="0" style="'.$style.'">
					<tr>
						<td id="inf_left_top">&nbsp;';

				$res .= '</td>
						<td id="inf_center_top">';
							if ($title!="") $res.="<div class=\"ui-accordion ui-accordion-header\" style=\"".$styletitle."\">".$title."</div>";
							else $res .= '&nbsp;';
				$res .='</td>
						<td id="inf_right_top">';
						if($image != '') {
							$res .= '	<div style="position:relative;z-index:1;">';
									$res .= '<a href="'.$href.'" onclick="'.$onclick.'" style="display:block;'.$a_style.'">
												<div style="position:absolute;with:'.$width.';height:'.$height.';top:'.$top.';left:'.$left.';z-index:5;">
													<img src="'.$image.'"/>
												</div>
											</a>
										</div>';
						}
						else {
							$res .= '&nbsp;';
						}
				$res.=	'</td>
					</tr>
					<tr>
						<td id="inf_left_center">&nbsp;</td>
						<td id="inf_center_center">
							<div id=\"infcorps\">';
		return $res;
	}

	function close_infobloc() {
		$fin = '			</div>
						</td>
						<td id="inf_right_center">&nbsp;</td>
					</tr>
					<tr>
						<td id="inf_left_bot">&nbsp;</td>
						<td id="inf_center_bot">&nbsp;</td>
						<td id="inf_right_bot">&nbsp;</td>
					</tr>
				</table>';
		return $fin;
	}

	function open_dialog($title = '', $style = '', $styletitle = '', $image='', $height='', $width='', $top='', $left='', $href="#", $onclick="", $a_style="") {
		$res = ' <div  class="ui-dialog-titlebar ui-widget-header ui-corner-all ui-helper-clearfix">
					<span id=ui-dialog-title-dialog" class="ui-dialog-title">'.$title.'</span>
					<a class="ui-dialog-titlebar-close ui-corner-all" href="#" role="button" onClick="dims_getelem(\'dims_popup\').style.display=\'none\'">
						<span class="ui-icon ui-icon-closethick">close</span>
					</a>
				</div>
				<div class="ui-dialog-content ui-widget-content">';
		return $res;
	}

	function close_dialog($onClick = '') {
		$res = '</div>';
		$res .= '	<div class="ui-dialog-buttonpane ui-widget-content ui-helper-clearfix">
					<div class="ui-dialog-buttonset">
						<button class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" onclick="'.$onClick.'">
							<span class="ui-button-text">Ok</span>
						</button>
					</div>
				</div>';
		return $res;
	}

}
?>
