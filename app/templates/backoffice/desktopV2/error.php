<?php
header('Status: 503 Service Unavailable', false, 503);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <title>Service non disponible</title>
    <link href="styles.css" rel="stylesheet" type="text/css" />
	<meta name="Content-Language" content="en-FR">
	<title>blabla</title>
	<meta name="description" content="Permis &agrave; points : service non disponible"/>
	<meta name="keywords" content="Permis &agrave; points : service non disponible"/>

<style>
BODY{font-size: 62.5%; font-family: Arial, Helvetica, sans-serif; letter-spacing: 0.1em;}
#page{width: 90em;}
#center{margin: 5em 0 0 5em;}
h1{font-size: 1.6em; text-transform: uppercase; margin: 0; padding: 0;}
h2{color: #666666; font-size: 1.4em; border-bottom: 1px solid #666666; margin: 2em 0 0 0;}
p{margin: 0.5em 0; padding: 0; font-size: 1.2em;}
li{margin: 0.5em 0; list-style: square; font-size: 1.2em;}
#bookmark{text-align: right; font-size: 1.2em; margin-top: 50px;}
</style>

</head>
<body>
<script type="text/javascript">
	var date = new Date();
	var displayDate = date.getDate() + '/' + (date.getMonth()+1) + '/' + date.getFullYear();
	var displayTime = date.getHours() + ':' + date.getMinutes() + ':' + date.getSeconds();
	var displayUrl = location.href;
</script>
<div id="page">
	<div id="center">
    <br><br>

		<h1>Erreur 503 : service non disponible.</h1>
		<br />
        <ul>
		<li>Le site est momentanément indisponible.</li>
        <li><b>Nous nous excusons pour ce désagréement</b></li>
        </ul>

		<p>Informations conplémentaires :</p>
		<ul>
			<li>Date : <script type="text/javascript">document.write(displayDate);</script></li>
			<li>Heure : <script type="text/javascript">document.write(displayTime);</script></li>
			<li>Page demand&eacute;e : <script type="text/javascript">document.write(displayUrl);</script></li>
			<li>
				<script type="text/javascript">if (document.referrer&&document.referrer!="") document.write('Page pr&eacute;c&eacute;dente : '+document.referrer+'</li><li>');</script>
				Type d'erreur : 503
			</li>
			<li>
			<?

			// test des erreurs possibles
			if ($dims->error==2) echo "Database connection error";
			elseif ($dims->error==3) echo "No backoffice workspace availabled";
			else echo "Internal error";
			?></li>
		</ul>
	</div>
</div>
</body>
</html>