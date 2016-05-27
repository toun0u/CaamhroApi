<?php
/**
* ! description !
*
* @param bool $hand
* @return string HTML style
*
* @version 2.09
* @since 0.1
*
* @category HTML styles management
*/

function display_avancement($avancement) {

	if ($avancement < '100') {
		/* On ne garde que les 2 premiers caracteres */
		$avancement = substr($avancement, 0, 2);
	}

	if ($avancement < '10') {
		/* Si l'avancement etait <10 alors on supprime le second carac (la virgule) */
		$avancement = substr($avancement, 0, 1);
	}

	$display_start = '';
	$display_end = '';
	if ($avancement > '0') {
		if ($avancement < '50') {
		$display_start = '<div style="width:'.$avancement.'px;height:14px;color:#FFFFFF;background:#4d6894;float:left;">&nbsp;</div>';
		}else {
		$display_start = '<div style="width:'.$avancement.'px;height:14px;color:#FFFFFF;background:#4d6894;float:left;">'.$avancement.'%</div>';
		}
	}

	if ($avancement < '100') {
		if ($avancement < '50') {
			$display_end = '<div style="float:left;width:'.(100-$avancement).'px;height:14px;text-align:center;background-color:#ffffff;color:#000000;">&nbsp;&nbsp;&nbsp;'.$avancement.'%</div>';
		}else {
			$display_end = '<div style="float:left;width:'.(100-$avancement).'px;height:14px;background-color:ffffff">&nbsp;</div>';
		}
	}
	$display =	"<span style=\"width:100px;border: 1px #ABABAB solid;display:inline-block;\">".$display_start.$display_end."</span>";

	return($display);
}


function dims_switchstyles($hand = TRUE) {
	$opacity = 80;
	if (strstr($_SERVER['HTTP_USER_AGENT'],'MSIE')) {
		if ($hand) return 'Style="filter:alpha(opacity:'.$opacity.');"	OnMouseOut="javascript:dims_switchstyle(this, '.$opacity.');this.style.cursor=\'default\'" OnMouseOver="javascript:dims_switchstyle(this,100);this.style.cursor=\'pointer\'"';
		else return 'Style="filter:alpha(opacity:'.$opacity.');"  OnMouseOut="javascript:dims_switchstyle(this, '.$opacity.');" OnMouseOver="javascript:dims_switchstyle(this,100);"';
	}
	else {
		if ($hand) return 'Style="-moz-opacity:'.($opacity/100).';opacity:'.($opacity/100).';"	OnMouseOut="javascript:dims_switchstyle(this, '.$opacity.');this.style.cursor=\'default\'" OnMouseOver="javascript:dims_switchstyle(this,100);this.style.cursor=\'pointer\'"';
		else return 'Style="-moz-opacity:'.($opacity/100).';opacity:'.($opacity/100).';"  OnMouseOut="javascript:dims_switchstyle(this, '.$opacity.');" OnMouseOver="javascript:dims_switchstyle(this,100);"';
	}
}


function dims_showpopup($msg, $width = '') {
	$msg = dims_nl2br(str_replace("'","\'",$msg));
	//return "onmouseover=\"javascript:this.style.cursor='help';dims_showpopup('{$msg}','{$width}', event);\" onmousemove=\"javascript:dims_showpopup('{$msg}', '{$width}', event);\" onmouseout=\"javascript:dims_hidepopup('{$msg}',event);\" onmouseup=\"javascript:dims_showpopup('{$msg}', '{$width}', event, 'click');\"";
	return "onmouseover=\"javascript:this.style.cursor='help';dims_showpopup('{$msg}','{$width}', event);\" onmouseout=\"javascript:dims_hidepopup();\" onmouseup=\"javascript:dims_showpopup('{$msg}', '{$width}', event, 'click');\"";
}

/**
* ! description !
*
* @return string HTML style
*
* @version 2.09
* @since 0.1
*
* @category HTML styles management
*/
function dims_switchfocus() {
	$opacity = 80;
	return 'Style="filter:alpha(opacity:'.$opacity.')"	OnBlur="javascript:dims_switchstyle(this,'.$opacity.');" OnFocus="javascript:dims_switchstyle(this,100);"';
}

