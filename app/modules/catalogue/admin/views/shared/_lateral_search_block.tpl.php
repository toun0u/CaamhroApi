<?php
$view = view::getInstance();

$form = new Dims\form(array(
	'name'				=> 'search_articles',
	'action'			=> $view->get('path_lateral_search'),
	'include_actions'	=> false
	));

$query = $view->get('keywords');
$default_search = $view->get('default_search');
?>
<div class="search_block">
	<div class="input_container">
		<?php
			echo $form->get_header();
			echo $form->text_field(array(
				'name' 		=> 'keywords',
				'id'		=> 'lat_keywords',
				'value' 	=> (!empty($query)) ? $query : $default_search,
				'classes' 	=> (!empty($query)) ? 'search_input': 'default search_input',
			));
			echo $form->hidden_field(array('name' => 'x'));
			echo $form->hidden_field(array('name' => 'y'));
			$img = ( ! empty($query) ) ? 'vider16.png' : 'loupe16.png';
			if(empty($query)){
				?>
				<input type="image" src="<?= $view->getTemplateWebPath('gfx/'.$img);?>" value=""/>
				<?php
			}
			else{
				?>
				<a href="<?= $view->get('path_lateral_search').'&filter_init=1' ; ?>"><img src="<?= $view->getTemplateWebPath('gfx/'.$img);?>" /></a>
				<?php
			}
			?>
		<?= $form->close_form(); ?>
	</div>
</div>

<script type="text/javascript">
	$('document').ready(function(){
		$('#lat_keywords').focus(function(){
			//focus on
			if($(this).val() == '<?= addslashes($default_search); ?>'){
				$(this).val('');
				$(this).removeClass('default');
			}
		});

		$('#lat_keywords').focusout(function(){
			//focus out
			if($(this).val() == ''){
				$(this).val('<?= addslashes($default_search); ?>');
				$(this).addClass('default');
			}
		});
	});
</script>