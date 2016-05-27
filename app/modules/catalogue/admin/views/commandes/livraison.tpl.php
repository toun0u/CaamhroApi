<?php
$view = view::getInstance();
$cde = $view->get('commande');
?>
<div style="width:20%;float:left;line-height:15px">
	<div>
		<?= dims_constant::getVal('_DELIVERY_ADDRESS'); ?> :
	</div>
	<div style="font-weight:bold;margin-top:5px;">
		<?php
		$country = new country();
		$country->open($cde->fields['cli_liv_id_pays']);
		$pays = (isset($country->fields['name']))?" (".$country->fields['name'].")":"";
		$address = $cde->fields['cli_liv_adr1'].", ".$cde->fields['cli_liv_cp']." ".$cde->fields['cli_liv_ville'].", ".$pays;
		?>
		<?= $cde->fields['cli_liv_adr1']; ?><br />
		<?= $cde->fields['cli_liv_cp']." ".$cde->fields['cli_liv_ville']." ".$pays; ?>
	</div>
</div>
<div style="width:79%;float:right;margin-bottom:10px;">
	<script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?sensor=false"></script>
	<script type="text/javascript" src="http://www.google.com/jsapi"></script>
	<div id="map_livraison" style="height:500px;"></div>
	<script type="text/javascript">
		var originAdd = '<?= $view->get('origine'); ?>';
		function initialize(){
			var geocoder = new google.maps.Geocoder();

			var directionsDisplay = new google.maps.DirectionsRenderer();
			var LatLngOr = null;
			if(originAdd != '' && geocoder){
				geocoder.geocode({'address': originAdd}, function (results, status) {
					if (status == google.maps.GeocoderStatus.OK) {
						LatLngOr = results[0].geometry.location;
					}else if(google.loader.ClientLocation){
						LatLngOr = new google.maps.LatLng(google.loader.ClientLocation.latitude,google.loader.ClientLocation.longitude);
					}
				});
			}else if(google.loader.ClientLocation){
				LatLngOr = new google.maps.LatLng(google.loader.ClientLocation.latitude,google.loader.ClientLocation.longitude);
			}

			var latlng = new google.maps.LatLng(<?= $view->get('_DEFAULT_LAT'); ?>, <?= $view->get('_DEFAULT_LON'); ?>);
			var latlngX = null;
			var myOptions = {
				zoom: <?= $view->get('_DEFAULT_ZOOM'); ?>,
				center: latlng,
				mapTypeId: google.maps.MapTypeId.ROADMAP,
				scrollwheel: true
			};
			map = new google.maps.Map(document.getElementById("map_livraison"),myOptions);
			directionsDisplay.setMap(map);

			if (geocoder) {
				geocoder.geocode({'address': "<?= $address; ?>"}, function (results, status) {
					if (status == google.maps.GeocoderStatus.OK) {
						latlngX = results[0].geometry.location;
						map.setCenter(latlngX);
						map.setZoom(15);
						if(LatLngOr != null){
							var directionsService = new google.maps.DirectionsService();
							var request = {
								origin: LatLngOr,
								destination: latlngX,
								travelMode: google.maps.TravelMode["DRIVING"]
							};
							directionsService.route(request, function(response, status) {
								if (status == google.maps.DirectionsStatus.OK) {
									directionsDisplay.setDirections(response);
								}
							});
						}else{
							var marker = new google.maps.Marker({
								position: latlngX,
								map: map/*,
								icon: "<?= $view->getTemplateWebPath('gfx/edit20.png'); ?>"*/
							});
						}
					}
				});

			}
		}
		$(document).ready(function(){
			google.load("maps", "3.x", {other_params: "sensor=false", callback:initialize});
		});
	</script>
</div>
