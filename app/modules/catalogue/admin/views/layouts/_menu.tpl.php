<div class="menu_principal" <?php if(!$_SESSION['catalogue']['display_menu']) echo 'style="display:none;"'; ?>>
	<div class="menu_principal">
		<?php if (dims_isadmin()): ?>
			<!--<a href="<?= dims::getInstance()->getScriptEnv().'?c=catalogues&a=index';?>" <?php if($this->get('selected_menu') == 'catalogues') echo 'class="selected"';?>>
				<img src="<?php echo $this->getTemplateWebPath("/gfx/home50x30.png"); ?>" />
				<div><?php echo dims_constant::getVal('CATA_HOME'); ?></div>
			</a>-->
			<a href="<?= dims::getInstance()->getScriptEnv().'?c=familles&a=index';?>" <?php if($this->get('selected_menu') == 'familles') echo 'class="selected"';?>>
				<img src="<?php echo $this->getTemplateWebPath("/gfx/cata50x30.png"); ?>" />
				<div><?php echo dims_constant::getVal('CATA_MANAGEMENT'); ?></div>
			</a>
			<a href="<?= dims::getInstance()->getScriptEnv().'?c=articles&a=edit';?>" <?php if($this->get('selected_menu') == 'articles') echo 'class="selected"';?>>
				<img src="<?php echo $this->getTemplateWebPath("/gfx/articles50x30.png"); ?>" />
				<div><?php echo dims_constant::getVal('CATA_PRODUCTS_LIST'); ?></div>
			</a>
		<?php endif ?>
		<a href="<?= dims::getInstance()->getScriptEnv().'?c=clients&a=index';?>" <?php if($this->get('selected_menu') == 'clients') echo 'class="selected"';?>>
			<img src="<?php echo $this->getTemplateWebPath("/gfx/clients50x30.png"); ?>" />
			<div><?php echo dims_constant::getVal('CATA_CLIENTS'); ?></div>
		</a>
		<?php if (dims_isadmin()): ?>
			<a href="<?= dims::getInstance()->getScriptEnv().'?c=promotions&a=index';?>" <?php if($this->get('selected_menu') == 'promotions') echo 'class="selected"';?>>
				<img src="<?php echo $this->getTemplateWebPath("/gfx/promos50x30.png"); ?>" />
				<div><?php echo dims_constant::getVal('CATA_PROMOTIONS_MANAGEMENT'); ?></div>
			</a>
		<?php endif ?>
		<a href="<?= dims::getInstance()->getScriptEnv().'?c=commandes&a=index';?>" <?php if($this->get('selected_menu') == 'commandes') echo 'class="selected"';?>>
			<img src="<?php echo $this->getTemplateWebPath("/gfx/commandes50x30.png"); ?>" />
			<div><?php echo dims_constant::getVal('CATA_WEB_ORDERS'); ?></div>
		</a>
		<a href="<?= dims::getInstance()->getScriptEnv().'?c=quotations&a=list';?>" <?php if($this->get('selected_menu') == 'quotations') echo 'class="selected"';?>>
			<img src="<?php echo $this->getTemplateWebPath("/gfx/commandes50x30.png"); ?>" />
			<div><?php echo dims_constant::getVal('QUOTATION'); ?></div>
		</a>
        <a href="<?= dims::getInstance()->getScriptEnv().'?dims_mainmenu=0&dims_desktop=block&dims_action=public&submenu=0&dims_moduleid=1&init_desktop=1&mode=default';?>" <?php if($this->get('selected_menu') == 'quotations') echo 'class="selected"';?>>
            <img src="/common/img/crm32.png"/>
            <div>CRM</div>
        </a>
		<a href="<?= dims::getInstance()->getScriptEnv().'?c=statistics&a=index';?>" <?php if($this->get('selected_menu') == 'statistics') echo 'class="selected"';?>>
			<img src="<?php echo $this->getTemplateWebPath("/gfx/clients50x30.png"); ?>" />
			<!-- <div><?php echo dims_constant::getVal('CATA_CLIENTS'); ?></div> -->
			<div>Statistiques</div>
		</a>

		<?php if (dims_isadmin()): ?>
			<a style="float: right;" href="<?= dims::getInstance()->getScriptEnv().'?c=params&a=identity';?>" <?php if($this->get('selected_menu') == 'params') echo 'class="selected"';?>>
				<img src="<?php echo $this->getTemplateWebPath("/gfx/params50x30.png"); ?>" />
				<div><?php echo dims_constant::getVal('CATA_GENERAL_PARAMETERS'); ?></div>
			</a>
			<!--<a style="float: right;" href="<?= dims::getInstance()->getScriptEnv().'?c=stats';?>" <?php if($this->get('selected_menu') == 'stats') echo 'class="selected"';?>>
				<img src="<?php echo $this->getTemplateWebPath("/gfx/stats50x30.png"); ?>" />
				<div><?php echo dims_constant::getVal('CATA_STATISTICS'); ?></div>
			</a>-->
			<a style="float: right;" href="<?= dims::getInstance()->getScriptEnv().'?c=objects&a=slide';?>" <?php if($this->get('selected_menu') == 'objects') echo 'class="selected"';?>>
				<img src="<?php echo $this->getTemplateWebPath("/gfx/objets_visuels50x30.png"); ?>" />
				<div><?php echo dims_constant::getVal('CATA_WEB_OBJECTS'); ?></div>
			</a>
		<?php endif ?>
	</div>
</div>

<div class="repli_depli">
	<a href="javascript:void(0);"><img src="<?php echo $this->getTemplateWebPath("/gfx/icon_".(($_SESSION['catalogue']['display_menu'])?'r':'d')."eplier.png"); ?>"></a>
</div>

<script type="text/javascript">
	$(document).ready(function(){
		$('div.repli_depli a').click(function(){
			$('div.menu_principal').slideToggle('slow',function(){
				if($(this).is(":visible")){
					$('div.repli_depli a img').attr("src","<?php echo $this->getTemplateWebPath("/gfx/icon_replier.png"); ?>");
				}else{
					$('div.repli_depli a img').attr("src","<?php echo $this->getTemplateWebPath("/gfx/icon_deplier.png"); ?>");
				}
				dims_xmlhttprequest('admin.php','dims_op=switch_display_menu');
			});
		});
	});
</script>
