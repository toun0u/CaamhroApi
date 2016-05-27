<div class="wce_tree_node" style="<? echo ($this->getLightAttribute('opened'))?"font-weight:bold;":"font-weight:normal;"; ?>">
	<img style="margin-right: 4px;" src="<? echo module_wce::getTemplateWebPath('gfx/racine16.png'); ?>" />
	<a title="<? echo $this->fields['label']; ?>" href="<? echo module_wce::get_url($_SESSION['dims']['wce']['sub']).((isset($sub))?"&sub=$sub".((isset($action))?"&action=$action":""):"")."&headingid=".$this->fields['id']; ?>">
		<? echo $this->fields['label']; ?>
	</a>
	<div style="float:right;margin-left:5px;">
		<?
		foreach($this->getListLang() as $lang){
			?>
			<img src="<? echo $lang->getFlag(); ?>" alt="<? echo $lang->getLabel(); ?>" title="<? echo $lang->getLabel(); ?>" />
			<?
		}
		?>
	</div>
</div>
