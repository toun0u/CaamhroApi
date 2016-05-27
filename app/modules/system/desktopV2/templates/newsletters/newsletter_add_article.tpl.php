<?php
if (!isset($_SESSION['dims']['NEWSLETTER']['id_env'])) $_SESSION['dims']['NEWSLETTER']['id_env']='';
//$id_news = dims_load_securvalue('id_news',dims_const::_DIMS_CHAR_INPUT,true,true);
$id_env = dims_load_securvalue('id_env',dims_const::_DIMS_CHAR_INPUT,true,true,false,$_SESSION['dims']['NEWSLETTER']['id_env']);
$sent = dims_load_securvalue('sent',dims_const::_DIMS_NUM_INPUT,true,true);

$inf_news = new newsletter();
$inf_news->open($id_news);

if(isset($id_env) && $id_env != '') {
	$inf_env = new news_article();
	$inf_env->open($id_env);
	$_SESSION['dims']['NEWSLETTER']['id_env']=$id_env;
}

// Sécurisation du formulaire par token
require_once DIMS_APP_PATH . '/include/class_form_tokenizer.php';
$token = new FormToken\TokenField;

$sel_template='';
$sel_template .= '<select name="env_template">';

$token->field("env_template");

$workspace = new workspace();
$workspace->open($_SESSION['dims']['workspaceid']);
$users = $workspace->getusers();

$sel_template .= '<option value="">--</option>';

$sql="	select		*
	from		dims_workspace_template where id_workspace=:idworkspace";

$res=$db->query($sql, array(
	':idworkspace' => array('type' => PDO::PARAM_INT, 'value' => $workspace->getId()),
));

if(isset($id_env) && $id_env != '') $template= $inf_env->fields['template'];
elseif(isset($id_news) && $id_news != '') $inf_env->fields['template']= $inf_news->fields['template'];
// collecte la liste des templates disponibles
$availabletpl = dims_getavailabletemplates();

$activateComplexContent=false;

while ($f=$db->fetchrow($res)) {
	
	if (in_array($f['template'],$availabletpl)) {
		if ($f['template'] == $inf_env->fields['template']) {
			$sel='selected';
			// modification pour desactiver le contenu avec zones
			//$activateComplexContent = true;
		}
		else $sel='';
		$sel_template .= "<option $sel>".$f['template']."</option>";
	}
}

$sel_template .= '</select>';


//else {
//	$inf_env = new news_article();
//	 $inf_env->init_description();
//	 $inf_env->fields['label']= $inf_news->fields['label']." ".$_DIMS['cste']['_RSS_LABELTAB_ADD'];
//	 $inf_env->fields['id_newsletter']=$id_news;
//	 $inf_env->fields['content']=$inf_news->fields['descriptif'];
//
//	 $inf_env->save();
//	$id_env=$inf_env->fields['id'];
//}

//$title = $_DIMS['cste']['_DIMS_NEWSLETTER_MAILLINKED']."\"".$inf_news->fields['label']."\"";

