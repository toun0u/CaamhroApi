<?php
$oldSub = module_wce::_SITE_PROPERTIES;
if(!isset($_SESSION['dims']['wce']['subsub']))
	$_SESSION['dims']['wce']['subsub'] = module_wce::_SITE_PROPERTIES;
else
	$oldSub = $_SESSION['dims']['wce']['subsub'];

$_SESSION['dims']['wce']['subsub'] = dims_load_securvalue('sub',dims_const::_DIMS_CHAR_INPUT,true,true,false,$_SESSION['dims']['wce']['subsub']);
if($oldSub != module_wce::_SITE_PREVIEW && $_SESSION['dims']['wce']['subsub'] == module_wce::_SITE_PREVIEW){
	$_SESSION['wce_display_tree'] = false;
	if($_SESSION['wce_display_menu']){
	?>
	<script type="text/javascript">
		$(document).ready(function(){
			$('div.repli_depli a').click();
		});
	</script>
	<?
	}
}

if ($_SESSION['dims']['wce']['subsub'] != module_wce::_SITE_PREVIEW && $_SESSION['dims']['wce']['subsub'] != module_wce::_SITE_PROPERTIES && $_SESSION['dims']['wce']['subsub'] != module_wce::_SITE_REF && $_SESSION['dims']['wce']['subsub'] != module_wce::_SITE_DIFF && $_SESSION['dims']['wce']['subsub'] != module_wce::_SITE_LIST)
	$_SESSION['dims']['wce']['subsub'] = module_wce::_SITE_PROPERTIES;

if ($_SESSION['dims']['wce']['subsub'] == module_wce::_SITE_PROPERTIES) $_SESSION['wce_display_tree'] = true;

// Gestion de la langue
if(!isset($_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang'])){
	$wce_site= new wce_site (dims::getInstance()->db,$_SESSION['dims']['moduleid']);
	$_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang'] = $wce_site->getDefaultLanguage();
}
if(!isset($_SESSION['dims']['wce_default_lg'])){
	if (!isset($site))
		$site = new wce_site(dims::getInstance()->getDb(),$_SESSION['dims']['moduleid']);
	$_SESSION['dims']['wce_default_lg'] = $site->getDefaultLanguage();
}
$_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang'] = dims_load_securvalue('lang',dims_const::_DIMS_NUM_INPUT,true,true,false,$_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang'],$_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang']);

$headingid = dims_load_securvalue('headingid',dims_const::_DIMS_NUM_INPUT,true,true,false);
$lstHeadings = wce_heading::getAllHeadings();

if ($headingid != '' && $headingid > 0){
	$heading = new wce_heading();
	$heading->open($headingid);
}else{
	$heading = current($lstHeadings);
}
$headingid = $heading->fields['id'];

if ($headingid==0 || isset($heading->fields['id']) && $heading->fields['id']=='') {
	dims_redirect ('/admin.php?sub2='.module_wce::_SUB_SITE);
}

require_once module_wce::getTemplatePath("gestion_site/sub_header.tpl.php");
require_once module_wce::getTemplatePath("common/display_browser_site.tpl.php");

?>
<div class="content_arbo" style="<? echo (!isset($_SESSION['wce_display_tree']) || (isset($_SESSION['wce_display_tree']) && $_SESSION['wce_display_tree']))?"width:78%;":"width:98%;"; ?>float: right;">
	<?
	$l = new wce_lang();
	$lgs = $l->getAll(true);
	if (count($lgs) > 0){
		?>
	<div class="wce_article_info_sup">
		<form method="POST" name="chang_lang" style="float:left;">
			<label>
				<? echo $_SESSION['cste']['_LIST_LANGUAGES']; ?> :&nbsp;
			</label>
			<select name="lang" id="wce_lang">
				<?
				foreach($lgs as $langue){
					if(isset($lstLangDipo[$langue->fields['id']])){
						$et = "";
						$opt = "ref=\"exist\"";
					}else{
						$et = " *";
						$opt = "ref=\"\"";
					}
					?>
					<option <? echo $opt; ?> <? if($_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang'] == $langue->fields['id']) echo 'selected=true'; ?> value="<? echo $langue->fields['id']; ?>">
						<? echo $langue->getLabel().$et; ?>
					</option>
					<?
				}
				?>
			</select>
			<script type="text/javascript">
				$(document).ready(function(){
					$('select#wce_lang').change(function(){
						if($('option:selected',$(this)).attr('ref') == 'exist'){
							document.chang_lang.submit();
						}else{
							if(confirm('<? echo $_SESSION['cste']['_CONFIRM_INITIALIZE_NEW_LANGUAGE']; ?>'))
								<?
								if (isset($article->fields['id_heading'])){
									?>
									document.location.href='<? echo module_wce::get_url($_SESSION['dims']['wce']['sub'])."&sub=".module_wce::_SITE_PROPERTIES."&action=".module_wce::_PROPERTIES_CLONE_ART."&headingid=".$article->fields['id_heading']."&id_article=".$article->fields['id']."&id_lang="; ?>'+$(this).val();
									<?
								}else{
									?>
									document.chang_lang.submit();
									<?
								}
								?>
							else
								$(this).val(<? echo $_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang']; ?>);
						}
					});
				});
			</script>
		</form>
	</div>
<? }
switch($_SESSION['dims']['wce']['subsub']){
	case module_wce::_SITE_PREVIEW:
		require_once module_wce::getTemplatePath("gestion_site/preview/controller.php");
		break;
	default:
	case module_wce::_SITE_PROPERTIES:
		require_once module_wce::getTemplatePath("gestion_site/properties/controller.php");
		break;
	case module_wce::_SITE_REF:
		require_once module_wce::getTemplatePath("gestion_site/referencement/controller.php");
		break;
	case module_wce::_SITE_DIFF:
		require_once module_wce::getTemplatePath("gestion_site/diffusion/controller.php");
		break;
	case module_wce::_SITE_LIST:
		require_once module_wce::getTemplatePath("gestion_site/list_articles/controller.php");
		break;
}
?>
</div>
