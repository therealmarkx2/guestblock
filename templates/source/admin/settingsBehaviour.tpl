<h1>Guestblock Behaviour</h1>
<p>Use this page to tune your Guestblock.</p>
<form action="" method="post">
<fieldset>
<legend>settings</legend>
<h2>Flood Control</h2>
<p>Flood control enables you to restrict how often a block can be laid by the same IP address.</p>
<div class="row">
<label for="flood">Flood Control</label>
<input name="flood" id="flood" type="text" class="short" value="{$flood}"/> <span>(in seconds, leave blank to turn OFF)</span>
</div>
<h2>Email Notification</h2>
<p>Notify this email address when a block is laid.</p>
<div class="row">
<label for="email">Email</label>
<input name="email" id="email" type="text" class="long" value="{$email}"/> <span>(leave blank to turn OFF)</span>
</div>
<h2>Time Zone</h2>
<p>If your server is in a different time zone you can offset the timings Guestblock uses. <strong>If the server is 3 hours ahead of you, for example, then you would enter</strong>:</p>
<blockquote><p>-3 hours</p></blockquote>
<div class="row">
<label for="timeZone">Time Zone Offset</label>
<input name="timeZone" id="timeZone" type="text" class="short" value="{$timeZone}"/> <span>(leave blank to turn OFF)</span>
</div>
<h2>Urls</h2>
<p>Modify how guestblock outputs the urls entered by block layers.</p>
<div class="row">
<label for="disableUrl">Disable urls</label>
{if $disableUrl eq 'TRUE'}
<input name="disableUrl" id="disableUrl" type="checkbox" checked="checked" value="TRUE"/>
{else}
<input name="disableUrl" id="disableUrl" type="checkbox" value="TRUE"/>
{/if}
 <span>urls can still be entered, they're just not displayed</span>
</div>
<div class="row">
<label for="overrideUrl">Override Urls</label>
<input name="overrideUrl" id="overrideUrl" type="text" class="long" value="{$overrideUrl}"/> <span>(all blocks will link to this url, leave blank to turn OFF)</span>
</div>
<h2>Messages</h2>
<p>Modify how guestblock outputs the messages entered by block layers.</p>
<div class="row">
<label for="hidePopup">Hide messages</label>
{if $hidePopup eq 'TRUE'}
<input name="hidePopup" id="hidePopup" type="checkbox" checked="checked" value="TRUE"/>
{else}
<input name="hidePopup" id="hidePopup" type="checkbox" value="TRUE"/>
{/if}
 <span>(messages can still be entered, they're just not displayed)</span>
</div>
<div class="row">
<input id="update" name="update" type="hidden" value="TRUE" />
<input id="submit" type="submit" value="Update" />
</div>
</fieldset>
</form>