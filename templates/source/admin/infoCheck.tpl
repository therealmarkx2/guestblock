<h1>Data Integrity</h1>
<p>This page will report any possible errors found with the guestblock data. Inconsistencies in the data can cause your guestblock to not display properly or in some cases not at all.</p>
<p>If any <b>block types</b> errors are found you must correct them <b>before importing new block types</b>.</p>
<p>Checking data...
{if $errorReport eq NULL}
<span class="green"> No errors found!</span></p>
{else}
<span class="red"> Warning! {$errorReport.count} error{if $errorReport.count ne 1}s have{else} has{/if} been found.</span></p>
<p>The errors are shown in the following reports. If a report is blank then no errors were found in that section.</p>
<h2>Block Types Report</h2>
{foreach from=$errorReport.types key=key item=types}
{if $key eq nulls & $types ne NULL}
<p>{$types} block type has a blank type field set. Block types should not have blank type values.</p>
{/if}
{if $key eq folders & $types ne NULL}
<p>Block types must have a corresponding folder. There are missing folders for the following block(s):</p>
<ul class="error">
{foreach from=$types item=folders}
<li>{$folders}</li>
{/foreach}
</ul>
{/if}
{if $key eq images & $types ne NULL}
<p>Block types should have a default image named 'block.gif' in their image folder. There are missing default images for the following block(s):
<br/><span class="small">(Note that block types with a missing folder will automatically generate a missing default image error)</span></p>
<ul class="error">
{foreach from=$types item=images}
<li>{$images}</li>
{/foreach}
</ul>
{/if}
{if $key eq codes & $types ne NULL}
<p>If a block type has a code entry then it should have a related class file under in a sub directory called 'code'. There are missing class files for the following block(s):
<br/><span class="small">(Note that block types with a missing folder will automatically generate a missing class file error)</span></p>
<ul class="error">
{foreach from=$types item=codes}
<li>{$codes}</li>
{/foreach}
</ul>
{/if}
{/foreach}
<h2>Blocks Report</h2>
{foreach from=$errorReport.blocks key=key item=block}
{if $key eq nulls & $block ne NULL}
<p>Blocks should not have a blank type value. Blank field values were found for the following block(s):</p>
<ul class="error">
{foreach from=$block item=nulls}
{foreach from=$nulls item=block}
<li>blockid: {$block.blockid}</li>
{/foreach}
{/foreach}
</ul>
{/if}
{if $key eq types & $block ne NULL}
<p>Blocks should have a type value that corresponds to an existing block type. Inconsistent type field values were found for the following block(s):</p>
<ul class="error">
{foreach from=$block item=block}
<li>blockid: {$block.blockid} (type '{$block.type}' is missing)</li>
{/foreach}
</ul>
{/if}
{/foreach}
{/if}