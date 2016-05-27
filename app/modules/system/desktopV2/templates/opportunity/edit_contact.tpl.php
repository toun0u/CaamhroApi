<?
global $desktop;
$id_tiers = $this->getLightAttribute('id_tiers');
$type_lien = $this->getLightAttribute('type_lien');
$fonction = $this->getLightAttribute('fonction');
?>

<div class="new_company_contact">
    <div class="zone_new_company_contact">

        <table cellspacing="10" cellpadding="0">
            <tbody>
                <tr>
                    <td>
                        <span style="float:right;"><? echo $_SESSION['cste']['_DIMS_LABEL_TITLE']; ?></span>
                    </td>
                    <td>
                        <input type="text" style="width: 98%;" name="civilite" value="<? echo $this->fields['civilite']; ?>"/>
                    </td>

                </tr>

                <tr>
                    <td class="text" name="lastname">
                        <? echo $_SESSION['cste']['_DIMS_LABEL_NAME']; ?>
                    </td>
                    <td>
                        <input type="text" style="width: 98%;" name="lastname" value="<? echo $this->fields['lastname']; ?>"/>
                    </td>
                </tr>
				<tr>
                    <td class="text">
                        <? echo $_SESSION['cste']['_FIRSTNAME']; ?>
                    </td>
                    <td>
                        <input type="text" style="width: 98%;" name="firstname"  value="<? echo $this->fields['firstname']; ?>"/>
                    </td>
                </tr>
                <!--<tr>
                    <td class="text">
                        Nickname
                    </td>
                    <td>
                        <input type="text" name="nickname" style="width: 98%;" value="<? if (isset($this->fields['nickname'])) echo $this->fields['nickname']; ?>"/>
                    </td>
                </tr>-->
				<?php
				$employed = false;
				if(isset($id_tiers) && $id_tiers > 0){
					$employed = true;
				}
				else{
					?>
						<tr>
		                    <td colspan="2">
								<span class="no-result"><?php echo $_SESSION['cste']['NOT_EMPLOYED']; ?></span>
							</td>
						</tr>
					<?php
				}
				?>
                <?php
			/*
				<tr>
                    <td class="text label_disabled">
                        <? echo $_SESSION['cste']['_DIMS_LABEL_LINK_TYPE']; ?>
                    </td>
                    <td>
                        <?

                        $tabempl=array();
                        $tabempl[0]['selected']='';
                        $tabempl[0]['value']=$_SESSION['cste']['_DIMS_LABEL_EMPLOYEUR'];
                        $tabempl[0]['label']=ucfirst($_SESSION['cste']['_DIMS_LABEL_EMPLOYEUR']);
                        $tabempl[1]['selected']='';
                        $tabempl[1]['value']=$_SESSION['cste']['_DIMS_LABEL_ASSOCIE'];
                        $tabempl[1]['label']=ucfirst($_SESSION['cste']['_DIMS_LABEL_ASSOCIE']);
                        $tabempl[2]['selected']='';
                        $tabempl[2]['value']=stripslashes($_SESSION['cste']['_DIMS_LABEL_CONSADMIN']);
                        $tabempl[2]['label']=ucfirst(stripslashes($_SESSION['cste']['_DIMS_LABEL_CONSADMIN']));
                        $tabempl[3]['selected']='';
                        $tabempl[3]['value']=$_SESSION['cste']['_DIMS_LABEL_OTHER'];
                        $tabempl[3]['label']=ucfirst($_SESSION['cste']['_DIMS_LABEL_OTHER']);

						$c = new dims_constant();
						if (in_array($type_lien,$c->getAllValues('_DIMS_LABEL_EMPLOYEUR')))
                            $tabempl[0]['selected']='selected=true';
                        elseif(in_array($type_lien,$c->getAllValues('_DIMS_LABEL_ASSOCIE')))
                            $tabempl[1]['selected']='selected=true';
                        elseif (in_array($type_lien,$c->getAllValues('_DIMS_LABEL_CONSADMIN')))
							$tabempl[2]['selected']='selected=true';
						elseif (in_array($type_lien,$c->getAllValues('_DIMS_LABEL_OTHER')))
                            $tabempl[3]['selected']='selected=true';
                        ?>

                        <select style="width: 100%;" name="type_lien" class="function" <?php if(!$employed)echo 'disabled="disabled"';?>>
                        <?
                        foreach ($tabempl as $elem) {
                            echo '<option value="'.$elem['value'].'" '.$elem['selected'].'>'.$elem['label'].'</option>';
                        }

                        ?>
                        </select>

                    </td>
                </tr>
			*/
				?>
				<tr>
                    <td class="text label_disabled">
                        <? echo $_SESSION['cste']['_DIMS_LABEL_FUNCTION']; ?>
                    </td>
                    <td>
                        <input style="width: 98%;" type="text" name="function" <?php if(!$employed)echo 'disabled="disabled"';?> value="<? echo $fonction; ?>"/>
                    </td>
                </tr>
                <tr>
                    <td class="text">
                        <? echo $_SESSION['cste']['_DIMS_LABEL_EMAIL']; ?>
                    </td>
                    <td>
                        <input type="text" class="email" name="email" style="width: 98%;" value="<? echo $this->fields['email']; ?>"/>
                    </td>
                </tr>
                <tr>
                    <td class="text">
                        <? echo $_SESSION['cste']['_DIMS_LABEL_EMAIL']; ?> 2
                    </td>
                    <td>
                        <input type="text" class="email" name="email2" style="width: 98%;" value="<? echo $this->fields['email2']; ?>"/>
                    </td>
                </tr>
                <!--<tr>
                    <td class="text">
                        <? echo $_SESSION['cste']['_DIMS_LABEL_ADDRESS']; ?>
                    </td>
                    <td>
                        <input type="text" class="email" name="address" style="width: 98%;" value="<? echo $this->fields['address']; ?>"/>
                    </td>
                </tr>
                <tr>
                    <td class="text">
                        <? echo $_SESSION['cste']['_DIMS_LABEL_CP']; ?>
                    </td>
                    <td>
                        <input type="text" class="email" name="postalcode" style="width: 98%;" value="<? echo $this->fields['postalcode']; ?>"/>
                    </td>
                </tr>
                <tr>
                    <td class="text">
                        <? echo $_SESSION['cste']['_DIMS_LABEL_CITY']; ?>
                    </td>
                    <td>
                        <input type="text" class="email" name="city" style="width: 98%;" value="<? echo $this->fields['city']; ?>"/>
                    </td>
                </tr>
                <tr>
                    <td class="text">
                        <? echo $_SESSION['cste']['_PHONE']; ?>
                    </td>
                    <td>
                        <input type="text" class="email" name="phone" style="width: 98%;" value="<? echo $this->fields['phone']; ?>"/>
                    </td>
                </tr>-->
                <tr>
                    <td class="text">
                        <? echo $_SESSION['cste']['_MOBILE']; ?>
                    </td>
                    <td>
                        <input type="text" class="email" name="mobile" style="width: 98%;" value="<? echo $this->fields['mobile']; ?>"/>
                    </td>
                </tr>
                <tr>
                    <td class="text">
                        <? echo $_SESSION['cste']['_DIMS_LABEL_FAX_WORK']; ?>
                    </td>
                    <td>
                        <input type="text" class="email" name="fax" style="width: 98%;" value="<? echo $this->fields['fax']; ?>"/>
                    </td>
                </tr>

                <tr>
                    <td colspan="2">
                        <div class="zone_contact_opportunity_enregistrement">
                            <input type="button" value="<? echo$_SESSION['cste']['_DIMS_LABEL_CANCEL']; ?>" onclick="javascript:document.getElementById('detail_search_ct_<? echo $this->fields['id']; ?>').innerHTML='';" />
							<span> <? echo $_SESSION['cste']['_DIMS_OR']; ?> </span>
							<input onclick="javascript:saveOppModifyContact('detail_search_ct_',<? echo $this->fields['id']; ?>,<? echo $this->getLightAttribute('id_tiers'); ?>);" type="button" value="<?= $_SESSION['cste']['_MODIFY_AND_ADD_TO_LIST']; ?>" />
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<script type="text/javascript">
	$(document).ready(function(){
		$("td.tag_for_contact_opp select.tags").chosen({no_results_text: "<div onclick=\"javascript:addNewTag('tag_for_contact_opp');\" style=\"float:right;color:#E21C2C;cursor:pointer;\"><img style=\"float:left;\" src=\"<? echo _DESKTOP_TPL_PATH; ?>/gfx/common/add.png\" /><div style=\"float:right;margin-top:3px;\">Add it !</div></div>No results matched"})
	});
</script>
