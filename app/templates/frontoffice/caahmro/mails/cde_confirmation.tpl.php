<?php
$workspace = new workspace();
$workspace->open($this->fields['id_workspace']);

$website_name = $workspace->fields['label'];
$expeditor = $workspace->fields['email_noreply'];
$tiers = $workspace->getTiers();


$user = $this->getUser();
$client = $this->getClient();
preg_match("#^([0-9]{4})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})$#", $this->fields['date_cree'], $date);

// chargement des paramètres du catalogue
$dims = dims::getInstance();
$mods = $dims->getModuleByType('catalogue');

$oCatalogue = new catalogue();
$oCatalogue->open($mods[0]['instanceid']);
$oCatalogue->loadParams();

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

<BR>
<TABLE WIDTH=\"780\" BORDER=\"0\">
  <TR>
	<TD COLSPAN=\"2\" WIDTH=\"100%\" ALIGN=\"CENTER\">";

if (file_exists($tiers->getPhotoPath(100))) {
  $message .= "<A HREF=\"".$dims->getProtocol().$dims->getHttpHost()."/\"><IMG SRC=\"".$dims->getProtocol().$dims->getHttpHost().substr($tiers->getPhotoWebPath(100), 1)."\" ALIGN=\"middle\" ALT=\"$website_name\" BORDER=\"0\"></A><BR><BR>";
}
else {
  $message .= "<A HREF=\"".$dims->getProtocol().$dims->getHttpHost()."/\">$website_name</A><BR><BR>";
}

$message .= "
	</TD>
  </TR>";
if ($this->fields['hors_cata']) {
  $message .= "
	<TR class=\"entete\">
	  <TD COLSPAN=\"2\" ALIGN=\"center\">
		Commande Hors-Catalogue
	  </TD>
	</TR>
	<TR><TD COLSPAN=\"2\">&nbsp;</TD></TR>";
}
$message .= "
  <TR>
	<TD WIDTH=\"50%\" ALIGN=\"left\" VALIGN=\"top\">
	  <TABLE WIDTH=\"100%\" BORDER=\"0\">
		<TR>
		  <TD> <B>Num&#233;ro de commande :</B> </TD>
		  <TD> ".$this->getId()." </TD>
		</TR>
		<TR>
		  <TD> <B>Date :</B> </TD>
		  <TD> $date[3]/$date[2]/$date[1] $date[4]:$date[5] </TD>
		</TR>
	  </TABLE>
	  <BR>
	</TD>
	<TD WIDTH=\"50%\" VALIGN=\"top\">
	  <TABLE WIDTH=\"100%\" BORDER=\"0\">
		<TR>
		  <TD> <B>Code client :</B> </TD>
		  <TD> ".$client->getCode()." </TD>
		</TR>
		<TR>
		  <TD> <B>Utilisateur :</B> </TD>
		  <TD> ".$user->fields['firstname']." ".$user->fields['lastname']." (".$user->fields['login'].") </TD>
		</TR>
	  </TABLE>
	</TD>
  </TR>
</TABLE>

<BR>
<TABLE WIDTH=\"780\" BORDER=\"0\">
  <TR>
	<TD WIDTH=\"0%\" ALIGN=\"center\" VALIGN=\"top\">
	  <DIV ALIGN=center><TABLE CLASS=\"bloc\" WIDTH=\"400px\" HEIGHT=\"150px\">
		<TR>
		  <TH STYLE=\"font-size: 16px;\">
			ADRESSE DE FACTURATION
		  </TH>
		</TR>
		<TR>
		  <TD STYLE=\"font-size: 16px;\">
			".$this->fields['cli_nom']."<BR>
			".$this->fields['cli_adr1']."<BR>";
		if ($this->fields['cli_adr2'] != '') {
			$message .= $this->fields['cli_adr2']."<BR>";
		}
		if ($this->fields['cli_adr3'] != '') {
			$message .= $this->fields['cli_adr3']."<BR>";
		}
		$message .= "
			".$this->fields['cli_cp']." ".$this->fields['cli_ville']."
		  </TD>
		</TR>
	  </TABLE></DIV>
	</TD>
	<TD WIDTH=\"50%\" ALIGN=\"center\" VALIGN=\"top\">
	  <DIV ALIGN=center><TABLE CLASS=\"bloc\" WIDTH=\"300px\" HEIGHT=\"120px\">
		<TR>
		  <TH STYLE=\"font-size: 14px;\">
			ADRESSE DE LIVRAISON
		  </TH>
		</TR>
		<TR>
		  <TD STYLE=\"font-size: 14px;\">";
		if ($this->fields['cli_liv_ville'] != '__NOPORT__') {
			$message .= $this->fields['cli_liv_nom']."<BR>".$this->fields['cli_liv_adr1']."<BR>";
			if ($this->fields['cli_liv_adr2'] != '') {
				$message .= $this->fields['cli_liv_adr2']."<BR>";
			}
			if ($this->fields['cli_liv_adr3'] != '') {
				$message .= $this->fields['cli_liv_adr3']."<BR>";
			}
			$message .= $this->fields['cli_liv_cp'].' '.$this->fields['cli_liv_ville'];
		} else {
			$message .= "Enl&#232;vement magasin";
		}
		$message .= "
		  </TD>
		</TR>
	  </TABLE></DIV>
	</TD>
  </TR>
</TABLE>

<BR>
<TABLE CELLPADDING=\"2\" WIDTH=\"780\" CLASS=\"bloc\">
  <TR>
	<TH> <B>Ref</B> </TH>
	<TH> <B>D&#233;signation</B> </TH>
	<TH> <B>PU</B> </TH>
	<TH> <B>Qt&#233;</B> </TH>
	<TH> <B>Total</B> </TH>
  </TR>";

