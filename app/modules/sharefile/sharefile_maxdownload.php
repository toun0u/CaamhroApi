<div class="dims_form" style="float:left; width:50%;padding-top:20px;">
	<div style="padding:2px;">
            <span style="width:10%;display:block;float:left;">
                    <img src="/common/modules/sharefile/img/btn_access_bg.gif">
            </span>
            <span style="width:90%;display:block;float:left;font-size:20px;color:#BABABA;font-weight:bold;">
                Erreur d'acc&egrave;s
            </span>
	</div>
	<div style="padding:2px;clear:both;float:left;width:100%;font-size:14px;">
	<?
			// Information sur le partage :
				if (isset($_SESSION['currentshare']['id_share'])) {
					$share = new sharefile_share();
					$share->open($_SESSION['currentshare']['id_share']);

					echo "<p style=\"float:left;text-align:center;width:100%;\">Nom du partage demand&eacute; : <b>".$share->fields['label']."</b></p>";
				}
			?>
            <p style="float:left;text-align:center;width:100%;">
                <label>&nbsp;</label>
                <?echo "<br><img src=\"./common/img/warning.png\"><font style=\"color:#FF0000\">Nombre maximum de t&eacute;l&eacute;chargement atteint.</font>";


                ?>
            </p>

	</div>
</div>
