<?php

function dims_clipboard()  {
	if (file_exists(DIMS_APP_PATH.$_SESSION['dims']['template_path']."/class_skin.php")) require_once(DIMS_APP_PATH.$_SESSION['dims']['template_path']."/class_skin.php");
	$skin=new skin();
	$res= $skin->open_simplebloc("");
	$res.="<div id=\"clipboard\">
			<div style=\"float:left; line-height:20px; height:20px; padding-left:5px\">
				<a  href=\"#\" onClick=\"javascript:clipboard_copy(event);\"><img style=\"vertical-align:middle;\" src=\"./common/img/clipboard/add.png\" alt=\"add\"></a>
			 </div>
			<div style=\"float:left; line-height:20px; height:20px; padding-left:5px\">
				<a href=\"#\" onClick=\"javascript:dims_show_clipboard(event);\"><img style=\"vertical-align:middle;\" src=\"./common/img/clipboard/view.png\" alt=\"view\"></a>
			</div>
			<div style=\"float:left; line-height:20px; height:20px; padding-left:5px\">
				<a href=\"#\" onClick=\"javascript:dims_hidepopup('dims_clipboard');if (elemselection) elemselection.focus();\"><img style=\"vertical-align:middle;\" src=\"./common/img/clipboard/close.png\" alt=\"view\"></a>
			</div>
		</div>";
	$res.=$skin->close_simplebloc("");
	return ($res);
}

function dims_clipboardGet()
{
	require_once(DIMS_APP_PATH.$_SESSION['dims']['template_path']."/class_skin.php");
	if (isset($_SESSION['dims']['clipboard']) && sizeof($_SESSION['dims']['clipboard']) > 0) {
		$skin=new skin();
		$res= $skin->open_simplebloc("");
		$res.="<div id=\"clipboard\">
				<div style=\"float:left; line-height:20px; height:20px; padding-left:5px\">
					<a  href=\"#\" onClick=\"javascript:clipboard_get(event);\"><img style=\"vertical-align:middle;\" src=\"./common/img/clipboard/get.png\" alt=\"get\"></a>
				 </div>
				<div style=\"float:left; line-height:20px; height:20px; padding-left:5px\">
					<a href=\"#\" onClick=\"javascript:dims_show_clipboard(event);\"><img style=\"vertical-align:middle;\" src=\"./common/img/clipboard/view.png\" alt=\"view\"></a>
				</div>
				<div style=\"float:left; line-height:20px; height:20px; padding-left:5px\">
					<a href=\"#\" onClick=\"javascript:dims_hidepopup('dims_clipboard');if (elemselection) elemselection.focus();\"><img style=\"vertical-align:middle;\" src=\"./common/img/clipboard/close.png\" alt=\"view\"></a>
				</div>
			</div>";
		$res.=$skin->close_simplebloc("");
	}
	else $res="<script language=\"javascript\">dims_hidepopup('dims_clipboard');</script>";

	return ($res);
}

function dims_show_clipboard_content() {
	require_once(DIMS_APP_PATH.$_SESSION['dims']['template_path']."/class_skin.php");
	$skin=new skin();
	echo $skin->open_simplebloc("");
	echo "<div id=\"clipboard\">";
		echo "<div style=\"text-align:right;padding: 2px;width:100%;float:left;text-align:center;\">
		<input type=\"button\" onclick=\"dims_getelem('dims_clipboard').style.visibility='hidden';\" value=\"Fermer\" class=\"flatbutton\"/>
		</div>";
	if (sizeof($_SESSION['dims']['clipboard']) > 0)
	{
		foreach($_SESSION['dims']['clipboard'] as $key => $copy)
		{
			$copy=dims_strcut($copy,250);
			?>
			<div style="width:370px;float:left;">
			<textarea  id="sel<?php echo $key; ?>" class="copy" onClick="javascript:clipboard_copyto(this.id);" value="<?php echo $copy;?>"><?php echo $copy;?>
			</textarea>
			</div><div style="width:16px;float:right;">
			<a href="#" onClick="javascript:clipboard_delete('<?php echo $key; ?>');"><img src="./common/img/clipboard/delete.png" alt="del"/></a>
			</div>
			<?php

		}

	} else echo "Votre presse-papier est vide.";


	echo $skin->close_simplebloc("");
}

function dims_get_all_clipboard_content() {
	$result="";

	if (sizeof($_SESSION['dims']['clipboard']) > 0) {
		foreach($_SESSION['dims']['clipboard'] as $key => $copy) {
			if ($result!="") $result.="\n";
			$result.=$copy;
		}
	}
	return $result;
}
?>
