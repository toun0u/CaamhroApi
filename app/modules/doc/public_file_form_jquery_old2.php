<?php
/*
*	Basé sur un docfolder
*/

// on vide le dossier de dl de l'user
$tmp_path = DIMS_ROOT_PATH.'www/data/uploads/'.session_id();
if(file_exists($tmp_path)){
	dims_deletedir($tmp_path);
}

$formId = uniqid();

$lstUsed = array();

?>


<link rel="stylesheet" href="./common/css/bootstrap.min.css">
<!-- Font Awesome icons -->
<link rel="stylesheet" href="//netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.css">
<!-- Generic page styles -->
<link rel="stylesheet" href="./common/js/jQuery-File-Upload/css/style.css">
<!-- CSS to style the file input field as button and adjust the Bootstrap progress bars -->
<link rel="stylesheet" href="./common/js/jQuery-File-Upload/css/jquery.fileupload-ui.css">

<div class="container">
    <form id="fileupload" action="<?= $this->getLightAttribute('save_url'); ?>" method="POST" enctype="multipart/form-data">
        <!-- The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->
        <div class="row fileupload-buttonbar">
            <div class="col-lg-7">
                <!-- The fileinput-button span is used to style the file input field as button -->
                <span class="btn btn-success fileinput-button">
                    <i class="icon-plus icon-white"></i>
                    <span>Add files...</span>
                    <input type="file" name="files[]" multiple>
                </span>
                <button type="submit" class="btn btn-primary start">
                    <i class="icon-upload icon-white"></i>
                    <span>Start upload</span>
                </button>
                <button type="reset" class="btn btn-warning cancel">
                    <i class="icon-ban-circle icon-white"></i>
                    <span>Cancel upload</span>
                </button>
                <button type="button" class="btn btn-danger delete">
                    <i class="icon-trash icon-white"></i>
                    <span>Delete</span>
                </button>
                <input type="checkbox" class="toggle">
                <!-- The loading indicator is shown during file processing -->
                <span class="fileupload-loading"></span>
            </div>
            <!-- The global progress information -->
            <div class="col-lg-5 fileupload-progress fade">
                <!-- The global progress bar -->
                <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100">
                    <div class="progress-bar progress-bar-success" style="width:0%;"></div>
                </div>
                <!-- The extended global progress information -->
                <div class="progress-extended">&nbsp;</div>
            </div>
        </div>
        <!-- The table listing the files available for upload/download -->
        <table role="presentation" class="table table-striped"><tbody class="files"></tbody></table>

    <br>
    <div class="panel">
        <table style="width:100%;">
		<tr>
			<td rowspan="2"><img src="/common/modules/doc/img/drop_here.png" /></td>
			<td style="font-size: 22px;font-weight: bold;vertical-align:bottom;"><?= $_SESSION['cste']['_DRAG_DROP_FILE_IN_THIS_BOX']; ?></td>
		</tr>
		<tr>
			<td style="font-size:18px;vertical-align:top;"><?= $_SESSION['cste']['_YOU_CAN_SELECT_MULTIPLE_AT_ONCE']; ?></td>
		</tr>
	</table>
    </div>
    <div class="actions" style="float: none;">
		<input type="submit" class="green submit" value="<?= $_SESSION['cste']['_SAVE_DOCUMENTS']; ?>" />
		<?= $_SESSION['cste']['_DIMS_OR']; ?>
		<a href="<?= $this->getLightAttribute('back_url'); ?>">
			<?= $_SESSION['cste']['_DIMS_LABEL_CANCEL']; ?>
		</a>
	</div>
    </form>
</div>

