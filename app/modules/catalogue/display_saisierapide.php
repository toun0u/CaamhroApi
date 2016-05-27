<?php
if ($oCatalogue->getParams('saisie_rapide')) {
	ob_start();
	?>

	<script language="JavaScript">
		get_element = document.all ?
		function (s_id) { return document.all[s_id] } :
		function (s_id) { return document.getElementById(s_id) };

		var ie4= (navigator.appName == "Microsoft Internet Explorer")?1:0;
		var ns4= (navigator.appName=="Netscape")?1:0;
		eventSetup();
		var tabarticle = new Array(30);

		function isNumberString (InString) {
			if(InString.length==0) return (false);
			var RefString="1234567890";
			for (Count=0; Count < InString.length; Count++) {
				TempChar= InString.substring (Count, Count+1);
				if (RefString.indexOf (TempChar, 0)==-1)
					return (false);
			}
			return (true);
		}

		function eventSetup() {
			if (ie4) {
				document.onkeydown = Ienterevent;
			}
			if(ns4) {
				document.captureEvents( Event.KEYDOWN );
				document.onkeydown = Nenterevent;
			}
		}

		function Ienterevent() {
			var key;
			if(window.event) {
				key = event.keyCode;
			}
			else if(event.which) {
				key = event.which;
			}

			switch(key) {
				case 13:
					if (checkAll()) enregistrer_saisierapide();
				case 40:
					var nextId = Number(event.srcElement.id) + 1;
					if(document.getElementById(event.srcElement.id).value != '') if(document.getElementById(nextId)) document.getElementById(nextId).focus();
					break;
				case 38:
					var previousId = Number(event.srcElement.id) - 1;
					if (document.getElementById(previousId)) {
						document.getElementById(previousId).focus();
						document.getElementById(previousId).value = '';
						var nextId = Number(event.srcElement.id);
						document.getElementById(nextId).value = '';
						checkAll();
					}
					break;
			}
		}

		function Nenterevent(e) {
			switch(e.keyCode) {
				case 13:
					if (checkAll()) enregistrer_saisierapide();
				case 40:
					var nextId = Number(e.target.id) + 1;
					if(document.getElementById(e.target.id).value != '') if(document.getElementById(nextId)) document.getElementById(nextId).focus();
					break;
				case 38:
					var previousId =  Number(e.target.id) - 1;
					if(document.getElementById(previousId)) {
						document.getElementById(previousId).focus();
						document.getElementById(previousId).value = "";
						var nextId = Number(e.target.id);
						document.getElementById(nextId).value = "";
						checkAll();
					}
					break;
			}
		}

		function testArticle(id) {
			var article=document.getElementById(''+(id*2)).value;
			var qte=document.getElementById(''+(id*2+1)).value;
			var l=article.length;

			if (l > 0) return true;
			else {
				divinfo=document.getElementById('divinfo_'+(id));
				divvalid=document.getElementById('divvalid_'+(id));
				divvalid.innerHTML="   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
				divinfo.innerHTML="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
				qte.value="";
				return false;
			}
		}
		function elemFocus(id) {
			document.getElementById(''+id*2).focus;
		}
		function getArticle(id) {
			var str=document.getElementById(''+(id*2)).value;
			return str.replace("&","");
		}

		function clearDetails() {
			document.getElementById('divdetail').innerHTML="Ce formulaire vous permet d'accélérer la saisie<br>des articles dont vous connaissez la référence.<br><br>Il vous suffit d'indiquer une référence et une quantité ci-contre<br>et de cliquer sur le bouton \"Ajouter au panier\".<br><br>Si la référence existe, l'article sera automatiquement<br>ajouté à votre panier.<br><br><br><br>";
			divinfo=document.getElementById('divinfo_0');
			divvalid=document.getElementById('divvalid_0');
			divvalid.innerHTML="   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
			divinfo.innerHTML="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
			document.getElementById('1').value='';
			document.getElementById('div_artRempl').innerHTML = '';
		}

		function checkAll() {
			var cpte=0;
			var val=0;
			var valch=0;
			var erreur=0;
			var divinfo;
			var cont="";
			var contentresult="";
			/* init des tableaux d'erreurs */
			var taberror= new Array(4);
			var taberrorcount= new Array(4);
			var divvalid;

			for (i=0;i<4;i++) {
				taberrorcount[i]=0;
				taberror[i] = new Array(1);
			}

			for(i=0;i<1;i++) {
				erreur=-1;

				val=document.getElementById(''+(i*2+1)).value;
				if (val>0) cpte++;

				valch=document.getElementById(''+(i*2)).value;
				divinfo=document.getElementById('divinfo_'+(i));
				divvalid=document.getElementById('divvalid_'+(i));

				if (valch!="") {
					divvalid.innerHTML="   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
					tabarticle[i]=valch;
					cont=divinfo.innerHTML;

					if (cont.indexOf("-")>0) {
						erreur=3;
						taberror[2][taberrorcount[2]*1]=i+1;
						taberrorcount[2]=taberrorcount[2]+1;

						divvalid.innerHTML="<img border=\"0\" src=\"./common/modules/catalogue/img/supprimer.png\" />";
					} else {
						if (val=="") {
							erreur=3;
						} else {
							if (!isNumberString(val)) {
								erreur=4;
								taberror[3][(taberrorcount[3]*1)]=i+1;
								taberrorcount[3]=taberrorcount[3]+1;
								divvalid.innerHTML="<img border=\"0\" src=\"./common/modules/catalogue/img/supprimer.png\" />";
							} else {
								if (val==0) {
									erreur=1;
									taberror[0][(taberrorcount[0]*1)]=i+1;
									taberrorcount[0]=taberrorcount[0]+1;
								}

								/* boucle de vérification des doublons*/
								for(j=0;j<30;j++) {
									if (j!=i) {
										if (tabarticle[i]==tabarticle[j]) {
											erreur=2;
											taberror[1][taberrorcount[1]*1]=i+1;
											taberrorcount[1]=taberrorcount[1]+1;
											divvalid.innerHTML="<img border=\"0\" src=\"./common/modules/catalogue/img/bell.png\" />";
										}
									}
								}
							}
							if (erreur==-1) divvalid.innerHTML="<img border=\"0\" src=\"./common/modules/catalogue/img/add-to-basket.png\" />";
						}
					}
				} else {
					divvalid.innerHTML="   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
					divinfo.innerHTML="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
					erreur=1;
					clearDetails();
				}
			}

			var diverreur=document.getElementById('erreur');
			var actionvalid=document.getElementById('actionvalid');
			var actioninvalid=document.getElementById('actioninvalid');
			/* boucle sur toutes les erreurs possibles */
			for (i=0;i<4;i++) {
				if (taberrorcount[i]>0) {
					switch(i) {
						case 0:
							contentresult+="<p align=\"justify\"><font color=\"red\">Il existe au moins un article possédant une quantité à zéro ou nulle. Veuillez rectifier votre saisie.</font>";
							erreur=1;
							break;
						case 1:
							contentresult+="<p align=\"justify\"><font color=\"#C9512C\">Il existe plusieurs articles portant sur la même référence d'article. Veuillez rectifier votre saisie.</font></p>";
							break;
						case 2:
							contentresult+="<p align=\"justify\"><font color=\"red\">Il existe au moins un article inexistant. Veuillez rectifier votre saisie.</font></p>";
							erreur=1;
							break;
						case 3:
							contentresult+="<p align=\"justify\"><font color=\"red\">Il existe au moins un article possédant une quantité non numérique. Veuillez rectifier votre saisie.</font></p>";
							erreur=1;
							break;
					}

					/*boucle sur les lignes */
					for (j=0;j<taberrorcount[i];j++) {
						contentresult+="<center><img src=\"./common/modules/catalogue/img/tab_right.png\" alt=\"\" border=\"0\" /> Ligne de saisie "+taberror[i][j]+"<Br>";
					}
					contentresult+="</p>";
				}
			}

			if (contentresult=="") {
				diverreur.style.display="none";
				diverreur.style.visibility="hidden";
			}

			if (erreur<=0 && cpte>0) {
				var result = true;
				actionvalid.style.display="block";
				actionvalid.style.visibility="visible";
				actioninvalid.style.display="none";
				actioninvalid.style.visibility="hidden";
			} else {
				var result = false;
				actionvalid.style.display="none";
				actionvalid.style.visibility="hidden";
				actioninvalid.style.display="block";
				actioninvalid.style.visibility="visible";
			}

			return result;
		}

		function enregistrer_saisierapide() {
			var i = 0;
			var nbart = 0;
			var args = "";

			for (i = 0; i < 1; i++) {
				var pref = document.getElementById(i * 2);
				var qte = document.getElementById((i * 2) + 1);

				if (pref.value != "" && qte.value != "") {
					args += "&pref" + i + "=" + pref.value + "&qte" + i + "=" + qte.value;

					pref.value = "";
					qte.value = "";
					nbart = i + 1;
				}
			}

			args += "&nbart=" + nbart;
			dims_xmlhttprequest_todiv('/index.php', 'op=enregistrer_saisierapide'+args, '|', 'nbArtPanier', 'div_panier_sr');

			clearDetails();
			pref.focus();
		}

		function ajouter_artRattSR(pref, id) {
			var qte = document.getElementsByName('rqte'+id)[0].value

			args = "&pref0=" + pref + "&qte0=" + qte + "&nbart=1";
			dims_xmlhttprequest_todiv('/index.php', 'op=enregistrer_saisierapide'+args, '|', 'nbArtPanier', 'div_panier_sr');
			clearDetails();

			document.getElementById('0').value='';
			document.getElementById('0').focus();
		}
	</script>

	<table width="100%" cellpadding="0" cellspacing="0">
		<tr bgcolor="#E8EEFF">
			<td>
				<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
	                    <td class="image_panier"><img border="0" style="float:right;" alt="Saisie rapide" src="/modules/catalogue/img/saisie_rapide.png"></td>
						<td class="WebNavTitle">&nbsp;<? echo _LABEL_SAISIERAPIDE; ?></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr bgcolor="#dddddd" height="1"><td></td></tr>
		<tr>
			<td width="772" align="center" valign="top">
				<table cellpadding="2" cellspacing="0" width="100%">
					<tr>
						<td valign="top" rowspan="2" width="400">
							<form name="sr_form" method="post">
							<input type="hidden" id="op" name="op" value="enregistrer_saisierapide">

							<br>
							<table cellpadding="0" cellspacing="0" width="100%">
								<tr><td colspan="10" height="1" bgcolor="#dddddd"></td></tr>
								<tr>
									<td colspan="5">
										<table cellpadding="0" cellspacing="0" width="100%">
											<tr>
												<td bgcolor="#f8f8f8" width="20">&nbsp;</td>
												<td bgcolor="#f8f8f8" width="80" align="center"><b>&nbsp;Référence</b></td>
												<td bgcolor="#f8f8f8" width="15"></td>
												<td bgcolor="#f8f8f8" width="30" align="center"><b>&nbsp;Quantité</b></td>
												<td bgcolor="#f8f8f8" width="5"></td>
												<?
												if ($oCatalogue->getParams('cata_show_stocks')) {
													echo "<td bgcolor=\"#f8f8f8\" width=\"30\" align=\"right\"><b>Dispo</b></td>";
												} else {
													echo "<td bgcolor=\"#f8f8f8\" width=\"30\" align=\"right\">&nbsp;</td>";
												}
												?>
												<td bgcolor="#f8f8f8">&nbsp;</td>
											</tr>
											<?
											for($i = 0; $i < 1; $i++) {
												?>
												<tr><td colspan="7" height="1" bgcolor="#dddddd"></td></tr>
												<tr height="20">
													<td width="20" align="center"><div id="divvalid_<? echo $i; ?>" type="text" name="divvalid_<? echo $i; ?>">   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div></td>
													<td width="80" align="center"><input id="<?=($i * 2);?>" class="WebText" type="text" onKeyUp="checkAll();" name="refqte[<?=$i;?>][0]" size="10" onFocus="javascript: clearDetails();" onBlur="if(testArticle(<?=$i;?>)) dims_xmlhttprequest_todiv('/index.php', 'op=detail_art&pref='+getArticle(<?=$i;?>), '|','divinfo_<?=$i;?>','divdetail', 'div_artRempl');"></td>
													<td width="10"></td>
													<td width="30" align="center"><input id="<?=($i * 2 + 1);?>" class="WebInput" type="text" onKeyUp="checkAll();" name="refqte[<?=$i;?>][1]" size="5"></td>
													<td width="10"></td>
													<td width="30" align="center"><div id="divinfo_<?=$i;?>" type="text" name="divinfo_<?=$i;?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div></td>
													<td align="right" height="32px;">
														<div id="actionvalid" style="visibility:hidden;display:none">
															<?=catalogue_makegfxbutton('', '<img src="./common/modules/catalogue/img/panier.png">', "enregistrer_saisierapide()", "*");?>
														</div>
														<div id="actioninvalid" style="visibility:visible;display:block">
															<?=catalogue_makeinvalidbutton('', '<img src="./common/modules/catalogue/img/panier.png">', "*");?>
														</div>
													</td>
												</tr>
												<?
											}
											?>
										</table>
									</td>
								</tr>
								<tr><td colspan="10" height="1" bgcolor="#dddddd"></td></tr>
							</form>
							</table>
						</td>
						<td valign="top" width="30"></td>
						<td valign="top">
							<div id="divdetail">
								Ce formulaire vous permet d'accélérer la saisie<br>des articles dont vous connaissez la référence.<br><br>
								Il vous suffit d'indiquer une référence et une quantité ci-contre<br>et de cliquer sur le bouton "Ajouter au panier".<br><br>
								Si la référence existe, l'article sera automatiquement<br>ajouté à votre panier.<br><br><br><br>
							</div>

							<?
							if(isset($_SESSION['catalogue']['errors']) && count($_SESSION['catalogue']['errors'])) {
								?>
								<table>
									<tr><td colspan="2" align="center"><b>Erreur de saisie !</b></td></tr>
									<tr><td colspan="2" height="1" bgcolor="#dddddd"></td></tr>
									<?
									foreach($_SESSION['catalogue']['errors'] as $ref => $msg) {
										echo "
											<tr height=\"25\" bgcolor=\"f8f8f8\">
												<td align=\"right\"><b>$ref&nbsp;:&nbsp;</b></td>
												<td>$msg</td>
											</tr>
											<tr><td colspan=\"2\" height=\"1\" bgcolor=\"#dddddd\"></td></tr>
										";
										unset($_SESSION['catalogue']['errors'][$ref]);
									}
									?>
								</table>
								<?
							}
							?>
						</td>
					</tr>
					<tr valign="bottom">
						<td align="center" height="10%">
							<table cellpadding="0" cellspacing="0" width="100%">
								<tr>
									<td width="100%">
										<div id="erreur" style="visibility:hidden;display:none;"></div>
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr valign="bottom">
			<td align="center" width="100%">
				<div id="div_artRempl" style="padding: 20px 0;"></div>
			</td>
		</tr>
		<tr>
			<td align="center">
				<div id="div_panier_sr"></div>
			</td>
		</tr>
	</table>

	<div id="raccourci">
	    <table width="100%" cellpadding="20" cellspacing="0">
	        <tr>
	            <?php if ($oCatalogue->getParams('saisie_rapide')): ?>
	            <td id="espace_raccourci">
	                <a href="/index.php?op=saisierapide"><img border="0" alt="Saisie rapide" src="/modules/catalogue/img/saisie_rapide.png" /><p>&nbsp;Saisie rapide</p></a>
	            </td>
	            <?php endif ?>
	            <?php if ($oCatalogue->getParams('panier_type')): ?>
	            <td id="espace_raccourci">
	                <a href="/index.php?op=panierstype"><img border="0" alt="Paniers types" src="/modules/catalogue/img/paniers_types.png" /><p>&nbsp;Paniers types</p></a>
	            </td>
	            <?php endif ?>
	            <?php if ($oCatalogue->getParams('wait_commandes')): ?>
				<td id="espace_raccourci">
					<a href="/index.php?op=commandes"><img border="0" alt="Commandes" src="/modules/catalogue/img/commandes.png" /><p>&nbsp;Commandes en cours</p></a>
				</td>
	            <?php endif ?>
	            <?php if ($oCatalogue->getParams('history_cmd')): ?>
				<td id="espace_raccourci">
					<a href="/index.php?op=historique"><img border="0" alt="Historique" src="/modules/catalogue/img/historique.png" /><p>&nbsp;Historique</p></a>
				</td>
	            <?php endif ?>
	            <?php if ($oCatalogue->getParams('exceptional_orders')): ?>
				<td id="espace_raccourci">
					<a href="/index.php?op=hors_catalogue"><img border="0" alt="Hors Catalogue" src="/modules/catalogue/img/hors_catalogue.png" /><p>&nbsp;Commandes exceptionnelles</p></a>
				</td>
	            <?php endif ?>
	        </tr>
	    </table>
	</div>

	<script language="JavaScript">
		document.getElementById("0").focus();
		enregistrer_saisierapide();
	</script>

	<?php
	$smarty->assign('catalogue', array('CONTENT' => ob_get_contents()));

	$page['TITLE'] = 'Saisie rapide';
	$page['META_DESCRIPTION'] = 'Commander une liste de produits en indiquant les référence';
	$page['META_KEYWORDS'] = 'Commandes, chrono, saisie, rapide';
	$page['CONTENT'] = '';

	ob_end_clean();
}
else {
	dims_404();
}
