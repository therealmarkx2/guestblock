<?php
/**
* @author Matthew Hadley
* @package guestblock
* @license http://www.gnu.org/licenses/gpl.txt
* @desc contains code for guestblock class
*/
/**
* @author Matthew Hadley
* @package guestblock
* @subpackage engine
* @desc guestblock class handles guestblock functions and the creation of stack and block objects
*/
class guestblock
{
	/** @var object $dbObject PEAR DB object to access database */
	var $dbObject;
	/** @var integer $activeBlockId blockid number of block that is being highlighted */
	var $activeBlockId;
	/** @var array $config string array of config variables */
	var $config;
	/** @var string $prefix database table string prefix for installation */
	var $prefix;
	/** @var boolean $auto flag to set if the guestblock instance should look for an use global variables in $_POST and $_REQUEST automatically, thus reducing the code needed to implement it, ON by default */
	var $auto = TRUE;
	/** @var boolean $auto flag to set whether to use cookies to store user info so they do not have to add it each time they lay a block, ON by default */
	var $cookie = TRUE;
	/**
	* @var array $blocks cached array of block classes.
	* All the block types are generated as their own block class (some of these classes are extened when the block type has it's own class file).
	* These classes ar stored in an array which is used to then get the imageState to be drawn as the blocks are constructed into stacks.
	* As each stack is built the class is given information about the next block being laid (such as time laid, global number, position etc) this data may alter the image that the block will show (in blocks that have extending classes the stock block variables are reset after a block is laid).
	* @see block
	*/
	var $blocks;
	/** @var array $links string array of link dates generated for a build stack (to navigate forward and back) */
	var $links;

	/**
	* constructor function
	* populates class var $config, includes related class files block and stack
	* @see stack
	* @see block
	* @param object $dbObject PEAR DB object to access database, passed by reference
	* @param string $prefix database table string prefix for installation
	*/
	function guestblock(&$dbObject, $prefix)
	{
		$this->dbObject=$dbObject;
		$this->prefix = $prefix;
		// constructor
		// include other classes
		include_once('stack.class.php');
		include_once('block.class.php');
		if($this->cookie == TRUE){
			include_once('cookie.class.php');
		}

		$this->populateConfig();
	}

	/**
	* fetch the config variables from the database
	*/
	function populateConfig(){
		$result = $this->dbObject->query('SELECT * FROM '.$this->prefix.'settings');
		while($result->fetchInto($row)){
			$this->config[$row['setting']] = $row['value'];
		}
	}

	/**
	* set the auto status of the guestblock
	* when ON the guestblock will look to use $_POST and $_REQUEST variables where necessary to reduce the code needed to implement it
	* @param boolean $value auto status
	*/
	function setAuto($value){
		$this->auto = $value;
	}

	/**
	* set the cookie use of the guestblock
	* when ON the guestblock will create a cookie for the block layer
	* @param boolean $value cookie usage
	*/
	function setCookie($value){
		$this->cookie = $value;
	}

	/**
	* Get the table prefix for the installation
	* @return string table prefix for installation
	*/
	function getTablePrefix(){
		return $this->prefix;
	}

	/**
	* get the current system time adjusted for timezone config
	* @return string formatted date|time string
	*/
	function getLocalTime(){
		// return local offset time
		return date('H:i, l jS \o\f F Y.',strtotime('now '.$this->config['timeZone']));
	}

	/**
	* set a guestblock config variable, stored in database to allow extensability
	* @param string $setting config name
	* @param string $value config value
	*/
	function setConfig($setting, $value){
		$this->config[$setting] = $value;
	}

	/**
	* get a config variable or the entire config array
	* @param string $setting config value to return, return entire array if set to NULL
	* @return mixed config variable string or array
	*/
	function getConfig($setting = NULL){
		if($setting == NULL){
			return $this->config;
		} else {
			return $this->config[$setting];
		}
	}

	/**
	* set the active blockid
	* @param integer $blockid sets the active blockid
	*/
	function setactiveBlockId($blockid){
		$this->activeBlockId = $blockid;
	}

	/**
	* get the active blockid
	* @return integer the active blockid
	*/
	function getactiveBlockId(){
		// return the active block
		return $this->activeBlockId;
	}

	/**
	* get the id of the last created stack
	* @return integer stackId of last created stack
	*/
	function getLastStackId(){
		$latestStack = $this->dbObject->getOne('SELECT MAX(stackid) FROM '.$this->prefix.'stacks');
		return $latestStack;
	}

