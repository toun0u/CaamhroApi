<table cellspacing="0" cellpadding="3">
	<tbody>
		<tr>
			<td>
				<?
				if ($this->getPhotoWebPath(60) != '' && file_exists($this->getPhotoPath(60)))
					echo '<img class="conc_img_ct" src="'.$this->getPhotoWebPath(60).'" border="0" />';
				else
					echo '<img class="conc_img_ct" src="'._DESKTOP_TPL_PATH.'/gfx/common/company_default_search.png" border="0" />';
				?>
			</td>
			<td><? echo $this->fields['intitule']; ?></td>
		</tr>
	</tbody>
</table>