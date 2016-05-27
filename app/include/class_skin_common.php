<?php
require_once 'ajax_datatable.php';

class skin_common {
	const I_DISPLAY_LENGTH = 15;
	const PAGE_NUMBERS_SHOWN = 2;

	private static $skin_instance = null;

	public static function getInstance(){
			if(skin_common::$skin_instance == null){
				global $skin ;
				if($skin != null){
					skin_common::$skin_instance = $skin;
				}else{
					skin_common::$skin_instance = new skin_common();
				}
			}
			return skin_common::$skin_instance ;
	}

	function skin_common($skin) {
		$this->values = array();
		$this->values['path'] = "./common/templates/backoffice$skin/img";
		$this->values['inifile'] = "./common/templates/backoffice$skin/skin.ini";
		if (file_exists($this->values['inifile'])) {
			$this->values = array_merge($this->values,parse_ini_file($this->values['inifile']));
		}
	}

	function create_toolbar($icons,&$iconsel=0, $sel = true, $vertical = false,$stylemenu="") {
		if (!isset($icons[$iconsel])) $iconsel = -1;
		$icons_content = '';
		if ($sel){
			foreach($icons AS $key => $value){
				if ($iconsel == -1) $iconsel = $key;
			}
		}

		foreach($icons AS $key => $value){
			if ($sel){
				$icons_content .= $this->create_icon($icons[$key], ($iconsel == $key), $key, $vertical,$stylemenu);
			}else{
				$icons_content .= $this->create_icon($icons[$key], false, $key, $vertical,$stylemenu);
			}
		}
		$res = '<div class="ui-tabs ui-widget ui-widget-content ui-corner-all">
					<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">'.$icons_content.'</ul>
				';

		return $res;
	}
		function close_toolbar() {
			return '</div>';
		}

	function create_onglet($icons,&$iconsel=0, $sel = true, $vertical = false,$stylemenu="",$nomdiv='',$nomdivdest='') {
		if (isset($iconsel) && !isset($icons[$iconsel])) $iconsel = -1;
		$icons_content = '';

		if ($sel) {
			foreach($icons AS $key => $value) {
				if ($iconsel == -1) $iconsel = $key;
			}
		}

		foreach($icons AS $key => $value) {
			if ($sel)
				$icons_content .= $this->create_icon($icons[$key], ($iconsel == $key), $key, $vertical,$stylemenu);
			else
				$icons_content .= $this->create_icon($icons[$key], false, $key, $vertical,$stylemenu);
		}

		if(empty($nomdiv)) $nomdiv = uniqid();

		$res = '
			<script>
				$(function() {
						$( "#'.$nomdiv.'" ).tabs({
								ajaxOptions: {
										error: function( xhr, status, index, anchor ) {
												$( anchor.hash ).html( "Error" );
										}
								}
						});
				});
			</script>';

		$res .= '
			<div id="'.$nomdiv.'" class="ui-tabs ui-widget ui-corner-all">
				<ul >'.$icons_content.'</ul>
			</div>';

		return $res;
	}

	function create_menu($icons,&$iconsel=0, $sel = true, $vertical = false,$stylemenu="") {
		if (!isset($icons[$iconsel])) $iconsel = -1;
		$icons_content = '';
		if ($sel){
			foreach($icons AS $key => $value){
				if ($iconsel == -1) $iconsel = $key;
			}
		}
		foreach($icons AS $key => $value){
			if(!empty($value['title'])) {
				if ($sel){
					$icons_content .= $this->create_menu_elem($icons[$key], ($iconsel == $key), $key, $vertical,$stylemenu);
				}else{
					$icons_content .= $this->create_menu_elem($icons[$key], false, $key, $vertical,$stylemenu);
				}
			}
		}
		$res = '<div>
					<ul class="ui-menu ui-widget ui-widget-content ui-corner-all">'.$icons_content.'</ul>
				</div>';

		return $res;
	}


	function create_icon($icon, $sel, $key, $vertical,$stylemenu)
	{
		$confirm = isset($icon['confirm']);

		$title = $icon['title'];
				$href='';
				$onclick = '';

		if (!empty($icon['javascript'])) $onclick = $icon['javascript'];
		elseif ($confirm) $onclick = "dims_confirmlink('".dims_urlencode($icon['url'])."','{$icon['confirm']}')";
		else {
					$href=dims_urlencode($icon['url']);
				}

		if (isset($icon['icon'])){
			$classpng = '';
			//$image = "<img $classpng alt=\"".strip_tags($title)."\" border=\"0\" src=\"$icon[icon]\">";
						$image = '<span style="background:url('.$icon["icon"].') repeat-y;width:20px;height:16px;"></span>';
						$title='<span style="margin-left:2px;">'.$title.'</span>';
		}
		else $image = '';

		$active = ($sel) ? "ui-tabs-selected ui-state-active" : "" ;

				if ($onclick!='') {
					$res = "	<li class=\"ui-state-default ui-corner-top $active\" id=\"{$key}\">
					<a href=\"javascript:void(0);\" onclick=\"javascript:{$onclick};return false;\">$image $title</a>
				</li>";
				}
				else {
					$res = "	<li class=\"ui-state-default ui-corner-top $active\" id=\"{$key}\">
					<a href=\"".$href."\">$image $title</a>
				</li>";
				}
		return $res;
	}

