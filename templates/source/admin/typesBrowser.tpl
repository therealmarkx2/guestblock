<h1>Types Browser</h1>
<p>From here you can view all the block types installed on your guestblock and manage them.</p>
{if $bucketData ne NULL}
<form id="bucketToView" action="" method="post">
<p>Select a bucket to view 
<select name="bucketView" onchange="document.forms['bucketToView'].submit();">
<option value="" selected="selected">Select a Bucket</option>
<option value="" >All blocks</option>
<option value="gb_bucketless" >Bucketless blocks</option>
{foreach from=$bucketData item=bucket}
<option value="{$bucket.bucket}" >{$bucket.name}</option>
{/foreach}
</select>
</p>
</form>
{else}
<p>To help you organise your block types you should <a title="view the buckets page" href="typesBuckets.php">create some buckets</a> for them to live in.</p>
{/if}
<h2>Block Types Installed{if $bucketView ne NULL} (<a href="typesBucketsEdit.php?bucket={$bucketView}">{$bucketName} Bucket</a>){/if}</h2>
{if $blockData ne NULL}

{literal}
<script type="text/javascript"><!--
function confirmDeletion(message) {return confirm("Are you sure you wish to delete block type [" + message + "] ?")};
function confirmActivate(message) {return confirm("Are you sure you wish to activate block type [" + message + "] ?")};
function confirmDeActivate(message) {return confirm("Are you sure you wish to deactivate block type [" + message + "] ?")};
function confirmModeration() {return confirm("Are you sure you wish to modify multiple block types?")};
--></script>
{/literal}

<form method="post" action="">
<table class="stack">
<tr class="title">
<td>&nbsp;</td>
<td>Type</td>
<td>Bucket</td>
<td>Name</td>
<td>Description</td>
<td>Display</td>
<td>Code</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
<td>Active</td>
<td>&nbsp;</td>
<td>Delete</td>
</tr>
{foreach from=$blockData item=block}
{if $block.approved eq 'FALSE'}
<tr class="row{cycle values="3,4" name=table}">
{else}
<tr class="row{cycle values="1,2" name=table}">
{/if}
<td class="white">
<img style="z-index: 1; position: absolute;" src="../blocks/{$block.type}/images/block.gif" alt="{$block.name} block" title="{$block.name} block" />
<img style="z-index: 0;left: -13px; top: 5px;" alt="" src="../images/stack/shadow.gif" />
</td>
<td class="small"><a href="#" id="{$block.type}"></a>{$block.type}</td>
<td class="small">{$block.bucketName}</td>
<td class="small">{$block.name}</td>
<td class="small">{$block.description}</td>
<td><input type="checkbox" {if $block.display eq "TRUE"}checked="checked" {/if}name="display_{$block.type}" disabled="disabled"/></td>
<td><input type="checkbox" {if $block.code eq "TRUE"}checked="checked" {/if}name="code_{$block.type}" disabled="disabled"/></td>
<td class="white">[<a title="edit type" href="typesEdit.php?type={$block.type}">edit</a>]</td>
{if $block.active eq "TRUE"}
<td class="white">[<a title="deactivate type" onclick="return confirmDeActivate('{$block.type}')" href="?action=deactivate&amp;type={$block.type}">deactivate</a>]</td>
{else}
<td class="white">[<a title="activate type" onclick="return confirmActivate('{$block.type}')" href="?action=activate&amp;type={$block.type}">activate</a>]</td>
{/if}
<td class="white"><input type="checkbox" {if $block.active eq "TRUE"}checked="checked" {/if}name="active_{$block.type}" value="{$block.type}"/>
<td class="white">[<a title="delete type" onclick="return confirmDeletion('{$block.type}')" href="?action=delete&amp;type={$block.type}">delete</a>]</td>
<td class="white"><input type="checkbox" name="delete_{$block.type}" value="{$block.type}" /></td>
</tr>
{/foreach}
</table>
<p>
<input type="hidden" name="moderate" value="TRUE" />
To delete or activate multiple block types check the required boxes and then click <input type="submit" onclick="return confirmModeration()" value="modify" />
</p>
</form>
{else}
<p class="red">No types found.</p>
{/if}