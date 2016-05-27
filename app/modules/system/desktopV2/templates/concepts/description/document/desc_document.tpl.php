<div class="desc_picture_mini">
    <?
	echo '<img class="conc_img_event" src="'.$this->getFileIcon(64).'" />';
	?>
</div>
<div class="desc_content">
    <table cellspacing="0" cellpadding="3">
        <tbody>
            <tr>
                <td>
                    <h1><? echo $this->fields['name']; ?></h1>
                </td>
            </tr>
            <tr>
                <td><?php echo $this->display(DIMS_APP_PATH.'modules/doc/templates/desktopv2_inlist/desktopv2_file_inlist.php');?></td>
            </tr>
        </tbody>
    </table>
</div>
