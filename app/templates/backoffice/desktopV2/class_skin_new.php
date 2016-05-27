<?
include_once DIMS_APP_PATH . "/include/class_skin_common.php";

class skin extends skin_common {
	function skin() {
		parent::skin_common('dims');
	}


		function create_toolbar($icons,&$iconsel=0, $sel = true, $vertical = false,$stylemenu="") {
			if ($vertical){
				return parent::create_toolbar($icons,$iconsel, $sel, $vertical,$stylemenu);
			}
			else {
		if (!isset($icons[$iconsel])) $iconsel = -1;

		$icons_content_left = '';
		$icons_content_right = '';

		if ($sel) {
					foreach($icons AS $key => $value) {
						if ($iconsel == -1) $iconsel = $key;
					}
		}

				$num=rand();
				$res= '<div id="tabs'.$num.'"><ul>';

		foreach($icons AS $key => $value) {
			$res.="<li> ".$this->create_icon($icons[$key], ($iconsel == $key), $key, $vertical,$stylemenu)."</li>";
		}

		//$res .= '</div>';

				$res.='<script type="text/javascript">
						$(function() {
								$("#tabs'.$num.'").tabs();
						});
						</script>';
		return $res;
			}
	}

		function close_toolbar() {
			return '</div>';
		}

		function create_icon($icon, $sel, $key, $vertical,$stylemenu) {
		$confirm = isset($icon['confirm']);

		$title = $icon['title'];

		if (!empty($icon['javascript'])) $onclick = $icon['javascript'];
		elseif ($confirm) $onclick = "dims_confirmlink('".dims_urlencode($icon['url'])."','{$icon['confirm']}')";
		else $onclick = "document.location.href='".dims_urlencode($icon['url'])."'";

		if (isset($icon['icon']))
		{
			$classpng = '';
			//if (strtolower(substr($icon['icon'],-4,4)) == '.png') $classpng = 'class="png"';
			$image = "<img $classpng alt=\"".strip_tags($title)."\" border=\"0\" src=\"$icon[icon]\">";
		}
		else $image = '';

		$class = ($vertical) ? 'toolbar_icon_vertical' : '';

		$style = (!empty($icon['width'])) ? "style=\"width:{$icon['width']}px;\"" : '';

		if ($sel)
		{
			$res =	"
					<div class=\"".$stylemenu."{$class}_sel\" id=\"{$key}\" {$style}>
						<a href=\"javascript:void(0);\" onclick=\"javascript:{$onclick};return false;\">
							<div>$image</div>
							<div>$title</div>
						</a>
					</div>
					";
		}
		else
		{
			$res =	"
					<div class=\"".$stylemenu."{$class}\" id=\"{$key}\" {$style}>
						<a href=\"javascript:void(0);\" onclick=\"javascript:{$onclick};return false;\">
							<div >$image</div>
							<div >$title</div>
						</a>
					</div>
					";
		}

		//if ($sel) $res ="<TD WIDTH=\"".$icon['width']."\" ALIGN=CENTER><FONT CLASS=TabSel><A CLASS=TabSel HREF=\"".$icon['url']."\">".$icon['title']."</A></TD>";
		//else $res ="<TD WIDTH=\"".$icon['width']."\" BGCOLOR=\"".$this->values['colsec']."\" ALIGN=CENTER><A CLASS=Tab HREF=\"".$icon['url']."\">".$icon['title']."</A></TD>";
		return $res;
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

			//echo '<span id="'.$nametab.$id.'_before" class="onglet_'.$sel.'_before"/>&nbsp;</span>
			//		<span id="'.$nametab.$id.'_label" class="onglet_'.$sel.'"><span style="height:20px;float:left;">'.$elem['name'].'</span></span>
			//		<span id="'.$nametab.$id.'_after" class="onglet_'.$sel.'_after"/>&nbsp;</span>
			//</li>';
		}

