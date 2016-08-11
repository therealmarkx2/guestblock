<h1>Block Types Import</h1>
<p>New blocks are available to <a title="get new blocks" href="http://www.guestblock.com/blocks/">download from Guestblock.com</a>.</p>
<p>After uploading new block type folders to your main blocks folder:</p>
<blockquote><p>{$blockPath}</p></blockquote>
<p>you can import the new block types into your Guestblock. If the new block type has an import script then it will populate the types table for you, otherwise you will have to enter these details yourself.</p>
<p>For each block you want to import you should specify which bucket you want it to belong to. You may need to <a href="typesBuckets.php" title="create a bucket">create some buckets </a> first.</p>
{if $install eq TRUE}
<h2>Importing Block Types</h2>
<p>Importing... <span class="green">OK</span></p>
{/if}
<h2>Import Search</h2>
<p>Searching for new block types... 
{if $importsFound eq PASS}
<span class="green">FOUND</span>
{else}
<span class="red">NONE FOUND</span>
{/if}
</p>

{if $blockDataInstall ne NULL | $blockDataNoInstall ne NULL}
{literal}
<script type="text/javascript"><!--
function confirmImport() {return confirm("Are you sure you wish to import the selected block(s)?")};
function importNoInstallToggle(type) {
	var state = false;
	if (eval('document.forms["importForm"].activateNoInstall_' + type + '.checked') ) state = true;
	eval('document.forms["importForm"].importNoInstall_' + type + '.checked = ' + state);
}
function importInstallToggle(type) {
	var state = false;
	if (eval('document.forms["importForm"].activateInstall_' + type + '.checked') ) state = true;
	eval('document.forms["importForm"].importInstall_' + type + '.checked = ' + state);
}
--></script>
{/literal}

<form id="importForm" method="post" action="">
{/if}

{if $blockDataInstall ne NULL}
<p>Block type(s) with import scripts found...</p>
<table class="stack">
<tr class="title">
<td>&nbsp;</td>
<td>Type</td>
<td>Bucket</td>
<td>Import</td>
<td>Activate</td>
</tr>
{foreach from=$blockDataInstall item=block}
<tr class="row{cycle values="1,2" name=table}">
<td class="white">
<img style="z-index: 1; position: absolute;" src="../blocks/{$block}/images/block.gif" alt="{$block.block} block" title="{$block.block} block" />
<img style="z-index: 0;left: -13px; top: 5px;" alt="" src="../images/stack/shadow.gif" />
</td>
<td>{$block}</td>
<td>
{if $bucketData ne NULL}
<select name="installBucket_{$block}">
<option value="" selected="selected">Select a Bucket</option>
{foreach from=$bucketData item=bucket}
<option value="{$bucket.bucket}">{$bucket.name}</option>
{/foreach}
</select>
{else}
<span class="red">No buckets found!</span>
{/if}
</td>
<td><input type="checkbox" name="importInstall_{$block}" value="{$block}" /></td>
<td><input type="checkbox" onclick="importInstallToggle('{$block}')" name="activateInstall_{$block}" value="{$block}" /></td>
</tr>
{/foreach}
</table>
{/if}

{if $blockDataNoInstall ne NULL}
<p>Block type(s) without import scripts found...</p>
<table class="stack">
<tr class="title">
<td>&nbsp;</td>
<td>Type</td>
<td>Bucket</td>
<td>Name</td>
<td>Description</td>
<td>Code</td>
<td>Display</td>
<td>Import</td>
<td>Activate</td>
</tr>
{foreach from=$blockDataNoInstall item=block}
<tr class="row{cycle values="1,2" name=table}">
<td class="white">
<img style="z-index: 1; position: absolute;" src="../blocks/{$block}/images/block.gif" alt="{$block.block} block" title="{$block.block} block" />
<img style="z-index: 0;left: -13px; top: 5px;" alt="" src="../images/stack/shadow.gif" />
</td>
<td>{$block}</td>
<td>
{if $bucketData ne NULL}
<select name="bucket_{$block}">
<option value="" selected="selected">Select a Bucket</option>
{foreach from=$bucketData item=bucket}
<option value="{$bucket.bucket}">{$bucket.name}</option>
{/foreach}
</select>
{else}
<span class="red">No buckets found!</span>
{/if}
</td>
<td><input type="text" maxlength="100" name="name_{$block}"/></td>
<td><input type="text" maxlength="255" name="description_{$block}"/></td>
<td><input type="checkbox" {if $blockDataNoInstallCode.$block eq TRUE}checked="checked" {/if}name="code_{$block}" value="TRUE"/></td>
<td><input type="checkbox" checked="checked" name="display_{$block}" value="TRUE"/></td>
<td><input type="checkbox" name="importNoInstall_{$block}" value="{$block}" /></td>
<td><input type="checkbox" onclick="importNoInstallToggle('{$block}')" name="activateNoInstall_{$block}" value="{$block}" /></td>
</tr>
{/foreach}
</table>
{/if}
{if $blockDataInstall ne NULL | $blockDataNoInstall ne NULL}
<p><input type="hidden" name="import" value="TRUE" />Select block types to import and whether to activate them as well, then click to <input type="submit" onclick="return confirmImport()" value="import" /></p>
</form>
{/if}