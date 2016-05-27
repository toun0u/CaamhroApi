<?php

$properties = $this->get('properties');
$lstfiles = $this->get('files');
$share = $this->get('share');
$user = $this->get('user');

$datecreate= dims_timestamp2local($share['timestp_create']);
?>

<div style="clear:both;float:left;width:100%;border:0px;border-bottom:1px;border-color:#dadada;border-style:solid;font-size:12px;">
	<div class="share_bg">
		<span style="font-weight:bold;float:left;margin-left:65px;width:140px;margin-top:20px">
			<?= $share['label']; ?>
		</span>
	</div>
	<div class="share_bg_right" style="background-color:#CCCCCC">
		<table style="width:100%">
			<tr>
				<td style="width:60%">
					<span style="margin-left:10px;float:left;">
						<font style="float:left;width:120px;text-decoration: underline;">Dossier cr&eacute;&eacute; le : </font>
						<?= $datecreate['date']; ?>
					</span>
					<span style="margin-left:10px;clear:both;float:left;">
						<font style="float:left;width:120px;;text-decoration: underline;">Par : </font>
						<font style="float:left;">
							<?= $user['firstname']." ".$user['lastname']; ?>
						</font>
					</span>
					<span style="margin-left:10px;clear:both;float:left;">
						<font style="float:left;width:120px;text-decoration: underline;">Contenant : </font>
						<?= $properties['nbfiles']; ?> fichier(s)
					</span>
					<span style="margin-left:10px;clear:both;float:left;">
						<font style="float:left;width:120px;text-decoration: underline;">Poids total : </font>
						<?= sprintf("%.02f",$properties['size']/1048576); ?> Mo
					</span>
				</td>
				<td style="valign:top;">
					<span style="float:left;">Descriptif : <br><br>
						<?= $share['description']; ?>
					</span>
				</td>
			</tr>
		</table>
	</div>
</div>
<div style="clear:both;float:left;padding-top:10px;width:100%;padding-bottom:20px;">
	<?php

	if (!empty($lstfiles)) {
		foreach ($lstfiles as $file) {

			echo "<div style=\"float:left;width:140px;cursor: default;padding-left:10px;text-align:center;\" onmouseout=\"javascript:this.style.cursor='default';\" onmouseover=\"javascript:this.style.cursor='pointer';\" style=\"cursor: default;\">
					<a style=\"text-decoration:none;\" href=".$file['downloadlink']." title=\"Voir le document.\">";

			switch ($file['extension']) {
				case "ppt":
					$namefile="ppt.png";
					break;
				case "pdf":
					$namefile="pdf.png";
					break;
				case "wav":
					$namefile="sound.png";
					break;
				case "doc":
				case "docx":
				case "odt":
					$namefile="doc.png";
					break;
				case "xls":
					$namefile="xls.png";
					break;
				case "jpg":
				case "png":
				case "bmp":
				case "gif":
				case "xcf":
				case "psd":
					$namefile="img.png";
					break;
				case "avi":
				case "mpg":
				case "mpeg":
				case "mpeg4":
					$namefile="video.png";
					break;
				default :
					$namefile="file.png";
					break;
			}

			if (strlen($file['name'])>15) {
				$title=dims_strcut($file['name'],15);
				$title.=" .".$file['extension'];
			}
			else $title=$file['name'];

			echo "<img style=\"\" src=\"./common/modules/sharefile/img/".$namefile."\" border=\"0\">
				<br><font style=\"font-size:12px;\">".$title."<br>".sprintf("%.02f",$file['size']/1048576)." Mo</font></a></div>";
		}
		echo "</span>";
	}
	?>
</div>
<div style="clear:both;float:left;padding-top:10px;width:100%;padding-bottom:20px;">
	<?
	$zip_path = realpath('./').'/data/sharefiles';
	$zip_file=$zip_path."/".$share['id']."_".$share['timestp_create'].".zip";
	$zip_file_web="./data/sharefiles/".$share['id']."_".$share['timestp_create'].".zip";
	if (file_exists($zip_file)) {
		$size=filesize($zip_file);
		echo "<div style=\"width:100%;text-align:center;\"><a href=\"".$zip_file_web."\"><img style=\"\" src=\"./common/modules/sharefile/img/archive.png\" border=\"0\">
			   <br><font style=\"font-size:12px;\">Fichier zip<br>".sprintf("%.02f",$size/1048576)." Mo</font></a>";
	}
	?>
</div>
