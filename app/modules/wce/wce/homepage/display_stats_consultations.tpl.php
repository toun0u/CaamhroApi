<?php

?>
<script src="/common/libs/RGraph/libraries/RGraph.common.core.js"></script>
<script src="/common/libs/RGraph/libraries/RGraph.common.effects.js"></script>
<script src="/common/libs/RGraph/libraries/RGraph.common.dynamic.js"></script>
<script src="/common/libs/RGraph/libraries/RGraph.bar.js"></script>
<script src="/common/libs/RGraph/libraries/RGraph.common.key.js"></script>
<script src="/common/modules/wce/wce/js/RGraph.line.js"></script>

<canvas style="position: relative;float:left;" id="consultations" width="750" height="350"><?php echo $_SESSION['cste']['SMILE_NO_CANVAS_SUPPORT']; ?></canvas>
<div class="cadre_legende">
	<div class="title_cadre"><? echo $_SESSION['cste']['_DIMS_LEGEND']; ?></div>
	<div class="legende_1">
		<img src="<? echo module_wce::getTemplateWebPath('gfx/ligne_verte.png'); ?>" />
		<span><? echo $_SESSION['cste']['_CONSULTED_PAGES']; ?></span>
	</div>
	<div class="legende_2">
		<img src="<? echo module_wce::getTemplateWebPath('gfx/ligne_bleue.png'); ?>" />
		<span><? echo $_SESSION['cste']['_NB_UNIQUE_VISITORS']; ?></span>
	</div>
</div>


<script type="text/javascript">
	function ajaxGraphConsult(){
		$.ajax({
			type: "POST",
			url: "admin.php",
			async: false,
			data: {
				'dims_op' : 'get_stats_consultations'
			},

			dataType: "json",
			success: function(data){
				refreshGraph(data);
			},

			error: function(data){
				$('canvas#consultations').text('Aucune donnée pour ces statistiques');
			}
		});
	}

	function refreshGraph(data){
		var bar = new RGraph.Bar('consultations',data['datas']);
		bar.Set('chart.labels', data['legende']);
				bar.Set('chart.gutter.left', 60);
		bar.Set('chart.tooltips', data['legende']);
		bar.Set('chart.colors', ['#9DC332','#71C6D3']);
		bar.Set('chart.text.size',8);
		bar.Set('chart.text.color','#686868');
		bar.Set('chart.axis.color','#686868');

		var line = new RGraph.Line('consultations',data['datas2']);
		var grad = line.context.createLinearGradient(0,0,0,150);
		grad.addColorStop(0,'rgba(255,243,237,0.5)');
		grad.addColorStop(1,'rgba(255,243,237,0.5)');
		line.Set('chart.filled', true);
		line.Set('chart.fillstyle', [grad]);
		line.Set('chart.tooltips', data['legende']);
		line.Set('chart.colors', ['#FA652B']);
		line.Set('chart.ymin',0);
		line.Set('chart.noaxes', true);

		var combo = new RGraph.CombinedChart(bar, line);
		combo.Draw();
	}
	$(document).ready(function(){
		ajaxGraphConsult();
	});
</script>
