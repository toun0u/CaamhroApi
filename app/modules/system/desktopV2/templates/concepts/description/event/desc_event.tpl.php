<div class="desc_picture_mini">
    <?
	$test_banner = $this->fields['banner_path'] != '' && file_exists($this->fields['banner_path']);
	if ($test_banner)
		echo '<img class="conc_img_event" src="'.$this->fields['banner_path'].'" />';
	else
		echo '<img class="conc_img_event" src="'._DESKTOP_TPL_PATH.'/gfx/common/event_default_search.png" />';
	?>
</div>
<div class="desc_content" <? if(!$test_banner) echo 'style="width:75%;"'; ?>>
    <table cellspacing="0" cellpadding="3">
        <tbody>
            <tr>
                <td>
                    <h1><? echo $this->fields['libelle']; ?></h1>
                </td>
            </tr>
            <tr>
                <td>
					<!-- 8 = _DIMS_VIEW_EVENTS_DETAILS -->
					<div>
						<a href="Javascript: void(0);" onclick="javascript:document.location.href='/admin.php?dims_mainmenu=events&submenu=8&dims_desktop=block&dims_action=public&action=add_evt&id=<? echo $this->fields['id']; ?>';">
							<img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/modify.png" />
							<span><? echo $_SESSION['cste']['_DIMS_LABEL_EDIT']; ?></span>
						</a>
					</div>

                </td>
            </tr>
            <tr>
                <td>
                    &nbsp;
                </td>
            </tr>
            <tr>
                <td>
                    <? echo $this->fields['description']; ?>
                </td>
            </tr>
        </tbody>
    </table>
</div>
<?php
if ($test_banner) {
    ?>
    <div class="desc_picture_grande">
        <img style="width:245px;" src="<?php echo $this->fields['banner_path']; ?>">
    </div>
    <?
}
?>
<div class="desc_date">
	<?php
    $datedeb = explode('-',$this->fields['datejour']);
    $datefin = explode('-',$this->fields['datefin']);
	?>
    <table class="desc_date_calendar">
        <tr>
            <td class="desc_date_bloc_calendar">
                <div class="bloc_ligne calendar">
                    <table class="ro_calendar">
                        <tbody>
                            <tr>
                                <td class="bloc_calendar">
                                    <table cellspacing="0" cellpadding="0" width="100%">
                                        <tbody>
                                            <tr>
                                                <td align="center" class="calendar_top"><? if ($datedeb[1] > 0) { echo date('M',mktime(00,00,00,$datedeb[1]))?>. <? } echo $datedeb[0]; ?></td>
                                            </tr>
                                            <tr>
                                                <td align="center" class="calendar_bot"><? if ($datedeb[2] > 0) echo $datedeb[2]; else echo '-'; ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </td>
        </tr>
    </table>

    <?php
    if($this->fields['datejour'] != $this->fields['datefin'] && $this->fields['datefin']!= '0000-00-00'){
        ?>
        <table class="desc_date_calendar">
            <tr>
                <td align="center"><img src="<?php echo _DESKTOP_TPL_PATH; ?>/gfx/common/separateur_date.png" /></td>
            </tr>
            <tr>
                <td class="desc_date_bloc_calendar">
                    <div class="bloc_ligne calendar">
                        <table class="ro_calendar">
                            <tbody>
                                <tr>
                                    <td class="bloc_calendar">
                                        <table cellspacing="0" cellpadding="0" width="100%">
                                            <tbody>
                                                <tr>
                                                    <td align="center" class="calendar_top"><? if ($datefin[1] > 0) { echo date('M',mktime(00,00,00,$datefin[1]))?>. <? } echo $datefin[0]; ?></td>
                                                </tr>
                                                <tr>
                                                    <td align="center" class="calendar_bot"><? if ($datefin[2] > 0) echo $datefin[2]; else echo '-'; ?></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </td>
            </tr>
        </table>
        <?php
    }
    ?>
</div>