//echo $skin->open_simplebloc($title);
?>
<div style="width:100%;float:left;">
	<form name="newsletter_env" id="newsletter_env" method="POST" action="admin.php?news_op=<? echo dims_const_desktopv2::_NEWSLETTER_ARTICLE_SAVE; ?>">
		<input type="hidden" name="id_news" value="<?php echo $id_news; ?>"/>
	<?php
		$token->field("id_news", $id_news);
		if(isset($id_env) && $id_env != '') {
	?>
		<input type="hidden" name="id_env" value="<?php echo $id_env; ?>"/>
	<?php
		$token->field("id_env", $id_env);
		}
	?>
	<table width="100%" cellpadding="0" cellspacing="5">
		<tr>
			<td colspan="2" align="left" style="font-size:15px;">
			</td>
		</tr>
		<tr>
			<td align="left" width="10%">
				<?php echo $_DIMS['cste']['_DIMS_LABEL_TITLE']; ?>&nbsp;
			</td>
			<td>
				<input type="text" id="env_label" style="width:400px;" name="env_label" <? if(isset($id_env) && $id_env != '') echo 'value="'.$inf_env->fields['label'].'"'; else echo 'value="'.$inf_news->fields['label'].'"';?> />
				<?
					$token->field("env_label");
				?>
			</td>
		</tr>
		<tr>
			<td align="right">
				<?php echo $_DIMS['cste']['_FORMS_MODEL']; ?>&nbsp;
			</td>
			<td>
				<?php
				echo $sel_template;
				?>
			</td>
		</tr>

		<tr>
			<td align="left" >
				<?php echo $_DIMS['cste']['_DIMS_LABEL_LANG']; ?>&nbsp;
			</td>
			<td>
					<select class="select" name="env_id_lang">
							<?
							$token->field("env_id_lang");
							if(isset($id_env) && $id_env != '')  $langsel=$inf_env->fields['id_lang'];
							else $langsel=1;
							$res=$db->query("select * from dims_lang");
							if ($db->numrows($res)>0) {
									while ($f=$db->fetchrow($res)) {
											$sel=($langsel == $f['id']) ? 'selected' : '';
											echo "<option value=\"".$f['id']."\" $sel>".$f['label']."</option>";
									}
							}
							?>
					</select>
			</td>
		</tr>

		<?php

			   if(isset($id_env) && $id_env != '') {
					$id_module = $_SESSION['dims']['moduleid'];
					$id_object = dims_const::_SYSTEM_OBJECT_NEWSLETTER;
					$id_record = $inf_env->fields['id'];
					$doc = '';
					require_once DIMS_APP_PATH.'include/functions/files.php';
					$lstfiles = dims_getFiles($dims,$id_module,$id_object,$id_record);

					if(isset($lstfiles) && $lstfiles != '') {
						foreach($lstfiles as $key => $file) {
							if($doc != '') $doc .= '<br/>';
							$doc .= '<a href='.$file['downloadlink'].' title="'.$file['name'].' - Voir le document.">'.$file['name'].'</a>';
							$doc .= "<a href=\"javascript:dims_confirmlink('".dims_urlencode("$scriptenv?dims_op=doc_file_delete&docfile_id=".$file['id'])."','".$_DIMS['cste']['_DIMS_CONFIRM']."');\"><img src=\"./common/img/delete.png\" alt=\"".$_DIMS['cste']['_DELETE']."\"></a>";
						}
					}
		 ?>
		<tr>
			<td align="left">
				<?php if($doc != '') echo $_DIMS['cste']['_DIMS_LABEL_PIECE_JOINTE']; ?>&nbsp;
			</td>
			<td align="left">
				<?php
					//if($doc == '') {
						//echo $id_module." ".$id_object." ".$id_record;
						echo dims_createAddFileLink($id_module,$id_object,$id_record,'float:left;');
					//}
					//else {
						echo "<div style=\"clear:both\">".$doc."</div>";
					//}
				?>
			</td>
		</tr>

		<?php }

		if ($activateComplexContent && isset($inf_env->fields['id_article'])) {
		?>
		<tr>
			<td align="center" colspan="2">
			   <?php
					echo  dims_create_button($_DIMS['cste']['_DIMS_SAVE'], './common/img/save.gif', 'javascript:document.newsletter_env.submit();', '', 'float:right;');
					echo  dims_create_button($_DIMS['cste']['_DIMS_BACK'], './common/img/undo.gif', 'javascript:document.location.href=\''.$scriptenv.'?news_op='.dims_const_desktopv2::_FICHE_NEWSLETTER.'\';', '', 'float:right;');
			   ?>

			</td>
		</tr>
		<tr><td colspan="2">
		<?php

			dims_init_module('wce');
			require_once DIMS_APP_PATH . '/modules/wce/include/classes/class_article.php';
			require_once DIMS_APP_PATH . '/modules/wce/include/classes/class_heading.php';
			require_once(DIMS_APP_PATH . '/modules/wce/include/classes/class_wce_block.php');
			require_once(DIMS_APP_PATH . '/modules/wce/include/classes/class_wce_block_model.php');
			require_once(DIMS_APP_PATH . '/modules/wce/include/classes/class_wce_site.php');

			require_once DIMS_APP_PATH."modules/wce/wiki/include/global.php";

			?>
			<script type="text/javascript" src="<? echo module_wiki::getTemplateWebPath("/include/functions.js"); ?>"></script>
			<?php
			$_GET['lang']=1;

			$article = new wce_article();
			$lang = dims_load_securvalue('lang', dims_const::_DIMS_NUM_INPUT, true, true, true);

			// on regarde pour créer ou récuperer un article fait
			if ($inf_env->fields['id_article']<=0) {
				// on doit créer un article
				//$heading = module_wiki::getRootHeading();
				$article->init_description();
				$article->setugm();
				$article->fields['author'] = $_SESSION['dims']['user']['firstname']." ".$_SESSION['dims']['user']['lastname'];
				$article->setvalues($_POST,"wce_article_");
				$article->fields['id_heading']=0;//$heading->fields['id'];
				$article->fields['visible']=0;
				$article->fields['template']=$inf_env->fields['template'];
				$article->fields['type']=  module_wiki::_TYPE_WIKI;
				$article->fields['model']=module_wiki::_ARTICLE_DEFAULT_MODEL;
				//$lstL = current(wce_lang::getInstance()->getAll(true));
				//$article->fields['id_lang']=(isset($lstL->fields['id'])?$lstL->fields['id']:1);
				$article->fields['id_lang']=$lang;
				$article->save();
				$inf_env->fields['id_article']=$article->fields['id'];
				$inf_env->save();
			}
			else {
			   $article->open($inf_env->fields['id_article'],$lang);
			   if ($article->fields['template']!=$inf_env->fields['template']) {
				   $article->fields['template']=$inf_env->fields['template'];
				   $article->save();
			   }
			}

			$action=module_wiki::_ACTION_SHOW_ARTICLE_NEWSLETTER;
			$_SESSION['dims']['wiki']['article']['action']=$action;
			$wce_mode='edit';

			$_SESSION['wiki']['articleid']=$inf_env->fields['id_article'];
			require_once module_wiki::getTemplatePath('/article/controller.php');
			?>
			</td></tr>
			<?
		}
		else {
		?>

		<tr>
			<td align="left">
				<?php echo $_DIMS['cste']['_CONTENT']; ?>&nbsp;
			</td>
			<td>
				<?
				if(isset($id_env) && $id_env != '') $content= $inf_env->fields['content'];
				elseif(isset($id_news) && $id_news != '') $content= $inf_news->fields['descriptif'];
				else $content="";
				$dims = dims::getInstance();
				$http_host = (isset($_SERVER['HTTP_HOST'])) ? $_SERVER['HTTP_HOST'] : '';
				$rootpath=$dims->getProtocol().$http_host;

				$config = $rootpath."/common/modules/wce/ckeditor/ckeditor_config_simple_fr.js";
				dims_fckeditor("env_content",$content,"800","600",false,$config);
				$token->field("fck_env_content");
				?>
			</td>
		</tr>
		<?php
		}
		?>
		<tr>
			<td align="center" colspan="2">
			   <?php
					echo  dims_create_button($_DIMS['cste']['_DIMS_SAVE'], './common/img/save.gif', 'javascript:document.newsletter_env.submit();', '', 'float:right;');
					echo  dims_create_button($_DIMS['cste']['_DIMS_BACK'], './common/img/undo.gif', 'javascript:document.location.href=\''.$scriptenv.'?news_op='.dims_const_desktopv2::_FICHE_NEWSLETTER.'\';', '', 'float:right;');
			   ?>

			</td>
		</tr>
	</table>
	<?
		$tokenHTML = $token->generate();
		echo $tokenHTML;
	?>
	</form>
</div>
<?php
//echo $skin->close_simplebloc();
?>
