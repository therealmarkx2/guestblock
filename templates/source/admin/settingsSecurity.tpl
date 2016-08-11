<h1>Admin Access</h1>
{if $update.status ne NULL}
<p>Updating admin access...
{if $update.status eq FAIL}
<span class="red">FAIL</span></p>
<p class="red">Warning! The admin access details have not been changed. 
{if $update.reason eq length}
The new username and password should be at least 6 characters in length.
{/if}
{if $update.reason eq retype}
The password did not match the retyped password.
{/if}
</p>
{else}
<span class="green">OK</span></p>
{/if}
{/if}
{if $update.status eq FAIL or $update eq NULL}
<p>You may change the admin username and password. For security reasons the username and password should be at least 6 characters in length.</p>
<form action="" method="post">
<fieldset>
<legend>Logon Details</legend>
<div class="row">
<label for="username">Username</label>
<input name="username" id="username" type="text" maxlength="100" class="long"/>
</div>
<div class="row">
<label for="password">Password</label>
<input name="password" id="password" type="password" maxlength="100" class="long"/>
</div>
<div class="row">
<label for="retype">Retype Password</label>
<input name="retype" id="retype" type="password" maxlength="100" class="long"/>
</div>
<div class="row">
<input id="update" name="update" type="hidden" value="TRUE" />
<input id="submit" type="submit" value="Update" />
</div>
</fieldset>
</form>
{/if}

<form action="" method="post">
<fieldset>
<legend>Admin Email</legend>
<p>This email address will be used if you need to reset the admin logon details.</p>
<div class="row">
<label for="email">Admin Email</label>
<input name="email" id="email" type="text" maxlength="255" class="long" value="{$adminEmail}"/>
</div>
<div class="row">
<input id="adminEmail" name="adminEmail" type="hidden" value="TRUE" />
<input id="submit" type="submit" value="Update" />
</div>
</fieldset>
</form>