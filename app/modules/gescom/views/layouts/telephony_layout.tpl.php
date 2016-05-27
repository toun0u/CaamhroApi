<!-- Lejal Simon 
Aside hidden en format tablette et mobile 
mod_telephony pour cheval liberté
-->
<?php
$Model = new TelephonyModel();
$account = $Model->getSipAccounts2();
// on prend la première ligne relative à l'user pour le moment
$token=$Model->retrieveOrGenerateToken($account[0]['sipaccount']);
?>

<!-- Scripts -->


<!-- Info boxes -->
<div id="telephony-box-info" class="telephony-infobox"></div>
<div id="telephony-box-error" class="telephony-infobox"></div>
<div id='aside_token' class="telephony-token" data-account="<?= $account[0]['sipaccount'] ?>" data-token="<?= $token ?>"></div> 

<!-- Main div mod telephony -->
<div class="telephony-body">

	<!-- div appels entrants -->
	<div class="telephony-tile-container">
		<div class="telephony-summary-tile">
			<h4 style='color:green'> Appels en cours</h4>
			<div id="ongoing-calls" >Aucun appels en cours</div>
		</div>
	</div>

	<hr> 

	<!-- div de log api_keyyo  -->
	<div class="telephony-log-container">
		<span class="title"> 
			<a id="logcall" href="<?= Gescom\get_path(array('c'=>'telephony', 'a'=>'index')); ?>" data-tabable="true">Voir tous les appels</a>
			<div id="missed-calls" data-countmc="0"></div>
		</span>
	</div>

</div>

<!-- css -->
<style>
	a#takecall{
		display:block;
		width:150px;
		line-height:35px;
		text-align:center;
		border-radius: 7px;
		vertical-align:middle;
		background-color: green;
		color:white;
		text-decoration:none;
	}
</style>


