<?php
ob_end_clean();

$rs = $db->query($_SESSION['business']['tiers_search'], $_SESSION['business']['tiers_searchparams']); // Gab : /!\ ATTENTION

switch($format)
{
	case 'email':

		header("Cache-control: private");
		header("Content-type: message/rfc822");
		header("Content-Disposition: attachment; filename=export-email.eml");
		header("Pragma: public");

		$eml = "To: contact@promotech.fr\r\n";
		while ($fields = $db->fetchrow($rs))
		{
			if ($fields['mel'] != '' && !is_null($fields['mel'])) $eml .= "Bcc: {$fields['intitule']} <{$fields['mel']}>;\r\n";
			//$eml .= "Bcc =?iso-8859-1?Q?St=E9phane_Escaich_=3Csescaich@netlorconcept.com=3E?=";
			//if ($fields['mel'] != '' && !is_null($fields['mel'])) $eml .= "Bcc: {$fields['mel']}\r\n";
		}

		echo "{$eml}Subject: sujet du message
X-Priority: 3
X-MSMail-Priority: Normal
X-Unsent: 1
X-MimeOLE: Produced By Microsoft MimeOLE V6.00.2900.2180
X-Accept-Language: fr, en
MIME-Version: 1.0
Content-Type: text/plain; charset=ISO-8859-1; format=flowed
Content-Transfer-Encoding: 7bit
";

	break;

	case 'csv':

		header("Cache-control: private");
		header("Content-type: text/x-csv");
		header("Content-Disposition: attachment; filename=export-clients.csv");
		header("Pragma: public");

		while ($fields = $db->fetchrow($rs))
		{
			echo "\"{$fields['intitule']}\";\"{$fields['typeclient']}\";\"{$fields['adresse']}\";\"{$fields['codepostal']}\";\"{$fields['ville']}\";\"{$fields['pays']}\";\"". business_display_tel($fields['telephone'])."\";\"". business_display_tel($fields['telecopie'])."\";\"". business_display_tel($fields['telmobile'])."\";\"{$fields['mel']}\";\"{$fields['internet']}\";\"".business_dateus2fr($fields['date_creation'])."\";\"".business_dateus2fr($fields['date_maj'])."\".\"{$fields['login']}\"\r\n";
		}
	break;

	case 'xml':

		header("Cache-control: private");
		header("Content-type: text/xml");
		header("Content-Disposition: attachment; filename=export-clients.xml");
		header("Pragma: public");
		echo '<?xml version="1.0" encoding="iso-8859-1" standalone="no"?>';
		echo '<export>';
		while ($fields = $db->fetchrow($rs))
		{
			echo 	"
					<client>
						<intitule>{$fields['intitule']}</intitule>
						<typeclient>{$fields['typeclient']}</typeclient>
						<adresse>{$fields['adresse']}</adresse>
						<codepostal>{$fields['codepostal']}</codepostal>
						<ville>{$fields['ville']}</ville>
						<pays>{$fields['pays']}</pays>
						<telephone>". business_display_tel($fields['telephone'])."</telephone>
						<telecopie>". business_display_tel($fields['telecopie'])."</telecopie>
						<telmobile>". business_display_tel($fields['telmobile'])."</telmobile>
						<email>{$fields['mel']}</email>
						<internet>{$fields['internet']}</internet>
						<date_creation_fiche>".business_dateus2fr($fields['date_creation'])."</date_creation_fiche>
						<date_maj_fiche>".business_dateus2fr($fields['date_maj'])."</date_maj_fiche>
						<responsable>{$fields['login']}</responsable>
					</client>";
		}
		echo '</export>';
	break;

	case 'xls':

		require_once 'Spreadsheet/Excel/Writer.php';

		// Creating a workbook
		$workbook = new Spreadsheet_Excel_Writer();

		// sending HTTP headers
		$workbook->send("export-clients.xls");


		$format_title =& $workbook->addFormat( array( 'Align' => 'center', 'Bold'  => 1, 'Color'  => 'black', 'Size'  => 10, 'FgColor' => 'silver'));
		$format_rubtitle =& $workbook->addFormat( array( 'Align' => 'center', 'Bold'  => 1, 'Color'  => 'black', 'Size'  => 14));
		$format =& $workbook->addFormat( array( 'Align' => 'left', 'Bold'  => 0, 'Color'  => 'black', 'Size'  => 10));
		$format_prix =& $workbook->addFormat( array( 'Align' => 'right', 'Bold'  => 0, 'Color'  => 'black', 'Size'  => 10));

		$l=0;

		$id_rub1= '';

		$worksheet =& $workbook->addWorksheet("Clients");
		$worksheet->setColumn(0, 0, 60);
		$worksheet->setColumn(1, 1, 20);
		$worksheet->setColumn(2, 2, 20);
		$worksheet->setColumn(2, 3, 10);
		$worksheet->setColumn(2, 4, 25);
		$worksheet->setColumn(2, 5, 10);
		$worksheet->setColumn(2, 6, 15);
		$worksheet->setColumn(2, 7, 15);
		$worksheet->setColumn(2, 8, 15);
		$worksheet->setColumn(2, 9, 25);
		$worksheet->setColumn(2, 10, 20);
		$worksheet->setColumn(2, 11, 10);
		$worksheet->setColumn(2, 12, 10);
		$worksheet->setColumn(2, 13, 5);

		$worksheet->write($l, 0, 'intitulé', $format_title);
		$worksheet->write($l, 1, 'type', $format_title);
		$worksheet->write($l, 2, 'adresse', $format_title);
		$worksheet->write($l, 3, 'code postal', $format_title);
		$worksheet->write($l, 4, 'ville', $format_title);
		$worksheet->write($l, 5, 'pays', $format_title);
		$worksheet->write($l, 6, 'téléphone', $format_title);
		$worksheet->write($l, 7, 'télécopie', $format_title);
		$worksheet->write($l, 8, 'mobile', $format_title);
		$worksheet->write($l, 9, 'email', $format_title);
		$worksheet->write($l, 10, 'internet', $format_title);
		$worksheet->write($l, 11, 'création de la fiche', $format_title);
		$worksheet->write($l, 12, 'maj de la fiche', $format_title);
		$worksheet->write($l++, 13, 'login', $format_title);

		while ($fields = $db->fetchrow($rs))
		{
			$worksheet->write($l, 0, $fields['intitule'], $format);
			$worksheet->write($l, 1, $fields['typeclient'], $format);
			$worksheet->write($l, 2, $fields['adresse'], $format);
			$worksheet->write($l, 3, $fields['codepostal'], $format);
			$worksheet->write($l, 4, $fields['ville'], $format);
			$worksheet->write($l, 5, $fields['pays'], $format);
			$worksheet->write($l, 6, business_display_tel($fields['telephone']), $format);
			$worksheet->write($l, 7, business_display_tel($fields['telecopie']), $format);
			$worksheet->write($l, 8, business_display_tel($fields['telmobile']), $format);
			$worksheet->write($l, 9, $fields['mel'], $format);
			$worksheet->write($l, 10, $fields['internet'], $format);
			$worksheet->write($l, 11, business_dateus2fr($fields['date_creation']), $format);
			$worksheet->write($l, 12, business_dateus2fr($fields['date_maj']), $format);
			$worksheet->write($l++, 13, $fields['login'], $format);
		}

		$workbook->close();
	break;
}

while (@ob_end_flush());
die();
?>
