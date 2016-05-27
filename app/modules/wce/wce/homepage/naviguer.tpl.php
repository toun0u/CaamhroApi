<div class="cadre_naviguer">
	<div class="picture_naviguer">
		<img src="img/ajax-loader.gif" style="margin: 50px 90px;" />
	</div>
	<div class="title_naviguer">
		<a href="<? echo module_wce::get_url(module_wce::_SUB_SITE); ?>">
			<? echo $_SESSION['cste']['_BROWSE_THE_SITE']; ?>
		</a>
	</div>
</div>
<script type="text/javascript">
	$(window).load(function(){
		$.ajax({
			type: "POST",
			url: "admin.php",
			async: true,
			data: {
				'dims_op' : 'load_thumbnail_site'
			},

			dataType: "text",
			success: function(data){
				if(data != ''){
					$("div.cadre_naviguer div.picture_naviguer img").attr('src',data).css({'width': '233px', 'margin': '0px'});
					//$("div.cadre_naviguer div.picture_naviguer").append('<img src="'+data+'" style="width:233px;" />');
				}
			},
			error: function(data){

			}
		});
	});
</script>