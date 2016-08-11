<h1>Edit Block</h1>
<p>This page allows you to edit the details entered for a particular block. Simply make the amendments and click edit.</p>
{if $blockData ne NULL}
<form action="" method="post">
<fieldset>
<legend>block details</legend>
<div class="row">
<label for="block">Block</label>
<select name="type" id="type">
{foreach from=$blockTypes item=block}
<option value="{$block.type}" {if $block.type eq $blockData.type}selected="selected"{/if}>{$block.type}</option>
{/foreach}
</select>
<img title="{$blockData.type} block" alt="{$blockData.type} block" src="../blocks/{$blockData.type}/images/block.gif" />
</div>
<div class="row">
<label for="name">Name</label>
<input type="text" name="name" id="name" maxlength="32" value="{$blockData.name}" class="long" />
</div>
<div class="row">
<label for="message">Message</label>
<input type="text" name="message" id="message" maxlength="100" value="{$blockData.message}" class="long" />
</div>
<div class="row">
<label for="email">Email</label>
<input type="text" name="email" id="email" maxlength="255" value="{$blockData.email}" class="long" />
</div>
<div class="row">
<label for="url">Url</label>
<input type="text" name="url" id="url" maxlength="255" value="{$blockData.url}" class="long" />
</div>
<div class="row">
<input type="hidden" name="edit" id="edit" value="TRUE" />
<input type="hidden" name="location" id="location" value="{$location}" />
<input type="hidden" name="blockid" id="blockid" value="{$blockData.blockid}" />
<input type="submit" value="Edit" />
</div>
</fieldset>
</form>
{/if}