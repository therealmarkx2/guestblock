<?php
/**
* @author Matthew Hadley
* @package guestblock
* @license http://www.gnu.org/licenses/gpl.txt
* @desc contains code for stack class
*/
/**
* @author Matthew Hadley
* @package guestblock
* @subpackage engine
* @desc stack class handles stack functions and the creation of stacks
*/
class stack
{
	/** @var object $dbObject PEAR DB object to access database */
	var $dbObject;
	/** @var mixed $date date of stack, mixed type so that when 'undefined' we know the stack has not even been built */
	var $date = 'undefined';
	/** @var intger $id id of stack */
	var $id;
	/** @var intger $offsetHorizontal horizontal positioning of stack so that stacks are created next to each other */
	var $offsetHorizontal;
	/** @var intger $index z-index of stack so that z-orederin of stacks that are next to each other is correct */
	var $index;
	/** @var intger $height height of the stack */
	var $height;
	/**
	* @var array $blocks cached array of block classes.
	* All the block types are generated as their own block class (some of these classes are extened when the block type has it's own class file).
	* These classes ar stored in an array which is used to then get the imageState to be drawn as the blocks are constructed into stacks.
	* As each stack is built the class is given information about the next block being laid (such as time laid, global number, position etc) this data may alter the image that the block will show (in blocks that have extending classes the stock block variables are reset after a block is laid).
	* @see block
	*/
	var $blocks;
	/** @var mixed $blockCount the number of blocks in the stack, mixed type so that when 'undefined' we know the stack has not even been built */
	var $blockCount = 'undefined';
	/** @var string $prefix database table string prefix for installation */
	var $prefix;
	/** @var array $config string array of config variables */
	var $config;
	
	function stack()
	{
		// constructor
	}
	
	/**
	* set the config variables from the database
	* @var array $config string array of config variables
	*/
	function setConfig($config){
		$this->config = $config;
	}
	
	/**
	* set the block classes to use
	* @param array $blocks the block classes to play with
	* @see guestblock::$blocks
	*/
	function setBlocks($blocks){
		$this->blocks = $blocks;
	}
	
	/**
	* Set the PEAR DB object to access the database with
	* @param object $dbObject PEAR DB object to access database, passed by reference
	*/
	function setDataObject(&$dbObject){
		$this->dbObject=$dbObject;
	}
	
	/**
	* Set the table prefix for the installation
	* @param string $prefix the table prefix for the installation
	*/
	function setTablePrefix($prefix){
		$this->prefix = $prefix;	
	}
	
	/**
	* Get the table prefix for the installation
	* @return string the stack height, in blocks
	*/
	function getStackHeight() {
		return $this->height;
	}
	
	/**
	* Set the stack id
	* @param integer $id the stack id
	*/
	function setStackId($id){
		$this->id=$id;
	}
	
	/**
	* Set the stack horizontal position
	* @param integer $offset the horizontal position
	*/
	function setOffsetHorizontal($offset){
		$this->offsetHorizontal = $offset;
	}
	
	/**
	* Set the stack z-index
	* @param integer $index the z-index
	*/
	function setZIndex($index){
		$this->index=$index;
	}
	
	/**
	* Set the active block id, this block will be highlghted on screen, this would happen when a block has just been laid
	* @param integer $id id of block to be highlighted
	*/
	function setActiveBlockId($id){
		$this->activeBlockId=$id;
	}

	/**
	* Get the date of the stack
	* @return string the date of the stack, if it has been created
	*/
	function getStackDate(){
		if($this->id != NULL){
			$this->date=$this->dbObject->getOne('SELECT date FROM '.$this->prefix.'stacks WHERE stackid='.$this->id);
			return $this->date;
		}else{
			// stack has not been constructed yet
			return NULL;
		}
	}
	
	/**
	* Set the date of the stack
	* @param string $date the stack date
	*/
	function setStackDate($date){
		// set the stack date
		$this->date = $date;
	}
	
	/**
	* Get the number of block in the stack
	* @return integer the number of blocks that make up the stack
	*/
	function getBlockCount(){
		if($this->id == NULL){
			// stack not yet created
			return 0;
		}else{
			$this->blockCount=$this->dbObject->getOne('SELECT COUNT(blockid) FROM '.$this->prefix.'blocks WHERE stackid='.$this->id);
			if($this->blockCount == NULL){
				$this->blockCount = 0;
			}
			return $this->blockCount;
		}
	}
	
