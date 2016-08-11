<h1>Stack Browser</h1>
<p>Enter the date of a stack to view the blocks in that stack. You will then be able to edit individual block details or even delete them. Blocks that are awaiting approval will appear with a red background.</p>
<form action="" method="post">
<fieldset>
<legend>stack finder</legend>
<div class="row">
<label for="date">Date</label>
<input type="text" name="date" id="date" maxlength="10" value="{$date}" class="short"/> <span>(use format YYYY-MM-DD)</span>
</div>
<div class="row">
<input type="submit" value="Display" />
</div>
</fieldset>
</form>
{if $blockData ne NULL}

{literal}
<script type="text/javascript"><!--
function confirmDeletion(message) {return confirm("Are you sure you wish to delete block id [" + message + "] ?")};
function confirmApproval(message) {return confirm("Are you sure you wish to approve block id [" + message + "] ?")};
function confirmModeration() {return confirm("Are you sure you wish to moderate multiple blocks?")};
--></script>
{/literal}

<h2>Blocks laid {$dateFormat}</h2>
<form method="post" action="">
<table class="stack">
<tr class="title">
<td>&nbsp;</td>
<td>Time</td>
<td>Name</td>
<td>Message</td>
<td>IP</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
<td>Delete</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
<td>Approve</td>
</tr>
{foreach from=$blockData item=block}
{if $block.approved eq 'FALSE'}
<tr class="row{cycle values="3,4" name=table}">
{else}
<tr class="row{cycle values="1,2" name=table}">
{/if}
<td class="white">
<img style="z-index: 1; position: absolute;" src="../blocks/{$block.type}/images/block.gif" alt="{$block.block} block" title="{$block.block} block" />
<img style="z-index: 0;left: -13px; top: 5px;" alt="" src="../images/stack/shadow.gif" />
</td>
<td class="small"><a href="#" id="id{$block.blockid}"></a>{$block.time}</td>
<td>{$block.name}</td>
<td>{$block.message}</td>
<td class="small">{$block.ip}</td>
<td class="white">
{if $block.url ne NULL}
[<a href="{$block.url}">url</a>]
{/if}
</td>
<td class="white">
{if $block.email ne NULL}
[<a href="mailto:{$block.email}">email</a>]
{/if}
</td>
<td class="white">&nbsp;</td>
<td class="white">[<a title="edit block" href="stacksEdit.php?blockid={$block.blockid}&amp;origin=browse">edit</a>]</td>
<td class="white">[<a title="delete block" onclick="return confirmDeletion('{$block.blockid}')" href="?action=delete&amp;blockid={$block.blockid}">delete</a>]</td>
<td class="white"><input type="checkbox" name="delete_{$block.blockid}" value="{$block.blockid}" /></td>
<td class="white">&nbsp;</td>
{if $block.approved eq 'FALSE'}
<td class="white">[<a title="approve block" onclick="return confirmApproval('{$block.blockid}')" href="?action=approve&amp;blockid={$block.blockid}">approve</a>]</td>
<td class="white"><input type="checkbox" name="approve_{$block.blockid}" value="{$block.blockid}" /></td>
{else}
<td class="white">&nbsp;</td>
<td class="white">&nbsp;</td>
{/if}
</td>
</tr>
{/foreach}
</table>
<p>
<input type="hidden" name="moderate" value="TRUE" />
To delete or approve multiple blocks check the required boxes and then click <input type="submit" onclick="return confirmModeration()" value="moderate selected" />
</p>
</form>
{else}
<h2>Blocks laid {$dateFormat}</h2>
<p class="red">No blocks found.</p>
{/if}