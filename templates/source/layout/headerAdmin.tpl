<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head  profile="http://gmpg.org/xfn/1">
<title>{$title}</title>
<link rel="Shortcut Icon" href="/favicon.ico" type="image/x-icon" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style type="text/css" media="screen">
	{foreach from=$css item=cssFile}
	@import url('{$cssFile}');
	{/foreach}	
</style>
{foreach from=$jsHeader item=jsFile}
<script type="text/JavaScript" src="/resources/js/{$jsFile}.js" ></script>
{/foreach}
</head>
<body class="{$domain}">
<div id="header">
<h1>Guestblock Admin</h1>
<span id="version">version {$version}<br/><a title="goto guestblock.com" href="http://guestblock.com/">guestblock.com</a></span>
</div>
{include file="admin/menu.tpl"}
<div id="content">
