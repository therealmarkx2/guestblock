<h2>file permissions</h2>
<p>Certain files/folders need to be writable for guestblock to function.</p>
<h2>settings file</h2>
<p>As part of the installation the following file needs to be writable:</p>
<blockquote><p>config.php</p></blockquote>
<p>Checking writable status of config.php...
{if $settingsWritable eq "TRUE"}
<span class="green">Ok</span></p>
<p class="green">Good, the file is writable.</p>
<p>Once installation is complete this file should be changed to <i>read-only</i>. Chmod the config.php so that it is not writable by the webserver.</p>
<h2>continue installation</h2>
<p>The next step is to check the <a title="continue the installation" href="install.php?action=database">database access</a>.</p>
{elseif $settingsWritable eq "FALSE"}
<span class="red">FAIL</span></p>
<p class="red">Warning! This file is currently read-only. To continue successfully you must make this file writable. You should CHMOD this file to 777 then refresh this page.</p>
<p><a title="refresh page" href="install.php?action=files">Refresh page</a>.</p>
{/if}