	/**
	* Get the xhtml for the stack label. The label is the stack date written vertically
	* @return string xhtml stack date
	*/
	function getStackLabel(){
		// try to populate date if don't have it
		if($this->date == NULL & $this->id != NULL){
			$stackDate=$this->dbObject->getOne('SELECT UNIX_TIMESTAMP(date) FROM '.$this->prefix.'stacks WHERE stackid='.$this->id);
		}else{
			$stackDate = $this->date;
		}
		$day = date('l',strtotime($stackDate));
		
		for($j=0;$j<strlen($day);$j++)
		{
			$dayString.=$day[$j].'<br/>';
		}
		$datestring = date('jS',strtotime($stackDate)).'<br/>'.date('M',strtotime($stackDate));
		$dayString = '<br/>'.$dayString;
		$stackLabel = '<span class="stackTitle" title="'.$this->getBlockCount().' block(s) in this stack">'.$datestring.'</span><span class="stackTitleDay">'.$dayString.'</span>';
		return $stackLabel;
	}
	
	/**
	* Build the stack and return the xhtml for it. Individual blocks in the stack are constructed from the relevant type class by passing pertinent data to the class which then generates the image state.
	* @return string xhtml stack code
	*/
	function build(){	
		// create the stack
		
		// gather the block types
		$blocks = $this->blocks;
		
		// get the global block number to tell a block what number it is in the big picture
		// $blockGlobalNumber is a running count of the total number of blocks (so different from blockid)
		$blockGlobalNumber = $this->dbObject->getOne('SELECT COUNT(blockid) FROM '.$this->prefix.'blocks WHERE stackid < '.$this->id);
		
		// counter for blocks in this stack, this counter is incremented as each block is laid to give position of next block
		$blockCount=0;
		
		// make sure we know how many blocks are in the stack
		if($this->blockCount=='undefined'){
			$this->getBlockCount();
		}
		
		// set offset for correct vertical positioning of stack
		$verticalPos=225;
		
		// reset the shadow laid flag
		$shadowDrawn=NULL;
		
		//  construct the div holding the stack
		$output.='<div style="position:absolute;left:'.$this->offsetHorizontal.'px;bottom:0px;z-index:'.$this->index.';">';
		
		// gather the blocks, if the stack has been started
		if($this->id != NULL) {
			$query = 'SELECT * FROM '.$this->prefix.'blocks WHERE stackid='.$this->id.' ORDER BY time';
			$result=$this->dbObject->query($query);
			$output.="\r\n";
			while ($result->fetchInto($row))
			{
				if($row['approved'] == 'TRUE'){
					// only lay approved blocks
					$blockGlobalNumber++;
					
					// reset message vars
					$message = NULL;
					$formatMessage = NULL;
					$format = NULL;
			
					// increment the z-index for the next block
					$zindex++;
					// increment the stack block count
					$blockCount++;
					// the standard vertical offest to make the stack grow is 5
					$verticalPos+=5;
					// add the inidividual block's vertical offset
					$verticalPos+=$row['verticalpos'];
					// add the inidividual block's horizontal offset
					$horizontalPos=$row['horizontalpos'];
					
					// tell the blocks what day and time they were laid in case it means something to them
					if($this->date == 'undefined'){
						$this->getStackDate();
					}
					
					// tell the blocks where they are
					$blocks[$row['type']]->setStatus('STACK');

					$blocks[$row['type']]->setLayDate($this->date);
					$blocks[$row['type']]->setLayTime($row['time']);
					
					// tell the block the message it will be displaying in case it means something to it
					$blocks[$row['type']]->setMessage($row["message"]);
					
					// tell the block who laid it in case it means something to it
					$blocks[$row['type']]->setLaidBy($row["name"]);
					
					// get the position of the next block to be laid
					if($this->blockCount == $blockCount){
						// next block will be at the top of the stack
						$blocks[$row['type']]->setPosition('TOP');
					}else{
						$blocks[$row['type']]->setPosition($blockCount);
					}
					
					// tell the block it's global number
					$blocks[$row['type']]->setGlobalNumber($blockGlobalNumber);
					
					// construct the block
					$blocks[$row['type']]->construct();
					
					// cloud generation
					if($blockCount>9){
						// minimum cloud cover height is above 10
						$cloudChance=rand(1,20); // chance of a cloud appearing
					}
					if($cloudChance==1){
						// a cloud is going to appear!
						$cloudChance=NULL;
						// determine the z-index position of the cloud
						$zPosition=rand(0,1);
						if($zPosition==0){
							$zPosition=998;
						}else{
							$zPosition=1;
						}
						// determine the horizontal offset of the cloud
						$cloudHorizontalPosition=rand(-8,16);
						// populate cloud data
						$output.='<div onmouseover="this.style.zIndex=1" style="position:absolute;width:50px;height:10px;z-index:'.$zPosition.';left:'.$cloudHorizontalPosition.'px;bottom:'.$verticalPos.'px;"><img alt="cloud" title="cloud" style="position:relative;left:-12px" src="'.$this->config['installPath'].'images/stack/cloud.gif"/></div>';
					}
					// lay down an arrow next to the active block
					if($row['blockid']==$this->activeBlockId){
						$output.='<div style="position:absolute;z-index:999;left:-12px;bottom:'.($verticalPos+5).'px;"><img alt="alert" style="position:relative;left:-12px" src="'.$this->config['installPath'].'images/stack/arrow.gif"/></div>';
					}
					
					// clear the block wording
					$name=$row['name'];
					$blockOwner=$row['name'];
					$url=NULL;
					$linkStart=NULL;
					$linkEnd=NULL;
					$subLine=NULL;
					
					// populate block wording
					if($row['name'] == NULL){
						$name = 'nobody';
						$blockOwner = 'nobody';
					}
					if($this->config['disableUrl'] != 'TRUE'){
						if($this->config['overrideUrl'] == NULL | $this->config['overrideUrl'] == ''){
							$blocks[$row['type']]->setUrl(htmlspecialchars($row['url']));
							$url = $blocks[$row['type']]->getUrl();
						} else {
							$url = $this->config['overrideUrl'];
						}
						if($url == NULL){
							$linkClass = 'class="none"';
						}else{
							$linkClass = NULL;
						}
					}  else {
						$linkClass = NULL;
					}			
					$horizontalPos += $blocks[$row['type']]->getOffsetHorizontal();

					// construct the block
					$output.="\r\n";
					$output.='<div style="position:absolute;z-index:'.$zindex.';left:'.$horizontalPos.'px;bottom:'.$verticalPos.'px;">';
					$output.="\r\n";
					$output.='<a id="id'.$row["blockid"].'" href="#"></a>';
					$output.="\r\n";
					$output.='<a '.$linkClass.' href="'.$url.'">';
					
					$output.='<img alt="" src="'.$this->config['installPath'].'blocks/'.$blocks[$row['type']]->getType().'/images/'.$blocks[$row['type']]->getImageState().'.gif" />';
					
					$blocks[$row['type']]->setMessage($row["message"]);
					$message = $blocks[$row['type']]->getMessage();
					
					if($blocks[$row['type']]->getSubLine()!=NULL){
						$subLine = $blocks[$row['type']]->getSubLine();
					}
					// split the message if it contains long unbroken string
					$message = explode(" ",$message);
					foreach($message as $word){
						if(strlen($word)>20){
							$format = TRUE;
							$word = wordwrap($word, 20, " ", 1);	
						}
						$formatMessage .= "$word ";
					}
					if($format == TRUE){
						$formatMessage = rtrim($formatMessage);
						$message = htmlspecialchars($formatMessage);
					} else {
						$message = htmlspecialchars($row["message"]);
					}
					
					if($blockOwner{strlen($blockOwner)-1} == 's'){
						$ownership = "'";
					} else {
						$ownership = "'s";
					}
					
					if($this->config['hidePopup'] != 'TRUE'){
						$output.='<span class="popup"><span class="owner">'.htmlspecialchars($blockOwner).$ownership.'</span> block laid <span class="time">'.substr($row['time'], 0, 5).'</span><span class="message">'.$message.'</span><span class="subLine">'.$subLine.'</span></span>';
					}
					$output.="</a> \r\n";
					$output.='</div>';
					$output.="\r\n";
					
					// add the shadow image div to the holding variable if not already drawn
					if($shadowDrawn==NULL){
						$shadowDrawn=TRUE;
						$output.='<div style="position:absolute;width:30px;height:17px;z-index:0;left:'.(($horizontalPos)-$blocks[$row['type']]->getOffsetHorizontal()).'px;bottom:'.($verticalPos-1).'px;"><img style="margin:0;padding:0;border:0;" alt="" src="'.$this->config['installPath'].'images/stack/shadow.gif" /></div>';
					}
				}
			}
		}
		
		$output.='</div>';
		$output.="\r\n";
		
		$stackLabel = $this->getStackLabel();
		// add the stack label to the holding variabe
		$output.='<div style="height:225px;position:absolute;width:30px;z-index:0;left:'.($this->offsetHorizontal).'px;bottom:-0px;">'.$stackLabel.'</div>';
		// update the height of the stack
		$this->height = $verticalPos;
		// return the stack
		return $output;
	}
}