<?php
$website_name = 'Artifêtes-Diffusion';
$expeditor = 'ne_pas_repondre@artifetes_diffusion.com';

$user = $this->getUser();
$client = $this->getClient();
preg_match("#^([0-9]{4})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})$#", $this->fields['date_cree'], $date);

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
	<TD COLSPAN=\"2\" WIDTH=\"100%\" ALIGN=\"CENTER\">
	  <A HREF=\"http://".$_SERVER['HTTP_HOST']."/\"><IMG SRC=\"http://".$_SERVER['HTTP_HOST']."/templates/frontoffice/artifetes/gfx/logo_artifetes.png\" ALIGN=\"middle\" ALT=\"Artifêtes-Diffusion\" BORDER=\"0\"></A><BR><BR>
	</TD>
  </TR>
  <TR class=\"entete\">
    <TD COLSPAN=\"2\">
      ".$this->fields['user_name']." a créé une commande qui attend votre validation
    </TD>
  </TR>
  <TR><TD COLSPAN=\"2\">&nbsp;</TD></TR>
  <TR>
    <TD WIDTH=\"50%\" ALIGN=\"left\" VALIGN=\"top\">
      <TABLE WIDTH=\"100%\" BORDER=\"0\">
        <TR>
          <TD> <B>Num&#233;ro de commande :</B> </TD>
          <TD> ".$this->get('id')." </TD>
        </TR>
        <TR>
          <TD> <B>R&#233;f&#233;rence de commande :</B> </TD>
          <TD> ".(($this->getLibelle() != '') ? $this->getLibelle() : '<i>Non renseigné</i>')." </TD>
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
      <DIV ALIGN=center><TABLE CLASS=\"bloc\" WIDTH=\"200px\">
        <TR>
          <TH>
            ADRESSE DE FACTURATION
          </TH>
        </TR>
        <TR>
          <TD>
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
      <DIV ALIGN=center><TABLE CLASS=\"bloc\" WIDTH=\"200px\">
        <TR>
          <TH>
            ADRESSE DE LIVRAISON
          </TH>
        </TR>
        <TR>
          <TD>";
		if ($this->fields['cli_liv_cp_ville'] != '__NOPORT__') {
			$message .= $this->fields['cli_liv_nom']."<BR>".$this->fields['cli_liv_adr1']."<BR>";
			if ($this->fields['cli_liv_adr2'] != '') {
				$message .= $this->fields['cli_liv_adr2']."<BR>";
			}
			if ($this->fields['cli_liv_adr3'] != '') {
				$message .= $this->fields['cli_liv_adr3']."<BR>";
			}
			$message .= $this->fields['cli_liv_cp_ville'];
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
      <TD> {$ligne['ref']} </TD>
      <TD> {$ligne['label']} </TD>
      <TD ALIGN=\"RIGHT\"> ".catalogue_formateprix($ligne['pu_ttc'])." &euro; </TD>
      <TD ALIGN=\"RIGHT\"> {$ligne['qte']} </TD>
      <TD ALIGN=\"RIGHT\"> ".catalogue_formateprix(sprintf("%.2f",round($ligne['qte'] * $ligne['pu_ttc'],2)))." &euro; </TD>
      </TR>";
  }
}
else {
  foreach ($this->getlignes() as $ligne) {
  	$l++;
  	$lclass = ($l % 2 == 0) + 1;

  	$message .= "
  		<TR CLASS=\"ligne$lclass\">
  		<TD> {$ligne['reference']} </TD>
  		<TD> {$ligne['designation']} </TD>
  		<TD ALIGN=\"RIGHT\"> ".catalogue_formateprix($ligne['pu'])." &euro; </TD>
  		<TD ALIGN=\"RIGHT\"> {$ligne['qte']} </TD>
  		<TD ALIGN=\"RIGHT\"> ".catalogue_formateprix(sprintf("%.2f",round($ligne['qte'] * $ligne['pu'],2)))." &euro; </TD>
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
  </TR>
  <TR CLASS=\"ligne1\">
    <TD COLSPAN=\"3\" ROWSPAN=\"2\" WIDTH=\"*\" VALIGN=\"TOP\"> ".str_replace(chr(10), '<BR>', stripslashes(str_replace('\r\n', '<br/>', $this->fields['commentaire'])))." </TD>
    <TD WIDTH=\"100px\"> <B>Montant TTC</B> </TD>
    <TD WIDTH=\"100px\" ALIGN=\"RIGHT\"> <B>".catalogue_formateprix($this->fields['total_ttc'])." &euro;</B> </TD>
  </TR>
  <TR CLASS=\"ligne1\">
    <TD> Dont TVA </TD>
    <TD ALIGN=\"RIGHT\"> ".catalogue_formateprix($this->fields['total_tva'])." &euro; </TD>
  </TR>
</TABLE>

<BR>
Notre &#233;quipe vous remercie pour votre commande sur notre site Internet.
</BODY>
</HTML>";
