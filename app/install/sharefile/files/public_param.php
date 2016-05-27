<form name="form_param" method="post" action="<? echo dims_urlencode($url->addParams(array('op' => 'sharefile', 'action' => 'sharefile_saveparam'))); ?>">
<div class="dims_form" style="float:left; width:80%;padding-top:20px;">
	<div style="padding:2px;clear:both;float:left;width:100%;">
		<p>
			<label>Code unique</label>
			<select name="share_param_uniquecode" value="1"  tabindex="1">
				<option value="1" <? if ($sharefile_param->fields['uniquecode']) echo "selected";  ?>>Oui</option>
				<option value="0"<? if ($sharefile_param->fields['uniquecode']==0) echo "selected";  ?>>Non</option>
			</select>
		</p>
		<p>
			<label>Nombre de caractères</label>
			<select name="share_param_nbcar" value="1"  tabindex="3">
				<option value="3" <? if ($sharefile_param->fields['nbcar']==3) echo "selected";  ?>>3</option>
				<option value="4" <? if ($sharefile_param->fields['nbcar']==4) echo "selected";  ?>>4</option>
				<option value="5" <? if ($sharefile_param->fields['nbcar']==5) echo "selected";  ?>>5</option>
				<option value="6" <? if ($sharefile_param->fields['nbcar']==6) echo "selected";  ?>>6</option>
				<option value="7" <? if ($sharefile_param->fields['nbcar']==7) echo "selected";  ?>>7</option>
				<option value="8" <? if ($sharefile_param->fields['nbcar']==8) echo "selected";  ?>>8</option>
				<option value="9" <? if ($sharefile_param->fields['nbcar']==9) echo "selected";  ?>>9</option>
				<option value="10" <? if ($sharefile_param->fields['nbcar']==10) echo "selected";  ?>>10</option>
			</select>
		</p>
		<p>
			<label>Nombre de jours maximum</label>
				<select name="share_param_nbdays" value="1"  tabindex="4">
				<option value="7" <? if ($sharefile_param->fields['nbdays']==7) echo "selected";  ?>>7 jours</option>
				<option value="15" <? if ($sharefile_param->fields['nbdays']==15) echo "selected";  ?>>15 jours</option>
				<option value="30" <? if ($sharefile_param->fields['nbdays']==30) echo "selected";  ?>>30 jours</option>
				<option value="60" <? if ($sharefile_param->fields['nbdays']==60) echo "selected";  ?>>60 jours</option>
				<option value="90" <? if ($sharefile_param->fields['nbdays']==90) echo "selected";  ?>>90 jours</option>
				<option value="120" <? if ($sharefile_param->fields['nbdays']==120) echo "selected";  ?>>120 jours</option>
				<option value="180" <? if ($sharefile_param->fields['nbdays']==180) echo "selected";  ?>>180 jours</option>
				<option value="365" <? if ($sharefile_param->fields['nbdays']==365) echo "selected";  ?>>365 jours</option>
			</select>
		</p>
		<p>
			<label>Nombre de t&eacute;l&eacute;chargement maximum</label>
			<select name="share_param_nbdownload" value="1"  tabindex="5">
				<?

				for($i=1;$i<=20;$i++) {
					if ($sharefile_param->fields['nbdownload']==$i) $sel="selected";
					else $sel="";
					echo "<option value=\"".$i."\" $sel>".$i."</option>";
				}

				?>
			</select>
		</p>
		<p>
			Tags disponibles : {FIRSTNAME} {LASTNAME} {EMAIL} {URL} {NBFILES} {DATE_END} {SHARE_NAME} {DATE_CREATE} {CODE} {WEIGHT}
		</p>
		<p>
			<label>Titre du message envoy&eacute; par email</label>
			<input class="text" type="text" name="share_param_email_title" value="<? echo $sharefile_param->fields['email_title']; ?>">
		</p>
		<p>
			<label>Contenu du message envoy&eacute; par email</label>
			<?
			include_once('./FCKeditor/fckeditor.php') ;

			$oFCKeditor = new FCKeditor('fck_share_param_send_message') ;

			$basepath = dirname($_SERVER['HTTP_REFERER']); // compatible with proxy rewrite
			if ($basepath == '/') $basepath = '';

			$oFCKeditor->BasePath	= "{$basepath}/FCKeditor/";

			// default value
			$oFCKeditor->Value = $sharefile_param->fields['send_message'];

			// width & height
			$oFCKeditor->Width='100%';
			$oFCKeditor->Height='160';

			$oFCKeditor->Config['CustomConfigurationsPath'] = "{$basepath}/modules/system/fckeditor/fckconfig.js"  ;
			//$oFCKeditor->Config['ToolbarLocation'] = 'Out:xToolbar' ;
			$oFCKeditor->Config['SkinPath'] = "{$basepath}/modules/system/fckeditor/skins/default/" ;
			$oFCKeditor->Config['EditorAreaCSS'] = "{$basepath}/modules/system/fckeditor/fck_editorarea.css" ;
			$oFCKeditor->Config['BaseHref'] = "http://{$_SERVER['HTTP_HOST']}{$basepath}/";
			$oFCKeditor->Create('FCKeditor_1') ;
?>
		</p>
		<p>
			<label>Message de confirmation d'envoi</label>
						<?
			include_once('./FCKeditor/fckeditor.php') ;

			$oFCKeditor = new FCKeditor('fck_share_param_message') ;

			$basepath = dirname($_SERVER['HTTP_REFERER']); // compatible with proxy rewrite
			if ($basepath == '/') $basepath = '';

			$oFCKeditor->BasePath	= "{$basepath}/FCKeditor/";

			// default value
			$oFCKeditor->Value = $sharefile_param->fields['message'];

			// width & height
			$oFCKeditor->Width='100%';
			$oFCKeditor->Height='160';

			$oFCKeditor->Config['CustomConfigurationsPath'] = "{$basepath}/modules/system/fckeditor/fckconfig.js"  ;
			//$oFCKeditor->Config['ToolbarLocation'] = 'Out:xToolbar' ;
			$oFCKeditor->Config['SkinPath'] = "{$basepath}/modules/system/fckeditor/skins/default/" ;
			$oFCKeditor->Config['EditorAreaCSS'] = "{$basepath}/modules/system/fckeditor/fck_editorarea.css" ;
			$oFCKeditor->Config['BaseHref'] = "http://{$_SERVER['HTTP_HOST']}{$basepath}/";
			$oFCKeditor->Create('FCKeditor_1') ;
?>
		</p>
	</div>
	<div id="sharefile_button" style="padding:2px;clear:both;float:left;width:100%;">
		<span style="width:50%;display:block;float:left;">&nbsp;</span>
		<span style="width:50%;display:block;float:left;"><a style="text-decoration:none;" href="javascript:void(0);" onclick="javascript:document.form_param.submit();"><img style="padding-left:50px;border:0px;" src="./common/img/save.gif" alt="<? echo $_DIMS['cste']['_DIMS_SAVE']; ?>"><? echo $_DIMS['cste']['_DIMS_SAVE']; ?></a></span>
	</div>
</div>
</form>