	/**
	* get the id of the current stack
	* @return integer stackId of current stack
	*/
	function getCurrentStackId(){
		// the id of the current stack, it might not be created yet
		$latestStack=$this->getLastStackId();
		// adjust for timeZone
		$date = date(('Y-m-d'),strtotime('now '.$this->config['timeZone']));
		// check if the latest stack created is todays stack or if a new one needs to be started
		$latestStackDate = $this->dbObject->getOne('SELECT MAX(date) FROM '.$this->prefix.'stacks WHERE stackid='.$latestStack);
		// set the active stack
		if($date==$latestStackDate){
			$stackId=$latestStack;
		}else{
			$stackId=$latestStack+1;
		}
		return $stackId;
	}

	/**
	* get the id of the current stack
	* @param string $date date of stack in format "Y-m-d"
	* @return integer stackId of current stack
	*/
	function getStackIdbyDate($date){
		// return a stackid for a given date
		$stackId=$this->dbObject->getOne("SELECT stackid FROM ".$this->prefix."stacks WHERE date='$date'");
		return $stackId;
	}

	/**
	* Construct a stack of blocks
	* Creates and returns stack(s) of blocks as xhtml. Stack(s) are consturcted from either (in order of override):
	* - $startDate to $endDate
	* - $requestDate
	* - $year and $week
	* - $year, $month and $day
	* - the current system date (altered for timezone)
	* @param integer $year year of stack to create
	* @param integer $month month of stack to create
	* @param integer $day day of stack to create
	* @param integer $week display the stacks for the requested week number
	* @param string $requestDate a particular date requested directly (such as from an input form)
	* @param string $startDate date from which to start constructing stack(s)
	* @param string $endDate date from which to stop constructing stack(s)
	* @return string xhtml for requested stack(s)
	*/
	function buildStack($year=NULL, $month=NULL, $day=NULL, $week=NULL, $requestDate=NULL, $startDate=NULL, $endDate=NULL){
		if($this->auto == TRUE){
			// as part of making guestblock easy for a user to implement automatically pick up request vars
			if($_REQUEST['year'] != NULL){
				$year = $_REQUEST['year'];
			}
			if($_REQUEST['month'] != NULL){
				$month = $_REQUEST['month'];
			}
			if($_REQUEST['day'] != NULL){
				$day = $_REQUEST['day'];
			}
			if($_REQUEST['week'] != NULL){
				$week = $_REQUEST['week'];
			}
			if($_REQUEST['date'] != NULL){
				$date = $_REQUEST['date'];
			}
			if($_REQUEST['requestDate'] != NULL){
				$requestDate = $_REQUEST['requestDate'];
			}
			if($_REQUEST['startDate'] != NULL){
				$startDate = $_REQUEST['startDate'];
			}
			if($_REQUEST['endDate'] != NULL){
				$endDate = $_REQUEST['endDate'];
			}
			$this->setActiveBlockId($_REQUEST['blockid']);
		}
		// build the stack
		// build stacks around year, month and day supplied (span variable is the number of stacks to display either side of requested stack) OR display stacks between the supplied start and end dates
		// if all arguments are NULL then build stacks up to current date

		if($requestDate != NULL){
			$year = substr($requestDate,0,4);
			$month = substr($requestDate,5,2);
			$day = substr($requestDate,8,2);
		}
		// check for valid input
		if($year!=NULL & $month!=NULL & $day!=NULL){
			if(@date('Y-m-d',@strtotime("$year-$month-$day")) == FALSE){
				$year = NULL;
				$month = NULL;
				$day = NULL;
			}
		}
		if($year!=NULL & $week!=NULL & $month==NULL & $day==NULL){
			if(@date('Y-m-d',@strtotime("1/1/$year +$week weeks")) == FALSE){
				$year = NULL;
				$week = NULL;
			}
		}
		if($requestDate!=NULL){
			if(@date('Y-m-d',@strtotime($requestDate)) == FALSE){
				$requestDate = NULL;
			} else {
				$week = NULL;
			}
		}

		$lowerDate = -$this->config['span'];
		$higherDate = $this->config['span']+1;
		if($day!=NULL){
			$stackDate = date("Y-m-d H:i",mktime(1,1,1,$month,$day,$year));
			// adjust for timeZone
			$stackDate = date(('Y-m-d'),strtotime("$stackDate ".$this->config['timeZone']));
		}
		if($week!=NULL){
			$week--;
			$stackDate = date("Y-m-d H:i",strtotime("1/1/$year +$week weeks"));
			// adjust for timeZone
			$stackDate = date(('Y-m-d'),strtotime("$stackDate ".$this->config['timeZone']));
		}
		if($year==NULL & $month==NULL & $day==NULL & $week==NULL){
			// show current date
			$stackDate = date("Y-m-d H:i");
			// adjust for timeZone
			$stackDate = date(('Y-m-d'),strtotime("$stackDate ".$this->config['timeZone']));
			$lowerDate = -(2*$this->config['span']);
			$higherDate = 1;
			$defaultPosition = TRUE;
		}
		if($startDate != NULL & $endDate != NULL){
			$daysDiff = strtotime($endDate) - strtotime($startDate);
			$lowerDate = round(-($daysDiff/86400));
			$higherDate=1;
			$stackDate = date("Y-m-d",strtotime($endDate));
		}
		// get the block types to play with
		$blocks = $this->createBlocks();

		$pos=0;
		// stackHorizontalOffset set to -40 to position the stacks in columns next to each other
		$stackHorizontalOffset=-40;
		$stackIndex=$higherDate + -1*($lowerDate);

		// loop to represent the week
		for($i=$lowerDate;$i<$higherDate;$i++)
		{
			// get the unix timestamp
			$date = date("Y-m-d",strtotime("$stackDate $i day"));

			$stackId = $this->getStackIdbyDate($date);

			$stack[$pos] = new stack();
			$stack[$pos]->setDataObject($this->dbObject);
			$stack[$pos]->setTablePrefix($this->prefix);
			$stack[$pos]->setConfig($this->config);
			$stack[$pos]->setStackId($stackId);

			if($stack[$pos]->getBlockCount() >0 | $this->config['drawEmptyStacks'] == 'TRUE'){
				$stackHorizontalOffset+=40;
				$stack[$pos]->setStackDate($date);
				$stack[$pos]->setOffsetHorizontal($stackHorizontalOffset);
				$stack[$pos]->setZIndex($stackIndex--);
				$stack[$pos]->setBlocks($blocks);
				$stack[$pos]->setActiveBlockId($this->activeBlockId);

				$output .= $stack[$pos]->build();
				if($height < $stack[$pos]->getStackHeight()){
					$height = $stack[$pos]->getStackHeight();
				}
				$pos++;
			} else {
				if(strtotime(date('Y-m-d')) < strtotime($date)){
					// don't go into the future and cause an infinite loop
					break;
				} else {
					$higherDate +=1;
				}
			}
			if($this->config['spanLimiter'] != NULL){
				// limiter, building large numbers of stacks results in large webpages, prevent this from happening
				if($pos == $this->config['spanLimiter']){
					break;
				}
			}
		}
		$width = 40*$pos;
		$output = '<div id="guestblockContainer" style="height:'.($height+20).'px; width:'.$width.'px;">'.$output.'</div>';
		// populate the browse links
		if($defaultPosition == TRUE){
			// default position is for the current stack to be at the end of the displayed stacks
			// however, if a date is requested the stack is in the middle of the displayed stacks
			// to ensure default position when no date requested, set stack date to NULL
			$stackDate = NULL;

		}
		if($startDate==NULL & $endDate==NULL){
			$this->buildStackLinks($stackDate);
		}

		// return the guestblock
		return $output;
	}

