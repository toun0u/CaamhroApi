<?php
$view = view::getInstance();
$menu = $view->get('menu');
$c = $view->get('c');
?>
<div class="menu-wrapper second-nav mb2">
	<a href="#dashmenu" class="menu-link" id="brodash"> Sous menu <span class="icon-menu right" aria-hidden="true"></span></a>
	<nav id="dashmenu" role="navigation">
		<div class="menu">
			<ul  class="menu">
				<?php
				foreach($menu as $k => $m){
					?>
					<li class="<?= ($k==$c)?"current-menu-item":"";?><?= (count($m['sub-menu']))?" has-subnav":"";?>">
						<a href="<?= (!empty($k)?Gescom\get_path(array('c'=>$k,'a'=>'index')):'#'); ?>"><?= $m['label']; ?></a>
						<?php if(count($m['sub-menu'])){ ?>
							<ul class="sub-menu">
								<?php foreach($m['sub-menu'] as $k2 => $m2){ ?>
									<li><a href="<?= $k2; ?>"><?= $m2; ?></a></li>
								<?php } ?>
							</ul>
						<?php } ?>
					</li>
					<?php
				}
				?>
			</ul>
		</div>
	</nav>
</div>
<br>
