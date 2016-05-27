<?
$us = new user();
$us->open($this->fields['id_user']);
?>
<div class="zone_recent_opportunities">
    <div class="bloc_ligne ro_avatar">
		<a href="/admin.php?submenu=<? echo _DESKTOP_V2_CONCEPTS; ?>&id=<? echo $this->fields['id']; ?>&type=<? echo dims_const::_SYSTEM_OBJECT_OPPORTUNITY; ?>&init_filters=1&from=desktop">
		<?
		if ($this->fields['banner_path'] != '' && file_exists($this->fields['banner_path']))
			echo '<img class="recent_companies_img" style="width:40px;" src="'.$this->fields['banner_path'].'" />';
		else{
			$type_action = $this->getSearchableType();
			if($type_action==search::RESULT_TYPE_OPPORTUNITY)
				echo '<img src="'._DESKTOP_TPL_PATH.'/gfx/common/opportunity40.png" />';
			else echo '<img src="'._DESKTOP_TPL_PATH.'/gfx/common/event40.png" />';
		}
		?>
		</a>
    </div>

	<div class="bloc_ligne  ro_details">
		<div class="title">
			<p>
				<a href="/admin.php?submenu=<? echo _DESKTOP_V2_CONCEPTS; ?>&id=<? echo $this->fields['id']; ?>&type=<? echo dims_const::_SYSTEM_OBJECT_OPPORTUNITY; ?>&init_filters=1&from=desktop">
					<?
					require_once  DIMS_APP_PATH.'modules/system/opportunity/class_type.php';
					$ot = new opportunity_type();
					$ot->open($this->fields['opportunity_type_id']);
					if(!$ot->isNew() && !empty($ot->fields['label'])){
						echo $ot->fields['label']. ' : ';
					}
					if(!empty($this->fields['libelle'])){
						echo $this->fields['libelle'];
					}
					else echo ucfirst($_SESSION['cste']['OPPORTUNITY']);
					?>
				</a>
				<?
				require_once DIMS_APP_PATH."modules/system/class_country.php";
				$country = new country();
				$location = explode(',',$this->fields['lieu']);
				$country->open($location[0]);
				if(!$country->isNew()){
					$imgC = $country->getFlag();
					if ($imgC != ''){
						?>
						<img src="<? echo $imgC; ?>" title="<? echo $country->fields['printable_name']; ?>" alt="<? echo $country->fields['printable_name']; ?>">
						<?
					}
				}
				?>
			</p>
		</div>
		<div class="details">
			<?php

			?>
		</div>
	</div>
	<div class="bloc_ligne calendar">
		<table class="ro_calendar">
	        <tr>
	            <td class="bloc_calendar">
                        <?
                        if ($this->fields['datejour']!='0000-00-00') {
                        ?>

	                <table cellspacing="0" cellpadding="0" width="100%">
						<?
						$date = explode('-',$this->fields['datejour']);
						?>
	                    <tbody>
	                        <tr>
	                            <td align="center" class="calendar_top"><? if ($date[1] > 0){ echo date('M',mktime(00,00,00,$date[1]))?>. <? } echo $date[0]; ?></td>
	                        </tr>
	                        <tr>
	                            <td align="center" class="calendar_bot"><? if ($date[2] > 0) echo $date[2]; else echo '-' ?></td>
	                        </tr>
	                    </tbody>
	                </table>
                        <?
                        }
                        ?>
	            </td>
	        </tr>
	    </table>
	</div>
	<div style="clear:both"></div>
	<div class="footer">
	<?php
	if(isset($this->fields['id_user']) && !empty($this->fields['id_user'])){
		$author = new user();
		$author->open($this->fields['id_user']);
		if(!$author->isNew()){

			$contact = new contact();
			$contact->open($author->fields['id_contact']);
			$a_open = '';
			$a_close = '';
			$date = '';
			$verb = $_SESSION['cste']['HAS_BEEN_CREATED_BY_FEMININ'];
			if(!$contact->isNew()){
				$a_open = '<a href="/admin.php?submenu='._DESKTOP_V2_CONCEPTS.'&id='.$contact->fields['id'].'&type='.dims_const::_SYSTEM_OBJECT_CONTACT.'&init_filters=1&from=desktop">';
				$a_close = '</a>';
				if(isset($this->fields['timestp_create']) && !empty($this->fields['timestp_create'])){
					$date = ' '.$_SESSION['cste']['SINGLE_THE'];
					$d = dims_timestamp2local($this->fields['timestp_create']);
					$date .= ' '.$d['date'].' '.strtolower($_SESSION['cste']['_DIMS_LABEL_FROM']);
					$verb = $_SESSION['cste']['HAS_BEEN_CREATED_BY_FEMININ'];
				}
			}
			echo $_SESSION['cste']['SINGLE_THIS_FEMININ'].' '.$_SESSION['cste']['OPPORTUNITY'].' '.$verb.$a_open.' <strong>'.ucfirst(substr($author->fields['firstname'],0,1)).'. '.$author->fields['lastname'].'</strong>'.$a_close;
		}
	}
	?>
	</div>
</div>