	function create_menu_elem($icon, $sel, $key, $vertical,$stylemenu)
	{
		$confirm = isset($icon['confirm']);

		$title = $icon['title'];

		if (!empty($icon['javascript'])) $onclick = $icon['javascript'];
		elseif ($confirm) $onclick = "dims_confirmlink('".dims_urlencode($icon['url'])."','{$icon['confirm']}')";
		else $onclick = "document.location.href='".dims_urlencode($icon['url'])."'";

		if (isset($icon['icon'])){
			$classpng = '';
			$image = "<img $classpng alt=\"".strip_tags($title)."\" border=\"0\" src=\"$icon[icon]\">";
		}
		else $image = '';

		$active = ($sel) ? "ui-state-active" : "" ;

		$res = "	<li class=\"ui-menu-item ui-state-default $active\" id=\"{$key}\">
					<a href=\"javascript:void(0);\" onclick=\"javascript:{$onclick};return false;\">$image $title</a>
				</li>";

		return $res;
	}



	/* public */
	function create_tabs($w,$tabs,&$tabsel)
	{

		$res = '<div class="ui-tabs ui-widget ui-corner-all">
		<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">';

		if (!isset($tabs[$tabsel])) $tabsel = -1;

		foreach($tabs AS $key => $value)
		{
			if ($tabsel == -1) $tabsel = $key;
			$res .= $this->create_tab($tabs[$key], ($tabsel==$key));
		}

		$res .= '</ul>
			</div>';
		return $res;
	}

	/* private */
	function create_tab($tab,$sel)
	{
		if (!empty($tab['width'])) $style = 'style="width:'.$tab['width'].'px;"';
		else $style = '';

		$active = ($sel) ? 'ui-tabs-selected ui-state-active' : '' ;

		if (!isset($tab['id'])) $tab['id']='';
		if (!isset($tab['script'])) $tab['script']='';

		$res = '<li id="'.$tab['id'].'" class="ui-state-default ui-corner-top '.$active.'">
					<a href="'.dims_urlencode($tab['url']).'" '.$style.' onclick="'.$tab['script'].'">'.$tab['title'].'</a>
				</li>';
		return $res;
	}


