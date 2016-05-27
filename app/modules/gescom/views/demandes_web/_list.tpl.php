<?php
$view = view::getInstance();
$nbWebAsk = $view->get('nbWebAsks');
$nbElem = $view->get('nbElem');
if(empty($nbWebAsk)){
	?>
	<div>
		Aucune demande web en attente de traitement
	</div>
	<?php
}else{
	?>
	<ul class="web-ask-list"></ul>
	<?php if($nbElem <= 0 && $nbWebAsk > _DASHBOARD_NB_ELEMS_DISPLAY){ ?>
		<div class="txtcenter"><span class="btn see-more-web-ask" onclick="javascript:loadWebAsks(<?= _DASHBOARD_NB_ELEMS_DISPLAY; ?>);">Afficher plus de demandes web</span></div>
	<?php } ?>
	<script type="text/html" id="web-ask-template">
	<li data-id="id">
		<div>
			<span class="green">#</span><span data-content="label" data-format="label"></span>&nbsp;-&nbsp;
			<span data-content="date"></span>
		</div>
		<div>
			<span data-content="authorPicture" data-format="authorPicture"></span>
			<span data-content="user"></span>&nbsp;:&nbsp;
			<span data-content="emailLink" data-alt="emailLink" data-link-wrap="emailLink" data-format="emailLink"/>&nbsp;-&nbsp;
			<span data-content="phone"></span>
		</div>
		<div>
			<span data-content="web-ask-lk" data-format="lkButton" />
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
	var startWebAsk = 0;
	function loadWebAsks(nb){
		if(startWebAsk < <?= $nbWebAsk; ?>){
			$.ajax({
				type: 'GET',
				url: '<?= Gescom\get_path(array('c'=>'web_ask','a'=>'load')); ?>',
				data: {
					'start' : startWebAsk,
					'nb' : nb
				},
				dataType: 'json',
				success: function(data) {
					startWebAsk += data.length;
					<?php if($nbElem <= 0 && $nbWebAsk > _DASHBOARD_NB_ELEMS_DISPLAY){ ?>
						var prev = $('.web-ask-list li').clone();
					<?php } ?>
					$(".web-ask-list").loadTemplate($("#web-ask-template"), data, {bindingOptions: {"ignoreUndefined": true, "ignoreNull": true, "overwriteCache": false}});
					<?php if($nbElem <= 0 && $nbWebAsk > _DASHBOARD_NB_ELEMS_DISPLAY){ ?>
						$('.web-ask-list').prepend(prev);
						if(startWebAsk >= <?= $nbWebAsk; ?>){
							$('.see-more-web-ask').remove();
						}
					<?php } ?>
				}
			});
		}

	}
	$(function(){
		$.addTemplateFormatter({
			label: function(v){
				return "<b>"+v+"</b>";
			},
			/*emailLink: function(v){
				return 'mailto:'+v;
			},*/
			authorPicture: function(v){
				return '<i class="'+v+'">&nbsp;</i>';
			},
			lkButton:function(v){
				return '<a href="'+v+'" class="btn btn-small see-web-ask" data-tabable="true">Voir le détail</a>';
			}
		});
		loadWebAsks(<?= $nbElem<=0?_DASHBOARD_NB_ELEMS_DISPLAY:$nbElem; ?>);
		$(".web-ask-list").delegate('li','mouseenter',function(){
			$(this).addClass('hover');
		}).delegate('li','mouseleave',function(){
			$(this).removeClass('hover');
		});
	});
	</script>
<?php
}