	/**
	* create the array of block classes
	* Blocks are passed necessary vars (such as type, name etc). The list of vars may need to be extended if more types of relevant block data is invented
	* Block classes get more vars when placed in a stack via stack::build() (such vars relate more specifically to the position/relation in the stack, whereas these vars are more general in nature)
	* @see block
	* @see $blocks
	* @see stack::build()
	* @param bool $refresh set to TRUE to recreate the array, otherwise use a cached version
	* @return integer stackId of current stack
	*/
	function createBlocks($refresh = FALSE){
		if($this->blocks == NULL | $refresh == TRUE){
			// create all the block classes for use with the stacks
			// done once to reduce calls to database
			include_once('block.class.php');
			$blockTypes = $this->dbObject->query('SELECT COUNT('.$this->prefix.'blocks.type) AS count, '.$this->prefix.'types.* FROM '.$this->prefix.'types LEFT JOIN '.$this->prefix.'blocks ON '.$this->prefix.'types.type='.$this->prefix.'blocks.type GROUP BY '.$this->prefix.'types.type ORDER BY type');
			while($blockTypes->fetchInto($row)){
				if($row['code']=='TRUE'){
					include_once($_SERVER['DOCUMENT_ROOT'].$this->config['installPath'].'blocks/'.$row['type'].'/code/block.'.$row['type'].'.class.php');
					$class = $row['type'].'Block'		;
					$blocks[$row['type']] = new $class;
				}else{
					$blocks[$row['type']] = new block;
				}
				// inform blocks of necessary data (may need to add to list)
				$blocks[$row['type']]->setDataObject($this->dbObject);
				$blocks[$row['type']]->setTablePrefix($this->prefix);
				$blocks[$row['type']]->setType($row['type']);
				$blocks[$row['type']]->setName($row['name']);
				$blocks[$row['type']]->setCode($row['code']);
				$blocks[$row['type']]->setDescription($row['description']);
				$blocks[$row['type']]->setDisplay($row['display']);
				$blocks[$row['type']]->setActive($row['active']);
				$blocks[$row['type']]->setTimeZone($this->config['timeZone']);
				$blocks[$row['type']]->setCount($row['count']);
			}
			$this->blocks = $blocks;
		}
		return $this->blocks;
	}