		public function displayArray($tabs,$id='',$ajax = '') {
			global $_DIMS;
			if ($id=='') {
				$id="tabelem".rand(0,10000);
			}

			$ajax_args = explode("?", $ajax);
			$ajax_args = $ajax_args[1];
			$ajax_args = explode("&", $ajax_args);

			foreach($ajax_args as $key => $value) {
				$ajax_args[$key] = explode("=", $value);

				if($ajax_args[$key][0] == 'dims_op')
					$dims_op = substr($ajax_args[$key][1], 5);
			}

			$result = '<table cellpadding="0" cellspacing="0" border="0" class="display" id="'.$id.'">';

			// boucle sur les entetes
			$lstNull = array();

			if (isset($tabs['headers'])) {
				$result.='<thead><tr>';
				foreach ($tabs['headers'] as $i => $header) {
					if( ! empty($tabs['data']['classes'][$i]) ){
						$class = 'class="'.$tabs['data']['classes'][$i].'"';
					}
					else $class = "";

					$result.= "<th ".$class.">".$header.'</th>';
					if (isset($tabs['data']['bSortable'][$i]) && !$tabs['data']['bSortable'][$i]){
						if (isset($tabs['data']['width'][$i]))
							$lstNull[] = '{"bSortable": false, "sWidth": "'.$tabs['data']['width'][$i].'" }';
						else
							$lstNull[] = '{"bSortable": false }';
					}else{
						if (isset($tabs['data']['width'][$i]))
							$lstNull[] = '{"sWidth": "'.$tabs['data']['width'][$i].'" }';
						else
							$lstNull[] = 'null';
					}
				}
				$result.="</tr></thead>";

			}
			// boucle sur les elements
			if (isset($tabs['data']['elements'])) {

				$selected='';
				if (isset($tabs['data']['selected'])) {
					$selected=$tabs['data']['selected'];
				}
				foreach ($tabs['data']['elements'] as $li => $elems) {
					//if ($selected==$li) {
						$result.= '<tr class="gradeX event">';
						foreach ($elems as $co => $elem) {

							$result.= "<td>".$elem.'</td>';
						}
						$result.= '</tr>';
					//}
				}


			}

			$aaSorting = '';
			if (isset($tabs['data']['aasorting']['num']) && isset($tabs['headers'][$tabs['data']['aasorting']['num']])){
				$aaSorting = '"aaSorting": [['.$tabs['data']['aasorting']['num'].', "'.((isset($tabs['data']['aasorting']['order'])) ? $tabs['data']['aasorting']['order'] : 'asc').'"]],';
				$aaSorting .= '"aoColumns" : ['.implode(',',$lstNull).'],';
			}

			$result.='</table>';
			$async = '';
			if ($ajax != ''){
				$async = '  "bProcessing": true,
							"bServerSide": true,
							"sAjaxSource": "'.$ajax.'",';
			}

			$search = $_SESSION['cste']['_WCE_KEYWORDS_META']." : ";
			$view=$_DIMS['cste']['_DIMS_LABEL_VIEW'];
			$recordbypage=$_DIMS['cste']['_DIMS_LABEL_RECORD_BY_PAGE'];
			$notfound=$_DIMS['cste']['_DIMS_LABEL_NOFOUND'];
			$to=$_DIMS['cste']['_DIMS_LABEL_A'];
			$of=$_DIMS['cste']['_DIMS_LABEL_OF'];
			$total=$_DIMS['cste']['_DIMS_LABEL_TOTAL'];
			$records=$_DIMS['cste']['_DIMS_LABEL_RECORDS'];
			$filteredfrom=$_DIMS['cste']['_DIMS_LABEL_FILTERED_FROM'];
			$first=$_DIMS['cste']['PAGINATION_FIRST'];
			$previous=$_SESSION['cste']['_PREVIOUS_FEM'];
			$next=$_DIMS['cste']['_NEXT_FEM'];
			$last=$_DIMS['cste']['_LAST'];

			// generate javascript code
			$result.='<script type="text/javascript" charset="utf-8">
			$(document).ready(function() {
				var form = $("#filter_'.$id.'");

				//if (typeof(tab_'.$id.')==\'undefined\' && tab_'.$id.' != null) var tab_'.$id.' = null;
				tab_'.$id.' = $(\'#'.$id.'\').dataTable( {
					"bJQueryUI": true,
					"bPaginate": true,
					"sPaginationType": "full_numbers",
					"iDisplayLength": '.self::I_DISPLAY_LENGTH.',
					"iDisplayStart": '.(isset($_SESSION['dims']['admin'][$dims_op]['iDisplayStart']) ? $_SESSION['dims']['admin'][$dims_op]['iDisplayStart'] : 0).',
					'.$async.'
					'.$aaSorting.'
					"oLanguage": {
						"sSearch": "'.$search.'",
						"sLengthMenu": "'.$view.' _MENU_ '.$recordbypage.'",
						"sZeroRecords": "'.$notfound.'",
						"sInfo": "'.$view.' _START_ '.$to.' _END_ '.$of.' _TOTAL_ '.$records.'",
						"sInfoEmpty": "'.$view.' 0 '.$to.' 0 '.$of.' 0 '.$records.'",
						"sInfoFiltered": "('.$filteredfrom.' _MAX_ '.$total.' '.$records.')",
						"oPaginate": {
								"sFirst":    "'.$first.'",
								"sPrevious": "'.$previous.'",
								"sNext":     "'.$next.'",
								"sLast":     "'.$last.'"
						}
					}
				} );
				search_timer = null;

				var delay = function(callback, ms) {
					if(search_timer != null)
						clearTimeout(search_timer);

					search_timer = setTimeout(callback, ms);
				};

				var form = $("#filter_'.$id.'");

				if(form.length > 0) {
					var change = function() {
							tab_'.$id.'.fnFilter(form.find("select:first").val(), 0);
							tab_'.$id.'.fnFilter(form.find("input:first").val(), null);
					};

					form.find("select").change(function() {
							change();
					});

					form.find("input").keyup(function() {
						delay(function(){change()}, 400);
					});

					form.find("a").click(function() {
						form.find("select:first").val(1);
						form.find("input:first").val("");
						change();
					});

					form.submit(function(e) {
					   e.preventDefault();
					   change();
					});

					tab_'.$id.'.fnFilter(form.find("input:first").val(), null);
				}

				$("#'.$id.'_info").remove();
				var divs = $("#'.$id.'_wrapper").children("div");
				divs.first().remove();
				divs.last().attr("class", "pagination");
				divs.last().insertAfter($("#'.$id.'_wrapper"));

				jQuery.fn.dataTableExt.oPagination.iFullNumbersShowPages = '.self::PAGE_NUMBERS_SHOWN.';

				$(window).bind("resize",function(){
					tab_'.$id.'.fnAdjustColumnSizing();
				});
			} );
		</script>';
			return $result;
		}

	//function create_menu($title, $link, $id_help='', $target='', $urlencode = true)
	//{
	//
	//	if ($urlencode) $link = dims_urlencode($link);
	//
	//	/*
	//	$res = "
	//		<TR>
	//			<TD ALIGN=\"LEFT\" VALIGN=\"MIDDLE\">
	//			<TABLE CELLPADDING=\"0\" CELLSPACING=\"0\" WIDTH=\"100%\">
	//			<TR>
	//				<BULLET>
	//				<TD VALIGN=\"MIDDLE\" <ID_HELP> ALIGN=\"".$this->values['menu_align']."\"><A <ID_HELP> <TARGET> HREF=\"{$link}\" CLASS=\"Menu\">$title</A></TD>
	//			</TR>
	//			</TABLE>
	//			</TD>
	//		</TR>
	//		";
	//
	//	if ($this->values['menu_bullet']) $res = str_replace("<BULLET>","<TD VALIGN=MIDDLE ALIGN=CENTER WIDTH=\"".$this->values['menu_bullet_width']."\"><IMG SRC=\"".$this->values['path']."/bullet".$this->values['img_extension']."\"</TD>",$res);
	//	else $res = str_replace("<BULLET>","",$res);
	//
	//	if ($id_help!='') $res = str_replace("<ID_HELP>","ID=\"$id_help\"",$res);
	//	else $res = str_replace("<ID_HELP>","",$res);
	//
	//	if ($target!='') $res = str_replace("<TARGET>","TARGET=\"$target\"",$res);
	//	else $res = str_replace("<TARGET>","",$res);
	//
	//	return $res;
	//	*/
	//}


	//function create_menutitle($title,$w)
	//{
	//
	//}


	//function create_menusubtitle($title, $id_help = '')
	//{
	//	/*
	//	if ($this->values['menusubtitle_align']=='left') $title = $title;
	//	if ($this->values['menusubtitle_align']=='right') $title = $title;
	//
	//	$res = "<TR><TD <ID_HELP> CLASS=MenuSubTitle ALIGN=\"".$this->values['menusubtitle_align']."\" VALIGN=MIDDLE>$title</TD></TR>\n";
	//
	//	if ($id_help!='') $res = str_replace("<ID_HELP>","ID=\"$id_help\"",$res);
	//	else $res = str_replace("<ID_HELP>","",$res);
	//
	//	return $res;
	//	*/
	//}

	//function create_sep()
	//{
	//	// unused
	//}




	/**
	*******************************************************************************
	//* TITLES METHODS
	function open_menubloc()
	{
		return $this->top('100%').$this->left()."<TABLE CELLPADDING=\"0\" CELLSPACING=\"0\" WIDTH=\"100%\">";
	}

	/**
	*
	*
	* @return html code to display
	*
	**/

	//function close_menubloc()
	//{
	//	return "<TR><TD ALIGN=LEFT VALIGN=MIDDLE><IMG SRC=\"./skins/blank.gif\" WIDTH=\"1\" HEIGHT=\"1\"></TD></TR></TABLE>".$this->right().$this->bottom(5);//$this->under('100%',5);
	//}

	/**
	*******************************************************************************
	* DESKTOP METHODS
	*******************************************************************************
	**/

	//function create_desktop($w, $icons, $ipl = 5) // $ipl = icon per line
	//{
	//	$icons_content = '';
	//	$nbic = 0;
	//
	//	foreach($icons as $icon)
	//	{
	//		if (!($nbic % $ipl)) $icons_content .= '</tr><tr>';
	//		$icon['width'] = 100/$ipl;
	//		$icons_content .= $this->create_desktopicon($icon);
	//		$nbic++;
	//	}
	//
	//	$icons_content .= '<td>&nbsp;</td>';
	//
	//	$res =	"
	//			<table cellspacing=\"0\" cellpadding=\"0\" width=\"100%\">
	//			<tr>
	//				$icons_content
	//			</tr>
	//			</table>
	//			";
	//
	//	return $res;
	//}

	//function create_desktopicon($icon)
	//{
	//	$confirm = isset($icon['confirm']);
	//
	//	if (isset($icon['id_help']) && $icon['id_help'] != '') $id_help = "ID=\"$icon[id_help]\"";
	//	else $id_help = '';
	//
	//	if (!isset($icon['url'])) $icon['url'] = '';
	//
	//	$admin = '';
	//	if (isset($icon['admin_url']))
	//	{
	//		$admin = "<tr><td style=\"color:#880000;\" $id_help align=\"center\" valign=\"middle\" onclick=\"javascript:document.location.href='".dims_urlencode($icon['admin_url'])."'\" onmouseout=\"javascript:this.style.cursor='default'\" onmouseover=\"javascript:this.style.cursor='pointer'\">Administration</td></tr>";
	//		if ($icon['url'] == '') $icon['url'] = $icon['admin_url'];
	//	}
	//
	//	if ($confirm) $onclick = "dims_confirmlink('".dims_urlencode($icon['url'])."','{$icon['confirm']}')";
	//	else $onclick = "document.location.href='".dims_urlencode($icon['url'])."'";
	//
	//	if (!isset($icon['description'])) $icon['description'] = '';
	//
	//
	//	$res =	"
	//			<td width=\"{$icon['width']}%\" align=\"center\" valign=\"top\"  >
	//				<table cellspacing=\"1\" cellpadding=\"2\" width=\"100%\">
	//				<tr>
	//					<td $id_help align=\"center\" valign=\"top\" onclick=\"javascript:$onclick\" onmouseout=\"javascript:this.style.cursor='default'\" onmouseover=\"javascript:this.style.cursor='pointer'\">
	//					<img $id_help alt=\"".strip_tags($icon['title'])."\" border=\"0\" src=\"{$icon['icon']}\">
	//					</td>
	//				</tr>
	//				<tr>
	//					<td $id_help align=\"center\" valign=\"middle\"  onclick=\"javascript:$onclick\" onmouseout=\"javascript:this.style.cursor='default'\" onmouseover=\"javascript:this.style.cursor='pointer'\">
	//					<b>{$icon['title']}</b>
	//					<br>{$icon['description']}
	//					</td>
	//				</tr>
	//				$admin
	//				</table>
	//			</td>
	//			";
	//
	//	//if ($sel) $res ="<TD WIDTH=\"".$icon['width']."\" ALIGN=CENTER><FONT CLASS=TabSel><A CLASS=TabSel HREF=\"".$icon['url']."\">".$icon['title']."</A></TD>";
	//	//else $res ="<TD WIDTH=\"".$icon['width']."\" BGCOLOR=\"".$this->values['colsec']."\" ALIGN=CENTER><A CLASS=Tab HREF=\"".$icon['url']."\">".$icon['title']."</A></TD>";
	//	return $res;
	//}




	//function create_desktoptoolbar($w, $icons, $ipl = 10) // $ipl = icon per line
	//{
	//	$icons_content = '';
	//	$nbic = 0;
	//
	//	foreach($icons as $icon)
	//	{
	//		$icons_content .= $this->create_desktoptoolbaricon($icon);
	//		$nbic++;
	//	}
	//
	//	$icons_content .= '<td>&nbsp;</td>';
	//
	//	$res =	"
	//			<table cellspacing=\"0\" cellpadding=\"0\">
	//			<tr>
	//				$icons_content
	//			</tr>
	//			</table>
	//			";
	//
	//	return $res;
	//}

	//function create_desktoptoolbaricon($icon)
	//{
	//	$confirm = isset($icon['confirm']);
	//
	//	if (isset($icon['id_help']) && $icon['id_help'] != '') $id_help = "ID=\"$icon[id_help]\"";
	//	else $id_help = '';
	//
	//	if (!isset($icon['url'])) $icon['url'] = '';
	//
	//	$admin = '';
	//	if (isset($icon['admin_url']))
	//	{
	//		$admin = "<td style=\"color:#880000;\" $id_help align=\"center\" valign=\"middle\" onclick=\"javascript:document.location.href='".dims_urlencode($icon['admin_url'])."'\" onmouseout=\"javascript:this.style.cursor='default'\" onmouseover=\"javascript:this.style.cursor='pointer'\">(Admin)</td>";
	//		if ($icon['url'] == '') $icon['url'] = $icon['admin_url'];
	//	}
	//
	//	if ($confirm) $onclick = "dims_confirmlink('".dims_urlencode($icon['url'])."','{$icon['confirm']}')";
	//	else $onclick = "document.location.href='".dims_urlencode($icon['url'])."'";
	//
	//	if (!isset($icon['description'])) $icon['description'] = '';
	//
	//	$res =	"
	//			<td align=\"center\" valign=\"top\">
	//				<table cellspacing=\"1\" cellpadding=\"2\">
	//				<tr>
	//					<td $id_help align=\"center\" valign=\"top\" onclick=\"javascript:$onclick\" onmouseout=\"javascript:this.style.cursor='default'\" onmouseover=\"javascript:this.style.cursor='pointer'\">
	//					<img $id_help alt=\"".strip_tags($icon['title'])."\" border=\"0\" src=\"{$icon['icontoolbar']}\">
	//					</td>
	//					<td $id_help align=\"left\" valign=\"middle\" onclick=\"javascript:$onclick\" onmouseout=\"javascript:this.style.cursor='default'\" onmouseover=\"javascript:this.style.cursor='pointer'\">
	//					<b>{$icon['title']}</b>
	//					</td>
	//					$admin
	//				</tr>
	//				</table>
	//			</td>
	//			";
	//
	//	//if ($sel) $res ="<TD WIDTH=\"".$icon['width']."\" ALIGN=CENTER><FONT CLASS=TabSel><A CLASS=TabSel HREF=\"".$icon['url']."\">".$icon['title']."</A></TD>";
	//	//else $res ="<TD WIDTH=\"".$icon['width']."\" BGCOLOR=\"".$this->values['colsec']."\" ALIGN=CENTER><A CLASS=Tab HREF=\"".$icon['url']."\">".$icon['title']."</A></TD>";
	//	return $res;
	//}


	function display_array($columns, $values) {
		global $nb_array;
		if (empty($nb_array)) $nb_array = 0;
		$nb_array++;
		?><div class="dims_explorer_main" id="dims_explorer_main_<? echo $nb_array; ?>"><?
			$i = 0; $w = 0;
			if (!empty($columns['actions_right'])) {
				foreach($columns['actions_right'] as $id => $c) {
					$w += $c['width'];
					?><div style="right:<? echo $w; ?>px;" class="dims_explorer_column" id="dims_explorer_column_<? echo $nb_array; ?>_<? echo $i; ?>"></div><?
					$i++;
				}
			}
			if (!empty($columns['right'])) {
				foreach($columns['right'] as $c) {
					$w += $c['width'];
					?><div style="right:<? echo $w; ?>px;" class="dims_explorer_column" id="dims_explorer_column_<? echo $nb_array; ?>_<? echo $i; ?>"></div><?
					$i++;
				}
			}
			$w = 0;
			if (!empty($columns['left'])) {
				foreach($columns['left'] as $c) {
					$w += $c['width'];
					?><div style="left:<? echo $w; ?>px;" class="dims_explorer_column" id="dims_explorer_column_<? echo $nb_array; ?>_<? echo $i; ?>"></div><?
					$i++;
				}
			}
			?>
			<div class="ui-resizable">
				<div class="ui-resizable dims_explorer_title">
					<?
					if (!empty($columns['actions_right'])) {
						foreach($columns['actions_right'] as $c) {
							?><a href="" style="width:<? echo $c['width']; ?>px;float:right;" class="dims_explorer_element"><p><? echo $c['label']; ?></p></a><?
						}
					}
					if (!empty($columns['right'])) {
						foreach($columns['right'] as $c) {
							?><a href="" style="width:<? echo $c['width']; ?>px;float:right;" class="dims_explorer_element"><p><? echo $c['label']; ?></p></a><?
						}
					}
					if (!empty($columns['left'])) {
						foreach($columns['left'] as $c) {
							?><a href="" style="width:<? echo $c['width']; ?>px;float:left;" class="dims_explorer_element"><p><? echo $c['label']; ?></p></a><?
						}
					}
					if (!empty($columns['auto'])) {
						foreach($columns['auto'] as $c) {
							?><a href="" style="overflow:auto;" class="dims_explorer_element"><p><? echo $c['label']; ?></p></a><?
						}
					}
				?></div><?
				foreach($values as $v) {
					?><div class="dims_explorer_line" <? if (!empty($v['style'])) echo "style=\"{$v['style']}\""; ?>><?
						if (!empty($columns['actions_right'])) {
							foreach($columns['actions_right'] as $id => $c) {
								?><div class="dims_explorer_tools" style="width:<? echo $c['width']; ?>px;float:right;<? echo $v['values'][$id]['style']; ?>"><? echo $v['values'][$id]['label']; ?></div><?
							}
						}
						$option = (empty($v['option'])) ? '' : $v['option'];
						if (!empty($v['link'])) {
							?><a class="dims_explorer_link" title="<? echo $v['description']; ?>" href="<? echo $v['link']; ?>" style="<? echo $v['style']; ?>" <? echo $option; ?>><?
						}
						if (!empty($columns['right'])) {
							foreach($columns['right'] as $id => $c) {
								?><div style="width:<? echo $c['width']; ?>px;float:right;<? echo $v['values'][$id]['style']; ?>" class="dims_explorer_element"><p><? echo $v['values'][$id]['label']; ?></p></div><?
							}
						}
						if (!empty($columns['left'])) {
							foreach($columns['left'] as $id => $c) {
								?><div style="width:<? echo $c['width']; ?>px;float:left;<? echo $v['values'][$id]['style']; ?>" class="dims_explorer_element"><p><? echo $v['values'][$id]['label']; ?></p></div><?
							}
						}
						if (!empty($columns['auto'])) {
							foreach($columns['auto'] as $id => $c) {
								?><div style="overflow:auto;<? echo $v['values'][$id]['style']; ?>" class="dims_explorer_element"><p><? echo $v['values'][$id]['label']; ?></p></div><?
							}
						}
						if (!empty($v['link'])) {
							?></a><?
						}
						?></div><?
				}
				?></div>
		</div>
		<!--[if IE 6]>
		<script language="javascript">
		function skin_ie6_load_array_<? echo $nb_array; ?>() {
			<? for ($j=0;$j<$i;$j++) {
				?>dims_getelem('dims_explorer_column_<? echo $nb_array; ?>_<? echo $j; ?>').style.height = dims_getelem('dims_explorer_main_<? echo $nb_array; ?>').offsetHeight+'px';<?
			}?>
		}
		dims_window_onload_stock(skin_ie6_load_array_<? echo $nb_array; ?>)
		</script>
		<![endif]-->
		<?
	}

		public static function table_ajax_buildingOutput($iTotalRecords, $iTotalDisplayRecords, $aaData) {
			$sEcho = intval(dims_load_securvalue('sEcho',dims_const::_DIMS_NUM_INPUT,true,true,true));
			/**Création de la sortie**/
			$output = array(
			"sEcho" => $sEcho,
			"iTotalRecords" => $iTotalRecords,
			"iTotalDisplayRecords" => $iTotalDisplayRecords,
			"aaData" => $aaData
			);

			return json_encode( $output );
		}

	/**
	 * @return (array) 	[0] => (string)	Clause LIMIT
	 *                  [1] => (array) Tableau de paramètres de la clause LIMIT
	 */
	public static function table_ajax_getPagingRules() {
		$sLimit = "";
		$params = array();
		if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' ) {
			$dims_op = substr(dims_load_securvalue("dims_op", dims_const::_DIMS_CHAR_INPUT, true, true, true), 5);
			$iDisplayStart = dims_load_securvalue('iDisplayStart',dims_const::_DIMS_NUM_INPUT,true,true,true);
			$iDisplayLength = dims_load_securvalue('iDisplayLength',dims_const::_DIMS_NUM_INPUT,true,true,true);
			$_SESSION['dims']['admin'][$dims_op]['iDisplayStart'] = $iDisplayStart;
			$_SESSION['dims']['admin'][$dims_op]['iDisplayLength'] = $iDisplayLength;
			$_SESSION['dims']['admin'][$dims_op]['pageNumber'] = $_SESSION['dims']['admin'][$dims_op]['iDisplayStart']/$_SESSION['dims']['admin'][$dims_op]['iDisplayLength'];
			$sLimit = " LIMIT :limit1 , :limit2 ";
			$params[':limit1'] = array('type' => PDO::PARAM_INT, 'value' => $iDisplayStart);
			$params[':limit2'] = array('type' => PDO::PARAM_INT, 'value' => $iDisplayLength);


			}

		return array($sLimit,$params) ;
	}

	public static function table_ajax_getOrderingRules($aColumns) {
		$sOrder = "";

		if(isset($_GET['iSortCol_0']) && !empty($aColumns)){
			$sOrder = "ORDER BY  ";
			for ( $i=0 ; $i<intval( dims_load_securvalue('iSortingCols',dims_const::_DIMS_NUM_INPUT,true,true,true) ) ; $i++ ){
				if ( dims_load_securvalue('bSortable_'.dims_load_securvalue('iSortCol_'.$i,dims_const::_DIMS_NUM_INPUT,true,true,true),dims_const::_DIMS_CHAR_INPUT,true,true,true) == "true" ){
					$sOrder .= $aColumns[ intval( dims_load_securvalue('iSortCol_'.$i,dims_const::_DIMS_NUM_INPUT,true,true,true)) ]."
							".dims_load_securvalue('sSortDir_'.$i,dims_const::_DIMS_CHAR_INPUT,true,true,true).", ";
				}
			}

			$sOrder = substr_replace($sOrder, "", -2 );
			if ( $sOrder == "ORDER BY" ){
				$sOrder = "";
			}
		}

		return $sOrder;
	}

	/**
	 * @return (array) 	[0] => (string)	Clauses WHERE & AND
	 *                  [1] => (array) Tableau de paramètres des clauses WHERE & AND
	 */
	public static function table_ajax_getFilteringRules($aColumns) {
		$sFilter = "";
		$params = array();
		$dims_op = substr(dims_load_securvalue("dims_op", dims_const::_DIMS_CHAR_INPUT, true, true, true), 5);
		$search = dims_load_securvalue('sSearch', dims_const::_DIMS_CHAR_INPUT, true, true, true, $_SESSION['dims']['admin'][$dims_op]['search_text']);

		if($search != " ")
			$_SESSION['dims']['admin'][$dims_op]['search_text'] = $search;
		else {
			unset($_SESSION['dims']['admin'][$dims_op]['search_text']);
			$search = "";
		}

		if($search != "" ) {
			$sFilter .= " WHERE (";
			for ( $i=0 ; $i<count($aColumns) ; $i++ ){
				if ( $aColumns[$i] != ' ' ){
					$sFilter .= $aColumns[$i]." LIKE :w".$i." OR ";
					$params[':w'.$i]= "%".$search."%";
				}
			}
			$sFilter = substr_replace( $sFilter, "", -3 );
			$sFilter .= ')';

			/* Individual column filtering */
			for ( $i=0 ; $i<count($aColumns) ; $i++ ){
			   $search = dims_load_securvalue('sSearch'.$i,dims_const::_DIMS_CHAR_INPUT,true,true,true);
				if ( dims_load_securvalue('bSearchable_'.$i,dims_const::_DIMS_CHAR_INPUT,true,true,true) == "true" && $search != '' ){
					$sFilter .= " AND ";
					$sFilter .= $aColumns[$i]." LIKE :a".$i." ";
					$params[':a'.$i]= "%".dims_load_securvalue('bSearchable_'.$i,dims_const::_DIMS_CHAR_INPUT,true,true,true)."%";
				}
			}
		}
		return array($sFilter, $params);
	}

	public static function table_ajax_executeQuery($sTable, $sWhere, $sFilter, $sLimit, $sOrder, $params) {
		$res = null;
		if(!empty($sTable) && !empty($sWhere)){
			if (!(strpos(strtolower($sWhere),'where ') !== false))
				$sWhere = " WHERE 1=1 $sWhere ";
			$db = dims::getInstance()->getDb();
			if (trim($sFilter) != '')
				$sql = "SELECT SQL_CALC_FOUND_ROWS *
					FROM $sTable
					$sFilter
					$sWhere
					$sOrder
					$sLimit
					";
			else
				$sql = "SELECT SQL_CALC_FOUND_ROWS *
					FROM $sTable
					$sWhere
					$sOrder
					$sLimit
					";
			$res = $db->query($sql,  $params);

		}
		return $res;
	}

	public static function table_ajax_getTotalDisplayRecords() {
		$db = dims::getInstance()->getDb();
		$sql = "SELECT FOUND_ROWS() as nb";

		$rResultFilterTotal = $db->query($sql);
		$aResultFilterTotal = $db->fetchrow($rResultFilterTotal);
		$iTotalDisplayRecords = $aResultFilterTotal['nb'];

		return $iTotalDisplayRecords;
	}

	public static function table_ajax_getTotalRecords($sTable, $sWhere, $sIndexColumn, $params) {
		$count = 0 ;

		if(!empty($sTable) && !empty($sWhere)){
			if(empty($sFilter) &&  strpos('where', strtolower($sWhere)) === false){
				$sWhere = 'WHERE 1=1 '.$sWhere;
			}

			$db = dims::getInstance()->getDb();
			$sql = "SELECT COUNT($sIndexColumn) as nb
					FROM $sTable
					$sWhere";
			$rResultTotal = $db->query($sql, $params);
			$aResultTotal = $db->fetchrow($rResultTotal);
			$count = $aResultTotal['nb'];
		}

		return $count;
	}

	public static function table_ajax_managing(ajax_datatable $ajax_datatable) {
		$sLimit = skin_common::table_ajax_getPagingRules();
		$sOrder = skin_common::table_ajax_getOrderingRules($ajax_datatable->get_aColumns());
		$sFilter = skin_common::table_ajax_getFilteringRules($ajax_datatable->get_aColumns());

		$sWhere = $ajax_datatable->get_sWhere();
		// $sLimit[1], $sFilter[1] et $sWhere[1] sont 2 tableaux de paramètres renvoyés par les méthodes table_ajax_getPagingRules & table_ajax_getOrderingRules
		// Ces paramètres sont utilisés pour PDO
		$sParams =  array_merge($sLimit[1],$sFilter[1],$sWhere[1]);

		$res_query = skin_common::table_ajax_executeQuery($ajax_datatable->get_sTable(),$sWhere[0], $sFilter[0], $sLimit[0], $sOrder, $sParams);

		$iTotalDisplayRecords = skin_common::table_ajax_getTotalDisplayRecords();
		$iTotalRecords = skin_common::table_ajax_getTotalRecords($ajax_datatable->get_sTable(),$sWhere[0], $ajax_datatable->get_sIndexColumn(), $sWhere[1]);

		$aaData = $ajax_datatable->get_aaData($res_query);
		echo skin_common::table_ajax_buildingOutput($iTotalRecords, $iTotalDisplayRecords, $aaData);
		//dims_print_r_file("Test - ".ob_get_contents(), "file1.html");
	}


}
?>
