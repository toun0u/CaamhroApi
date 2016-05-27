<h2><?= $_SESSION['cste']['_TAG_GEOGRAPHICAL']." : ".$this->get('tag'); ?></h2>

<label for="search_city"><?=$_SESSION['cste']['_SEARCH_FOR_CITIES']; ?></label>
<input type="text" class="search-city" id="search_city" placeholder="cp-cp <?= $_SESSION['cste']['_DIMS_OR']; ?> insee-insee <?= $_SESSION['cste']['_DIMS_OR']; ?> <?= $_SESSION['cste']['_DIMS_LABEL_CITY']; ?>" />
<div class="city-res-search"></div>

<h2><?= $_SESSION['cste']['_RELATED_CITIES']; ?></h2>
<div class="city-linked">
	<?php
	$sel = "SELECT 		c.*
			FROM 		".city::TABLE_NAME." c
			INNER JOIN 	".tag_globalobject::TABLE_NAME." t 
			ON 			t.id_globalobject = c.id_globalobject
			WHERE 		t.id_tag = :id
			ORDER BY 	c.label";
	$params = array(
		':id'=>array('type'=>PDO::PARAM_INT,'value'=>$this->get('id')),
	);
	$db = dims::getInstance()->getDb();
	$res = $db->query($sel,$params);
	while($r = $db->fetchrow($res)){
		$c = new city();
		$c->openFromResultSet($r);
		?>
		<div class="block-city" id="block_city_<?= $c->get('id'); ?>" dims-data-value="<?= $c->get('id'); ?>">
			<?= $c->get('label').(($c->get('cp')!='' && $c->get('cp')>0)?" (".$c->get('cp').")":(($c->get('insee')!='' && $c->get('insee') > 0)?" (".substr($c->get('insee'), 0,2).")":"")); ?>
			<a href="javascript:void(0);" class="del"><img src="<?= _DESKTOP_TPL_PATH; ?>/gfx/common/delete16.png" /></a>
		</div>
		<?php
	}
	?>
</div>
<a href="<?= dims::getInstance()->getScriptEnv(); ?>?submenu=1&mode=admin&o=geo&action=show" style="float: right;margin-right: 20px;margin-top: 10px;"><?= $_SESSION['cste']['_DIMS_BACK']; ?></a>
<script type="text/javascript">
var temp_search = null;
function searchCityTag(val){
	$.ajax({
		type: "POST",
        url: "<?= dims::getInstance()->getScriptEnv(); ?>",
        data: {
            'submenu': '1',
            'mode': 'admin',
            'o': 'geo',
            'action' : 'search_city',
            'id': '<?= $this->get('id'); ?>',
            'val': val,
        },
        dataType: "html",
        success: function(data){
			$('div.city-res-search').html(data);
        },
	});
	clearTimeout(temp_search);
	temp_search = null;
}
$(document).ready(function(){
	$('input.search-city').keyup(function(event){
		var keycode = event.keyCode;
		var value = $(this).val();
		if(value != ''){
			clearTimeout(temp_search);
			temp_search = setTimeout('searchCityTag("'+value+'")' , 2000);
		}

		if(keycode == 13){ // enter
			event.preventDefault();
			searchCityTag($(this).val());
		}
	}).keydown(function(event){
		var keycode = event.keyCode;
		if(keycode == 13){ // enter
			event.preventDefault();
		}
	});
	$('.city-res-search').delegate('.block-city a.add','click',function(){
		var elem = $(this),
			div = $(this).parents('div:first'),
			id = div.attr('dims-data-value');
		$.ajax({
			type: "POST",
	        url: "<?= dims::getInstance()->getScriptEnv(); ?>",
	        data: {
	            'submenu': '1',
	            'mode': 'admin',
	            'o': 'geo',
	            'action' : 'add_city',
	            'id': '<?= $this->get('id'); ?>',
	            'idc':id,
	        },
	        dataType: "html",
	        success: function(data){
	        	elem.removeClass('add').addClass('del');
	        	$('img',elem).attr('src','<?= _DESKTOP_TPL_PATH; ?>/gfx/common/delete16.png');
				$('div.city-linked').append(div);
	        },
		});
	});
	$('.city-linked').delegate('.block-city a.del','click',function(){
		var elem = $(this),
			div = $(this).parents('div:first'),
			id = div.attr('dims-data-value');
		$.ajax({
			type: "POST",
	        url: "<?= dims::getInstance()->getScriptEnv(); ?>",
	        data: {
	            'submenu': '1',
	            'mode': 'admin',
	            'o': 'geo',
	            'action' : 'del_city',
	            'id': '<?= $this->get('id'); ?>',
	            'idc':id,
	        },
	        dataType: "html",
	        success: function(data){
	        	div.remove();
	        	if($('input.search-city').val() != ''){
	        		searchCityTag($('input.search-city').val());
	        	}
	        },
		});
	});
});
</script>