	/**
	* build the block creation form for the webpage
	* @param array $userData string array of stored user details (username, email, url)
	* @return string xhtml for form
	*/
	function blockForm($userData = NULL){
		if($this->auto == TRUE){
			// automatically pick up variables
			if($_POST['username'] != NULL | $_POST['email']!= NULL | $_POST['url']!= NULL){
				// new $_POST data coming in, use that instead of the cookie
				$userData['username'] = $_POST['username'];
				$userData['email'] = $_POST['email'];
				$userData['url'] = $_POST['url'];
			} else {
				if($this->cookie == TRUE){
					$cookieObject =  new cookie($this->config['cookie']);
					$userData = $cookieObject->getCookie('gb_guestblock');
				}
			}
		}

		// get the block types to play with
		$blocks = $this->createBlocks();
		$output = '
			<a id="lay" href="#"></a>
			<div id="guestblockForm">';
		if(sizeOf($blocks) == 0){
			$output .= '<p class="red">There are currently no block types imported. Please add some blocks via the admin menu.</p>	';
		} else {
			$output .= '
			<form id="block" action="" method="post">
			<fieldset>
			<input type="hidden" name="gid" value="-1" />
	
			<div class="row">
			<label for="username">Name</label>
			<input name="username" id="username" type="text" maxlength="32" value="'.htmlspecialchars($userData['username']).'"/> <span>(max 32 chars)</span>
			</div>
			';
			if($this->config['allowEmail'] == 'TRUE'){
				$output .= '
			<div class="row">
			<label for="email">Email</label>
			<input name="email" id="email" type="text" maxlength="255" value="'.htmlspecialchars($userData['email']).'"/>
			</div>
			';
			}
			if($this->config['allowUrl'] == 'TRUE'){
				$output .= '
			<div class="row">
			<label for="url">Url</label>
			<input name="url" id="url" type="text" maxlength="255" value="'.htmlspecialchars($userData['url']).'"/>
			</div>
			';
			}
			$output .= '
			<p>Block style (<a href="about/blocks/">info on the blocks</a>) <input type="checkbox" class="borderless" name="random" value="TRUE" /> pick my block for me</p>
			<div id="guestblockSelect">';

			if($this->config['blockSelectTable'] != NULL){
				$output .= '<table>';
				$cell = 0;
			} else {
				$output .= '<ul>';
			}

			foreach($blocks as $block){
				$block->setStatus('DISPLAY');
				$block->construct();
				if($block->getDisplay() == 'TRUE' & $block->getActive() == 'TRUE'){

					$blockString = '<a href="about/blocks/#'.$block->getType().'">
				<img src="'.$this->config['installPath'].'blocks/'.$block->getType().'/images/'.$block->getImageState().'.gif" title="view info on '.$block->getName().' block" alt="view info on '.$block->getName().' block"/></a><input type="radio" name="type" value="'.$block->getType().'"/>';

					if($this->config['blockSelectTable'] != NULL){
						if($cell == $this->config['blockSelectTable']){
							$output .= '</tr>';
							$cell = 0;
						}
						if($cell == 0){
							$output .= '<tr>';
						}
						$output .= '<td>'.$blockString.'</td>';
						$cell++;

					} else {
						$output .= '<li>'.$blockString.'</li>';
					}
				}
			}

			if($this->config['blockSelectTable'] != NULL){
				if($cell != 1){
					$output .= '<td colspan="'.($this->config['blockSelectTable']-$cell).'"></td></tr>';
				}
				$output .= '</table>';
			} else {
				$output .= '</ul>';
			}

			$output.='</div><p>Inscribe your block with a message (max 100 chars) (no markup)</p>
			<input name="message" id="message" type="text" maxlength="100" />
			<p><input name="submit" id="submit" class="submit" type="submit" value="Lay Your Block" /></p>
			</fieldset>
			</form>';		
		}
		$output.='</div>';
		return $output;
	}