// FCK richtext editor
function dims_fckeditor($field,$value,$w,$h,$restrict=false,$specific_config="") {
	//require_once(DIMS_APP_PATH . '/FCKeditor/editor/fckeditor.php') ;
	require_once(DIMS_APP_PATH . '/www/assets/javascripts/common/ckeditor/ckeditor.php') ;

	/*if (substr($_SERVER['SERVER_PROTOCOL'],0,5)=="HTTP/") $rootpath="http://";
	else $rootpath="https://";
	$rootpath.=$_SERVER['HTTP_HOST'];*/
	$dims = dims::getInstance();
	$http_host = (isset($_SERVER['HTTP_HOST'])) ? $_SERVER['HTTP_HOST'] : '';
	$rootpath=$dims->getProtocol().$http_host;

	$basepath='';
	if (isset($_SERVER['HTTP_REFERER'])) {
		$basepath = dirname($_SERVER['HTTP_REFERER']); // compatible with proxy rewrite
	}
	if ($basepath == '/') $basepath = '';

	$oFCKeditor = new CKEditor($rootpath."/common/js/ckeditor/") ;

	// default path for FCKEditor
	$oFCKeditor->BasePath	= $rootpath."/common/js/ckeditor/";

	$oFCKeditor->Config['BaseHref'] = "{$basepath}/common/";
	if ($restrict) {
		$oFCKeditor->Config['customConfig'] = $rootpath."/common/js/ckeditor/fckconfigrestrict.js";
	}elseif($specific_config!="") {
		$oFCKeditor->Config['customConfig'] = $specific_config;
	}

	// default value
	$oFCKeditor->Value= $value;

	// width & height
	if ($w!="" && $w!="*") $oFCKeditor->Width=$w;
	if ($h!="" && $h!="*") $oFCKeditor->Height=$h;

	// language definition
	$oFCKeditor->Config["AutoDetectLanguage"] = false ;
	$oFCKeditor->Config["DefaultLanguage"]	  = "fr" ;
	// render
	$oFCKeditor->editor("fck_".$field, $oFCKeditor->Value,$oFCKeditor->Config);
}

function dims_create_button($value,$icon="",$script='',$id='',$style='',$link="javascript:void(0);",$mouseover="",$title="",$place=true,$focus=false) {
	if ($id != "") $id = 'id="'.$id.'" ';
	if ($style != "") $style = 'style="'.$style.'" ';
	if ($title != "") $title = 'title="'.$title.'" ';
	if ($link != "") $link = 'href="'.$link.'" ';
	if ($script != "") $script = 'onclick="'.$script.'" ';
	if ($mouseover != "") $mouseover = 'onmouseover="'.$mouseover.'" ';

	if (strpos($script,'submit')>0 || $icon=='disk') { // a voir pour une option eventuelle
		$button = '<input '.$id.' type="submit" value="'.$value.'" '.$script.$style.' class="ui-button ui-widget ui-state-default ui-corner-all" role="button" aria-disabled="false">';
	}
	else {
		if($value == ""){
			$class ='icon-only';
			$icon = '<span class="ui-icon ui-icon-'.$icon.'"></span>';
			$value = "&nbsp;";
		}else{
			if ($icon == ""){
				$class ='text-only';
			}else{
				if($place || $place == ""){
					$class ='text-icon-primary';
					$icon = '<span class="ui-button-icon-primary ui-icon ui-icon-'.$icon.'"></span>';
				}else{
					$class ='text-icon-secondary';
					$icon = '<span class="ui-button-icon-secondary ui-icon ui-icon-'.$icon.'"></span>';
				}
			}
		}
		$focusUI = '';
		if ($focus)
			$focusUI = ' ui-state-focus';
		$button = '<a class="ui-button ui-widget ui-state-default ui-corner-all ui-button-'.$class.$focusUI.'" '.$style.$id.$title.$link.$script.$mouseover.' >
						'.$icon.'
						<span class="ui-button-text">'.$value.'</span>
					</a>';
	}
	return $button;
}

function dims_create_button_nofloat($value,$title="",$icon="",$place=true,$script,$id='',$style='',$link="javascript:void(0);",$mouseover="") {
	$button="<span class=\"NSButtonNoFloat\" style=\"$style\"";

	if ($id != "") $id = 'id="'.$id.'" ';
	if ($style != "") $style = 'style="'.$style.'" ';
	if ($title != "") $title = 'title="'.$title.'" ';
	if ($link != "") $link = 'href="'.$link.'" ';
	if ($script != "") $script = 'onclick="'.$script.'" ';
	if ($mouseover != "") $mouseover = 'onmouseover="'.$mouseover.'" ';
	if($value == ""){
		$class ='icon-only';
		$icon = '<span class="ui-icon ui-icon-'.$icon.'"></span>';
		$value = "&nbsp;";
	}else{
		if ($icon == ""){
			$class ='text-only';
		}else{
			if($place || $place == ""){
				$class ='text-icon-primary';
				$icon = '<span class="ui-button-icon-primary ui-icon ui-icon-'.$icon.'"></span>';
			}else{
				$class ='text-icon-secondary';
				$icon = '<span class="ui-button-icon-secondary ui-icon ui-icon-'.$icon.'"></span>';
			}
		}
	}
	$button.=">
			<div style=\"margin:2px;\">";

	$button .= '<a class="ui-button ui-widget ui-state-default ui-corner-all ui-button-'.$class.'" '.$style.$id.$title.$link.$script.$mouseover.' >
				'.$icon.'
				<span class="ui-button-text">'.$value.'</span>
			</a>';
	$button.="</div>
		</span>
		";

	return $button;
}

function dims_create_aff_skin($idskin,$nomskin,$suppr=false) {
	$skin = new skin();
	$r = $skin->open_simplebloc($nomskin,"width:15%;text-align:center;float:left;margin:10px;");
	$r .= '<div><img src="img/skins-jquery-ui/'.$nomskin.'.png"></div>';
	$r .= dims_create_button("Appliquer","check","change_skin('$idskin')");
	if($suppr) $r .= dims_create_button("Supprimer","close","if(confirm('Etes vous sûr ?')){delete_skin('$idskin')}");
	$r .= $skin->close_simplebloc();
	return $r;
}

?>
