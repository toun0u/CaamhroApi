<?php
require_once DIMS_APP_PATH."modules/wce/wiki/include/global.php";
require_once(DIMS_APP_PATH . '/modules/wce/wiki/include/class_wce_reference.php');

$op_wiki = dims_load_securvalue('op_wiki',dims_const::_DIMS_CHAR_INPUT,true,true);
switch($op_wiki){
	case 'updateDocRef':
		require_once(DIMS_APP_PATH . '/modules/doc/class_docfile.php');
		$id_doc = dims_load_securvalue('id_doc',dims_const::_DIMS_NUM_INPUT,true,true);
		ob_end_clean();
		if ($id_doc>0) {
			$doc= new docfile();
			$doc->open($id_doc);

			echo $doc->fields['name']." <a href=\"javascript:void(0);\" onclick=\"javascript:initDocLink();\"><img src=\"./common/img/delete.png\"></a>";
		}
		die();
		break;

	case 'articlewiki_xml':
		$id = dims_load_securvalue('id_article',dims_const::_DIMS_NUM_INPUT,true,true);
		$article = new wce_article();
		if ($id != '' && $id > 0){
			$article->open($id);
			$article->getContentToXml();
		}
		break;

	case 'articlewiki_deleteref':
		$id_article=0;
		$id = dims_load_securvalue('id_reference',dims_const::_DIMS_NUM_INPUT,true,true);
		$ref = new wce_reference();
		if ($id != '' && $id > 0){
			$ref->open($id);
			$id_article=$ref->fields['id_article'];
			if ($ref->fields['id']>0 && (dims_isadmin() || dims_isactionallowed(0) || $ref->fields['id_user']==$_SESSION['dims']['userid']))
				$ref->delete();

			dims_redirect(module_wiki::getScriptEnv('sub='.module_wiki::_SUB_NEW_ARTICLE.'&action='.module_wiki::_PARAMETERS_VIEW.'&articleid='.$id_article.'&params_op='.module_wiki::_REFERENCES));
		}
		die();
		break;

	case 'articlewiki_delete':
		$id = dims_load_securvalue('id_article',dims_const::_DIMS_NUM_INPUT,true,true);
		$article = new wce_article();
		if ($id != '' && $id > 0){
			$article->open($id);
			if (dims_isadmin() || dims_isactionallowed(0)) {
				$article->delete();
			}
		}
		dims_redirect(module_wiki::getScriptEnv('sub='.module_wiki::_SUB_LST_ARTICLES));
		break;
	case module_wiki::_ACTION_PROPERTIES_ARTICLE:
		// formulaire d'ajout d'article
		ob_clean();

		$id = dims_load_securvalue('id_article',dims_const::_DIMS_NUM_INPUT,true,true);
		$article = new wce_article();
		if ($id != '' && $id > 0){
			$article->open($id);
			echo $skin->open_simplebloc($_DIMS['cste']['_MODIFY'],'100%','','');
		}else {
			$article->init_description();
			echo $skin->open_simplebloc($_DIMS['cste']['_ADD'],'100%','','');
		}
		$article->display(module_wiki::getTemplatePath('/article/properties_article.php'));
		echo $skin->close_simplebloc();
		break;

	case 'getAjaxEditContentBlock':
		ob_clean();
		$article= new wce_article();
		$block = new wce_block();
		$block_id=dims_load_securvalue('block_id',dims_const::_DIMS_NUM_INPUT,true,true);
		$id_lang=dims_load_securvalue('lang',dims_const::_DIMS_NUM_INPUT,true,true);
		$content_id=dims_load_securvalue('content_id',dims_const::_DIMS_NUM_INPUT,true,true);
		$linksmodify=dims_load_securvalue('linksmodify',dims_const::_DIMS_NUM_INPUT,true,true);

		$versionid=0;

		if (isset($_SESSION['dims']['connected'])
			&& $_SESSION['dims']['connected']
			&& (dims_isadmin() || (dims_isactionallowed(_WCE_ACTION_ARTICLE_EDIT) || dims_isactionallowed(0)))
			&& $content_id <= $article->getNbElements() ) {
			$block->open($block_id,$id_lang);
			$articleid=$block->fields['id_article'];

		   /* if (isset($_SESSION['wce'][$_SESSION['dims']['moduleid']][$articleid]['versionid'])
				&& $_SESSION['wce'][$_SESSION['dims']['moduleid']][$articleid]['versionid']>0) {

				$versionid=$_SESSION['wce'][$_SESSION['dims']['moduleid']][$articleid]['versionid'];
				$article->open($articleid);
				$blocksversion=$article->loadBlocksFromVersion($versionid,$id_lang);

				if ($versionid >0 && isset($blocksversion[$block->fields['section']][$block->fields['id']])) {
					$contents=$blocksversion[$block->fields['section']][$block->fields['id']];

					if (isset($contents['content'.$content_id])) echo $contents['content'.$content_id];
				}
			}
			else {
			// attention on peut des données avec cela

				if ($linksmodify==0) {
					// on test si bloc modifie ou non
					if ($block->fields['uptodate']) echo $block->fields['content'.$content_id];
					else echo $block->fields['draftcontent'.$content_id];
				}
				else {
					// on demande la version modifiée
					// on test si bloc modifie ou non
					if ($block->fields['uptodate']) $content= $block->fields['content'.$content_id];
					else $content= $block->fields['draftcontent'.$content_id];


					// on transforme
					$content=wce_article::convertLinksToLinksEdit($content,'wiki');

					echo $content;
				}
		   // }
		   */
			if ($linksmodify==0) {
				// on test si bloc modifie ou non
				if ($block->fields['uptodate'] && $block->fields['content'.$content_id] == $block->fields['draftcontent'.$content_id])
					echo $block->fields['content'.$content_id];
				else
					echo $block->fields['draftcontent'.$content_id];
			}
			else {
				// on demande la version modifiée
				// on test si bloc modifie ou non
				if ($block->fields['uptodate'] && $block->fields['content'.$content_id] == $block->fields['draftcontent'.$content_id])
					$content= $block->fields['content'.$content_id];
				else
					$content= $block->fields['draftcontent'.$content_id];

				// on transforme
				$content=wce_article::convertLinksToLinksEdit($content,'wiki');

				echo $content;
			}

		}
		die();
		break;
	case 'getAjaxEditInfoBlock':
		ob_clean();
		$block = new wce_block();
		$block_id=dims_load_securvalue('block_id',dims_const::_DIMS_NUM_INPUT,true,true);
		$id_lang=dims_load_securvalue('lang',dims_const::_DIMS_NUM_INPUT,true,true);
		if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected'] && (dims_isadmin() || (dims_isactionallowed(_WCE_ACTION_ARTICLE_EDIT) || dims_isactionallowed(0)))) {
			$block->open($block_id,$id_lang);
			// construction du formulaire d'edition de changement de valeur
			$block->display(module_wiki::getTemplatePath('/article/block/admin_article_block_form_smalledit.php'));
		}
		die();
		break;
	case 'add_block':
		$section_id=dims_load_securvalue('section',dims_const::_DIMS_NUM_INPUT,true,true);
		$id_lang=dims_load_securvalue('lang',dims_const::_DIMS_NUM_INPUT,true,true);
		$block = new wce_block();
		$block->init_description();
		$block->fields['id_article']=$_SESSION['wiki']['articleid'];
		$block->fields['section']=$section_id;
		$block->fields['id_lang']=$id_lang;
		$block->setugm();
		if ($section_id>0)
			echo $skin->open_simplebloc($_DIMS['cste']['_ADD']. " > section ".$section_id,'100%','','');
		else
		echo $skin->open_simplebloc($_DIMS['cste']['_ADD'],'100%','','');
		$block->display(module_wiki::getTemplatePath('/article/block/admin_article_block_form.php'));
		echo $skin->close_simplebloc();
		break;
	case 'modify_block':
		$block = new wce_block();
		$block_id=dims_load_securvalue('block_id',dims_const::_DIMS_NUM_INPUT,true,true);
		$lang=dims_load_securvalue('lang',dims_const::_DIMS_NUM_INPUT,true,true);
		if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected'] && (dims_isadmin() || (dims_isactionallowed(_WCE_ACTION_ARTICLE_EDIT) || dims_isactionallowed(0)))) {
			$block->open($block_id,$lang);
			echo $skin->open_simplebloc($_DIMS['cste']['_MODIFY'],'100%','','');
			$block->display(module_wiki::getTemplatePath('/article/block/admin_article_block_form.php'));
			echo $skin->close_simplebloc();
		}
		break;
	case 'modify_blockcontent':
		$block_id=dims_load_securvalue('block_id',dims_const::_DIMS_NUM_INPUT,true,true);

		if (isset($_SESSION['dims']['connected']) && $_SESSION['dims']['connected'] && (dims_isadmin() || (dims_isactionallowed(_WCE_ACTION_ARTICLE_EDIT) || dims_isactionallowed(0)))) {
			$block = new wce_block();
			$block->open($block_id);
			echo $skin->open_simplebloc($_DIMS['cste']['_MODIFY']." : ".$block->fields['title'],'100%','','');
			$block->display(module_wiki::getTemplatePath('/article/block/admin_article_blockcontent_form.php'));
			echo $skin->close_simplebloc();
		}
		break;

	case 'sel_article_wiki':
		$heading = module_wiki::getRootHeading();
            $sel = 	"SELECT 	a.*
                    FROM 		(
                                SELECT      a.*
                                FROM        ".wce_article::TABLE_NAME." a
                                WHERE       a.id_heading = :id_heading
                                AND         a.id_lang IN (:defaultlang,:langarticle)
                                ORDER BY    a.id_lang ".(($_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang'] > $_SESSION['dims']['wce_default_lg'])?'DESC':'ASC')."
                                ) as a
                    GROUP BY    a.id
					ORDER BY	title";

		$db = dims::getInstance()->getDb();
		$lst = array();
		$elem = array();
		$elem[] = "(Aucun)";
		$elem[] = "0";
		$lst[] = $elem;
		$res = $db->query($sel,array(
			':id_heading'=>array('value'=>$heading->fields['id'],'type'=>PDO::PARAM_INT),
			':defaultlang' => array('value' => $_SESSION['dims']['wce_default_lg'], 'type' => PDO::PARAM_INT),
			':langarticle' => array('value' => $_SESSION['wce'][$_SESSION['dims']['moduleid']]['id_lang'], 'type' => PDO::PARAM_INT),
		));
		while($r = $db->fetchrow($res)){
			$elem = array();
			$elem[] = $r['title'];
			$elem[] = $r['id'];
			$lst[] = $elem;
		}
		echo json_encode($lst);
		break;
	case 'sel_section_wiki':
		$id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
		$lst = array();
		$elem = array();
		$elem[] = "(Aucun)";
		$elem[] = "0";
		$elem[] = "1";
		$elem[] = "1";
		$lst[] = $elem;
		if ($id != '' && $id > 0){
			$sel = "SELECT		title, id, section, page_break
					FROM		dims_mod_wce_article_block
					WHERE		id_article = :id_article
					ORDER BY	level, position";
			$db = dims::getInstance()->getDb();
			$res = $db->query($sel,array(':id_article'=>array('value'=>$id,'type'=>PDO::PARAM_INT)));
			$i = 1;
			while($r = $db->fetchrow($res)){
				if($r['page_break']) $i ++;
				$elem = array();
				$elem[] = $r['title'];
				$elem[] = $r['id'];
				$elem[] = $r['section'];
				$elem[] = $i;
				$lst[] = $elem;
			}
		}
		echo json_encode($lst);
		break;
	case 'article_refresh_categ':
		$root = module_wiki::getCategRoot();
		$load = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);

		$num = 0;
		$opened = array();

		if ($load != '' && $load > 0){
			$par = new category();
			$par->open($load);
			$root->setLightAttribute('parent',$par);
			$num = $par->fields['id'];
			$opened = explode(';',$par->fields['parents']);
			$opened[] = $par->fields['id'];
		}else{
			$root->setLightAttribute('parent',null);
		}
		$root->setLightAttribute('current',$num);
		$root->setLightAttribute('opened',$opened);

		$root->display(module_wiki::getTemplatePath('/categories/ajax_categ_browser_lvl.tpl.php'));
		break;
	case 'create_new_article':
		$name = trim(dims_load_securvalue('name',dims_const::_DIMS_CHAR_INPUT,true,true,true));
		if (!empty($name)){
			$article = new wce_article();
			// on initialise l'article
			$heading = module_wiki::getRootHeading();

			$article->init_description();
			$article->setugm();
			$article->fields['author'] = $_SESSION['dims']['user']['firstname']." ".$_SESSION['dims']['user']['lastname'];
			$article->fields['title']=$name;
			$article->fields['id_heading']=$heading->fields['id'];
			$article->fields['visible']=0;
			$article->fields['type']=  module_wiki::_TYPE_WIKI;
			$article->fields['model']=module_wiki::_ARTICLE_DEFAULT_MODEL;
			$article->fields['id_lang']=1;
			$article->save();
			echo $article->fields['id'];
		}else
			echo "0";
		break;
	case 'loadResponseForm':
		$todo_id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
		if( ! empty($todo_id) ){
			$parent = new todo();
			$parent->open($todo_id);
			$article = new wce_article();
			$article->openWithGB($parent->fields['id_globalobject_ref']);
			$todo = new todo();
			$todo->init_description();
			$todo->setugm();
			//récupération de la liste des utilisateurs
			$todo->setLightAttribute('mode', 'answer');
			$todo->setLightAttribute('action_path', module_wiki::getScriptEnv('collab_op='.module_wiki::_SAVE_INTERVENTION.'&id='.$article->getId()));
			$todo->setLightAttribute('back_path', module_wiki::getScriptEnv('collab_op='.module_wiki::_SHOW_COLLABORATION.'&id='.$article->getId()));
			$todo->setLightAttribute('todo_id_globalobject_ref', $article->fields['id_globalobject']);//pour l'association du todo à l'article WCE
			$todo->setLightAttribute('todo_user_from', $_SESSION['dims']['userid']);
			$todo->setLightAttribute('todo_id_parent', $todo_id);
			$todo->display(module_wiki::getTemplatePath('/article/collaboration/form.tpl.php'));
		}
		break;
	case 'loadValidForm':
		$todo_id = dims_load_securvalue('id',dims_const::_DIMS_NUM_INPUT,true,true,true);
		$from = dims_load_securvalue('from',dims_const::_DIMS_CHAR_INPUT,true,true,true);
		if( ! empty($todo_id) ){
			$parent = new todo();
			$parent->open($todo_id);
			$article = new wce_article();
			$article->openWithGB($parent->fields['id_globalobject_ref']);
			$todo = new todo();
			$todo->init_description();
			$todo->setugm();
			//récupération de la liste des utilisateurs
			$todo->setLightAttribute('mode', 'validation');
			if( isset($from) && $from == 'desktop'){
				$todo->setLightAttribute('action_path', module_wiki::getScriptEnv('sub='.module_wiki::_SUB_NEW_ARTICLE.'&action='.module_wiki::_COLLABORATION_VIEW.'&collab_op='.module_wiki::_SAVE_INTERVENTION.'&id='.$article->getId().'&from='.$from));
			}
			else $todo->setLightAttribute('action_path', module_wiki::getScriptEnv('collab_op='.module_wiki::_SAVE_INTERVENTION.'&id='.$article->getId()));
			$todo->setLightAttribute('back_path', module_wiki::getScriptEnv('collab_op='.module_wiki::_SHOW_COLLABORATION.'&id='.$article->getId()));
			$todo->setLightAttribute('todo_id_globalobject_ref', $article->fields['id_globalobject']);//pour l'association du todo à l'article WCE
			$todo->setLightAttribute('todo_user_from', $_SESSION['dims']['userid']);
			$todo->setLightAttribute('todo_id_parent', $todo_id);
			$todo->display(module_wiki::getTemplatePath('/article/collaboration/form.tpl.php'));
		}
		break;

	case 'get_historic':
		require_once(module_wiki::getTemplatePath('/accueil/historic.tpl.php'));
		break;

	case 'get_tags':
		$mode = dims_load_securvalue('mode',dims_const::_DIMS_CHAR_INPUT,true,true,true);
		if ( !empty($mode) && $mode == 'complexe'){
			//dans ce cas le script javascript doit jouer avec des ids et les chaînes
			$tags = tag::getAllTags(true, '', ' WHERE id_workspace = '.$_SESSION['dims']['workspaceid']);
			$data = array();
			foreach($tags as $t){
				$data['availableTags'][] = $t->fields['tag'];
				$data['ids'][$t->fields['tag']] = $t->getId();
			}
			echo json_encode($data);
		}
		else echo json_encode(tag::getAllTags(false, '', ' WHERE id_workspace = '.$_SESSION['dims']['workspaceid']));
		break;
	case 'article_import_xml':
		$id = dims_load_securvalue('id_article',dims_const::_DIMS_NUM_INPUT,true,true,true);
		if ($id != '' && $id > 0){
			$article = new wce_article();
			$article->open($id);
			echo $skin->open_simplebloc($_DIMS['cste']['_ADD'],'100%','','');
			$article->display(module_wiki::getTemplatePath('/article/import_lang_article.tpl.php'));
			echo $skin->close_simplebloc();
		}
		break;
	case 'choose_article':
		$heading = module_wiki::getRootHeading();
		$input_id = dims_load_securvalue('input_id',dims_const::_DIMS_CHAR_INPUT,true,true,false);
		if (trim($input_id) == '')
			$input_id = 'input_id';
		?>
		<div style="padding: 5px;height: 500px;overflow-x: auto;">
			<ul>
				<?
				foreach($heading->getArticles() as $art){
					?>
					<li>
						<a class="select_article" href="javascript:void(0);" ref="<? echo $art->fields['id']; ?>">
							<? echo $art->fields['title']; ?>
						</a>
					</li>
					<?
				}
				?>
			</ul>
		</div>
		<input type="button" value="<? echo $_SESSION['cste']['_DIMS_CLOSE']; ?>" onclick="javascript:$('input#<? echo $input_id; ?>').val('').trigger('change'); dims_hidepopup();" style="margin:5px; float: right;font-size:12px;" />
		<script type="text/javascript">
			$(document).ready(function(){
				$('ul li a.select_article').click(function(){
					$('input#<? echo $input_id; ?>').val($(this).attr('ref')).trigger('change');
					dims_hidepopup();
				});
			});
		</script>
		<?
		break;
	case 'duplicate_ref':
		$id = dims_load_securvalue('id_ref',dims_const::_DIMS_NUM_INPUT,true,true);
		if ($id != '' && $id > 0){
			require_once(DIMS_APP_PATH . '/modules/wce/wiki/include/class_wce_reference.php');
			$ref = new wce_reference();
			$ref->open($id);
			$art = new wce_article();
			$art->open($ref->fields['id_article']);
			?>
			<img onclick="javascript:dims_hidepopup('dims_popup');" style="float:right;cursor: pointer;" src="<? echo module_wiki::getTemplateWebPath('/gfx/icone_suppression.png'); ?>" alt="<? echo $_SESSION['cste']['_DIMS_CLOSE']; ?>" title="<? echo $_SESSION['cste']['_DIMS_CLOSE']; ?>" />
			<ul style="margin:10px;">
			<?
			foreach($art->getListArticleLangVersion() as $lang){
				if ($lang->fields['id'] != $ref->fields['id_lang']){
					?>
					<li>
						<a href="<? echo module_wiki::getScriptEnv('sub='.module_wiki::_SUB_NEW_ARTICLE.'&action='.module_wiki::_PARAMETERS_VIEW.'&articleid='.$ref->fields['id_article'].'&params_op='.module_wiki::_DUPLICATE_REFERENCES."&id_ref=".$id."&id_lang=".$lang->fields['id']); ?>" style="color:#424242;">
							<img src="<? echo $lang->getFlag(); ?>" style="float:left;padding-right:5px;" alt="<? echo $lang->fields['ref']; ?>" title="<? echo $lang->fields['ref']; ?>" />
							<? echo $lang->fields['label']; ?>
						</a>
					</li>
					<?
				}
			}
			?>
			</ul>
			<?
		}
		break;
}
?>
