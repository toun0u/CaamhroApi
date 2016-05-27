
<?
        $tabscriptenv = "$scriptenv?cat="._BUSINESS_CAT_INTERLOCUTEUR;
        $part = dims_load_securvalue('part',dims_const::_DIMS_NUM_INPUT,true,true);
        if(empty($part)) $part=0;
?>

		<td width="100%" style="vertical-align:top;color:rgb(153, 153, 153)">
            <? //echo $skin->open_simplebloc($_DIMS['cste']['_PROFIL'].' : ','100%');
				echo $skin->open_widgetbloc($_DIMS['cste']['_PROFIL'],'width:100%','padding-left:10px;color:#cccccc;','');
			?>
			<div id="contentdesktopcontact" style="width: 100%;" class="contentdesktop">
				<div id="vertical_container">
					<h3 class="accordion_toggle">
						<table style="width:100%;">
							<tr>
								<td align="left" width="30%">&nbsp;</td>
								<td align="left" width="30%">
									<table style="width:100%;" cellpadding="0" cellspacing="0">
										<tr>
											<td class="bgb20"><img src="<? echo $_SESSION['dims']['template_path']; ?>/media/1.gif"/></td>
											<td class="midb20">
											<? echo $_DIMS['cste']['_DIMS_PERS_IDENTITY'] ?>
											</td>
											<td class="bdb20"><img src="<? echo $_SESSION['dims']['template_path']; ?>/media/1.gif"/></td>
										</tr>
									</table>
								</td>
								<td  style="width:30%;text-align:right">&nbsp;</td>
							</tr>
						</table>
					</h3>
					<div class="accordion_content" style="background-color:transparent;">
						<form>
							<table width="100%">
                                <tr>
                                    <td width="30%">
                                        <table width="100%" style="color:#cccccc;font-size:12px;">
                                            <tr>
                                                <td align="right" width="45%">Titre 1 : </td>
                                                <td align="left" width="25%">
                                                    <select>
                                                        <option>Mr</option>
                                                        <option selected="selected">Mme</option>
                                                        <option>Melle</option>
                                                        <option>Dr</option>
                                                        <option>Cheik</option>
                                                    </select>
                                                </td>
                                                <td align="left">
                                                    <div style="float:left;position:relative;"><a href="#" onclick="affich_blocinfo('inf_titre1');"><img src="./common/img/properties.png"/></a>
                                                    <div id="inf_titre1" style="position:absolute;width:250px;height:100px;left:-10px;top:-10px;display:none;z-index:2;">
                                                        <? echo $skin->open_infobloc('', 'width:100%;', '', './common/img/no.png','21px', '21px', '-7px', '-5px', '#', 'javascript:close_blocinfo(\'inf_titre1\');', ''); ?>
                                                        <div style="padding:2px;color:#cccccc;">Cr&eacute;&eacute; le 14/02/09 par Patrick Nourrissier (LFB)</div>
                                                        <? echo $skin->close_infobloc(); ?>
                                                    </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td align="right">Nom : </td>
                                                <td align="left"><input type="text" id="nom_pers" value="Aldente"/></td>
                                                <td align="left">
                                                    <div style="position:relative;"><a href="#" onclick="affich_blocinfo('inf_nom');"><img src="./common/img/properties.png"/></a>
                                                    <div id="inf_nom" style="position:absolute;width:250px;height:100px;left:-10px;top:-10px;display:none;z-index:2;">
                                                        <? echo $skin->open_infobloc('', 'width:100%;', '', './common/img/no.png','21px', '21px', '-7px', '-5px', '#', 'javascript:close_blocinfo(\'inf_nom\');', ''); ?>
                                                        <div style="padding:2px;color:#cccccc;">Cr&eacute;&eacute; le 14/02/09 par Patrick Nourrissier (LFB)</div>
                                                        <? echo $skin->close_infobloc(); ?>
                                                    </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td align="right">Pr&eacute;nom : </td>
                                                <td align="left"><input type="text" value="Monica"/></td>
                                                <td align="left">
                                                    <div style="position:relative;"><a href="#" onclick="affich_blocinfo('inf_prenom');"><img src="./common/img/properties.png"/></a>
                                                    <div id="inf_prenom" style="position:absolute;width:250px;height:100px;left:-10px;top:-10px;display:none;z-index:2;">
                                                        <? echo $skin->open_infobloc('', 'width:100%;', '', './common/img/no.png','21px', '21px', '-7px', '-5px', '#', 'javascript:close_blocinfo(\'inf_prenom\');', ''); ?>
                                                        <div style="padding:2px;color:#cccccc;">Cr&eacute;&eacute; le 14/02/09 par Patrick Nourrissier (LFB)</div>
                                                        <? echo $skin->close_infobloc(); ?>
                                                    </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td>
                                        <table width="100%" style="color:#cccccc;font-size:12px;">
                                            <tr>
                                                <td align="right" width="45%">Sexe : </td>
                                                <td align="left" width="25%">
                                                    <select>
                                                        <option>Masculin</option>
                                                        <option selected="selected">F&eacute;minin</option>
                                                    </select>
                                                </td>
                                                <td align="left">
                                                    <div style="position:relative;"><a href="#" onclick="affich_blocinfo('inf_sexe');"><img src="./common/img/properties.png"/></a>
                                                    <div id="inf_sexe" style="position:absolute;width:250px;height:100px;left:-10px;top:-10px;display:none;z-index:2;">
                                                        <? echo $skin->open_infobloc('', 'width:100%;', '', './common/img/no.png','21px', '21px', '-7px', '-5px', '#', 'javascript:close_blocinfo(\'inf_sexe\');', ''); ?>
                                                        <div style="padding:2px;color:#cccccc;">Cr&eacute;&eacute; le 14/02/09 par Patrick Nourrissier (LFB)</div>
                                                        <? echo $skin->close_infobloc(); ?>
                                                    </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td align="right">Nationnalit&eacute; : </td>
                                                <td align="left">
                                                    <input type="text" value="Fran&ccedil;aise"/>
                                                </td>
                                                <td align="left">
                                                    <div style="position:relative;"><a href="#" onclick="affich_blocinfo('inf_nat');"><img src="./common/img/properties.png"/></a>
                                                    <div id="inf_nat" style="position:absolute;width:250px;height:100px;left:-10px;top:-10px;display:none;z-index:2;">
                                                        <? echo $skin->open_infobloc('', 'width:100%;', '', './common/img/no.png','21px', '21px', '-7px', '-5px', '#', 'javascript:close_blocinfo(\'inf_nat\');', ''); ?>
                                                        <div style="padding:2px;color:#cccccc;">Cr&eacute;&eacute; le 12/02/09 par Thierry Nourrissier (LFB)</div>
                                                        <? echo $skin->close_infobloc(); ?>
                                                    </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td align="right">Pays de r&eacute;sidence : </td>
                                                <td align="left"><input type="text" id="pays_res" value="France"/></td>
                                                <td align="left">
                                                    <div style="position:relative;"><a href="#" onclick="affich_blocinfo('inf_pays');"><img src="./common/img/properties.png"/></a>
                                                    <div id="inf_pays" style="position:absolute;width:250px;height:100px;left:-250px;top:-10px;display:none;z-index:2;">
                                                        <? echo $skin->open_infobloc('', 'width:100%;', '', './common/img/no.png','21px', '21px', '-7px', '-5px', '#', 'javascript:close_blocinfo(\'inf_pays\');', ''); ?>
                                                        <div style="padding:2px;color:#cccccc;">Cr&eacute;&eacute; le 14/02/09 par Patrick Nourrissier (LFB)</div>
                                                        <? echo $skin->close_infobloc(); ?>
                                                    </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <table width="100%" style="color:#cccccc;font-size:12px;">
                                            <tr>
                                                <td align="left" colspan="3"><b><u>Plus d'informations m&eacute;tier</u></b></td>
                                            </tr>
                                            <tr>
                                                <td align="right" width="20%">BED : </td>
                                                <td align="left" width="20%">Champ carte de voeux</td>
                                                <td align="left">
                                                    <div style="position:relative;"><a href="#" onclick="affich_blocinfo('inf_met1');"><img src="./common/img/properties.png"/></a>
                                                    <div id="inf_met1" style="position:absolute;width:250px;height:100px;left:-10px;top:-10px;display:none;z-index:2;">
                                                        <? echo $skin->open_infobloc('', 'width:100%;', '', './common/img/no.png','21px', '21px', '-7px', '-5px', '#', 'javascript:close_blocinfo(\'inf_met1\');', ''); ?>
                                                        <div style="padding:2px;color:#cccccc;">Cr&eacute;&eacute; le 14/02/09 par <a style="color:#738CAD;">Patrice Perrin (BED)</a></div>
                                                        <? echo $skin->close_infobloc(); ?>
                                                    </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td align="right" width="20%">BED : </td>
                                                <td align="left" width="20%">Champ cadeau</td>
                                                <td align="left">
                                                    <div style="position:relative;"><a href="#" onclick="affich_blocinfo('inf_met2');"><img src="./common/img/properties.png"/></a>
                                                    <div id="inf_met2" style="position:absolute;width:250px;height:100px;left:-10px;top:-10px;display:none;z-index:2;">
                                                        <? echo $skin->open_infobloc('', 'width:100%;', '', './common/img/no.png','21px', '21px', '-7px', '-5px', '#', 'javascript:close_blocinfo(\'inf_met2\');', ''); ?>
                                                        <div style="padding:2px;color:#cccccc;">Cr&eacute;&eacute; le 14/02/09 par <a style="color:#738CAD;">Patrice Perrin (BED)</a></div>
                                                        <? echo $skin->close_infobloc(); ?>
                                                    </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td align="right" width="20%">CASES : </td>
                                                <td align="left" width="20%">Champ Position</td>
                                                <td align="left">
                                                    <div style="position:relative;"><a href="#" onclick="affich_blocinfo('inf_met3');"><img src="./common/img/properties.png"/></a>
                                                    <div id="inf_met3" style="position:absolute;width:250px;height:100px;left:-10px;top:-10px;display:none;z-index:2;">
                                                        <? echo $skin->open_infobloc('', 'width:100%;', '', './common/img/no.png','21px', '21px', '-7px', '-5px', '#', 'javascript:close_blocinfo(\'inf_met3\');', ''); ?>
                                                        <div style="padding:2px;color:#cccccc;">Cr&eacute;&eacute; le 21/03/09 par <a style="color:#738CAD;">Francis Houlet (CASES)</a></div>
                                                        <? echo $skin->close_infobloc(); ?>
                                                    </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td align="right" colspan="3"><input type="button" value="<? echo $_DIMS['cste']['_MODIFY']; ?>"/></td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
						</form>
					</div>
					<h3 class="accordion_toggle">
						<table style="width:100%;">
							<tr>
								<td align="left" width="30%">&nbsp;</td>
								<td align="left" width="30%">
									<table style="width:100%;" cellpadding="0" cellspacing="0">
										<tr>
											<td class="bgb20"><img src="<? echo $_SESSION['dims']['template_path']; ?>/media/1.gif"/></td>
											<td class="midb20">
											<? echo $_DIMS['cste']['_DIMS_PERS_COORD'] ?>
											</td>
											<td class="bdb20"><img src="<? echo $_SESSION['dims']['template_path']; ?>/media/1.gif"/></td>
										</tr>
									</table>
								</td>
								<td  style="width:30%;text-align:right">&nbsp;</td>
							</tr>
						</table>
					</h3>
					<div class="accordion_content" style="background-color:transparent;">
						<form>
							<table width="100%" style="color:rgb(153, 153, 153);font-size:12px;">
								<tr>
									<td width="30%">
                                        <table width="100%" style="color:#cccccc;font-size:12px;">
                                            <tr>
                                                <td align="right" width="45%" style="vertical-align:top;">Adresse perso compl&egrave;te : </td>
                                                <td align="left" width="25%"><textarea id="adr_perso">12 avenue de Strasbourg 57000 Metz</textarea></td>
                                                <td align="left">
                                                    <div style="position:relative;"><a href="#" onclick="affich_blocinfo('inf_adr_p');"><img src="./common/img/properties.png"/></a>
                                                    <div id="inf_adr_p" style="position:absolute;width:250px;height:100px;left:-10px;top:-10px;display:none;z-index:2;">
                                                        <? echo $skin->open_infobloc('', 'width:100%;', '', './common/img/no.png','21px', '21px', '-7px', '-5px', '#', 'javascript:close_blocinfo(\'inf_adr_p\');', ''); ?>
                                                        <div style="padding:2px;color:#cccccc;">Cr&eacute;&eacute; le 14/02/09 par Patrick Nourrissier (LFB)</div>
                                                        <? echo $skin->close_infobloc(); ?>
                                                    </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td align="right">Tel bureau : </td>
                                                <td align="left"><input type="text" id="tel_bureau" value="+352-2478-8431"/></td>
                                                <td align="left">
                                                    <div style="position:relative;"><a href="#" onclick="affich_blocinfo('inf_tel_b');"><img src="./common/img/properties.png"/></a>
                                                    <div id="inf_tel_b" style="position:absolute;width:250px;height:100px;left:-10px;top:-10px;display:none;z-index:2;">
                                                        <? echo $skin->open_infobloc('', 'width:100%;', '', './common/img/no.png','21px', '21px', '-7px', '-5px', '#', 'javascript:close_blocinfo(\'inf_tel_b\');', ''); ?>
                                                        <div style="padding:2px;color:#cccccc;">Cr&eacute;&eacute; le 14/02/09 par Patrick Nourrissier (LFB)</div>
                                                        <? echo $skin->close_infobloc(); ?>
                                                    </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td align="right">Fax bureau : </td>
                                                <td align="left"><input type="text" id="fax_b" value="+352-2479-5256"/></td>
                                                <td align="left">
                                                    <div style="position:relative;"><a href="#" onclick="affich_blocinfo('inf_fax_b');"><img src="./common/img/properties.png"/></a>
                                                    <div id="inf_fax_b" style="position:absolute;width:250px;height:100px;left:-10px;top:-10px;display:none;z-index:2;">
                                                        <? echo $skin->open_infobloc('', 'width:100%;', '', './common/img/no.png','21px', '21px', '-7px', '-5px', '#', 'javascript:close_blocinfo(\'inf_fax_b\');', ''); ?>
                                                        <div style="padding:2px;color:#cccccc;">Cr&eacute;&eacute; le 14/02/09 par Patrick Nourrissier (LFB)</div>
                                                        <? echo $skin->close_infobloc(); ?>
                                                    </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td>
                                        <table width="100%" style="color:#cccccc;font-size:12px;">
                                            <tr>
                                                <td align="right" width="45%">Tel Fixe personnel : </td>
                                                <td align="left" width="25%"><input type="text" id="tel_fpers" value="+352-5412-4568"/></td>
                                                <td align="left">
                                                    <div style="position:relative;"><a href="#" onclick="affich_blocinfo('inf_tel_fpers');"><img src="./common/img/properties.png"/></a>
                                                    <div id="inf_tel_fpers" style="position:absolute;width:250px;height:100px;left:-10px;top:-10px;display:none;z-index:2;">
                                                        <? echo $skin->open_infobloc('', 'width:100%;', '', './common/img/no.png','21px', '21px', '-7px', '-5px', '#', 'javascript:close_blocinfo(\'inf_tel_fpers\');', ''); ?>
                                                        <div style="padding:2px;color:#cccccc;">Cr&eacute;&eacute; le 14/02/09 par Patrick Nourrissier (LFB)</div>
                                                        <? echo $skin->close_infobloc(); ?>
                                                    </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td align="right">Tel Port. personnel : </td>
                                                <td align="left"><input type="text" id="tel_ppers" value="+352-1354-4687"/></td>
                                                <td align="left">
                                                    <div style="position:relative;"><a href="#" onclick="affich_blocinfo('inf_tel_ppers');"><img src="./common/img/properties.png"/></a>
                                                    <div id="inf_tel_ppers" style="position:absolute;width:250px;height:100px;left:-10px;top:-10px;display:none;z-index:2;">
                                                        <? echo $skin->open_infobloc('', 'width:100%;', '', './common/img/no.png','21px', '21px', '-7px', '-5px', '#', 'javascript:close_blocinfo(\'inf_tel_ppers\');', ''); ?>
                                                        <div style="padding:2px;color:#cccccc;">Cr&eacute;&eacute; le 14/02/09 par Patrick Nourrissier (LFB)</div>
                                                        <? echo $skin->close_infobloc(); ?>
                                                    </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td align="right">Mail : </td>
                                                <td align="left"><input type="text" id="mail" value="maldente@societe.com"/></td>
                                                <td align="left">
                                                    <div style="position:relative;"><a href="#" onclick="affich_blocinfo('inf_mail');"><img src="./common/img/properties.png"/></a>
                                                    <div id="inf_mail" style="position:absolute;width:250px;height:100px;left:-10px;top:-10px;display:none;z-index:2;">
                                                        <? echo $skin->open_infobloc('', 'width:100%;', '', './common/img/no.png','21px', '21px', '-7px', '-5px', '#', 'javascript:close_blocinfo(\'inf_mail\');', ''); ?>
                                                        <div style="padding:2px;color:#cccccc;">Cr&eacute;&eacute; le 14/02/09 par Patrick Nourrissier (LFB)</div>
                                                        <? echo $skin->close_infobloc(); ?>
                                                    </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <table width="100%" style="color:#cccccc;font-size:12px;">
                                            <tr>
                                                <td align="right" colspan="3"><input type="button" value="<? echo $_DIMS['cste']['_MODIFY']; ?>"/></td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
							</table>
						</form>
					</div>
					<h3 class="accordion_toggle">
						<table style="width:100%;" style="color:rgb(153, 153, 153);font-size:12px;">
							<tr>
								<td align="left" width="30%">&nbsp;</td>
								<td align="left" width="30%">
									<table style="width:100%;" cellpadding="0" cellspacing="0">
										<tr>
											<td class="bgb20"><img src="<? echo $_SESSION['dims']['template_path']; ?>/media/1.gif"/></td>
											<td class="midb20">
											<? echo $_DIMS['cste']['_DIMS_PERS_INFOS'] ?>
											</td>
											<td class="bdb20"><img src="<? echo $_SESSION['dims']['template_path']; ?>/media/1.gif"/></td>
										</tr>
									</table>
								</td>
								<td  style="width:30%;text-align:right">&nbsp;</td>
							</tr>
						</table>
					</h3>
					<div class="accordion_content" style="background-color:transparent;">
						<form>
							<table width="100%" style="color:rgb(153, 153, 153);font-size:12px;">
								<tr>
									<td width="50%">
                                        <table width="100%" style="color:#cccccc;font-size:12px;">
                                            <tr>
                                                <td align="right" width="45%">D&eacute;j&agrave; Visit&eacute; Luxembourg : </td>
                                                <td align="left" width="25%">
                                                    <select>
                                                        <option>oui</option>
                                                        <option selected="selected">non</option>
                                                    </select>
                                                </td>
                                                <td align="left">
                                                    <div style="position:relative;"><a href="#" onclick="affich_blocinfo('inf_vulux');"><img src="./common/img/properties.png"/></a>
                                                    <div id="inf_vulux" style="position:absolute;width:250px;height:100px;left:-250px;top:-10px;display:none;z-index:2;">
                                                        <? echo $skin->open_infobloc('', 'width:100%;', '', './common/img/no.png','21px', '21px', '-7px', '-5px', '#', 'javascript:close_blocinfo(\'inf_vulux\');', ''); ?>
                                                        <div style="padding:2px;color:#cccccc;">Cr&eacute;&eacute; le 14/02/09 par Patrick Nourrissier (LFB)</div>
                                                        <? echo $skin->close_infobloc(); ?>
                                                    </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td align="right">Secteur d'activit&eacute; entreprise employeur : </td>
                                                <td align="left">
                                                    <select>
                                                        <option>Services</option>
                                                        <option selected="selected">Banque</option>
                                                        <option>Informatique</option>
                                                    </select>
                                                </td>
                                                <td align="left">
                                                    <div style="position:relative;"><a href="#" onclick="affich_blocinfo('inf_sect_act');"><img src="./common/img/properties.png"/></a>
                                                    <div id="inf_sect_act" style="position:absolute;width:250px;height:100px;left:-250px;top:-10px;display:none;z-index:2;">
                                                        <? echo $skin->open_infobloc('', 'width:100%;', '', './common/img/no.png','21px', '21px', '-7px', '-5px', '#', 'javascript:close_blocinfo(\'inf_sect_act\');', ''); ?>
                                                        <div style="padding:2px;color:#cccccc;">Cr&eacute;&eacute; le 14/02/09 par Patrick Nourrissier (LFB)</div>
                                                        <? echo $skin->close_infobloc(); ?>
                                                    </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td align="right">Importance du contact : </td>
                                                <td align="left">
                                                    <select>
                                                        <option>normal</option>
                                                        <option selected="selected">VIP</option>
                                                        <option>top</option>
                                                    </select>
                                                </td>
                                                <td align="left">
                                                    <div style="position:relative;"><a href="#" onclick="affich_blocinfo('inf_vip');"><img src="./common/img/properties.png"/></a>
                                                    <div id="inf_vip" style="position:absolute;width:250px;height:100px;left:-250px;top:-10px;display:none;z-index:2;">
                                                        <? echo $skin->open_infobloc('', 'width:100%;', '', './common/img/no.png','21px', '21px', '-7px', '-5px', '#', 'javascript:close_blocinfo(\'inf_vip\');', ''); ?>
                                                        <div style="padding:2px;color:#cccccc;">Cr&eacute;&eacute; le 14/02/09 par Patrick Nourrissier (LFB)</div>
                                                        <? echo $skin->close_infobloc(); ?>
                                                    </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td width="50%">
                                        &nbsp;
                                    </td>
                                </tr>
								<tr>
									<td align="right" colspan="3"><input type="button" value="<? echo $_DIMS['cste']['_MODIFY']; ?>"/></td>
								</tr>
							</table>
						</form>
					</div>
				</div>
			</div>
            <? echo $skin->close_widgetbloc(); ?>
        </td>
	</tr>
</table>