	/**
	* Check if new block being laid is in conflict with flood control. Flood control prevents a block being laid from the same IP address within a set time limit.
	* Flood control is in seconds and is set via the setConfig() function.
	* <code>
	* // set flood control to one minute
	* $guestblockObject->setConfig('floodControl','60');
	* </code>
	* @see setConfig()
	* @param array $userData string array of stored user details (username, email, url)
	* @return string xhtml for form
	*/
	function floodControl() {
		// determine if another block can be laid yet by posting IP
		if($this->config['floodControl'] == NULL){
			// flood control is off
			return FALSE;
		}
		$floodTime = $this->dbObject->getOne("SELECT timestamp FROM ".$this->prefix."flood WHERE ip ='".$_SERVER['REMOTE_ADDR']."' ORDER BY timestamp DESC");
		if($floodTime == NULL){
			$floodControl = FALSE;
		}else{
			if((strtotime ($floodTime.' + '.$this->config['floodControl'].' seconds')) < strtotime("now ".$this->config['timeZone'])){
				$floodControl = FALSE;
			}else{
				$floodControl = TRUE;
				$nextDrought = date('Y-m-d H:i:s',strtotime($floodTime.' + '.$this->config['floodControl'].' seconds'));
			}
		}

		// delete old flood control entries
		$lastFloodTime = strtotime("now -".$this->config['floodControl']." seconds");
		// adjust for timeZone
		$lastFloodTime = strtotime(date('Y-m-d H:i',"$lastFloodTime ".$this->config['timeZone']));
		$lastFloodTime = date('Y-m-d H:i',$lastFloodTime);
		$this->dbObject->query("DELETE FROM ".$this->prefix."flood WHERE timestamp < '".$lastFloodTime."'");

		return array($floodControl, $nextDrought);
	}

	/**
	* Detects if a new block is being laid
	* No arguments are accepted to reduce the user intervention required to call the function. This is part of making the guestblock code more useable
	* On successful block laying the page is redirected. If the block failed then ther error message is returned.
	* function works in bot auto and non-auto mode
	* @param string $type chosen block type
	* @param bool $random should the block be randomly chosen, overrides $type
	* @param string $name the name of the block layer
	* @param string $email the email for the block layer
	* @param string $url the url for the block layer
	* @param string $message the message of the block layer
	* @return string error warning to display if the block failed to be laid.
	*/
	function processBlock($type = NULL, $random = FALSE, $name = NULL, $email = NULL, $url = NULL, $message = NULL){
		if($this->auto == TRUE){
			if($_POST['gid'] == NULL){
				// not trying to lay a block, called in auto mode
				return NULL;
			}
			// try to add a block using global POST variables
			$retVal = $this->addBlock($_POST['type'], $_POST['random'], $_POST['username'], $_POST['email'], $_POST['url'], $_POST['message']);
			// as we are on auto let guestblock create the cookie
			if($this->cookie == TRUE){
				$cookieObject =  new cookie($this->config['cookie']);
				$cookieData['username']=$_POST['username'];
				$cookieData['email']=$_POST['email'];
				$cookieData['url']=$_POST['url'];
				$cookieObject->setCookie('gb_guestblock', $cookieData, time()+60*60*24*365*10, $this->config['installPath']);
			}
		} else {
			// try to add a block using supplied variables
			$retVal = $this->addBlock($type, $random, $name, $email, $url, $message);
		}
		if($retVal['status']=='PASS'){
			if($this->auto == TRUE){
				// success
				header('location: '.$_SERVER['PHP_SELF'].'?blockid='.$this->activeBlockId);
				exit;
			}
		} elseif($retVal['status']=='FAIL') {
			switch($retVal['reason']){
				case 'SPAM':{
					// spam found in the block
					$error = '<p class="red"><b>Spam control</b> is in effect, the block is in a moderation queue for approval.</p>';
					break;
				}
				case 'FLOOD CONTROL':{
					// failed flood control
					$error = '<p class="red"><b>Flood control</b> is in effect, this ip address attempted to add a block recently, the next block from this ip address can be laid '.$retVal['drought'].'</p>';
					break;
				}
			}
		}
		return $error;
	}

