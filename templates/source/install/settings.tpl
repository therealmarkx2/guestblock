<h2>Guestblock Settings</h2>
<p>Please provide the following guestblock setting.</p>
<form class="install" action="install.php?action=settingsSave" method="post">
<fieldset>
<legend>guestblock config</legend>
<h2>Installation Path</h2>
<div class="row">
<label for="path">Guestblock Path</label>
<input name="path" id="path" type="text" value="/guestblock/" class="long"/>
</div>
<p>This should be the path to the guestblock package on your webserver. If, for example, it is located at:</p>
<blockquote><p>http://www.yourdomain.com/guestblock/</p></blockquote>
<p>Then you would simply enter (<span class="red">note the slashes at the start and end</span>):</p>
<blockquote><p>/guestblock/</p></blockquote>
<h2>Admin Email</h2>
<div class="row">
<label for="adminEmail">Email Address</label>
<input name="adminEmail" id="adminEmail" type="text" maxlength="255" class="long"/>
</div>
<p>This is the email contact for you, the admin user. This email address will be used if you need to recover a lost username/password.</p>
<h2>finish installation</h2>
<div class="row">
<input id="submit" class="submit" type="submit" value="Save these settings" />
</div>
</fieldset>
</form>