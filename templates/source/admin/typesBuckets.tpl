<h1>Block Buckets</h1>
<p>Blocks are stored in buckets, just to keep things tidy. Use this page to set which block type belongs to which bucket.</p>
<h2>Modify Existing Buckets</h2>
<p>When you import blocks you should add them to a bucket that you have created. To manage the contents of a created bucket, click edit.</p>

{if $bucketData ne NULL}
{literal}
<script type="text/javascript"><!--
function confirmDeletion(message) {return confirm("Are you sure you wish to delete bucket [" + message + "] ?")};
function confirmDeletionAll() {return confirm("Are you sure you wish to delete multiple buckets?")};
--></script>
{/literal}

<form method="post" action="">
<table class="stack">
<tr class="title">
<td>Bucket</td>
<td>Name</td>
<td>Description</td>
<td>Blocks</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
<td>&nbsp;</td>
<td>Delete</td>
</tr>

{foreach from=$bucketData item=bucket}

<tr class="row{cycle values="1,2" name=table}">
<td><a href=""><a href="#" id="{$bucket.bucket}"></a>{$bucket.bucket}</td>
<td>{$bucket.name}</td>
<td>{$bucket.description}</td>
<td>{$bucket.count}</td>
<td class="white">&nbsp;</td>
<td class="white">[<a title="edit bucket" href="typesBucketsEdit.php?bucket={$bucket.bucket}">edit</a>]</td>
<td class="white">&nbsp;</td>
<td class="white">[<a title="delete bucket" onclick="return confirmDeletion('{$bucket.bucket}')" href="?action=delete&amp;bucket={$bucket.bucket}">delete</a>]</td>
<td class="white"><input type="checkbox" name="delete_{$bucket.bucket}" value="{$bucket.bucket}" /></td>
</tr>

{/foreach}
</table>

<p>
<input type="hidden" name="moderate" value="TRUE" />
To delete multiple buckets check the required boxes and then click <input type="submit" onclick="return confirmDeletionAll()" value="delete" />
</p>
</form>
{else}
<p class="red">No buckets found, please create some.</p>
{/if}


<h2>Create New Bucket</h2>
<p>When you import a new block you have the option to add it to an existing bucket (to keep things organised). You create your buckets here.</p>
<form action="" method="post">
<fieldset>
<legend>New Bucket Details</legend>
<div class="row">
<label for="bucket">Bucket</label>
<input type="text" name="bucket" id="bucket" class="long"  maxlength="50"/> <span>(50 chars max, no special chars or whitespace)</span>
</div>
<div class="row">
<label for="name">Name</label>
<input type="text" name="name" id="name" class="long"  maxlength="100"/> <span>(100 chars max)</span>
</div>
<div class="row">
<label for="description">Description</label>
<input type="text" name="description" id="description" class="long"  maxlength="255"/> <span>(255 chars max)</span>
</div>
<div class="row">
<input id="bucketCreate" name="bucketCreate" type="hidden" value="TRUE" />
<input id="submit" type="submit" value="Create" />
</div>
</fieldset>
</form>