	/**
	* Adds a new block to the guestblock.
	* Flood control is checked and a new stack is created as necessary. Position deviation is calculated. Easter egg block occurrence is assessed. Optionally, spam and bad word filters are used on input.
	* @param string $type chosen block type
	* @param bool $random should the block be randomly chosen, overrides $type
	* @param string $name the name of the block layer
	* @param string $email the email for the block layer
	* @param string $url the url for the block layer
	* @param string $message the message of the block layer
	* @return string success status array
	*/
	function addBlock($type, $random = FALSE, $name, $email, $url, $message){
		// check flood Control
		$floodCheck = $this->floodControl();
		if($floodCheck[0] == TRUE){
			$status = array(
			'status'=>'FAIL',
			'reason'=>'FLOOD CONTROL',
			'drought'=>$floodCheck[1]
			);
			return $status;
		}
		// add a block to the stack
		// build the stack foundation
		$currentStackId = $this->getCurrentStackId();
		if($currentStackId > $this->getLastStackId()){
			// adjust for timeZone
			$date = date(('Y-m-d'),strtotime("now ".$this->config['timeZone']));
			// create stack first
			$this->dbObject->query("INSERT INTO ".$this->prefix."stacks (date) VALUES ('$date')");
		}
		$selected = 'SELECTED';
		if($type == NULL | $random == TRUE){
			$random = TRUE;
			$selected = 'RANDOM';
		}
		// get the block number are we adding
		$blockGlobalNumber = $this->dbObject->getOne('SELECT COUNT(blockid) FROM '.$this->prefix.'blocks');
		$blockGlobalNumber++;
		// loop is in place for testing purposes only
		for($i=0;$i<1;$i++){
			// deviation is used to offset the placing of the block horizontally
			// max:  7
			// mean: 4
			// min:  1

			$deviation=rand(1,110);
			switch ($deviation)
			{
				case $deviation<=5:
				{
					$horizontalPosition=1;
					break;
				}
				case $deviation<=15:
				{
					$horizontalPosition=2;
					break;
				}
				case $deviation<=35:
				{
					$horizontalPosition=3;
					break;
				}
				case $deviation<=75:
				{
					$horizontalPosition=4;
					break;
				}
				case $deviation<=95:
				{
					$horizontalPosition=5;
					break;
				}
				case $deviation<=105:
				{
					$horizontalPosition=6;
					break;
				}
				case $deviation<=110:
				{
					$horizontalPosition=7;
					break;
				}
			}
			// deviation is used to offset the placing of the block vertically
			// max:  6
			// mean: 4
			// min:  1
			// note this is not symmetrical but gives best results
			$deviation=rand(1,105);
			switch ($deviation)
			{
				case $deviation<=5:
				{
					$verticalPosition=1;
					break;
				}
				case $deviation<=15:
				{
					$verticalPosition=2;
					break;
				}
				case $deviation<=35:
				{
					$verticalPosition=3;
					break;
				}
				case $deviation<=75:
				{
					$verticalPosition=4;
					break;
				}
				case $deviation<=95:
				{
					$verticalPosition=5;
					break;
				}
				case $deviation<=105:
				{
					$verticalPosition=6;
					break;
				}
			}
			// get the blocks to play with
			$blocks = $this->createBlocks();
			// ensure selected block is a legal type
			foreach($blocks as $block){
				if($block->getDisplay() == 'TRUE' & $block->getActive() == 'TRUE')
				{
					$legalType[] = $block->getType();
				}
			}
			// choose a randon block
			if($random == TRUE){
				$type = $legalType[array_rand($legalType)];
			}
			// make sure a user has not selected a non-display or non-active block
			if($blocks[$type]->getDisplay() == 'FALSE' | $blocks[$type]->getActive() == 'FALSE'){
				// give user a legal type instead
				$type = $legalType[array_rand($legalType)];
			}

			// determine if an easter egg type block will override the selection
			foreach($blocks as $block){
				// tell block pertinent information - just the globalblocknumber right now
				$block->setGlobalNumber($blockGlobalNumber);

				// block has to have an override value > 0 (and be active) to be considered an easter egg
				// override is a coded value present in the block type class file
				if($block->getOverride() > 0  & $block->getActive() == 'TRUE')
				{
					$easterEgg[$block->getrank()] = array(
					'type' => $block->getType(),
					'override' => $block->getOverride(),
					'rank' => $block->getrank());
				}
			}
			if($easterEgg != NULL){
				// sort easter eggs in order of rank
				rsort($easterEgg);

				foreach($easterEgg as $block){
					$ran = rand(1,$block['override']);
					if($ran==1){
						// easter egg found
						$type = $block['type'];
						$selected = 'OVERRIDE';
						// don't loop through other possible easter egg blocks, we have our block
						$easterEgg = NULL; // for when loop testing
						break;
					}
				}
			}

			// check that block submitted contains valid url
			if (strpos($url, 'http://') === FALSE & $url!=NULL)
			{ // note: three equal signs
			$url='http://'.$url;
			}
			// purge user content
			$name = addslashes($name);
			$email = addslashes($email);
			$url = addslashes($url);
			$message = addslashes($message);
			// check for spam approval
			$spamApproved = $this->checkSpamClear($name, $email, $url, $message, $_SERVER['REMOTE_ADDR']);
			if($spamApproved == TRUE){
				$approve = 'TRUE';
			} else {
				$approve = 'FALSE';
			}
			if($this->config['approveAll'] == 'TRUE'){
				$approve = 'FALSE';
			}

			if($this->config['badWords'] != NULL){
				// replace bad words
				$name = $this->replaceBadWords($name);
				$message = $this->replaceBadWords($message);
			}

			// check what should be stored
			if($this->config['allowUrl'] == 'FALSE'){
				$url = NULL;
			}
			if($this->config['allowEmail'] == 'FALSE'){
				$email = NULL;
			}

			// adjust for timeZone
			$time = date(('H:i:s'),strtotime("now ".$this->config['timeZone']));
			$timestamp = date(('Y-m-d H:i:s'),strtotime("now ".$this->config['timeZone']));

			// add the block to the database
			$query="INSERT INTO ".$this->prefix."blocks (type,time,name,email,url,message,ip,stackid,verticalpos,horizontalpos,approved,selected) VALUES ('$type','$time','$name','$email','$url','$message','".$_SERVER['REMOTE_ADDR']."',".$this->getCurrentStackId().",$verticalPosition,$horizontalPosition,'$approve','$selected ')";
			$this->dbObject->query($query);
			// set the active blockid so an arrow can be drawn next to it
			$this->activeBlockId = mysql_insert_id();

			// update flood control
			$query="INSERT INTO ".$this->prefix."flood (ip, timestamp) VALUES ('".$_SERVER['REMOTE_ADDR']."','$timestamp')";
			$this->dbObject->query($query);

			// email notification
			if($this->config['emailNotification'] != NULL){
				$this->sendNotification($this->config['emailNotification'], $this->activeBlockId, $name, $email, $url, $message, $_SERVER['REMOTE_ADDR']);
			}

			if($spamApproved == FALSE | $this->config['approveAll'] == 'TRUE'){
				// check if need to nofiy about spam block
				if($this->config['spamNotification'] != NULL){
					$this->sendNotification($this->config['spamNotification'], $this->activeBlockId, $name, $email, $url, $message, $_SERVER['REMOTE_ADDR'], TRUE);
				}
				if($this->config['spamIpCapture'] == 'TRUE'){
					if (strpos($this->config['spamWords'], $_SERVER['REMOTE_ADDR']) === FALSE)
					{ // note: three equal signs
					if($this->config['spamWords'] == '' | $this->config['spamWords'] == NULL){
						$newSpam = $_SERVER['REMOTE_ADDR'];
					} else {
						$newSpam = $this->config['spamWords'].chr(10).$_SERVER['REMOTE_ADDR'];
					}
					$this->dbObject->query('UPDATE '.$this->prefix.'settings set value="'.$newSpam.'" WHERE setting="spamWords"');
					}
				}

				$status = array(
				'status'=>'FAIL',
				'reason'=>'SPAM');
				return $status;
			} else {
				$status = array(
				'status'=>'PASS');
				return $status;
			}
		}
	}

