<ul>
	<li <?= ($isIn = in_array($this->get('a'), array('slide','showslid','editslid')))?'class="selected"':''; ?>>
		<a href="<?= get_path('objects', 'slide'); ?>">
			<?= dims_constant::getVal('_SLIDESHOW'); ?>
			<img src="<?php echo $this->getTemplateWebPath("/gfx/droite16_".(($isIn)?'':'n')."s.png"); ?>" />
		</a>
	</li>
	<li <?= ($isIn = in_array($this->get('a'), array('tags','showtag','edittags')))?'class="selected"':''; ?>>
		<a href="<?= get_path('objects', 'tags'); ?>">
			<?= dims_constant::getVal('_TAG_CLOUD'); ?>
			<img src="<?php echo $this->getTemplateWebPath("/gfx/droite16_".(($isIn)?'':'n')."s.png"); ?>" />
		</a>
	</li>
	<li <?= ($isIn = in_array($this->get('a'), array('slidart','showart','editart')))?'class="selected"':''; ?>>
		<a href="<?= get_path('objects', 'slidart'); ?>">
			<?= dims_constant::getVal('_SLIDESHOW_ARTICLES'); ?>
			<img src="<?php echo $this->getTemplateWebPath("/gfx/droite16_".(($isIn)?'':'n')."s.png"); ?>" />
		</a>
	</li>
	<li <?= ($isIn = in_array($this->get('a'), array('families_selections')))?'class="selected"':''; ?>>
		<a href="<?= get_path('objects', 'families_selections'); ?>">
			<?= dims_constant::getVal('CATA_FAMILIES_SELECTIONS'); ?>
			<img src="<?php echo $this->getTemplateWebPath("/gfx/droite16_".(($isIn)?'':'n')."s.png"); ?>" />
		</a>
	</li>
</ul>
