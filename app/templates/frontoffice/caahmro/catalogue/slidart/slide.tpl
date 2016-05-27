<div class="mod clearfix rounded-pic secondary-zone light-shadow mt1">
	<a href="/article/{$article.urlrewrite}.html" title="{$article.label}">
		<img src="{$article.image}" alt="{$article.label}" style="height: 100px; display: block; margin: 0 auto;">
	</a>

	<div class="pa1 line" style="min-height: 80px;">
		<div class="mod wordbnormal small">
			{$article.label}
		</div>
	</div>
	<div class="bigger">
		<a href="/article/{$article.urlrewrite}.html" title="{$article.label}" class="left inbl blue-area w20 txtcenter">
			<i class="icon-search"></i>
		</a>
		<div class="left inbl w60 txtcenter">
			{$article.prix} &euro; HT
		</div>
		<a href="javascript:void(0);" onclick="javascript:addToCart({$article.id});" class="cart-add left inbl orange-area w20 txtcenter ml1">
			<i class="icon-cart"></i>
		</a>
	</div>
</div>
