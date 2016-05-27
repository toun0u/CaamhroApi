<div id="col_<?php echo $browser->getDepth(); ?>" class="browser_column">

<ul>
	<?php
	$pos = 0;
	foreach($browser->getChildren() as $child){
		$data = $child->getData();
		//dims_print_r($data);
		if($data['key'] != 'GHOST'){
		?>
			<li class="elem <?php echo ($child->isSelected())?'selected':'';?>" rel="<?php echo $pos;?>">
				<table>
					<tr>
						<td class="icone">
							<a href="admin.php?level=<?php echo $browser->getDepth();?>&selitem=<?php echo $data['key'];?>">
								<img src="<? echo _DESKTOP_TPL_PATH.'/gfx/'.(($data['type']=='folder')?'common/open_record.png':'external/document_vertical16.png'); ?>">
							</a>
						</td>
						<td>
							<a href="admin.php?level=<?php echo $browser->getDepth();?>&selitem=<?php echo $data['key'];?>">
							<?php
							echo dims_strcut($data['libelle'],23);
							?>
							</a>
						</td>
						<td class="arrow">
							<?php
							if($child->hasChildren()){
								?>
								<div class="has_children">
									<a href="admin.php?level=<?php echo $browser->getDepth();?>&selitem=<?php echo $data['key'];?>">
										<img src="<? echo _DESKTOP_TPL_PATH.'/gfx/external/puce_ged_'.(($child->isSelected())?'':'not').'selected.png'; ?>"/>
									</a>
								</div>
								<?php
							}
							?>
						</td>
					</tr>
				</table>
			</li>
		<?php
		}
		$pos ++;
	}
	?>
</ul>
</div>

<?php
//partie récursive
$selected_child = $browser->getSelectedChild();
if(!is_null($selected_child)){
	$selected_child->display();
}
?>
