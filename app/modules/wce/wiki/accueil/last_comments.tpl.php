<script type="text/javascript" src="./common/js/views/todos/functions.js"></script>
<div class="cadre cadre_article_gauche cadre_fixed_height" id="todos">
	<h2>Derniers Commentaires</h2>
	<?php
	$todos = wce_article::getLastMessages($_SESSION['dims']['userid'], 10);
	if( ! empty($todos) ){
		foreach($todos as $todo){
			$go = $todo->getLightAttribute('gobject');

			$go->setLightAttribute('title_object', $todo->getLightAttribute('article_title'));
			$go->setLightAttribute('on_the_record', $_SESSION['cste']['ON_THE_RECORD_OF_THE_ARTICLE']);

			$go->setLightAttribute('todo_param', '&action='.module_wiki::_COLLABORATION_VIEW.'&todo_op='.dims_const::_SHOW_COLLABORATION.'#todo_'.$todo->getId());
			$go->setLightAttribute('home_param', '&action='.module_wiki::_ACTION_SHOW_ARTICLE);

			$go->setLightAttribute('additional_object_classes', 'lien_bleu');
			$go->setLightAttribute('link_to', dims::getInstance()->getScriptEnv().'?dims_mainmenu=content&op=wiki&sub='.module_wiki::_SUB_NEW_ARTICLE.'&articleid='.$go->fields['id_record'].'&wce_mode=render');

			$todo->setLightAttribute('from', 'desktop');
			$todo->setLightAttribute('gobject', $go);//on écrase avec le go mis à jour
			$todo->setLightAttribute('keep_context', '&dims_mainmenu=content&op=wiki&sub='.module_wiki::_SUB_NEW_ARTICLE.'&articleid='.$go->fields['id_record'].'&wce_mode=render');
			$todo->setLightAttribute('redirect_on', dims::getInstance()->getScriptEnv().'?dims_mainmenu=content&op=wiki&sub='.module_wiki::_SUB_HOMEPAGE);
			$todo->display(DIMS_APP_PATH.'/include/views/todos/todo.tpl.php');
		}
	}
	else{
		?>
		<div class="div_no_elem"><?= $_SESSION['cste']['NO_MESSAGE'];?></div>
		<?php
	}
	?>
</div>