	/**
	* Bad word filter.
	* Replaces user defined bad words in $string with [!]
	* @param string $string string to filter
	* @return string filtered string
	*/
	function replaceBadWords($string){
		$badWordsDictionary = explode(chr(10),$this->config['badWords']);
		$searchDictionary = explode(' ', $string);
		foreach($searchDictionary as $key => $searchWord){
			$searchWord = strtolower($searchWord);
			if(in_array($searchWord, $badWordsDictionary) == TRUE){
				$searchDictionary[$key] = '[!]';
			}
		}
		$string = implode(' ', $searchDictionary);
		return $string;
	}

	/**
	* Checks for user defined spam words in block strings
	* @param string $name name entered for block layer
	* @param string $email email entered for block layer
	* @param string $url url entered for block layer
	* @param string $message message by block layer
	* @return bool status of spam clear
	*/
	function checkSpamClear($name, $email, $url, $message, $ip){
		if($this->config['spamWords'] == NULL){
			return TRUE;
		}
		$spamClear = TRUE;
		$spamDictionary = explode(chr(10),$this->config['spamWords']);

		$name = explode(' ',strtolower($name));
		$email = explode(' ',strtolower($email));
		$url = explode(' ',strtolower($url));
		$message = explode(' ',strtolower($message));
		$ip = array($ip);

		foreach($spamDictionary as $spam){
			if(in_array($spam, $ip) == TRUE){
				return FALSE;
			}
			if(in_array($spam, $name) == TRUE){
				return FALSE;
			}
			if(in_array($spam, $email) == TRUE){
				return FALSE;
			}
			if(in_array($spam, $url) == TRUE){
				return FALSE;
			}
			if(in_array($spam, $message) == TRUE){
				return FALSE;
			}
		}
		return $spamClear;
	}

