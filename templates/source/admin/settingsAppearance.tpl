<h1>Guestblock Appearance</h1>
<p>Use this page to control some the way guestblock creates it's output.</p>
<form action="" method="post">
<fieldset>
<legend>Settings</legend>
<h2>Empty Stacks</h2>
<p>The default behaviour for empty stacks (days without a block having being laid) is for them to be displayed (or drawn). If you prefer you can set guestblock to not draw these stacks and skip that day until it finds a stack with blocks laid.</p>
<div class="row">
<label for="drawEmptyStacks">Draw Stacks</label>
{if $drawEmptyStacks eq 'TRUE'}
<input name="drawEmptyStacks" id="drawEmptyStacks" type="checkbox" value="TRUE" checked="checked"/>
{else}
<input name="drawEmptyStacks" id="drawEmptyStacks" type="checkbox" value="TRUE"/>
{/if}
</div>
<h2>Tabular Block Select Form</h2>
<p>By default guestblock outputs the blocks to be chosen to be laid as an unordered list. If you prefer they can be outputed as a table. This should make it easier to reduce any css alignment problems if you are experiencing any (typically noticed across different browser interpretations of W3C standards).</p>
<p>To use the table layout enter the number of blocks to be shown <em>per row</em>. To use the unordered list, simply blank the contents of the below box.</p>
<div class="row">
<label for="blockSelectTable">Blocks/row</label>
<input name="blockSelectTable" id="blockSelectTable" type="text" class="short" value="{$blockSelectTable}"/> <span>(leave blank to use an unordered list)</span>
</div>
<div class="row">
<input id="update" name="update" type="hidden" value="TRUE" />
<input id="submit" type="submit" value="Update" />
</div>
</fieldset>
</form>