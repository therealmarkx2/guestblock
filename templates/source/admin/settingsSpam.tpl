<h1>Spam control</h1>
<p>Use these settings to help prevent block spam and bad language. Blocks that are caught by the spam settings will need to be approved before they appear in a stack.</p>
<form action="" method="post">
<fieldset>
<legend>spam</legend>
<h2>Spam word filter</h2>
<p>Hold block in queue if it has any of the following (each entry on a new line), you can also enter ip addresses (if you later approve a block that contained a banned ip address that ip is automatically removed from this list).</p>
<div class="row">
<label for="spam">Spam word filter</label>
<textarea  name="spam" id="spam" class="long"/>{foreach from=$spamWords item=spam}{$spam}{/foreach}</textarea>
</div>

<p>If a block is identified as containing word spam you can automatically have the IP address added to the future spam checks.</p>
<div class="row">
<label for="spamIpCapture">Record IP</label>
{if $spamIpCapture eq 'TRUE'}
<input name="spamIpCapture" id="spamIpCapture" type="checkbox" checked="checked" value="TRUE"/>
{else}
<input name="spamIpCapture" id="spamIpCapture" type="checkbox" value="TRUE"/>
{/if}
</div>

<p>Alternatively you can specify that all blocks require approval.</p>
<div class="row">
<label for="approveAll">Approve all blocks</label>
{if $approveAll eq 'TRUE'}
<input name="approveAll" id="approveAll" type="checkbox" checked="checked" value="TRUE"/>
{else}
<input name="approveAll" id="approveAll" type="checkbox" value="TRUE"/>
{/if}
</div>
<h2>Notification</h2>
<p>Notify this email address when a block requires approval.</p>
<div class="row">
<label for="spamNotification">Email</label>
<input name="spamNotification" id="spamNotification" type="text" class="long" value="{$spamNotification}"/> <span>(leave blank to turn OFF)</span>
</div>
<h2>Url and email posting</h2>
<p>Select if you wish to give block layers the option to enter a url or email.</p>
<div class="row">
<label for="url">Allow url</label>
{if $allowUrl eq 'TRUE'}
<input name="url" id="url" type="checkbox" checked="checked" value="TRUE"/>
{else}
<input name="url" id="url" type="checkbox" value="TRUE"/>
{/if}
</div>
<div class="row">
<label for="email">Allow email</label>
{if $allowEmail eq 'TRUE'}
<input name="email" id="email" type="checkbox" checked="checked" value="TRUE"/>
{else}
<input name="email" id="email" type="checkbox" value="TRUE"/>
{/if}
</div>
<h2>Bad word filter</h2>
<p>Replace the following words (each word on a new line) if they appear in a block.</p>
<div class="row">
<label for="bad">Bad word filter</label>
<textarea  name="bad" id="bad" class="long"/>{foreach from=$badWords item=bad}{$bad}{/foreach}</textarea>
</div>
<div class="row">
<input id="update" name="update" type="hidden" value="TRUE" />
<input id="submit" type="submit" value="Update" />
</div>
</fieldset>
</form>
<form action="" method="post">
<fieldset>
<a href="#" id="filter"></a>
<legend>Retrospective Filtering</legend>
<h2>Filtering</h2>
<p>If you have updated your spam and/or bad word filters you can apply the changes to the existing blocks that have been laid.</p>
<p>There are currently <span class="red">{if $spamCount eq NULL}0{else}{$spamCount}{/if}</span> block(s) in the spam <a title="view moderation queue" href="stacksModeration.php">moderation queue</a>.</p>
{if $retroAttempt eq 'TRUE'}
<p><span class="red">{$retroSpam}</span> block(s) have been added to the <a title="view moderation queue" href="stacksModeration.php">moderation queue</a>.</p>
<p><span class="red">{$retroBad}</span> block(s) have had bad language amended.</p>
{if $retroSpam eq 0 & $retroBad eq 0}<p class="red">No blocks matched.</p>{/if}
{/if}
<div class="row">
<input id="retro" name="retro" type="hidden" value="TRUE" />
<input id="submit" type="submit" value="Filter" />
</div>
</fieldset>
</form>