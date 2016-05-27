
<script type="text/javascript">
<?

echo "clearTimeout(timerdisplayresult);timerdisplayresult = setTimeout(\"";

foreach ($repartition as $iso => $tab) {
	$percent = $tab['total'];
	if($percent <= 25){
		$color = '#FEC6C6';
	}
	else if($percent > 25 && $percent <= 50) {
		$color = '#FE9E9E';
	}
	else if($percent > 50 && $percent <= 75) {
		$color = '#FE4D4D';
	}
	else {
		$color = '#DF1D31';
	}

	 echo "selectWorld('".strtolower($iso)."','".$color."','".$tab['label']."',".$tab['id'].");";
}
echo "\",400);";
?>
</script>
