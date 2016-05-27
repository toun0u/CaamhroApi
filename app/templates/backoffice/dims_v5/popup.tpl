<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<meta http-equiv="content-type" content="text/html; charset={$site.ENCODING}" />
<meta name="description" content="{$site.WORKSPACE_META_DESCRIPTION}" />
<meta name="keywords" content="{$site.WORKSPACE_META_KEYWORDS}" />
<meta name="author" content="{$site.WORKSPACE_META_AUTHOR}" />
<meta name="copyright" content="{$site.WORKSPACE_META_COPYRIGHT}" />
<meta name="robots" content="{$site.WORKSPACE_META_ROBOTS}" />
<title>{$site.WORKSPACE_TITLE}</title>
<link rel="icon" href="{$site.TEMPLATE_PATH}/img/favicon.png" type="image/png" />
<link type="text/css" rel="stylesheet" href="{$site.TEMPLATE_PATH}/css/main.css" media="screen" />

{$site.DIMS_NS_CSS}
{$site.ADDITIONAL_HEAD}
{if (isset($switch_user_logged_out))}
    <link type="text/css" href="{$site.TEMPLATE_PATH}/css/black/jquery-ui-1.8.5.custom.css" rel="stylesheet" />
{else}
    <link type="text/css" href="{$site.TEMPLATE_PATH}/css/custom-theme/jquery-ui-1.8.5.custom.css" rel="stylesheet" />
{/if}
</head>

<body class="popup">

<div class="dims_popup" id="dims_popup"></div>

<div style="width:100%;display;block;">
{$site.PAGE_CONTENT}
</div>

</body>
</html>
