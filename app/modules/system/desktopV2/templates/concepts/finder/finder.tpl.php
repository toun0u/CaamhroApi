<link type="text/css" rel="stylesheet" href="<? echo _DESKTOP_TPL_PATH.'/concepts/finder/gedfinder.css?v=2'; ?>"/>
<div id="recurs_ged">
	<div class="blocged_interne">
		<?php $browser->display(); ?>
	</div>
</div>
<div style="clear: both;"></div>
<script type="text/javascript">
	$(document).ready(function(){
		var count = $('div.blocged_interne').find('div.browser_column').length;
		var count2 = $('div.blocged_interne').find('div.browser_leaf').length;
		if (count2 > 0){
			$('div#recurs_ged').css('position', 'relative').prepend('<div style="overflow-x:auto;overflow-y:hidden;height:100%;position:absolute;top:0px;left:0px;width:'+($('div#recurs_ged').innerWidth()-370)+'px;"></div>');
			$('div.blocged_interne').after($('div.blocged_interne div.browser_leaf').css({'position' : 'absolute','top': '0px', 'right': '0px'}));
			$('div#recurs_ged div:first').append($('div.blocged_interne').append('<p style="clear: both; height: 1px;"></p>'));
			if ($('div.blocged_interne').innerWidth() > $('div#recurs_ged div:first').innerWidth())
				$('div.blocged_interne div.browser_column:last').css({'border-right': '0px', 'width': '200px'});
			else
				$('div.blocged_interne div.browser_column:last').css({'border-right': '2px solid #D6D6D6', 'width': '200px'});
		}else
			$('div.blocged_interne').append('<p style="clear: both; height: 1px;"></p>');
		$('div.blocged_interne').width(count*200 + 10);
		$('div#recurs_ged').scrollLeft(count*200 + 20 );
		if ($('div.blocged_interne').innerWidth() > $('div#recurs_ged').innerWidth())
			$('div.blocged_interne div.browser_column:last').css({'border': '0px', 'width': '200px'});

		if ($('div#recurs_ged').innerWidth()-$('div.blocged_interne').innerWidth()+7 < 370)
			$('div.browser_leaf').css({'width': '370px'});
		else
			$('div.browser_leaf').css({'width': ($('div#recurs_ged').innerWidth()-$('div.blocged_interne').innerWidth()+7)+'px'});

		/*$('div.blocged_interne').width(count*200 + count2*370 + 20);
		$('div#recurs_ged').scrollLeft(count*200 + count2*370 + 20 );*/

		$('div.blocged_interne div.browser_column').each(function(){
			var id = $(this).attr('id');
			var sel_item = $('div.blocged_interne div#'+id+' li.selected');
			$(this).scrollTop((sel_item.attr('rel')-1) *21);
		});

		// permet de charger le 1re folder si aucun n'est sélectionné
		/*if ($("div#col_0 li.selected",$(this)).length == 0)
			document.location.href = $("div#col_0 li:first td.icone a",$(this)).attr('href');*/
		$(window).resize(function(){
			if (count2 > 0){
				$('div.browser_leaf').css({'width': '370px'});
				$('div#recurs_ged div:first').css('width', ($('div#recurs_ged').innerWidth()-370)+'px');
				if ($('div.blocged_interne').innerWidth() > $('div#recurs_ged div:first').innerWidth())
					$('div.blocged_interne div.browser_column:last').css({'border-right': '0px', 'width': '200px'});
				else{
					$('div.blocged_interne div.browser_column:last').css({'border-right': '2px solid #D6D6D6', 'width': '200px'});
					if ($('div#recurs_ged').innerWidth()-$('div.blocged_interne').innerWidth()+7 < 370)
						$('div.browser_leaf').css({'width': '370px'});
					else
						$('div.browser_leaf').css({'width': ($('div#recurs_ged').innerWidth()-$('div.blocged_interne').innerWidth()+7)+'px'});
				}
			}else{
				$('div.blocged_interne').width(count*200 + 10);
				if ($('div.blocged_interne').innerWidth() > $('div#recurs_ged').innerWidth())
					$('div.blocged_interne div.browser_column:last').css({'border-right': '0px', 'width': '200px'});
				else
					$('div.blocged_interne div.browser_column:last').css({'border-right': '2px solid #D6D6D6', 'width': '200px'});
			}
		});
	});
</script>
