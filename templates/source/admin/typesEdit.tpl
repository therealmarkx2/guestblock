<h1>Edit Block Type</h1>
<p>This page allows you to edit the details entered for a particular block. Simply make the amendments and click edit.</p>
{if $blockData ne NULL}
<form action="" method="post">
<fieldset>
<legend>block details</legend>
<div class="row">
<label for="type">Type</label>
<input type="text" name="type" id="type" value="{$blockData.type}" class="long" disabled="disabled"/>
<img title="{$blockData.type} block" alt="{$blockData.type} block" src="../blocks/{$blockData.type}/images/block.gif" />
</div>

<div class="row">
<label for="bucket">Bucket</label>

<select name="bucket">
<option value="">Select a Bucket</option>
{foreach from=$bucketData item=bucket}

{if $bucket.bucket eq $blockData.bucket}
<option value="{$bucket.bucket}" selected="selected">{$bucket.name}</option>
{else}
<option value="{$bucket.bucket}">{$bucket.name}</option>
{/if}
{/foreach}
</select>


</div>


<div class="row">
<label for="name">Name</label>
<input type="text" name="name" id="name" maxlength="255" value="{$blockData.name}" class="long" />
</div>
<div class="row">
<label for="description">Description</label>
<input type="text" name="description" id="description" maxlength="255" value="{$blockData.description}" class="long" />
</div>

<div class="row">
<label for="display">Display</label>
<input type="checkbox" name="display" id="display" value="TRUE" {if $blockData.display eq "TRUE"}checked="checked" {/if}/>
</div>

<div class="row">
<label for="code">Code</label>
<input type="checkbox" name="code" id="code" value="TRUE" {if $blockData.code eq "TRUE"}checked="checked" {/if} {if $codeFound eq FALSE & $blockData.code ne "TRUE"}disabled="disabled"{/if}/>{if $codeFound eq FALSE} <span>no class file found</span>{else} <span>class file found</span>{/if}
</div>

<div class="row">
<label for="active">Active</label>
<input type="checkbox" name="active" id="active" value="TRUE" {if $blockData.active eq "TRUE"}checked="checked" {/if}/>
</div>


<div class="row">
<input type="hidden" name="typeEdit" id="typeEdit" value="{$blockData.type}" />
<input type="hidden" name="edit" id="edit" value="TRUE" />
<input type="submit" value="Edit" />
</div>
</fieldset>
</form>
{/if}