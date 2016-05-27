<?php

$workspace = new workspace();
$workspace->open($client->fields['id_workspace']);

$website_name = $workspace->fields['label'];
$expeditor = $workspace->fields['email_noreply'];

$message = "
<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.0 TRANSITIONAL//EN\">
<HTML>
<HEAD>
	<META HTTP-EQUIV=\"Content-Type\" CONTENT=\"text/html; CHARSET=UTF-8\">
	<META NAME=\"GENERATOR\" CONTENT=\"GtkHTML/3.24.1.1\">
	<STYLE>
		* { font-size: 8pt; }
		BODY {
			font-family:Tahoma, Verdana, Arial, Helvetica, sans-serif;
			font-weight:none;
			margin:0px;
			padding:0px;
		}
		TABLE.bloc {
			border: 1px solid #ccc;
		}
		TABLE.bloc TR.ligne1 { background-color:#fff; }
		TABLE.bloc TR.ligne2 { background-color:#eee; }
		TABLE.bloc TH {
			background-color:#eeeeee;
			font-weight:bold;
			text-decoration:underline;
		}
		TR.entete { background-color:#eee; }
		TR.entete TD {
			font-size: 14pt;
			border-bottom: 1px solid #888;
		}
		TD.info { font-size: 12pt; }
		TD.info A { font-size: 12pt; font-weight: bold; }
		TD.info P { font-size: 12pt; border: 2px dashed #888; background-color: #eee; }
	</STYLE>
</HEAD>

<BODY>

Bonjour ".trim($representative->get('firstname').' '.$representative->get('lastname')).",<br><br>

Le client \"".$client->getName()."\" (".$client->getCode().") souhaite commander des quantités plus importantes de ces produits :";

foreach ($articles as $detail) {
	if ($detail['qte'] > $detail['stock']) {
		$message .= "<br>- ".$detail['ref']." (".$detail['label'].") : ".$detail['qte']." souhaités pour ".$detail['stock']." en stock";
		if ($detail['end_of_life']) {
			$message .= ' - Derniers en stock, pas de réappro possible';
		}
	}
}

$message .= "
<br><br>L'équipe Caahmro
</BODY>
</HTML>";
