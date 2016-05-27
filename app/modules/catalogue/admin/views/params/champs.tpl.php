<div class="params_content">
	<h2><?= dims_constant::getVal('_FILTERS_&_FREE_FIELDS'); ?></h2>

	<div class="actions">
	    <a href="<?= get_path('params', 'editchamp'); ?>" >
	        <img src="<?php echo $this->getTemplateWebPath("/gfx/ajouter16.png"); ?>" title="<?= dims_constant::getVal('_NEW_FIELD'); ?>" alt="<?= dims_constant::getVal('_NEW_FIELD'); ?>" />
	        <span><?= dims_constant::getVal('_NEW_FIELD'); ?></span>
	    </a>
	</div>
	<table class="tableau">
	    <tr>
	        <td>
	            <?= dims_constant::getVal('_DIMS_LABEL_RULEFIELD'); ?>
	        </td>
	        <td>
	            <?= dims_constant::getVal('_TYPE'); ?>
	        </td>
	        <td>
	            <?= dims_constant::getVal('_RSS_LABEL_CATEGORY'); ?>
	        </td>
	        <td>
	            <?= dims_constant::getVal('_TECHNICAL'); ?>
	        </td>
	        <td>
	            <?= dims_constant::getVal('_FORMS_FILTER'); ?>
	        </td>
			<td>
				<?= dims_constant::getVal('GLOBAL_FILTER'); ?>
			</td>
	        <td style="width:75px;text-align:center;">
	            <?= dims_constant::getVal('_DIMS_ACTIONS'); ?>
	        </td>
	    </tr>
	    <?php
	    if(count($this->get('lst_champs'))){
	        $lstCateg = array(); // liste pour éviter d'ouvir autant de catégorie qu'il n'y a de champ libre
	        foreach($this->get('lst_champs') as $champ){
	            ?>
	            <tr>
	                <td>
	                    <?= $champ->fields['libelle']; ?>
	                </td>
	                <td>
	                    <?= $champ->fields['type']; ?>
	                </td>
	                <td>
	                    <?= $champ->getLabelCateg($lstCateg); ?>
	                </td>
	                <td>
	                    <?php
	                    if($champ->fields['fiche']){
	                        ?>
	                        <img src="<?= $this->getTemplateWebPath('gfx/pastille_verte12.png'); ?>" title="" alt="" />
	                        <?php
	                    }
	                    else{
	                        ?>
	                        <img src="<?= $this->getTemplateWebPath('gfx/pastille_rouge12.png'); ?>" title="" alt="" />
	                        <?php
	                    }
	                    ?>
	                </td>
	                <td>
	                    <?php
	                    if($champ->fields['filtre']){
	                        ?>
	                        <img src="<?= $this->getTemplateWebPath('gfx/pastille_verte12.png'); ?>" title="" alt="" />
	                        <?php
	                    }
	                    else{
	                        ?>
	                        <img src="<?= $this->getTemplateWebPath('gfx/pastille_rouge12.png'); ?>" title="" alt="" />
	                        <?php
	                    }
	                    ?>
	                </td>
					<td>
						<?php
						if($champ->fields['global_filter']){
							?>
							<img src="<?= $this->getTemplateWebPath('gfx/pastille_verte12.png'); ?>" title="" alt="" />
							<?php
						}
						else{
							?>
							<img src="<?= $this->getTemplateWebPath('gfx/pastille_rouge12.png'); ?>" title="" alt="" />
							<?php
						}
						?>
					</td>
	                <td>
	                    <a href="<?= get_path('params', 'editchamp', array('id'=>$champ->get('id'))); ?>" style="text-decoration:none;">
	                        <img src="<?= $this->getTemplateWebPath('gfx/edit16.png'); ?>" alt="<?= dims_constant::getVal('_DIMS_LABEL_EDIT'); ?>" title="<?= dims_constant::getVal('_DIMS_LABEL_EDIT'); ?>" />
	                    </a>
	                    <a onclick="javascript:dims_confirmlink('<?= get_path('params', 'deletechamp', array('id'=>$champ->get('id'))); ?>','<?= dims_constant::getVal('_CONFIRM_DELETE_ARTICLE'); ?>');" href="javascript:void(0);" style="text-decoration:none;">
	                        <img src="<?= $this->getTemplateWebPath('gfx/supprimer16.png'); ?>" alt="<?= dims_constant::getVal('_DELETE'); ?>" title="<?= dims_constant::getVal('_DELETE'); ?>" />
	                    </a>
	                </td>
	            </tr>
	            <?php
	        }
	    }else{
	        ?>
	        <tr>
	            <td colspan="6" style="text-align:center;">
	                <?= dims_constant::getVal('NO_RESULT'); ?>
	            </td>
	        </tr>
	        <?
	    }
	    ?>
	</table>
</div>
