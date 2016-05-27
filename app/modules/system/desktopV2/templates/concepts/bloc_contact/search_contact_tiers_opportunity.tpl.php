<?
if (isset($this->contacts)){
?>
<div id="search_<? echo $this->fields['id']; ?>">
	<div class="existing_contact">
		<?
		foreach($this->contacts as $ct)
			$ct->display(_DESKTOP_TPL_LOCAL_PATH.'/concepts/bloc_contact/search_contact_opportunity.tpl.php');
		?>
	</div>
</div>
<script type="text/javascript">
	adapteImage('div#search_<? echo $this->fields['id']; ?> img.opportunity_img_ct',true,36);
	adapteImage('div#search_<? echo $this->fields['id']; ?> img.opportunity_img_tiers',true,24);
</script>
<?
}
?>
