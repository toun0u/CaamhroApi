<?
$db = dims::getInstance()->getDb();
$sql = "SELECT 	COUNT(position) as maxi
		FROM 	dims_mod_wce_article_block
		WHERE 	id_article = :id_article
		AND 	section = :section";

$respos=$db->query($sql,array(':id_article'=>array('value'=>$this->fields['id_article'],'type'=>PDO::PARAM_INT),
							':section'=>array('value'=>$this->fields['section'],'type'=>PDO::PARAM_INT)));

if ($db->numrows($respos)>0) {
	$fresu=$db->fetchrow($respos);
	if ($fresu['maxi']=="") $maxi=0;
	else $maxi=$fresu['maxi'];
}
else $maxi=0;

if ( $this->isNew()) {
	$maxi++;
	$this->fields['position']=$maxi;
    $this->fields['level']=1;
}
?>
<table style="width:100%">
	<tr>
		<td style="width:40%;height:30px;">Titre</td>
		<td>
			<input class="text" style="width:350px;" type="text" name="wce_block_title" value="<?
			if ($this->fields['title']=="") $this->fields['title']="Bloc ".$maxi;
			echo str_replace('"','&quot;',$this->fields['title']);
			?>" tabindex="1" />
		</td>
	</tr>
    <tr>
		<td style="width:40%;height:30px;"></td>
		<td><div style="display:none;visibility: hidden;">
		<?
                // construction des sections

		echo "<select name=\"block_position\" tabindex=\"2\" >";

		if ($this->fields['position']=="") $this->fields['position']=$maxi;

		for ($indi=1;$indi<=$maxi;$indi++) {
			if ($this->fields['position']==$indi) $selected="selected";
			else $selected="";

			echo "<option ".$selected." value=\"".$indi."\">".$indi."</option>";
		}

		echo "</select>";
		?>
                    </div></td>
	</tr>

	<tr>
		<td style="width:40%;height:30px;">Afficher le titre dans le contenu</td>
		<td>
			<input type="checkbox" name="wce_block_display_title" id="wce_block_display_title" style="width:14px;" value="1" <? if ($this->fields['display_title']) echo 'checked'; ?> tabindex="3" />
		</td>
	</tr>
	<tr>
		<td style="width:40%;height:30px;"><? echo $_SESSION['cste']['_FORMS_MODEL'];?> :</td>
		<td>

		<select name="wce_block_id_model" id="wce_block_id_model" class="select" tabindex="4">

		<?
		/*<option <? echo ($this->fields['id_model'] == 0) ? 'selected' : ''; ?> value=""><? echo "aucun"; ?></option>
		 *
		 */
		$wce_site = new wce_site(dims::getInstance()->db,$_SESSION['dims']['moduleid']);
		$wce_site->loadBlockModels();
		foreach($wce_site->getBlockModels() as $key => $model) {
			?>
			<option <? echo ($this->fields['id_model'] == $model['id']) ? 'selected' : ''; ?> value="<? echo $model['id']; ?>"><? echo $model['label']; ?></option>
			<?
		}
		?>
		</select>
		</td>
	</tr>
	<?php
	/* Si le bloc est au début il n'est pas logique de donner
	 * La possibilité de faire un page_break avant le premier
	 * contenu
	 * */
	if($this->fields['position'] > 1) {
		?>
		<tr>
			<td style="width:40%;height:30px;">Page Break :</td>
			<td>
				<input name="wce_block_page_break" value="1" type="checkbox" <?php if($this->fields['page_break']) echo 'checked="checked"'; ?> />
			</td>
		</tr>
		<?php
	}
	?>
</table>