$l = 0;
if (!$this->fields['hors_cata']) {
	foreach ($this->getlignes() as $ligne) {
		$l++;
		$lclass = ($l % 2 == 0) + 1;
		$message .= "
			<TR CLASS=\"ligne$lclass\">
			<TD> {$ligne->fields['ref']} </TD>
			<TD> {$ligne->fields['label']} </TD>
			<TD ALIGN=\"RIGHT\"> ".catalogue_formateprix($ligne->fields['pu_remise'])." &euro; </TD>
			<TD ALIGN=\"RIGHT\"> {$ligne->fields['qte']} </TD>
			<TD ALIGN=\"RIGHT\"> ".catalogue_formateprix(sprintf("%.2f",round($ligne->fields['qte'] * $ligne->fields['pu_remise'],2)))." &euro; </TD>
			</TR>";
	}
}else {
  foreach ($this->getlignes() as $ligne) {
	$l++;
	$lclass = ($l % 2 == 0) + 1;

	$message .= "
		<TR CLASS=\"ligne$lclass\">
		<TD> {$ligne->fields['reference']} </TD>
		<TD> {$ligne->fields['designation']} </TD>
		<TD ALIGN=\"RIGHT\"> ".catalogue_formateprix($ligne->fields['pu'])." &euro; </TD>
		<TD ALIGN=\"RIGHT\"> {$ligne->fields['qte']} </TD>
		<TD ALIGN=\"RIGHT\"> ".catalogue_formateprix(sprintf("%.2f",round($ligne->fields['qte'] * $ligne->fields['pu'],2)))." &euro; </TD>
		</TR>";
  }
}

$message .= "
  <TR CLASS=\"ligne2\">
	<TD> <BR> </TD>
	<TD> <BR> </TD>
	<TD> <BR> </TD>
	<TD> <BR> </TD>
	<TD> <BR> </TD>
  </TR>";
if ($oCatalogue->getParams('cata_base_ttc')) {
  $message .= "
	<TR CLASS=\"ligne1\">
	  <TD COLSPAN=\"2\" ROWSPAN=\"4\" WIDTH=\"*\" VALIGN=\"TOP\"> ".str_replace(chr(10), '<BR>', stripslashes(str_replace('\r\n', '<br/>', $this->fields['commentaire'])))." </TD>
	  <TD COLSPAN=\"2\" WIDTH=\"100px\"> <B STYLE=\"font-size: 14px;\">Sous-total TTC</B> </TD>
	  <TD WIDTH=\"100px\" ALIGN=\"RIGHT\"> <B STYLE=\"font-size: 14px;\">".catalogue_formateprix($this->fields['total_ttc'] - $this->fields['port'])." &euro;</B> </TD>
	</TR>
	<TR CLASS=\"ligne1\">
	  <TD COLSPAN=\"2\"> Port </TD>
	  <TD ALIGN=\"RIGHT\"> ".catalogue_formateprix($this->fields['port'])." &euro; </TD>
	</TR>
	<TR CLASS=\"ligne1\">
	  <TD COLSPAN=\"2\" WIDTH=\"100px\"> <B STYLE=\"font-size: 14px;\">Montant TTC</B> </TD>
	  <TD WIDTH=\"100px\" ALIGN=\"RIGHT\"> <B STYLE=\"font-size: 14px;\">".catalogue_formateprix($this->fields['total_ttc'])." &euro;</B> </TD>
	</TR>
	<TR CLASS=\"ligne1\">
	  <TD COLSPAN=\"2\"> Dont TVA </TD>
	  <TD ALIGN=\"RIGHT\"> ".catalogue_formateprix($this->fields['total_tva'])." &euro; </TD>
	</TR>";
}
else {
  $message .= "
	<TR CLASS=\"ligne1\">
	  <TD COLSPAN=\"3\" ROWSPAN=\"5\" WIDTH=\"*\" VALIGN=\"TOP\"> ".str_replace(chr(10), '<BR>', stripslashes(str_replace('\r\n', '<br/>', $this->fields['commentaire'])))." </TD>
	  <TD WIDTH=\"100px\"> <B>Sous-total HT</B> </TD>
	  <TD WIDTH=\"100px\" ALIGN=\"RIGHT\"> <B>".catalogue_formateprix($this->fields['total_ht'])." &euro;</B> </TD>
	</TR>
	<TR CLASS=\"ligne1\">
	  <TD> Port </TD>
	  <TD ALIGN=\"RIGHT\"> ".catalogue_formateprix($this->fields['port'])." &euro; </TD>
	</TR>
	<TR CLASS=\"ligne1\">
	  <TD> Total HT </TD>
	  <TD ALIGN=\"RIGHT\"> ".catalogue_formateprix($this->fields['total_ht'])." &euro; </TD>
	</TR>
	<TR CLASS=\"ligne1\">
	  <TD> TVA </TD>
	  <TD ALIGN=\"RIGHT\"> ".catalogue_formateprix($this->fields['total_tva'])." &euro; </TD>
	</TR>
	<TR CLASS=\"ligne1\">
	  <TD> <B>Montant TTC</B> </TD>
	  <TD ALIGN=\"RIGHT\"> <B>".catalogue_formateprix($this->fields['total_ttc'])." &euro;</B> </TD>
	</TR>";
}
$message .= "
</TABLE>

<BR>
Notre &#233;quipe vous remercie pour votre commande sur notre site Internet.
</BODY>
</HTML>";
