<?php
$keep_context = $this->getLightAttribute('keep_context');
if( ! isset($keep_context)) $keep_context = '';
?>
<script type="text/javascript" src="/js/views/todos/functions.js"></script>
<div class="content_todos">
	<a href="<?= dims::getInstance()->getScriptEnv().'?todo_op='.dims_const::_EDIT_INTERVENTION.$keep_context; ?>" class="link_img action_view">
		<img src="./common/img/views/todos/gfx/ajouter16.png" /> <span><?php echo $_SESSION['cste']['COMMENT'];?></span>
	</a>
	<div class="messages_list" id="todo_list">
		<?php
		$todos = $this->getLightAttribute('todo_list');
		if( ! empty($todos) ){
			foreach($todos as $todo){
				$todo->setLightAttribute('keep_context', $keep_context);
				$todo->display(DIMS_APP_PATH.'/include/views/todos/todo.tpl.php');
			}
		}
		else{
			?>
			<div class="div_no_elem"><?= $_SESSION['cste']['NO_COMMENT_FOR_THE_MOMENT'];?></div>
			<?php
		}
		?>
	</div>
</div>
