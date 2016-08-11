{if $reset ne TRUE}
<h1>Logon</h1>
<p>You must logon to gain accesss to the admin section.</p>
{if $logon eq "FAIL"}
<p>Processing logon... <span class="red">FAIL</span></p>
<p class="red">Warning! Logon details were incorrect. Please try again.</p>
{/if}
<form method="post" action="index.php" id="logon">
<fieldset>
<legend>logon details</legend>
<div class="row">
<label for="username">Username</label>
<input name="username" id="username" type="text" />
</div>
<div class="row">
<label for="password">Password</label>
<input name="password" id="password" type="password" />
</div>
<div class="row">
<input name="logonAttempt" id="logonAttempt" type="hidden" value="TRUE" />
<input type="submit" value="Logon" />
</div>
</fieldset>
</form>
{else}
<h1>Logon Reset</h1>
{/if}
{if $resetOn eq TRUE AND $reset ne TRUE}
<p>I forgot my username/password! Send a <a href="?reset=TRUE" title="Click here to start a username/password reset">reset command to the registered email address</a>.</p>
{/if}
{if $reset eq TRUE}
<p class="green">Reset email sent, please follow instructions in email.</p>
{/if}