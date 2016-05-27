<?php
$view = view::getInstance();
$nbDocfiles = $view->get('nbDocfiles');
$nbElem = $view->get('nbElem');
if(empty($nbDocfiles)){
	?>
	<div>
		Aucun document
	</div>
	<?php
}else{
	?>
	<ul class="docfile-list"></ul>
	<?php if($nbElem <= 0 && $nbDocfiles > _DASHBOARD_NB_ELEMS_DISPLAY){ ?>
		<div class="txtcenter"><span class="btn see-more-docfile" onclick="javascript:loadDocfiles(<?= _DASHBOARD_NB_ELEMS_DISPLAY; ?>);">Afficher plus de documents</span></div>
	<?php } ?>
	<!--<script type="text/javascript" src="/assets/javascripts/common/jquery.loadTemplate-1.4.3.min.js"></script>-->
	<script type="text/html" id="docfile-template">
	<li data-id="id">
		<div style="display:inline-block;margin-right:10px;" data-content="thumbnail" data-format="thumbnail" data-alt="name">
		</div>
		<div style="display:inline-block;">
			<span data-content="name" data-alt="name" /><br />
			Par&nbsp;<span data-content="user" data-alt="user" data-link-wrap="userLk" />&nbsp;sur&nbsp;
			<span data-content="lkObject" data-alt="lkObject" data-link-wrap="lkObjectLk" />
		</div>
		<div style="display:inline-block;" class="right">
			<span data-content="date" />
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

	var startDocfile = 0;
	function loadDocfiles(nb){
		if(startDocfile < <?= $nbDocfiles; ?>){
			$.ajax({
				type: 'GET',
				url: '<?= Gescom\get_path(array('c'=>'document','a'=>'load')); ?>',
				data: {
					'start' : startDocfile,
					'nb' : nb
				},
				dataType: 'json',
				success: function(data) {
					startDocfile += data.length;
					<?php if($nbElem <= 0 && $nbDocfiles > _DASHBOARD_NB_ELEMS_DISPLAY){ ?>
						var prev = $('.docfile-list li').clone();
					<?php } ?>
					$(".docfile-list").loadTemplate($("#docfile-template"), data, {bindingOptions: {"ignoreUndefined": true, "ignoreNull": true, "overwriteCache": false}});
					<?php if($nbElem <= 0 && $nbDocfiles > _DASHBOARD_NB_ELEMS_DISPLAY){ ?>
						$('.docfile-list').prepend(prev);
						if(startDocfile >= <?= $nbDocfiles; ?>){
							$('.see-more-docfile').remove();
						}
					<?php } ?>
				}
			});
		}

	}
	$(function(){
		$.addTemplateFormatter({
			lkObject: function(v){
				if(v != ''){
					return v+'&nbsp;-&nbsp;';
				}else{
					return v;
				}
			},
			thumbnail: function(v){
				return '<img src="'+v+'" />';
			}
		});
		loadDocfiles(<?= $nbElem<=0?_DASHBOARD_NB_ELEMS_DISPLAY:$nbElem; ?>);
		$(".docfile-list").delegate('li','mouseenter',function(){
			$(this).addClass('hover');
		}).delegate('li','mouseleave',function(){
			$(this).removeClass('hover');
		});
	});
	</script>
<?php
}
