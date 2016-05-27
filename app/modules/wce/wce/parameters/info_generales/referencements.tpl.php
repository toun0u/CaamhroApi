<div class="title_h3">
    <h3><? echo $_SESSION['cste']['_WCE_PAGE_REFER']; ?> - <? echo $_SESSION['cste']['_DIMS_LABEL_META']; ?></h3>
</div>
<div class="lien_modification">
    <a href="<? echo module_wce::get_url(module_wce::_SUB_PARAM)."&sub=".module_wce::_PARAM_INFOS."&action=".module_wce::_PARAM_INFOS_EDIT_REF; ?>">
		<? echo $_SESSION['cste']['_CHANGE_REFERENCING_INFORMATION']; ?>
		<img src="<? echo module_wce::getTemplateWebPath('/gfx/icon_mini_modif.png'); ?>" />
	</a>
</div>
<table class="table_referencement" cellspacing="0" cellpadding="0" border="1" style="width: 100%; border-collapse: collapse;">
    <tr>
        <td class="title_table" style="width:150px">
            <? echo $_SESSION['cste']['_DIMS_LABEL_TITLE']; ?>
        </td>
        <td>
            <? echo $this->fields['title']; ?>
        </td>
    </tr>
    <tr class="table_ligne1">
        <td class="title_table">
            <? echo $_SESSION['cste']['_DIMS_LABEL_DESCRIPTION']; ?>
        </td>
        <td>
			<? echo $this->fields['meta_description']; ?>
        </td>
    </tr>
    <tr>
        <td class="title_table">
            <? echo $_SESSION['cste']['_DIMS_LABEL_KEYWORDS']; ?>
        </td>
        <td>
            <? echo $this->fields['meta_keywords']; ?>
        </td>
    </tr>
    <tr class="table_ligne1">
        <td class="title_table">
            <? echo $_SESSION['cste']['_AUTHOR']; ?>
        </td>
        <td>
            <? echo $this->fields['meta_author']; ?>
        </td>
    </tr>
    <tr>
        <td class="title_table">
            Copyright
        </td>
        <td>
            <? echo $this->fields['meta_copyright']; ?>
        </td>
    </tr>
    <tr class="table_ligne1">
        <td class="title_table">
            Robots
        </td>
        <td>
			<? echo $this->fields['meta_robots']; ?>
        </td>
    </tr>
    <tr>
        <td class="title_table">
            Favicon
        </td>
        <td>
            <img style="height:16px;" src="<? echo $this->getFrontFavicon(); ?>">
        </td>
    </tr>
	<tr>
        <td class="title_table">
            Twitter
        </td>
        <td>
			<?
			if (!empty($this->fields['twitter'])){
			?>
            <a target="_blank" href="<? echo dims_const::_RS_TWITTER.$this->fields['twitter']; ?>">
				@<? echo $this->fields['twitter']; ?>
			</a>
			<?
			}
			?>
        </td>
    </tr>
	<tr class="table_ligne1">
        <td class="title_table">
            Facebook
        </td>
        <td>
			<?
			if (!empty($this->fields['facebook'])){
			?>
            <a target="_blank" href="<? echo dims_const::_RS_FACEBOOK.$this->fields['facebook']; ?>">
				<? echo dims_const::_RS_FACEBOOK.$this->fields['facebook']; ?>
			</a>
			<?
			}
			?>
        </td>
    </tr>
    <tr>
        <td class="title_table">
            Google+
        </td>
        <td>
            <?
            if (!empty($this->fields['google_plus'])){
            ?>
            <a target="_blank" href="<? echo dims_const::_RS_GOOGLE_PLUS.$this->fields['google_plus']; ?>">
                <? echo dims_const::_RS_GOOGLE_PLUS.$this->fields['google_plus']; ?>
            </a>
            <?
            }
            ?>
        </td>
    </tr>
    <tr>
        <td class="title_table">
            YouTube
        </td>
        <td>
            <?
            if (!empty($this->fields['youtube'])){
            ?>
            <a target="_blank" href="<? echo dims_const::_RS_YOUTUBE.$this->fields['youtube']; ?>">
                <? echo dims_const::_RS_YOUTUBE.$this->fields['youtube']; ?>
            </a>
            <?
            }
            ?>
        </td>
    </tr>
</table>

<div class="title_h3">
    <h3><? echo $_SESSION['cste']['CONTENT_MODEL']; ?> - <? echo $_SESSION['cste']['_BUSINESS_FIELD_DEFAULTVALUE']; ?></h3>
</div>
<table class="table_referencement" cellspacing="0" cellpadding="0" border="1" style="width: 100%; border-collapse: collapse;">
    <tr>
        <td class="title_table" style="width:150px">
            <? echo $_SESSION['cste']['CONTENT_MODEL']; ?>
        </td>
        <td>
            <?php

	    echo $this->fields['page_default_template'];
	    ?>
        </td>
    </tr>
</table>
