<TABLE WIDTH="100%" CELLPADDING="2" CELLSPACING="1">

<? $color = $skin->values['bgline1']; ?>
<TR BGCOLOR="<? echo $color; ?>" CLASS="Title">
	<TD ALIGN="LEFT"><? echo _BUSINESS_LABEL_NATURE_ACTIVITE; ?></TD>
	<TD ALIGN="LEFT"><? echo _BUSINESS_LABEL_PROCEDURE; ?></TD>
	<TD ALIGN="LEFT">Client</TD>
	<TD ALIGN="LEFT"><? echo _BUSINESS_LABEL_AVANCEMENT_DOSSIER; ?></TD>
	<TD ALIGN="LEFT"><? echo _BUSINESS_LABEL_DATE_DEBUT_DOSSIER; ?></TD>
</TR>
<?
$select =  "SELECT 		d.*, t.id as idtiers, t.intitule as intituletiers
			FROM		dims_mod_business_dossier d,
						dims_mod_business_tiers_dossier td
			LEFT JOIN	dims_mod_business_tiers t
			ON			td.tiers_id = t.id
			WHERE		td.dossier_id = d.id
			AND			td.interlocuteur_id = :interlocuteurid
			";

$db->query($select, array(
	':interlocuteurid' => $_SESSION['business']['interlocuteur_id']
));
if (!$db->numrows()) echo '<TR BGCOLOR="'.$skin->values['bgline2'].'"><TD COLSPAN="6" ALIGN="CENTER">Aucune réponse</TD></TR>';
else
{
	while ($fields = $db->fetchrow())
	{

			if ($fields['termine'] == 'Oui')
			{
				$avancement =	"
								<table cellpadding=\"0\" cellspacing=\"1\" width=\"100\" height=\"14\" bgcolor=\"#000000\">
								<tr>
									<td bgcolor=\"#FF0000\"><font style=\"font-size:9px;color:#000000\">&nbsp;terminé&nbsp;</font></td>
								</tr>
								</table>
								";
			}
			else
			{
				$datejour = date(_DIMS_DATEFORMAT_US);
				if ($datejour > $fields['date_fin']) $avancement = 100;
				else
				{
					$duree_dossier = business_datediff($fields['date_debut'],$fields['date_fin']);
					$temps_ecoule = business_datediff($fields['date_debut'],$datejour);
					$avancement = sprintf("%d",($temps_ecoule * 100) / $duree_dossier);
					if ($avancement < 0) $avancement = 0;
				}

				$avancement_debut = '';
				$avancement_fin = '';
				if ($avancement > '0')
				{
					$avancement_debut = "<td width=\"$avancement\" background=\"./common/modules/business/img/avancement.gif\"><font style=\"font-size:9px;color:#000000\">&nbsp;$avancement%&nbsp;</font></td>";
				}
				if ($avancement < '100')
				{
					if ($avancement == '0') $avancement_fin = '<td bgcolor="#CCCCCC"><font style=\"font-size:9px;color:#000000\">&nbsp;0%&nbsp;</font></td>';
					else $avancement_fin = '<td bgcolor="#CCCCCC">&nbsp;</td>';
				}
				$avancement = 	"
								<table cellpadding=\"0\" cellspacing=\"1\" width=\"100\" height=\"14\" bgcolor=\"#000000\">
								<tr>
									$avancement_debut
									$avancement_fin
								</tr>
								</table>
								";
			}

		$color = ($color == $skin->values['bgline1']) ? $skin->values['bgline2'] : $skin->values['bgline1'];
		$ouvrir_tiers = "$scriptenv?cat="._BUSINESS_CAT_TIERS."&tiers_id={$fields['idtiers']}";
		$ouvrir_dossier = "$scriptenv?cat="._BUSINESS_CAT_TIERS."&dims_moduletabid="._BUSINESS_TAB_TIERSDOSSIERS."&op=dossier_ouvrir&tiers_id={$fields['idtiers']}&dossier_id={$fields['id']}";
		?>
			<TR BGCOLOR="<? echo $color; ?>">
				<TD><A HREF="<? echo $ouvrir_dossier; ?>"><? echo $fields['objet_dossier'];?></A></TD>
				<TD><? echo $fields['procedure'];?></TD>
				<TD><a href="<? echo $ouvrir_tiers; ?>"><? echo $fields['intituletiers'];?></a></TD>
				<TD><? echo $avancement; ?></TD>
				<TD><? echo business_dateus2fr($fields['date_debut']);?></TD>
			</TR>
		<?
	}
}
?>
</TABLE>
