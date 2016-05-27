<?php
$view = view::getInstance();
$adr = $view->get('adr');
?>
<div style="width:20%;float:left;line-height:15px">
    <div>
        <?= dims_constant::getVal('_DELIVERY_ADDRESS'); ?> :
    </div>
    <div style="font-weight:bold;margin-top:5px;">
        <?php
        $city = new city();
        $city->open($adr->fields['id_city']);
        $country = new country();
        $country->open($adr->fields['id_country']);
        $address = $adr->fields['adr1'].(($adr->fields['adr2']!='')?" ".$adr->fields['adr2']:"").(($adr->fields['adr3']!='')?" ".$adr->fields['adr3']:"").", ".$adr->fields['cp'].((!$city->isNew())?" ".$city->getlabel():"").((!$country->isNew())?", ".$country->getLabel()."":"");
        ?>
        <?= $adr->fields['adr1'].(($adr->fields['adr2']!='')?"<br />".$adr->fields['adr2']:"").(($adr->fields['adr3']!='')?"<br />".$adr->fields['adr3']:""); ?><br />
        <?= $adr->fields['cp'].((!$city->isNew())?" ".$city->getlabel():"").((!$country->isNew())?" (".$country->getLabel().")":""); ?>
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