	/**
	* Issues email notifications of a new block being laid
	* @param string $notify email address to be notified
	* @param integer $blockid blockid of block that message relates to
	* @param string $name name entered for block layer
	* @param string $email email entered for block layer
	* @param string $url url entered for block layer
	* @param string $message message by block layer
	* @param string $ip ip address of block layer
	* @param bool $spam flag for if the block has been identified as spam
	*/
	function sendNotification($notify, $blockid, $name, $email, $url, $message, $ip, $spam=FALSE){
		// send out email notification
		$headers .= "MIME-Version: 1.0\r\n";
		$headers .= "Content-type: text/plain; charset=iso-8859-1\r\n";
		$headers .= "From: \"Guestblock\" <Guestblock@".$_SERVER['SERVER_NAME'].">\r\n";

		if($spam == TRUE){
			$content = "A new block has been laid on your guestblock. The block is in the moderation queue awaiting approval. \r\n\r\n";
		} else {
			$content = "A new block has been laid on your guestblock. \r\n\r\n";
		}

		$content .= "By: $name \r\n";
		$content .= "Email: $email \r\n";
		$content .= "Url: $url \r\n";
		$content .= "Whois: http://ws.arin.net/cgi-bin/whois.pl?queryinput=$ip \r\n\r\n";
		$content .= "Message: $message \r\n\r\n";

		if($spam == TRUE){
			$content .= "The block is being held in the moderation queue: http://".$_SERVER['HTTP_HOST'].$this->config['installPath'].'admin/stacksModeration.php';
			$subject = 'new block laid on your Guestblock [awaiting approval]';
		} else {
			$content .= "You may view the block here: http://".$_SERVER['HTTP_HOST'].$this->config['installPath']."?blockid=".$blockid;
			$subject = 'new block laid on your Guestblock';
		}

		mail($notify,$subject,$content,$headers);
	}

	/**
	* Create the url links for stack navigation
	* @param string $stackDate the date of the stack being displayed
	*/
	function buildStackLinks($stackDate)
	{
		// get the number of stacks to show either side
		$span = $this->getConfig('span');
		if($stackDate == NULL){
			$olderSpan = $span+(2*$span);
			$newerSpan = $span;
		} else {
			$olderSpan = 2*$span;
			$newerSpan = 2*$span;
		}
		// format the stack date
		$stackDate = date('Y-m-d',strtotime($stackDate));

		// create links
		$olderDate = date('Y-m-d',strtotime("$stackDate -$olderSpan days"));
		$olderYear = substr($olderDate,0,4);
		$olderMonth = substr($olderDate,5,2);
		$olderDay = substr($olderDate,8,2);

		$newerDate = date('Y-m-d',strtotime("$stackDate +$newerSpan days"));
		$newerYear = substr($newerDate,0,4);
		$newerMonth = substr($newerDate,5,2);
		$newerDay = substr($newerDate,8,2);

		$olderLink = "?year=$olderYear&amp;month=$olderMonth&amp;day=$olderDay";
		$newerLink = "?year=$newerYear&amp;month=$newerMonth&amp;day=$newerDay";

		$this->links = array(
		'date' => $stackDate,
		'older' => $olderLink,
		'newer' => $newerLink);
	}

	/**
	* Return the links for guestblock navigation
	* @return array array cotaining xhtml links
	*/
	function getStackLinks(){
		return $this->links;
	}

	/**
	* Return the xhtml for stack browsing, typically used as part of the auto setup
	* @return string xhtml for stack navigation
	*/
	function buildBrowser(){
		$browser ='
		<div id="guestblockBrowse">
		<div class="link">
		<img title="view older stacks" alt="older stacks" src="'.$this->config['installPath'].'images/admin/stackOld.gif"/> <a title="view older stacks" href="'.$_SERVER['PHP_SELF'].$this->links['older'].'">Older</a>
		</div>
		<div id="browse">
		<img title="view stacks by date" alt="calendar stacks" src="'.$this->config['installPath'].'images/admin/calendar.gif"/> By date <span id="format">YYYY-MM-DD</span>
		<form action="" method="post">
		<p><input id="requestDate" name="requestDate" maxlength="10" type="text" value="'.$this->links['date'].'"/> <input type="submit" value="Display"/></p>
		</form>
		</div>
		<div class="link">
		<img title="view newer stacks" alt="newer stacks" src="'.$this->config['installPath'].'images/admin/stack.gif"/> <a title="view newer stacks" href="'.$_SERVER['PHP_SELF'].$this->links['newer'].'">Newer</a>
		</div>
		<hr class="cleaner" />
		</div>';
		return $browser;
	}

}