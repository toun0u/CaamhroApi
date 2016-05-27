<?php
require_once DIMS_APP_PATH."modules/wce/wiki/include/global.php";
wce_lang::initLangs();
?>

<script type="text/javascript" src="<? echo module_wiki::getTemplateWebPath("/include/functions.js"); ?>"></script>
<?php
if(! isset($_SESSION['dims']['wiki']['sub'])) $_SESSION['dims']['wiki']['sub'] = module_wiki::_SUB_HOMEPAGE;
$sub = dims_load_securvalue('sub',dims_const::_DIMS_NUM_INPUT,true,true,false,$_SESSION['dims']['wiki']['sub'], module_wiki::_SUB_HOMEPAGE);
?>
<link href="<? echo module_wiki::getTemplateWebPath('/styles.css'); ?>" rel="stylesheet" tyae="text/css">

<div class="header">

	<div class="zone_title">
		<a class="header_link" href="<?php echo module_wiki::getScriptEnv('sub='.module_wiki::_SUB_HOMEPAGE); ?>">
			<span class="logo_wiki">
				<img src="<? echo module_wiki::getTemplateWebPath('/gfx/icon_grand_wiki.png'); ?>" title="wiki" alt="wiki" />
			</span>
			<h1><? echo $_SESSION['cste']['_MANAGEMENT_OF_WIKI']; ?></h1>
		</a>

		<div id="historic">
		<?php
			if($sub != module_wiki::_SUB_NEW_ARTICLE) //dans le cas du 3ème sub on l'affiche dynamiquement via un appel ajax
				require_once module_wiki::getTemplatePath('/accueil/historic.tpl.php');
		?>
		</div>
	</div>
	<div class="menu_principal">
		<table cellpadding="0" cellspacing="0">
			<tbody>
				<tr>
					<td onclick="javascript:document.location.href='<? echo module_wiki::getScriptEnv('sub='.module_wiki::_SUB_HOMEPAGE); ?>';" <? if($sub == module_wiki::_SUB_HOMEPAGE) echo 'class="selected"'; ?>>
						<img src="<? echo module_wiki::getTemplateWebPath('/gfx/icon_home.png'); ?>" title="<? echo $_SESSION['cste']['_MANAGEMENT_OF_HOMEPAGE']; ?>" alt="<? echo $_SESSION['cste']['_MANAGEMENT_OF_HOMEPAGE']; ?>" />
						<div>
							<? echo $_SESSION['cste']['_MANAGEMENT_OF_HOMEPAGE']; ?>
						</div>
					</td>
					<td onclick="javascript:document.location.href='<? echo module_wiki::getScriptEnv('sub='.module_wiki::_SUB_LST_ARTICLES); ?>';" <? if($sub == module_wiki::_SUB_LST_ARTICLES) echo 'class="selected"'; ?>>
						<img src="<? echo module_wiki::getTemplateWebPath('/gfx/icone_liste.png'); ?>" title="<? echo $_SESSION['cste']['_LIST_OF_ARTICLES']; ?>" alt="<? echo $_SESSION['cste']['_LIST_OF_ARTICLES']; ?>" />
						<div>
							<? echo $_SESSION['cste']['_LIST_OF_ARTICLES']; ?>
						</div>
					</td>
					<td onclick="propertiesArticleWiki();" <? if($sub == module_wiki::_SUB_NEW_ARTICLE) echo 'class="selected"'; ?>>
						<img src="<? echo module_wiki::getTemplateWebPath('/gfx/icone_article.png'); ?>" title="<? echo $_SESSION['cste']['_NEW_ARTICLE']; ?>" alt="<? echo $_SESSION['cste']['_NEW_ARTICLE']; ?>" />
						<div>
							<?
							$id = dims_load_securvalue('articleid',dims_const::_DIMS_NUM_INPUT,true,true);
							if ($id>0)
								echo $_SESSION['cste']['_EDIT_ARTICLE'];
							else
								echo $_SESSION['cste']['_NEW_ARTICLE'];
							?>
						</div>
					</td>
					<?
					if (dims_isadmin()) {
						?>
						<td onclick="javascript:document.location.href='<? echo module_wiki::getScriptEnv('sub='.module_wiki::_SUB_CATEGORIES); ?>';" <? if($sub == module_wiki::_SUB_CATEGORIES) echo 'class="selected"'; ?>>
							<img src="<? echo module_wiki::getTemplateWebPath('/gfx/icone_categorie.png'); ?>" title="<? echo $_SESSION['cste']['_MANAGEMENT_OF_CATEGORIES']; ?>" alt="<? echo $_SESSION['cste']['_MANAGEMENT_OF_CATEGORIES']; ?>" />
							<div>
								<? echo $_SESSION['cste']['_MANAGEMENT_OF_CATEGORIES']; ?>
							</div>
						</td>
						<td onclick="javascript:document.location.href='<? echo module_wiki::getScriptEnv('sub='.module_wiki::_SUB_COLLAB); ?>';" <? if($sub == module_wiki::_SUB_COLLAB) echo 'class="selected"'; ?>>
							<img src="<? echo module_wiki::getTemplateWebPath('/gfx/icon_collab.png'); ?>" title="<? echo $_SESSION['cste']['_MANAGEMENT_OF_ASSOCIATES']; ?>" alt="<? echo $_SESSION['cste']['_MANAGEMENT_OF_ASSOCIATES']; ?>" />
							<div>
								<? echo $_SESSION['cste']['_MANAGEMENT_OF_ASSOCIATES']; ?>
							</div>
						</td>
						<td onclick="javascript:document.location.href='<? echo module_wiki::getScriptEnv('sub='.module_wiki::_SUB_LANGU); ?>';" <? if($sub == module_wiki::_SUB_LANGU) echo 'class="selected"'; ?>>
							<img src="<? echo module_wiki::getTemplateWebPath('/gfx/icone_langue.png'); ?>" title="<? echo $_SESSION['cste']['_MANAGEMENT_OF_LANGUAGES']; ?>" alt="<? echo $_SESSION['cste']['_MANAGEMENT_OF_LANGUAGES']; ?>" />
							<div>
								<? echo $_SESSION['cste']['_MANAGEMENT_OF_LANGUAGES']; ?>
							</div>
						</td>
						<?
					}
					if ($_SESSION['dims']['currentworkspace']['web'] == 1) {
						?>
						<td onclick="javascript:document.location.href='<? echo dims::getInstance()->getScriptEnv()."?sub2=1" ?>';">
							<img src="/common/modules/wce/img/mod32.png" title="WCE" alt="WCE" />
							<div>
								WCE
							</div>
						</td>
						<?php
					}
					?>
				</tr>
			</tbody>
		</table>
	</div>
</div>
<div class="contener">
	<?
	switch($sub){
		default:
		case module_wiki::_SUB_HOMEPAGE:
			require_once module_wiki::getTemplatePath('/accueil/controller.php');
			break;
		case module_wiki::_SUB_LST_ARTICLES:
			require_once module_wiki::getTemplatePath('/liste_articles/controller.php');
			break;
		case module_wiki::_SUB_NEW_ARTICLE:
			require_once module_wiki::getTemplatePath('/article/controller.php');
			break;
		case module_wiki::_SUB_CATEGORIES:
			require_once module_wiki::getTemplatePath('/categories/controller.php');
			break;
		case module_wiki::_SUB_COLLAB:
			require_once module_wiki::getTemplatePath('/collaborateurs/controller.php');
			break;
		case module_wiki::_SUB_LANGU:
			require_once module_wiki::getTemplatePath('/languages/controller.php');
			break;
	}

	?>
</div>