<!-- The template to display files available for upload -->
<script id="template-upload" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-upload fade">
        <td>
            <span class="preview"></span>
        </td>
        <td>
            <p class="name">{%=file.name%}</p>
            {% if (file.error) { %}
                <div><span class="label label-important">Error</span> {%=file.error%}</div>
            {% } %}
        </td>
        <td>
            <p class="size">{%=o.formatFileSize(file.size)%}</p>
            {% if (!o.files.error) { %}
                <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><div class="progress-bar progress-bar-success" style="width:0%;"></div></div>
            {% } %}
        </td>
        <td>
            {% if (!o.files.error && !i && !o.options.autoUpload) { %}
                <button class="btn btn-primary start">
                    <i class="icon-upload icon-white"></i>
                    <span>Start</span>
                </button>
            {% } %}
            {% if (!i) { %}
                <button class="btn btn-warning cancel">
                    <i class="icon-ban-circle icon-white"></i>
                    <span>Cancel</span>
                </button>
            {% } %}
        </td>
    </tr>
{% } %}
</script>
<!-- The template to display files available for download -->
<script id="template-download" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-download fade">
        <td>
            <span class="preview">
                {% if (file.thumbnailUrl) { %}
                    <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" data-gallery><img src="{%=file.thumbnailUrl%}"></a>
                {% } %}
            </span>
        </td>
        <td>
            <p class="name">
                <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" {%=file.thumbnailUrl?'data-gallery':''%}>{%=file.name%}</a>
            </p>
            {% if (file.error) { %}
                <div><span class="label label-important">Error</span> {%=file.error%}</div>
            {% } %}
        </td>
        <td>
            <span class="size">{%=o.formatFileSize(file.size)%}</span>
        </td>
        <td>
            <button class="btn btn-danger delete" data-type="{%=file.deleteType%}" data-url="{%=file.deleteUrl%}"{% if (file.deleteWithCredentials) { %} data-xhr-fields='{"withCredentials":true}'{% } %}>
                <i class="icon-trash icon-white"></i>
                <span>Delete</span>
            </button>
            <input type="checkbox" name="delete" value="1" class="toggle">
        </td>
    </tr>
{% } %}
</script>
<!-- The Templates plugin is included to render the upload/download listings -->
<script src="./common/js/JavaScript-Templates/js/tmpl.min.js"></script>
<!-- The Load Image plugin is included for the preview images and image resizing functionality -->
<script src="./common/js/JavaScript-Load-Image/js/load-image.min.js"></script>
<!-- The Canvas to Blob plugin is included for image resizing functionality -->
<script src="./common/js/JavaScript-Canvas-to-Blob/js/canvas-to-blob.min.js"></script>

<script type="text/javascript" src="./common/js/jQuery-File-Upload/js/vendor/jquery.ui.widget.js"></script>
<script type="text/javascript" src="./common/js/jQuery-File-Upload/js/jquery.iframe-transport.js"></script>
<script type="text/javascript" src="./common/js/jQuery-File-Upload/js/jquery.fileupload.js"></script>
<script type="text/javascript" src="./common/js/jQuery-File-Upload/js/jquery.fileupload-ui.js"></script>
<script type="text/javascript" src="./common/js/jQuery-File-Upload/js/jquery.fileupload-process.js"></script>
<script type="text/javascript" src="./common/js/jQuery-File-Upload/js/jquery.fileupload-image.js"></script>
<script type="text/javascript" src="./common/js/jQuery-File-Upload/js/jquery.fileupload-audio.js"></script>
<script type="text/javascript" src="./common/js/jQuery-File-Upload/js/jquery.fileupload-video.js"></script>
<script type="text/javascript" src="./common/js/jQuery-File-Upload/js/jquery.fileupload-validate.js"></script>


<script type="text/javascript" src="./common/js/dims_autocomplete.js"></script><!-- contient la lib tmpl -->

<!-- blueimp Gallery script -->
<script src="./common/js/Gallery/js/jquery.blueimp-gallery.min.js"></script>


<!-- The main application script -->
<script>
$(function () {
    'use strict';

    // Initialize the jQuery File Upload widget:
    $('#fileupload').fileupload({
        // Uncomment the following to send cross-domain cookies:
        //xhrFields: {withCredentials: true},
        url: '<?= dims::getInstance()->getScriptEnv(); ?>?dims_op=jquery_upload_file'
    });

    // Enable iframe cross-domain access via redirect option:
    $('#fileupload').fileupload(
        'option',
        'redirect',
        window.location.href.replace(
            /\/[^\/]*$/,
            '<?= $this->getLightAttribute('save_url')."%s"; ?>'
        )
    );


        // Load existing files:
        $('#fileupload').addClass('fileupload-processing');
        $.ajax({
            // Uncomment the following to send cross-domain cookies:
            //xhrFields: {withCredentials: true},
            url: $('#fileupload').fileupload('option', 'url'),
            dataType: 'json',
            context: $('#fileupload')[0]
        }).always(function () {
            $(this).removeClass('fileupload-processing');
        }).done(function (result) {
            $(this).fileupload('option', 'done')
                .call(this, null, {result: result});
        });

	$('#fileupload').bind('fileuploaddone', function (e, data) {

	});

});

</script>
<!-- The XDomainRequest Transport is included for cross-domain file deletion for IE 8 and IE 9 -->
<!--[if (gte IE 8)&(lt IE 10)]>
<script src="js/cors/jquery.xdr-transport.js"></script>
<![endif]-->