<?php
$scriptenv=dims::getInstance()->getScriptEnv();
$pospoint=strpos(dims::getInstance()->getScriptEnv(),"?");

if ($pospoint>0) $sep="&";
else $sep="?";

$wcelink='';


if (isset($_SERVER['QUERY_STRING'])) {
	// on a directement la valeur dans les params
	$reqserver=explode("&",$_SERVER['QUERY_STRING']);
	foreach($reqserver as $idtab=>$elem) {
		if (substr($elem,0,12)=='WCE_section_') {
			unset($reqserver[$idtab]);
		}
	}
}

$wcelink.=$scriptenv.'?'.implode("&",$reqserver);

if (isset($this->maxItem) && $this->maxItem>0) {
	?>
	<div class="right mt2 phone-hidden">
		Page :
		<?php
		for($id=1;$id<=$this->maxItem;$id++) {
			if ($this->selectedItem==$id)
				echo '<span class="btn btn-disable btn-small">'.$id.'</span> ';
			else    
				echo '<a href="'.$wcelink.'&WCE_section_'.$this->idelement.'='.$id.'" class="btn btn-small">'.$id.'</a> ';
		}
		?>
	</div>
	<?php
}
