<h1>Reset Logon Details Request</h1>
{if $reset eq 'TRUE'}
<p>Verifying attempt... <span class="green">OK</span></p>
<p class="green">An email has been sent to the registered email address with the new username/password</p>
<p><a href="index.php" title="Attempt to logon">Try to logon</a></p>
{else}
<p>Verifying attempt... <span class="red">FAIL</span></p>
<p class="red">The username/password has not been reset.</p>
<p><a href="index.php?reset=TRUE" title="Make a new reset request">Make a new reset request</a></p>
{/if}