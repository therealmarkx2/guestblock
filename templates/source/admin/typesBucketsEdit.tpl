<h1>Edit Bucket</h1>
<p>You may edit the details of the bucket from here and set the block types it contains.</p>

{foreach from=$bucketData item=bucket}
<form action="" method="post">
<fieldset>
<legend>bucket details</legend>
<div class="row">
<label for="bucket">Bucket</label>
<input type="text" name="bucket" id="bucket" class="long" value="{$bucket.bucket}"  maxlength="50"/> <span>(50 chars max, no special chars or whitespace)</span>
</div>
<div class="row">
<label for="name">Name</label>
<input type="text" name="name" id="name" class="long" value="{$bucket.name}"  maxlength="100"/> <span>(50 chars max)</span>
</div>
<div class="row">
<label for="description">Description</label>
<input type="text" name="description" id="description" class="long" value="{$bucket.description}"  maxlength="255"/> <span>(255 chars max)</span>
</div>
<div class="row">
<input id="edit" name="edit" type="hidden" value="TRUE" />
<input id="submit" type="submit" value="Edit" />
</div>

</fieldset>
</form>

<form action="" method="post">
<fieldset>
<legend>Block Types In This Bucket</legend>
<p>The bucket currently contains the following blocks, you may move any of these blocks to another bucket if you wish.</p>
<table class="stack">
<tr class="title">
<td>&nbsp;</td>
<td>Block</td>
<td>Bucket</td>
</tr>
{foreach from=$bucketData item=bucket}
{foreach from=$bucket.blocks item=block}
<tr class="row{cycle values="1,2" name=table}">
<td class="white">
<img style="z-index: 1; position: absolute;" src="../blocks/{$block.type}/images/block.gif" alt="{$block.name} block" title="{$block.name} block" />
<img style="z-index: 0;left: -13px; top: 5px;" alt="" src="../images/stack/shadow.gif" />
</td>
<td>{$block.name}</td>
<td>
<select name="bucket_{$block.type}">
{foreach from=$bucketDataAll item=bucketAll}
{if $bucket.bucket eq $bucketAll.bucket}
<option value="{$bucketAll.bucket}" selected="selected">{$bucketAll.name}</option>
{else}
<option value="{$bucketAll.bucket}">{$bucketAll.name}</option>
{/if}
{/foreach}
</select>
</td>
</tr>
{/foreach}
{/foreach}
</table>


<div class="row">
<input id="manage" name="manage" type="hidden" value="TRUE" />
<input id="submit" type="submit" value="Edit" />
</div>

</fieldset>
</form>
{/foreach}

{if $bucketDataNull ne NULL}
<form action="" method="post">
<fieldset>
<legend>Bucketless Blocks</legend>
<p>These blocks currently don't belong to any bucket. Help 'em out.</p>
<table class="stack">
<tr class="title">
<td>&nbsp;</td>
<td>Block</td>
<td>Bucket</td>
</tr>
{foreach from=$bucketDataNull item=block}
<tr class="row{cycle values="1,2" name=table}">
<td class="white">
<img style="z-index: 1; position: absolute;" src="../blocks/{$block.type}/images/block.gif" alt="{$block.name} block" title="{$block.name} block" />
<img style="z-index: 0;left: -13px; top: 5px;" alt="" src="../images/stack/shadow.gif" />
</td>
<td>{$block.name}</td>
<td>
<select name="bucketless_{$block.type}">
<option value="" selected="selected">Select a Bucket</option>
{foreach from=$bucketDataAll item=bucketAll}
<option value="{$bucketAll.bucket}" >{$bucketAll.name}</option>
{/foreach}
</select>
</td>
</tr>
{/foreach}
</table>
<p>
<input type="hidden" name="bucketless" value="TRUE" />
Select the new home for each block type then click <input type="submit" value="Edit" />
</p>
</fieldset>
</form>
{/if}