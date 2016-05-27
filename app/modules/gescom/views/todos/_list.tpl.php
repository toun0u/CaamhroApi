<a class="add-todo" href="javascript:void(0);">Ajouter un todo</a>
<?php
$view = view::getInstance();
$nbTodos = $view->get('nbTodos');
$nbElem = $view->get('nbElem');
$id_go = $view->get('id_go');
if(empty($nbTodos)){
	?>
	<div>
		Aucun todo
	</div>
	<?php
}else{
	?>
	<ul class="todo-list"></ul>
	<?php if($nbElem <= 0 && $nbTodos > _DASHBOARD_NB_ELEMS_DISPLAY){ ?>
		<div class="txtcenter"><span class="btn see-more-todo" onclick="javascript:loadTodos(<?= _DASHBOARD_NB_ELEMS_DISPLAY; ?>);">Afficher plus de todos</span></div>
	<?php } ?>
	<!--<script type="text/javascript" src="/assets/javascripts/common/jquery.loadTemplate-1.4.3.min.js"></script>-->
	<script type="text/html" id="todo-template">
	<li data-id="id">
		<div>
			<input type="checkbox" value="1"  style="display:inline-block;" class="left" />&nbsp;
			<span data-content="content" style="display:inline-block;"></span>
			<span data-content="actionallowed" data-format="actionallowed" style="display:inline-block;" class="right"></span>
		</div>
		<div>
			<span data-content="lkObject" data-format="lkObject" data-alt="lkObject" data-link-wrap="lkObjectLk" />
			Par&nbsp;<span data-content="user" data-alt="user" data-link-wrap="userLk" />&nbsp;le&nbsp;<span data-content="date" />&nbsp;-&nbsp;
			<span data-content="echeance" />
		</div>
	</li>
	</script>
	<script type="text/javascript">
	if(libLoadTemplate === undefined){
		var libLoadTemplate = true;
		jQuery.ajax({
			dataType: 'script',
			cache: true,
			url: "/assets/javascripts/common/jquery.loadTemplate-1.4.3.min.js",
			async: false
		});
	}

	var startTodo = 0;
	function loadTodos(nb){
		if(startTodo < <?= $nbTodos; ?>){
			$.ajax({
				type: 'GET',
				url: '<?= Gescom\get_path(array('c'=>'todo','a'=>'load')); ?>',
				data: {
					'start' : startTodo,
					'nb' : nb,
					'id_go': <?= empty($id_go)?0:$view->get('id_go'); ?>
				},
				dataType: 'json',
				success: function(data) {
					startTodo += data.length;
					<?php if($nbElem <= 0 && $nbTodos > _DASHBOARD_NB_ELEMS_DISPLAY){ ?>
						var prev = $('.todo-list li').clone();
					<?php } ?>
					$(".todo-list").loadTemplate($("#todo-template"), data, {bindingOptions: {"ignoreUndefined": true, "ignoreNull": true, "overwriteCache": false}});
					<?php if($nbElem <= 0 && $nbTodos > _DASHBOARD_NB_ELEMS_DISPLAY){ ?>
						$('.todo-list').prepend(prev);
						if(startTodo >= <?= $nbTodos; ?>){
							$('.see-more-todo').remove();
						}
					<?php } ?>
				}
			});
		}

	}
	$(function(){
		$.addTemplateFormatter({
			actionallowed: function(v){
				if(v){
					return '<a class="icon-pencil edit-todo" alt="&Eacute;diter" title="&Eacute;diter">&nbsp;</a><a class="icon-remove del-todo" alt="Supprimer" title="Supprimer">&nbsp;</a>';
				}else{
					return "";
				}
			},
			lkObject: function(v){
				if(v != ''){
					return v+'&nbsp;-&nbsp;';
				}else{
					return v;
				}
			}
		});
		loadTodos(<?= $nbElem<=0?_DASHBOARD_NB_ELEMS_DISPLAY:$nbElem; ?>);
		$(".todo-list").delegate('li','mouseenter',function(){
			$(this).addClass('hover');
		}).delegate('li','mouseleave',function(){
			$(this).removeClass('hover');
		}).delegate('a.edit-todo','click',function(){
			var id = $(this).parents('li:first').attr('id');
			var id_popup = dims_openOverlayedPopup(500,400);
			dims_xmlhttprequest_todiv('<?= dims::getInstance()->getScriptEnv(); ?>', '<?= http_build_query(array('c'=>'todo','a'=>'edit','id_go'=>$id_go,'return'=>Gescom\get_path(array('c'=>$view->get('c'),'a'=>$view->get('a'))),'id'=>'')); ?>'+id+'&id_popup='+id_popup,'','p'+id_popup);
		}).delegate('a.del-todo','click',function(){
			var id = $(this).parents('li:first').attr('id');
			dims_confirmlink('<?= Gescom\get_path(array('c'=>'todo','a'=>'delete','id_go'=>$id_go,'return'=>Gescom\get_path(array('c'=>$view->get('c'),'a'=>$view->get('a'))),'id'=>'')); ?>'+id,'Êtes-vous sûr de vouloir supprimer ce todo ?');
		});
	});
	</script>
<?php
}
?>
<script type="text/javascript">
$(function(){
	$('a.add-todo').click(function(){
		var id_popup = dims_openOverlayedPopup(500,400);
		dims_xmlhttprequest_todiv('<?= dims::getInstance()->getScriptEnv(); ?>', '<?= http_build_query(array('c'=>'todo','a'=>'edit','id_go'=>$id_go,'return'=>Gescom\get_path(array('c'=>$view->get('c'),'a'=>$view->get('a'))),'id_popup'=>'')); ?>'+id_popup,'','p'+id_popup);
	});
});
</script>
