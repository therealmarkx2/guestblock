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
<body class="install">
<div id="header">
<h1>guestblock installation</h1>
</div>
<div id="content">
<div id="sidebar"><div id="sidebar-content">
<div id="menu">
{include file="install/menu.tpl"}
</div>
</div></div>
<div id="main"><div id="main-content">