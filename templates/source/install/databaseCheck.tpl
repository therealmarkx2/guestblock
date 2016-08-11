<h2>database access</h2>
<p>Looking for pear at:</p>
<blockquote><p>{$pearDebug}</p></blockquote>
<p>Attempting to establish database connection...
{if $pear eq "FAIL"}
<span class="red">FAIL</span></p>
<p class="red">Warning! unable to locate PEAR files. Please go back and check the PEAR setting.</p>
<p><a title="go back" href="javascript:history.back()">Go back to correct settings</a>.</p>
{else}

{if $databaseConnect eq "TRUE"}
<span class="green">OK</span></p>
<p class="green">Database connection made.</p>
<h2>creating tables</h2>

<p>Attempting to create table {$prefix}blocks... 
{if $tableBlocks.create eq "PASS"}
<span class="green">OK</span></p>
{elseif $tableBlocks.create eq "FAIL"}
<span class="red">FAIL</span></p>
<p class="red">Warning! unable to create table {$prefix}blocks. The error message returned was:</p>
<blockquote><p>{$tableBlocks.debug}</p></blockquote>
<p class="red">Please go back and check the database settings.</p>
<p><a title="go back" href="javascript:history.back()">Go back to correct settings</a>.</p>
{/if}

<p>Attempting to create table {$prefix}blocks_data... 
{if $tableBlocksData.create eq "PASS"}
<span class="green">OK</span></p>
{elseif $tableBlocksData.create eq "FAIL"}
<span class="red">FAIL</span></p>
<p class="red">Warning! unable to create table {$prefix}blocks_data. The error message returned was:</p>
<blockquote><p>{$tableBlocksData.debug}</p></blockquote>
<p class="red">Please go back and check the database settings.</p>
<p><a title="go back" href="javascript:history.back()">Go back to correct settings</a>.</p>
{/if}

<p>Attempting to create table {$prefix}buckets... 
{if $tableBuckets.create eq "PASS"}
<span class="green">OK</span></p>
{elseif $tableBuckets.create eq "FAIL"}
<span class="red">FAIL</span></p>
<p class="red">Warning! unable to create table {$prefix}flood. The error message returned was:</p>
<blockquote><p>{$tableBuckets.debug}</p></blockquote>
<p class="red">Please go back and check the database settings.</p>
<p><a title="go back" href="javascript:history.back()">Go back to correct settings</a>.</p>
{/if}

<p>Attempting to create table {$prefix}flood... 
{if $tableFlood.create eq "PASS"}
<span class="green">OK</span></p>
{elseif $tableFlood.create eq "FAIL"}
<span class="red">FAIL</span></p>
<p class="red">Warning! unable to create table {$prefix}flood. The error message returned was:</p>
<blockquote><p>{$tableFlood.debug}</p></blockquote>
<p class="red">Please go back and check the database settings.</p>
<p><a title="go back" href="javascript:history.back()">Go back to correct settings</a>.</p>
{/if}

<p>Attempting to create table {$prefix}settings... 
{if $tableSettings.create eq "PASS"}
<span class="green">OK</span></p>
{elseif $tableSettings.create eq "FAIL"}
<span class="red">FAIL</span></p>
<p class="red">Warning! unable to create table {$prefix}settings. The error message returned was:</p>
<blockquote><p>{$tableSettings.debug}</p></blockquote>
<p class="red">Please go back and check the database settings.</p>
<p><a title="go back" href="javascript:history.back()">Go back to correct settings</a>.</p>
{/if}

<p>Attempting to create table {$prefix}stacks... 
{if $tableStacks.create eq "PASS"}
<span class="green">OK</span></p>
{elseif $tableStacks.create eq "FAIL"}
<span class="red">FAIL</span></p>
<p class="red">Warning! unable to create table {$prefix}stacks. The error message returned was:</p>
<blockquote><p>{$tableStacks.debug}</p></blockquote>
<p class="red">Please go back and check the database settings.</p>
<p><a title="go back" href="javascript:history.back()">Go back to correct settings</a>.</p>
{/if}

<p>Attempting to create table {$prefix}types... 
{if $tableTypes.create eq "PASS"}
<span class="green">OK</span></p>
{elseif $tableTypes.create eq "FAIL"}
<span class="red">FAIL</span></p>
<p class="red">Warning! unable to create table {$prefix}types. The error message returned was:</p>
<blockquote><p>{$tableTypes.debug}</p></blockquote>
<p class="red">Please go back and check the database settings.</p>
<p><a title="go back" href="javascript:history.back()">Go back to correct settings</a>.</p>
{/if}

{if $continue eq "TRUE"}
<p class="green">Excellent, all tables have been created.</p>
<h2>Continue Installation</h2>
<p>The final step is to confirm some <a title="continue installation" href="install.php?action=settings"/>guestblock settings</a>.</p>
{else}
<p class="red">The attempt to create some or all of the tables has failed. Please go back and check the database settings.</p>
{/if}

{elseif $databaseConnect eq "FALSE"}
<span class="red">FAIL</span></p>
<p class="red">Warning! A database connection could not be established. The error returned was:</p>
<blockquote><p>{$databaseDebug}</p></blockquote>
<p class="red">Please go back and check the database settings.</p>
<p><a title="go back" href="javascript:history.back()">Go back to correct settings</a>.</p>
{/if}

{/if}