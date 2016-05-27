<?php
require_once DIMS_APP_PATH.'/modules/system/faq/class_dims_glossaire.php';
$action = dims_load_securvalue('action',dims_const::_DIMS_CHAR_INPUT,true,true,true);
switch($action){
	case 'changeLang' :
		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
		if ($id != '' && $id > 0)
			$_SESSION['dims']['glossaire']['lang'] = $id;
		else
			$_SESSION['dims']['glossaire']['lang'] = $_SESSION['dims']['currentlang'];
		global $dims;
		dims_redirect($dims->getScriptEnv());
		break;
	case 'edit':
		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
		$faq = new dims_glossaire();
		$faq->init_description();
		if ($id != '' && $id > 0)
			$faq->open($id);
		else $id = 0;

		$AddCateg = dims_load_securvalue('categ',dims_const::_DIMS_NUM_INPUT,true,true,true);
		if ($AddCateg){
			$link = $faq->searchGbLink(dims_const::_SYSTEM_OBJECT_CATEGORY);
			require_once DIMS_APP_PATH.'modules/system/class_category.php';
			?>
			<span style="width:100%;margin-bottom:10px;" id="arianeCategG_<? echo $id; ?>">
			<?
			if (count($link) > 0){
				$categ = new category();
				$categ->openWithGB(current($link));
				echo 'Cat&eacute;gorie : '.$categ->getAriane();
				echo dims_create_button("Changer cat&eacute;gorie",'','javascript:publiGlossaireAddCateg('.$id.');','','float:right;');
			}else{
				echo 'Aucune cat&eacute;gorie';
				echo dims_create_button("Choisir cat&eacute;gorie",'','javascript:publiGlossaireAddCateg('.$id.');','','float:right;');
			}
			?>
			</span>
			<?
		}
		?>
		<form method="POST" action="<? echo $dims->getScriptEnv(); ?>" name="saveGlossaire_<? echo $id; ?>">
			<?
				// Sécurisation du formulaire par token
				require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
				$token = new FormToken\TokenField;
				$token->field("saveGlossaire");
				$token->field("id");
				$token->field("idCateg");
				$token->field("langGlossaire_title");
				$token->field("isBefore");
				$token->field("popupidCateg");
				$token->field("fck_".'lang'.$id.'_content');
				$tokenHTML = $token->generate();
				echo $tokenHTML;
			?>
			<input type="hidden" name="saveGlossaire" value="saveGlossaire" />
			<input type="hidden" name="id" value="<? echo $id; ?>" />
			<?
			if ($AddCateg){
				?>
				<input type="hidden" name="idCateg" id="idCategG_<? echo $id; ?>" value="<? if (count($link) > 0) echo current($link); else echo 0; ?>" />
				<?
			}
			dims_fckeditor('lang'.$id.'_content', $faq->getContent(), '100%', 300,true);
			?>
			<div style="width: 60%;float:left;">
				<table cellpadding="0" cellspacing="0">
					<tr>
						<td style="text-align:right;width:30%;">
							<? echo $_SESSION['cste']['_DIMS_LABEL_TITLE']; ?> :
						</td>
						<td>
							<input onfocus="javascript: $(this).css('background','');" type="text" name="langGlossaire_title" value="<? echo $faq->getTitle(); ?>" style="width:100%" />
						</td>
					</tr>
					<tr>
						<td style="text-align:right;">
							Mise en avant :
						</td>
						<td>
							<input type="checkbox" name="isBefore" <? if($faq->getIsBefore()) echo 'checked=true'; ?> />
						</td>
					</tr>
				</table>
			</div>
			<div style="float:left;margin-left:5%;">
				<?
				require_once DIMS_APP_PATH.'/modules/system/class_lang.php';
				$lang = new lang();
				$lang->open($_SESSION['dims']['faq']['lang']);
				if ($urlFlag = $lang->getFlag())
					echo '<img src="'.$urlFlag.'">';
				else
					echo 'No flag available !';
				?>
			</div>
		</form>
		<?
		echo dims_create_button($_SESSION['cste']['_DIMS_SAVE'],'','javascript: if(document.saveGlossaire_'.$id.'.langGlossaire_title.value != \'\') document.saveGlossaire_'.$id.'.submit(); else document.saveGlossaire_'.$id.'.langGlossaire_title.style.backgroundColor=\'#FF0000\';','','float:right;','javascript:void(0);');
		echo dims_create_button($_SESSION['cste']['_DIMS_LABEL_CANCEL'],'','javascript:dims_xmlhttprequest_todiv(\'admin.php\',\'dims_op=dims_glossaire_manager&action=view&id='.$id.'\',\'\',\'content_'.$id.'\');','','float:right;');
		echo dims_create_button($_SESSION['cste']['_DELETE'],'','javascript:dims_confirmlink(\'?dims_op=dims_glossaire_manager&action=delete&id='.$id.'\',\'Êtes-vous sur de vouloir supprimer ce FAQ ?\');','','float:right;');
		break;
	case 'view':
		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
		if ($id != '' && $id > 0){
			$faq = new dims_glossaire();
			$faq->open($id);
			?>
				<iframe class="frame_<? echo $id; ?>" style="border:none;width:100%;height:380px;"></iframe>
				<div class="contentIframe_<? echo $id; ?>">
					<?
					echo $faq->getContent();
					?>
				</div>
				<script type="text/javascript">
					window['loadIframe'] = function loadIframe(id){
						$('iframe.frame_'+id).contents().find('body').append($('div.contentIframe_'+id)).css({'color': '#1F1F1F', 'font': '13px Trebuchet MS,Tahoma,Verdana,Arial,sans-serif'});
					}
					$(document).ready(function(){
						setTimeout("loadIframe(<? echo $id; ?>)",500);
					});
				</script>
			<?
		}
		break;
	case 'delete':
		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
		if ($id != '' && $id > 0){
			$faq = new dims_glossaire();
			$faq->open($id);
			$faq->delete();
		}
		global $dims;
		dims_redirect($dims->getScriptEnv());
		break;
	case 'viewCateg' :
		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
		$id_popup = dims_load_securvalue('id_popup',dims_const::_DIMS_NUM_INPUT,true,true,true);
		require_once DIMS_APP_PATH.'modules/system/class_category.php';
		$categ = new category();
		$categ->init_description();
		if ($id != '' && $id > 0){
			$faq = new dims_glossaire();
			$faq->open($id);
			$lk = $faq->searchGbLink(dims_const::_SYSTEM_OBJECT_CATEGORY);
			if (count($lk) > 0)
				$categ->openWithGB(current($lk));
		}
		$db = dims::getInstance()->getDb();
		$sel = "SELECT	id_category
				FROM	dims_category_object
				WHERE	object_id_module_type = :moduletypeid
				AND		id_object = :idobject ";
		$res = $db->query($sel, array(
			':moduletypeid' => $_SESSION['dims']['moduletypeid'],
			':idobject' => dims_const::_SYSTEM_OBJECT_GLOSSAIRE
		));
		$lstCat = array();
		$lvl = 0;
		while($r = $db->fetchrow($res)){
			$cat = new category();
			$cat->open($r['id_category']);
			$cat->initDescendance();
			$lstCat[$r['id_category']] = $cat->getArborescence();
			if ($lstCat[$r['id_category']]['nbLvl'] > $lvl)
				$lvl = $lstCat[$r['id_category']]['nbLvl'];
		}
		require_once(DIMS_APP_PATH.'modules/system/class_dims_browser.php');
		$browser = new dims_browser($lvl+1,$lstCat,'listeCategGlossaireSave');
		?>
		<div id="bank_account_detail">
			<div class="actions">
				<a href="Javascript: void(0);" onclick="Javascript: dims_closeOverlayedPopup('<?php echo $id_popup; ?>');">
					<img src="modules/notaire/templates/backoffice/img/icon_close.gif" />
				</a>
			</div>
			<h2>
				Ajout d'une cat&eacute;gorie pour : <?php if ($id > 0) echo $faq->getTitle(); else echo 'Nouvelle FAQ' ?>
			</h2>
			<input type="hidden" name="popupidCateg" id="popupidCateg" value="<? echo $categ->getId(); ?>" />
			<div style="width:100%;">
				<?
				if ($categ->getId() > 0)
					$browser->displayBrowser('./common/modules/system/class_category.tpl.php',$categ->getBrowserAriane());
				else
					$browser->displayBrowser('./common/modules/system/class_category.tpl.php');
				?>
			</div>
			<?
			if ($id != '' && $id > 0)
				echo dims_create_button($_SESSION['cste']['_DIMS_ADD'],'','javascript:dims_xmlhttprequest_todiv(\'admin.php\',\'dims_op=dims_glossaire_manager&action=refreshCateg&id='.$id.'&idCateg=\'+document.getElementById(\'popupidCateg\').value,\'\',\'arianeCategG_'.$id.'\');dims_closeOverlayedPopup('.$id_popup.');','','float:right;margin-bottom:10px;margin-right:20px;');
			else
				echo dims_create_button($_SESSION['cste']['_DIMS_ADD'],'','javascript:dims_xmlhttprequest_todiv(\'admin.php\',\'dims_op=dims_glossaire_manager&action=refreshCateg&id='.$id.'&idCateg=\'+document.getElementById(\'popupidCateg\').value,\'\',\'arianeCategG_X\');dims_closeOverlayedPopup('.$id_popup.');','','float:right;margin-bottom:10px;margin-right:20px;');
			echo dims_create_button($_SESSION['cste']['_DIMS_LABEL_CANCEL'],'','javascript:dims_closeOverlayedPopup('.$id_popup.');','','float:right;margin-bottom:10px;');
			?>
		</div>
		<script type="text/javascript">
			function initCateg(elem,parent){
				if (parent.attr("name"))
					var curr = elem.attr("name").split(parent.attr("name"));
				else
					var curr = elem.attr("name").split("_");
				$("input#popupidCateg").val(curr[1]);
				$("div.listeCategGlossaireSave li[class~='browser']").click(function(){initCateg($(this),$(this).parents("div:first"));});
			}

			<? if (isset($_SESSION['dims']['dims_browser']['listeCategGlossaireSave']['selected_lvl']) && isset($_SESSION['dims']['dims_browser']['listeCategGlossaireSave']['selected_id'][$_SESSION['dims']['dims_browser']['listeCategGlossaireSave']['selected_lvl']])){ ?>
				var selected = $("div.listeCategGlossaireSave li[class~='selected'][name*='_<? echo $_SESSION['dims']['dims_browser']['listeCategGlossaireSave']['selected_id'][$_SESSION['dims']['dims_browser']['listeCategGlossaireSave']['selected_lvl']]; ?>']") ;
				var prt = selected.parents("div#<? echo $_SESSION['dims']['dims_browser']['listeCategGlossaireSave']['selected_lvl']; ?>");
				initCateg(selected,prt);
			<? } else { ?>
				$("input#popupidCateg").val(0);
				$("div.listeCategGlossaireSave li[class~='browser']").click(function(){initCateg($(this),$(this).parents("div:first"));});
			<? } ?>
		</script>
		<?
		break;
	case 'refreshCateg' :
		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
		$idCateg = dims_load_securvalue('idCateg',dims_const::_DIMS_NUM_INPUT,true,true,true);
		require_once DIMS_APP_PATH.'modules/system/class_category.php';
		if ($idCateg > 0 && $idCateg != ''){
			$categ = new category();
			$categ->open($idCateg);
			echo 'Cat&eacute;gorie : '.$categ->getAriane();
			echo dims_create_button("Changer cat&eacute;gorie",'','javascript:publiGlossaireAddCateg('.$id.');','','float:right;');
		}else{
			echo 'Aucune cat&eacute;gorie';
			echo dims_create_button("Choisir cat&eacute;gorie",'','javascript:publiGlossaireAddCateg('.$id.');','','float:right;');
		}
		?>
		<script type="text/javascript">
			document.getElementById('idCategG_<? if ($id != '' && $id > 0) echo $id; else echo 'X'; ?>').value = <? echo $idCateg; ?>;
		</script>
		<?
		break;
}

?>