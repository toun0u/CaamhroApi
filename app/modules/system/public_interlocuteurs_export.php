<?
ob_end_clean();

$rs = $db->query($_SESSION['business']['interlocuteur_search']); // ATTENTION /!\


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
			if ($fields['mel'] != '' && !is_null($fields['mel'])) $eml .= "Bcc: {$fields['prenom']} {$fields['nom']} <{$fields['mel']}>;\r\n";

			$select =  "SELECT 		t.intitule, ti.mel
						FROM 		dims_mod_business_tiers t,
									dims_mod_business_tiers_interlocuteur ti
						WHERE		ti.tiers_id = t.id
						AND			ti.interlocuteur_id = :interlocuteurid
						ORDER BY 	intitule
						";

			$result_tiers = $db->query($select, array(
				':interlocuteurid' => $fields['id']
			));

			if ($db->numrows($result_tiers))
			{
				while ($fields_tiers = $db->fetchrow($result_tiers))
				{
					if ($fields_tiers['mel'] != '' && !is_null($fields_tiers['mel'])) $eml .= "Bcc: {$fields['prenom']} {$fields['nom']} ({$fields_tiers['intitule']}) <{$fields_tiers['mel']}>;\r\n";
				}
			}

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
		header("Content-Disposition: attachment; filename=export-interlocuteurs.csv");
		header("Pragma: public");

		while ($fields = $db->fetchrow($rs))
		{
			$select = 	"
						SELECT 		t.intitule, t.telephone as t_telephone, t.telmobile as t_telmobile, ti.*
						FROM 		dims_mod_business_tiers t,
									dims_mod_business_tiers_interlocuteur ti
						WHERE		ti.tiers_id = t.id
						AND			ti.interlocuteur_id = :interlocuteurid
						ORDER BY 	intitule
						";

			$result_tiers = $db->query($select, array(
				':interlocuteurid' => $fields['id']
			));

			if ($db->numrows($result_tiers))
			{
				while ($fields_tiers = $db->fetchrow($result_tiers))
				{
					echo "\"{$fields['nom']}\";\"{$fields['prenom']}\";\"{$fields_tiers['intitule']}\";\"{$fields_tiers['fonction']}\";\"{$fields_tiers['service']}\";\"".business_display_tel($fields_tiers['t_telephone'])."\";\"".business_display_tel($fields_tiers['t_telmobile'])."\";\"".business_display_tel($fields_tiers['telephone'])."\";\"".business_display_tel($fields_tiers['telmobile'])."\";\"{$fields_tiers['mel']}\";\"{$fields_tiers['adresse']}\";\"{$fields_tiers['codepostal']}\";\"{$fields_tiers['ville']}\";\"{$fields_tiers['pays']}\"\r\n";
				}
			}
			else echo "\"{$fields['nom']}\";\"{$fields['prenom']}\"\r\n";
		}
	break;

	case 'xml':

		header("Cache-control: private");
		header("Content-type: text/xml");
		header("Content-Disposition: attachment; filename=export-interlocuteurs.xml");
		header("Pragma: public");
		echo '<?xml version="1.0" encoding="iso-8859-1" standalone="no"?>';
		echo '<export>';
		while ($fields = $db->fetchrow($rs))
		{
			$select = 	"
						SELECT 		t.intitule, t.telephone as t_telephone, t.telmobile as t_telmobile, ti.*
						FROM 		dims_mod_business_tiers t,
									dims_mod_business_tiers_interlocuteur ti
						WHERE		ti.tiers_id = t.id
						AND			ti.interlocuteur_id = :interlocuteurid
						ORDER BY 	intitule
						";

			$result_tiers = $db->query($select, array(
				':interlocuteurid' => $fields['id']
			));

			echo 	"
					<interlocuteur>
						<nom>{$fields['nom']}</nom>
						<prenom>{$fields['prenom']}</prenom>
						<clients>
					";

			if ($db->numrows($result_tiers))
			{
				while ($fields_tiers = $db->fetchrow($result_tiers))
				{
					echo 	"
							<client>
								<intitule>{$fields_tiers['intitule']}</intitule>
								<fonction>{$fields_tiers['fonction']}</fonction>
								<service>{$fields_tiers['service']}</service>
								<telephone_client>". business_display_tel($fields_tiers['t_telephone'])."</telephone_client>
								<mobile_client>". business_display_tel($fields_tiers['t_telmobile'])."</mobile_client>
								<telephone_interlocuteur>". business_display_tel($fields_tiers['telephone'])."</telephone_interlocuteur>
								<telephone_interlocuteur>". business_display_tel($fields_tiers['telmobile'])."</telephone_interlocuteur>
								<email>{$fields_tiers['mel']}</email>
								<adresse>{$fields_tiers['adresse']}</adresse>
								<codepostal>{$fields_tiers['codepostal']}</codepostal>
								<ville>{$fields_tiers['ville']}</ville>
								<pays>{$fields_tiers['pays']}</pays>
							</client>
							";
				}
			}

			echo 	"
						</clients>
					</interlocuteur>
					";
		}
		echo '</export>';
	break;

	case 'xls':

		require_once 'Spreadsheet/Excel/Writer.php';

		// Creating a workbook
		$workbook = new Spreadsheet_Excel_Writer();

		// sending HTTP headers
		$workbook->send("export-interlocuteurs.xls");


		$format_title =& $workbook->addFormat( array( 'Align' => 'center', 'Bold'  => 1, 'Color'  => 'black', 'Size'  => 10, 'FgColor' => 'silver'));
		$format_rubtitle =& $workbook->addFormat( array( 'Align' => 'center', 'Bold'  => 1, 'Color'  => 'black', 'Size'  => 14));
		$format =& $workbook->addFormat( array( 'Align' => 'left', 'Bold'  => 0, 'Color'  => 'black', 'Size'  => 10));
		$format_prix =& $workbook->addFormat( array( 'Align' => 'right', 'Bold'  => 0, 'Color'  => 'black', 'Size'  => 10));

		$l=0;

		$id_rub1= '';

		$worksheet =& $workbook->addWorksheet("Interlocuteurs");
		$worksheet->setColumn(0, 0, 20);
		$worksheet->setColumn(1, 1, 15);
		$worksheet->setColumn(2, 2, 20);
		$worksheet->setColumn(2, 3, 15);
		$worksheet->setColumn(2, 4, 15);
		$worksheet->setColumn(2, 5, 15);
		$worksheet->setColumn(2, 6, 15);
		$worksheet->setColumn(2, 7, 15);
		$worksheet->setColumn(2, 8, 15);
		$worksheet->setColumn(2, 9, 25);
		$worksheet->setColumn(2, 10, 20);
		$worksheet->setColumn(2, 11, 10);
		$worksheet->setColumn(2, 12, 10);
		$worksheet->setColumn(2, 13, 5);

		$worksheet->write($l, 0, 'nom', $format_title);
		$worksheet->write($l, 1, 'prenom', $format_title);
		$worksheet->write($l, 2, 'intitule', $format_title);
		$worksheet->write($l, 3, 'fonction', $format_title);
		$worksheet->write($l, 4, 'service', $format_title);
		$worksheet->write($l, 5, 'téléphone client', $format_title);
		$worksheet->write($l, 6, 'mobile client', $format_title);
		$worksheet->write($l, 7, 'téléphone interlocuteur', $format_title);
		$worksheet->write($l, 8, 'mobile interlocuteur', $format_title);
		$worksheet->write($l, 9, 'email', $format_title);
		$worksheet->write($l, 10, 'adresse', $format_title);
		$worksheet->write($l, 11, 'code postal', $format_title);
		$worksheet->write($l, 12, 'ville', $format_title);
		$worksheet->write($l++, 13, 'pays', $format_title);

		while ($fields = $db->fetchrow($rs))
		{
			$select = 	"
						SELECT 		t.intitule, t.telephone as t_telephone, t.telmobile as t_telmobile, ti.*
						FROM 		dims_mod_business_tiers t,
									dims_mod_business_tiers_interlocuteur ti
						WHERE		ti.tiers_id = t.id
						AND			ti.interlocuteur_id = :interlocuteurid
						ORDER BY 	intitule
						";

			$result_tiers = $db->query($select, array(
				':interlocuteurid' => $fields['id']
			));

			if ($db->numrows($result_tiers))
			{
				while ($fields_tiers = $db->fetchrow($result_tiers))
				{
					$worksheet->write($l, 0, $fields['nom'], $format);
					$worksheet->write($l, 1, $fields['prenom'], $format);
					$worksheet->write($l, 2, $fields_tiers['intitule'], $format);
					$worksheet->write($l, 3, $fields_tiers['fonction'], $format);
					$worksheet->write($l, 4, $fields_tiers['service'], $format);
					$worksheet->write($l, 5, business_display_tel($fields_tiers['t_telephone']), $format);
					$worksheet->write($l, 6, business_display_tel($fields_tiers['t_telmobile']), $format);
					$worksheet->write($l, 7, business_display_tel($fields_tiers['telephone']), $format);
					$worksheet->write($l, 8, business_display_tel($fields_tiers['telmobile']), $format);
					$worksheet->write($l, 9, $fields_tiers['mel'], $format);
					$worksheet->write($l, 10, $fields_tiers['adresse'], $format);
					$worksheet->write($l, 11, $fields_tiers['codepostal'], $format);
					$worksheet->write($l, 12, $fields_tiers['ville'], $format);
					$worksheet->write($l++, 13, $fields_tiers['pays'], $format);
				}
			}
			else
			{
				$worksheet->write($l, 0, $fields['nom'], $format);
				$worksheet->write($l++, 1, $fields['prenom'], $format);
			}
		}


		$workbook->close();
	break;
}

while (@ob_end_flush());
die();
?>