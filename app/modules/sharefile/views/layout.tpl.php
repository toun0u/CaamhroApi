<?php if(!$this->get('home')): ?>
	<div style="text-align: left; float: left; clear: both;">
		<a href="<?= dims_urlencode($this->get('urlbase')->addParams(array('op' => 'sharefile', 'action' => 'main_menu'))); ?>">
			<img src="/common/modules/sharefile/img/puce_retour_accueil.png" style="border:0px;">
		</a>
	</div>
<?php endif; ?>
<div id="sharefile">
	<link type="text/css" rel="stylesheet" href="<?= $this->getTemplateWebPath('/styles.css'); ?>" media="screen" title="styles" />
	<?php if($this->isYieldable('header')): ?>
		<div class="header">
			<?php $this->yields('header'); ?>
		</div>
		<div class="separateur">
			<?php $this->yields('separateur'); ?>
		</div>
	<?php endif; ?>
	<div class="content">
		<?php $this->yields('default'); ?>
	</div>
</div>
