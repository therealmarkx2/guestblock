<h2>saving settings</h2>
<p>Writing to config.php file... 
{if $settingsSave eq "PASS"}
<span class="green">OK</span></p>
<p class="green">Settings have been saved.</p>
<p class="red">Warning! You should now make this file read-only (chmod 644).</p>
<h2>installation complete</h2>
<p>Congratulations! Guestblock should now have been installed.</p>
<p><span class="red">Important!</span> To log into the admin section you should use this username and password (record these now before you browse away from this page):</p>
<blockquote><p>username: {$username}</p></blockquote>
<blockquote><p>password: {$password}</p></blockquote>
<p>The admin section, where you can further customize and manage your guestblock, is <a title="view your guestblock admin" href="../admin/">available here</a>.</p>
{elseif $settingsSave eq "FAIL"}
<span class="red">FAIL</span></p>
<p class="red">Could not save settings. Has the config.php file been changed to read-only? If the file does not exist in the root of the guestblock package please create it, ensure it is writable by the webserver and try again by refreshing this page.</p>
<p><a title="go back" href="install.php?action=settingsSave">Refresh page</a>.</p>
{/if}
<p>Your guestblock should be <a title="view your guestblock" href="../">available here</a>.</p>