		echo '</ul>';
	}

		function open_simplebloc($title = '', $style = '', $styletitle = '', $image='', $height='', $width='', $top='', $left='', $href="#", $onclick="", $a_style="") {
			$res='<div style="width:100%;'.$style.'" class="ui-tabs ui-widget ui-widget-content ui-corner-all">';
			$res.='<div style="width:100%;" class="ui-widget-header ui-corner-top">'.$title.'</div>';
			$res.='<div style="width:100%;">';
			return $res;
		}

		function close_simplebloc() {
			return '</div></div>';
		}

	function open_simplebloc_old($title = '', $style = '', $styletitle = '', $image='', $height='', $width='', $top='', $left='', $href="#", $onclick="", $a_style="") {
		if($style == "" || $style == "100%") $style = "width:100%";
		$res = '<table cellpadding="0" cellspacing="0" border="0" style="'.$style.'">
					<tr>
						<td id="w_left_top">';
		if($image != '') {
						$res .= '	<div style="position:relative;z-index:1;">';
						//$res .= $onclick;
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
						$res .= '</td>
								<td id="w_center_top">';
									if ($title!="") $res.="<div id=\"wtitle\" style=\"".$styletitle."\">".$title."</div>";
									else $res .= '&nbsp;';
						$res .='</td>
								<td id="w_right_top">&nbsp;</td>
							</tr>
							<tr>
								<td id="w_left_center">&nbsp;</td>
								<td id="w_center_center">
									<div id="wcorps">';
				//$res="<div class=\"NSFrame_widget\" style=\"".$style."\">";
				//if ($title!="") $res.="<div id=\"wbtitle\"><div id=\"wtitle\" style=\"".$styletitle."\">".$title."</div></div>";
				//$res .= "";

		return $res;
	}

	function close_simplebloc_old() {
		$fin = '			</div>
						</td>
						<td id="w_right_center">&nbsp;</td>
					</tr>
					<tr>
						<td id="w_left_bot">&nbsp;</td>
						<td id="w_center_bot">&nbsp;</td>
						<td id="w_right_bot">&nbsp;</td>
					</tr>
				</table>';
		return $fin;
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
		//$res="</div></div><div class=\"blockbm\"><div class=\"blockbg\"/></div><div class=\"blockbd\"></div></div>";
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
		$res="<div class=\"NSFrame_blue\" style=\"".$style."\">";
		if ($title!="") $res.="<div id=\"btitle\"><div id=\"title\" style=\"".$styletitle."\">".$title."</div></div>";
		$res .= "<div id=\"corps\">";

		return $res;
	}

	function close_backgroundbloc() {
		$fin = "</div></div>";
		return $fin;
	}

	function open_widgetbloc($title = '', $style = '', $styletitle = '', $image='', $height='', $width='', $top='', $left='', $href="#", $onclick="", $a_style="") {
		$res = '<table cellpadding="0" cellspacing="0" border="0" style="'.$style.'">
					<tr>
						<td id="w_left_top">';
		if($image != '') {
						$res .= '	<div style="position:relative;z-index:1;">';
						//$res .= $onclick;
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
						$res .= '</td>
								<td id="w_center_top">';
									if ($title!="") $res.="<div id=\"wtitle\" style=\"".$styletitle."\">".$title."</div>";
									else $res .= '&nbsp;';
						$res .='</td>
								<td id="w_right_top">&nbsp;</td>
							</tr>
							<tr>
								<td id="w_left_center">&nbsp;</td>
								<td id="w_center_center">
									<div id="wcorps">';

		return $res;
	}

	function close_widgetbloc() {
		$fin = '</div>';
		return $fin;
	}

	function open_infobloc($title = '', $style = '', $styletitle = '', $image='', $height='', $width='', $top='', $left='', $href="#", $onclick="", $a_style="") {
		$res='<div style="width:100%;'.$style.'" class="ui-tabs ui-widget ui-widget-content ui-corner-all">';
		$res.='<div style="width:100%;" class="ui-widget-header ui-corner-top">'.$title.'</div>';
		$res.='<div style="width:100%;">';
		return $res;
	}

	function close_infobloc() {
		$fin = '</div></div>';
		return $fin;
	}

}
?>
