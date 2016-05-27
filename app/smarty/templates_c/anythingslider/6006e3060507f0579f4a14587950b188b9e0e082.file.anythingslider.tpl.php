<?php /* Smarty version Smarty-3.1.19, created on 2016-01-05 09:07:02
         compiled from "/var/www/caahmro-mobile/app/templates/objects/slideshows/anythingslider.tpl" */ ?>
<?php /*%%SmartyHeaderCode:141747710568b79a6a2f5e7-42395578%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '6006e3060507f0579f4a14587950b188b9e0e082' => 
    array (
      0 => '/var/www/caahmro-mobile/app/templates/objects/slideshows/anythingslider.tpl',
      1 => 1451925996,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '141747710568b79a6a2f5e7-42395578',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'slideshow' => 0,
    'slide' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_568b79a6a62ce7_22979176',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_568b79a6a62ce7_22979176')) {function content_568b79a6a62ce7_22979176($_smarty_tpl) {?><link href="/common/templates/objects/slideshows/anythingslider.css" rel="stylesheet" type="text/css" />
<!--<link href="/common/templates/objects/slideshows/theme.css" rel="stylesheet" type="text/css" />-->
<script type="text/javascript" language="javascript" src="/common/templates/objects/slideshows/jquery.anythingslider.min.js"></script>

<div id="layerslider">
	<?php  $_smarty_tpl->tpl_vars['slide'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['slide']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['slideshow']->value['slide']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['slide']->key => $_smarty_tpl->tpl_vars['slide']->value) {
$_smarty_tpl->tpl_vars['slide']->_loop = true;
?>
	<div class="ls-layer" rel="slidedelay: 3000" style="width:100%;">
		<div class="line" style="width:100%">
			<div style="float:left">
				<img src="<?php echo $_smarty_tpl->tpl_vars['slide']->value['filePath'];?>
" alt="Icone slider"  rel="durationin: 5800; easingin: easeOutQuad" class="ls-s1">
			</div>
		</div>
	</div>
	<?php } ?>
</div>
<script type="text/javascript">

$(function() {
	$('#slider<?php echo $_smarty_tpl->tpl_vars['slideshow']->value['id'];?>
')
		.anythingSlider({
			'buildArrows' : false,
			'hashTags' : false,
			'autoPlayLocked' : true,
			'resumeDelay' : 0,
			'delay' : 8500,
			})
		.find('.panel')
		.find('div[class*=caption]').css({position:'absolute'}).end();
});

</script>
<?php }} ?>
