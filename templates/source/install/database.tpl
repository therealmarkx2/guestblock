<h2>database access</h2>
<p>Guestblock is designed to work using a MySQL database. This step will create the required tables in a database that you should have already created.</p>
<h2>database settings</h2>
<p>Please provide the following database details.</p>
<form class="install" action="install.php?action=databaseCheck" method="post">
<fieldset>
<legend>data access</legend>
<h2>database details</h2>
<div class="row">
<label for="username">Username</label>
<input name="username" id="username" type="text" />
</div>
<div class="row">
<label for="password">Password</label>
<input name="password" id="password" type="text" />
</div>
<div class="row">
<label for="database">Database Name</label>
<input name="database" id="database" type="text" />
</div>
<div class="row">
<label for="host">Database Host</label>
<input name="host" id="host" type="text" value="localhost"/>
</div>
<div class="row">
<label for="prefix">Table Prefix</label>
<input name="prefix" id="prefix" type="text" value="gb_"/>
</div>
<h2>PEAR installation</h2>
<p>Guestblock uses PEAR for the database abstraction layer. This basically means that a PEAR implementation is used to interact with your database. Guestblock needs to know the location of your PEAR installation in order to function.</p>
<div class="row">
<label for="pear">PEAR Location</label>
<input name="pear" id="pear" type="text"/>
</div>
<p>If you intend to use the PEAR distribution that may be bundled with your PHP installation then leave the above box blank and guestblock will attempt to use that distribution.</p>
<p>Otherwise, If you have copied the pear files somewhere onto your webserver enter the path to them here. You should enter the path to the directory in the same way as you did for locating the Smarty package. Again, for <strong>security reasons</strong> you can have the pear files outside of your public web space.</p>
<h2>continue installation</h2>
<div class="row">
<input id="submit" class="submit" type="submit" value="Create tables to continue" />
</div>
</fieldset>
</form>