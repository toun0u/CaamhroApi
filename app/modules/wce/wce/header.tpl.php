<?php
if (!isset($_SESSION['wce_display_menu'])) $_SESSION['wce_display_menu']=true;
?>

<div class="menu_principal" <? if(!$_SESSION['wce_display_menu']) echo 'style="display:none;"'; ?>>
    <div class="header">
	<div class="header_title">
		<img src="<? echo module_wce::getTemplateWebPath("/gfx/logo_dims.png"); ?>" alt="dims" title="logo dims" />
		<h1 class="title_header"><? echo $_SESSION['cste']['_YOUR_WEBSITE']; ?></h1>
	</div>
	<div class="searchform">
		<span>
			<img style="float:left" name="button_search" src="<? echo module_wce::getTemplateWebPath("/gfx/img_search_left.png"); ?>" alt="search" title="img search left" />
			<input type="text" onblur="Javascript:if (this.value=='')this.value='Rechercher un article';" onfocus="Javascript:if (this.value == 'Rechercher un article') this.value='';" value="Rechercher un article" maxlength="80" id="editbox_search" name="editbox_search" onkeyup="javascript:searchTags(this.value);" />
			<input type="image" style="float:left" src="<? echo module_wce::getTemplateWebPath("/gfx/img_search_right.png"); ?>" alt="search" title="button search right" />
		</span>
	</div>
</div>
	<table cellspacing="0" cellpadding="0" class="table_gauche">
		<tr>
			<td class="picture">
				<a href="<? echo module_wce::get_url(module_wce::_SUB_HOMEPAGE); ?>" <? if($_SESSION['dims']['wce']['sub'] == module_wce::_SUB_HOMEPAGE) echo 'class="selected"'; ?>>
					<img src="<? echo module_wce::getTemplateWebPath("/gfx/icon_home.png"); ?>" />
					<div style="clear: both;"><? echo $_SESSION['cste']['SMILE_HOME']; ?></div>
				</a>
			</td>
			<td class="picture">
				<a href="<? echo module_wce::get_url(module_wce::_SUB_SITE); ?>" <? if($_SESSION['dims']['wce']['sub'] == module_wce::_SUB_SITE) echo 'class="selected"'; ?>>
					<img src="<? echo module_wce::getTemplateWebPath("/gfx/icon_gest_site.png"); ?>" />
					<div style="clear: both;"><? echo $_SESSION['cste']['_SITE_MANAGEMENT']; ?></div>
				</a>
			</td>
			<td class="picture">
				<a href="<? echo module_wce::get_url(module_wce::_SUB_LIST); ?>" <? if($_SESSION['dims']['wce']['sub'] == module_wce::_SUB_LIST) echo 'class="selected"'; ?>>
					<img src="<? echo module_wce::getTemplateWebPath('/gfx/icon_gest_article.png'); ?>" title="<? echo $_SESSION['cste']['_LIST_OF_ARTICLES']; ?>" alt="<? echo $_SESSION['cste']['_LIST_OF_ARTICLES']; ?>" />
					<div style="clear: both;"><? echo $_SESSION['cste']['_LIST_OF_ARTICLES']; ?></div>
				</a>
			</td>
			<td class="picture">
				<a href="<? echo module_wce::get_url(module_wce::_SUB_PARAM); ?>" <? if($_SESSION['dims']['wce']['sub'] == module_wce::_SUB_PARAM) echo 'class="selected"'; ?>>
					<img src="<? echo module_wce::getTemplateWebPath("/gfx/icon_param.png"); ?>" />
					<div style="clear: both;"><? echo $_SESSION['cste']['_SITE_SETTINGS']; ?></div>
				</a>
			</td>
			<!--
			<td class="picture">
				<a href="<? echo module_wce::get_url(module_wce::_SUB_ARTICLE); ?>" <? if($_SESSION['dims']['wce']['sub'] == module_wce::_SUB_ARTICLE) echo 'class="selected"'; ?>>
					<img src="<? echo module_wce::getTemplateWebPath("/gfx/icon_gest_article.png"); ?>" />
					<div style="clear: both;"><? echo $_SESSION['cste']['_MANAGEMENT_ARTICLES']; ?></div>
				</a>
			</td>-->
			<td class="picture">
				<a href="<? echo module_wce::get_url(module_wce::_SUB_DYN); ?>" <? if($_SESSION['dims']['wce']['sub'] == module_wce::_SUB_DYN) echo 'class="selected"'; ?>>
					<img src="<? echo module_wce::getTemplateWebPath("/gfx/icon_obj_dynamique.png"); ?>" />
					<div style="clear: both;"><? echo $_SESSION['cste']['_DYNAMIC_OBJECTS']; ?></div>
				</a>
			</td>
			<!-- TODO : gérer en back les param de piwik
			<td class="picture">
				<a href="<? echo module_wce::get_url(module_wce::_SUB_STATS); ?>" <? if($_SESSION['dims']['wce']['sub'] == module_wce::_SUB_STATS) echo 'class="selected"'; ?>>
					<img src="<? echo module_wce::getTemplateWebPath("/gfx/icon_stats.png"); ?>" />
					<div style="clear: both;"><? echo $_SESSION['cste']['STATISTIQUES']; ?> / <? echo $_SESSION['cste']['_TRACKERS']; ?></div>
				</a>
			</td>-->
		</tr>
	</table>
    <?
    if(defined('_DISPLAY_WIKI') && _DISPLAY_WIKI){
        ?>
        <table cellspacing="0" cellpadding="0" class="table_droite">
            <tr>
                <td class="picture">
                    <a href="<? echo dims::getInstance()->getScriptEnv()."?op=wiki&sub=1"; ?>">
                        <img src="<? echo module_wce::getTemplateWebPath("/gfx/icon_wiki.png"); ?>" />
                        <div style="clear: both;"><? echo $_SESSION['cste']['_GO_TO_WIKI']; ?></div>
                    </a>
                </td>
            </tr>
        </table>
        <?
    }
    ?>
</div>
<div class="repli_depli">
	<a href="javascript:void(0);"><img src="<? echo module_wce::getTemplateWebPath("/gfx/icon_".(($_SESSION['wce_display_menu'])?'r':'d')."eplier.png"); ?>"></a>
</div>
<script type="text/javascript">
	$(document).ready(function(){
		$('div.repli_depli a').click(function(){
			$('div.menu_principal').slideToggle('slow',function(){
				if($(this).is(":visible")){
					$('div.repli_depli a img').attr("src","<? echo module_wce::getTemplateWebPath("/gfx/icon_replier.png"); ?>");
				}else{
					$('div.repli_depli a img').attr("src","<? echo module_wce::getTemplateWebPath("/gfx/icon_deplier.png"); ?>");
				}
				dims_xmlhttprequest('admin.php','dims_op=switch_display_menu');
			});
		});
	});
</script>
