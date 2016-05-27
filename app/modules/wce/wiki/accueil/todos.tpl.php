<script type="text/javascript" src="./common/js/views/todos/functions.js"></script>
<div class="cadre cadre_article_droite cadre_fixed_height" id="todos">
	<h2>TODOs</h2>
	<?php

	$todos = todo::getLastTasks($_SESSION['dims']['userid'], null, _WCE_OBJECT_ARTICLE, $_SESSION['dims']['moduletypeid']);
	if( ! empty($todos) ){
		foreach($todos as $todo){

			$todo->setLightAttribute('from', 'desktop');
			$go = $todo->getLightAttribute('gobject');
			$article = new wce_article();
			$article->open($go->fields['id_record']);

			$go->setLightAttribute('title_object', $article->fields['title']);
			$go->setLightAttribute('on_the_record', $_SESSION['cste']['ON_THE_RECORD_OF_THE_ARTICLE']);

			$go->setLightAttribute('todo_param', '&action='.module_wiki::_COLLABORATION_VIEW.'&todo_op='.dims_const::_SHOW_COLLABORATION.'#todo_'.$todo->getId());
			$go->setLightAttribute('home_param', '&action='.module_wiki::_ACTION_SHOW_ARTICLE);

			$go->setLightAttribute('additional_object_classes', 'lien_bleu');
			$go->setLightAttribute('link_to', dims::getInstance()->getScriptEnv().'?dims_mainmenu=content&op=wiki&sub='.module_wiki::_SUB_NEW_ARTICLE.'&articleid='.$article->getId().'&wce_mode=render');

			$todo->setLightAttribute('gobject', $go);//on écrase avec le go mis à jour
			$todo->setLightAttribute('keep_context', '&dims_mainmenu=content&op=wiki&sub='.module_wiki::_SUB_NEW_ARTICLE.'&articleid='.$article->getId().'&wce_mode=render&action='.module_wiki::_COLLABORATION_VIEW);
			$todo->setLightAttribute('redirect_on', dims::getInstance()->getScriptEnv().'?dims_mainmenu=content&op=wiki&sub='.module_wiki::_SUB_HOMEPAGE);
			$todo->display(DIMS_APP_PATH.'/include/views/todos/todo.tpl.php');
		}
	}
	else{
		?>
		<div class="div_no_elem"><?= $_SESSION['cste']['NO_TASK_FOR_THE_MOMENT'];?></div>
		<?php
	}
	?>
</div>
