<h1>Block Moderation</h1>
<p>Blocks shown here have been held in a queue because they have been flagged for approval due to the current <a title="view spam control settings" href="settingsSpam.php">spam control settings</a>.</p>
<h2>Blocks Awaiting Approval</h2>
{if $blockData ne NULL}

{literal}
<script type="text/javascript"><!--
function confirmDeletion(message) {return confirm("Are you sure you wish to delete block id [" + message + "] ?")};
function confirmApproval(message) {return confirm("Are you sure you wish to approve block id [" + message + "] ?")};
function confirmModeration() {return confirm("Are you sure you wish to moderate multiple blocks?")};

function deleteToggle(block) {
	var state = false;
	if (eval('document.forms["moderateForm"].approve_' + block + '.checked') ) state = true;
	eval('document.forms["moderateForm"].delete_' + block + '.checked = false');
	
};

function approveToggle(block) {
	var state = false;
	if (eval('document.forms["moderateForm"].delete_' + block + '.checked') ) state = true;
	eval('document.forms["moderateForm"].approve_' + block + '.checked = false');
};

--></script>
{/literal}

<form id="moderateForm" method="post" action="">
<table class="stack">
<tr class="title">
<td>&nbsp;</td>
<td>Date</td>
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
<tr class="row{cycle values="3,4" name=table}">
<td class="white">
<img style="z-index: 1; position: absolute;" src="../blocks/{$block.type}/images/block.gif" alt="{$block.block} block" title="{$block.block} block" />
<img style="z-index: 0;left: -13px; top: 5px;" alt="" src="../images/stack/shadow.gif" />
</td>
<td class="small"><a href="#" id="id{$block.blockid}"></a><a title="view stack" href="../?year={$block.year}&amp;month={$block.month}&amp;={$block.day}">{$block.date}</a></td>
<td class="small">{$block.time}</td>
<td>{$block.name}</td>
<td >{$block.message}</td>
<td class="small">{$block.ip}</td>
<td class="white">
{if $block.url ne NULL}
[<a title="{$block.url}" href="{$block.url}">url</a>]
{/if}
</td>
<td class="white">
{if $block.email ne NULL}
[<a href="mailto:{$block.email}">email</a>]
{/if}
</td>
<td class="white">&nbsp;</td>
<td class="white">[<a title="edit block" href="stacksEdit.php?blockid={$block.blockid}&amp;origin=moderation">edit</a>]</td>
<td class="white">[<a title="delete block" onclick="return confirmDeletion('{$block.blockid}')" href="?action=delete&amp;blockid={$block.blockid}">delete</a>]</td>
<td class="white"><input type="checkbox" onclick="approveToggle('{$block.blockid}')" name="delete_{$block.blockid}" value="{$block.blockid}" /></td>
<td class="white">&nbsp;</td>
<td class="white">[<a title="approve block" onclick="return confirmApproval('{$block.blockid}')" href="?action=approve&amp;blockid={$block.blockid}">approve</a>]</td>
<td class="white"><input type="checkbox" onclick="deleteToggle('{$block.blockid}')" name="approve_{$block.blockid}" value="{$block.blockid}" /></td>
</tr>
{/foreach}
</table>
<p>
<input type="hidden" name="moderate" value="TRUE" />
To delete or approve multiple blocks check the required boxes and then click <input type="submit" onclick="return confirmModeration()" value="moderate selected" />
</p>
</form>
<h2>Possible Spam</h2>
<p>Your spam filter does not currently have the following entries which the blocks held above in moderation currently contain. You may (or may not) like to add some or all of these entries to your filter by copy and paste.</p>
<p>
{foreach from=$possibleSpam item=spam}
<small>{$spam}</small><br/>
{/foreach}
</p>

{else}
<p class="red">No blocks found.</p>
{/if}