<?php
/**
* @author Matthew Hadley
* @package guestblock
* @license http://www.gnu.org/licenses/gpl.txt
* @desc contains code for block class
*/
/**
* @author Matthew Hadley
* @package guestblock
* @subpackage engine
* @desc block class handles block functions and can be a parent class for block types with extended functions.
* All the block types are generated as their own block class (some of these classes are extened when the block type has it's own class file).
* These classes ar stored in an array which is used to then get the imageState to be drawn as the blocks are constructed into stacks.
* As each stack is built the class is given information about the next block being laid (such as time laid, global number, position etc) this data may alter the image that the block will show (in blocks that have extending classes the stock block variables are reset after a block is laid).
*/
class block
{
	/** @var object $dbObject PEAR DB object to access database */
	var $dbObject;
	/** @var string $type the block type */
	var $type;
	/** @var string $name the block type name */
	var $name;
	/** @var string $description the block description */
	var $description;
	/** @var bool $display whether the block is displayed in the block selection form */
	var $display;
	/** @var string $imageState the image to be displayed when the block::getImageState() function is called */
	var $imageState = 'block';
	/** @var bool $code whether the block has own class file code to extend this parent class */
	var $code;
	/** @var string $status sets where the block is to be displayed 
	* 'STACK' - in a stack
	* 'DISPLAY' - in the form to lay a block
	* 'ACTIVE' - used for stats, construct image
	* 'INERT' - used for stats, default image
	*/
	var $status;
	/** @var string $subLine the text to be displayed under the block layer message */
	var $subLine;
	/** @var string $position the position of the block in the stack
	* 'TOP' - top of the current stack
	*/
	var $position;
	/** @var string $layDate date block was laid */
	var $layDate;
	/** @var string $layTime time block was laid */
	var $layTime;
	/** @var integer $count number of times block has been laid */
	var $count;
	/** @var bool $shadow will this block cast a shadow in he appropriate positions */
	var $shadow = TRUE;
	/** @var integer $offsetHorizontal if the block image is different from the default 13*13 how should the block be offset horizontally to ensure correct positioning in the stack */
	var $offsetHorizontal = 0;
	/** @var integer $offsetVertical if the block image is different from the default 13*13 how should the block be offset vertically to ensure correct positioning in the stack */
	var $offsetVertical = 0;
	/** @var integer $globalNumber what number block the current block is out of all the blocks laid so far */
	var $globalNumber;
	/** @var string $message the message given to the block */
	var $message;
	/** @var string $laidBy the block layer's name */
	var $laidBy;
	/** @var string $url the url given to the block by the layer */
	var $url;
	/** @var string $prefix database table string prefix for installation */
	var $prefix;
	/** @var string $timeZone the time zone offset from the server time in hours */
	var $timeZone;
	/** @var integer $rank the block rank. The higher the rank the more likely it is that this block will appear more than other easter egg blocks attempting to butt in */
	var $rank=1;
	/** @var integer $override the block rank. Chance the block has of appearing as an easter egg block 1 = always, above 1 means (1/$override) chance of appearing */
	var $override=0; 
	/** @var boolean $active whether this block is active and so can be used */
	var $active;
	/** @var boolean $data whether this block has custom persistent data in the blocks_data table */
	var $data;
	
	function block()
	{
		// constructor	
	}
	
	/**
	* Restore the default values to the stock block vars. This is necessary for blocks with an extending class so that the factors determing the block image are not rolled over into the next image state
	*/
	function restoreDefaults(){
		$this->imageState = 'block';
		$this->subLine = NULL;
		$this->shadow = TRUE;
		$this->offsetHorizontal = 0;
		$this->offsetVertical = 0;
	}
	
	/**
	* set the timezone offset
	* @param integer $timeZone the hours offset from the current system time
	*/
	function setTimeZone($timeZone){
		$this->timeZone = $timeZone;	
	}
	
	/**
	* set if block is active or disabled (this is different from merely not being displayed)
	* blocks which are not active can no longer be laid but existing laid blocks of the same type will show
	* disabling a block type is an alternative to deleting it
	* @param bool $active block active status
	*/
	function setActive($active){
		
		$this->active = $active;	
	}
	
	/**
	* Return the block active status
	* @return bool block active status
	*/
	function getActive(){
		return $this->active;
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
	* Get the block rank
	* @return integer block rank value
	*/
	function getRank(){
		return $this->rank;
	}
	
	/**
	* Get the block ovverride
	* @return integer $this->override
	*/
	function getOverride(){
		return $this->override;
	}
	
	/**
	* Get the block global number
	* @return integer global block number
	*/
	function getGlobalNumber(){
		return $this->globalNumber;
	}
	
	/**
	* Set the block global number
	* @param integer $number the number of the block out of all the blocks laid
	*/
	function setGlobalNumber($number){
		$this->globalNumber = $number;
	}
	
	/**
	* Set the block message, the block is told what message in case it needs to make use of it
	* @param string $message the message to be shown with the block
	*/
	function setMessage($message){
		$this->message = $message;	
	}
	
	/**
	* return the message to display with the block
	* @return string the message to display
	*/
	function getMessage(){
		return $this->message;	
	}
	
	/**
	* Set the block url, the block is told what message in case it needs to make use of it
	* @param string $url the url to be shown with the block
	*/
	function setUrl($url){
		$this->url = $url;	
	}
	
	/**
	* return the url to display with the block
	* @return string the url to display
	*/
	function getUrl(){
		return $this->url;	
	}
	
	
	/**
	* Set the block layer
	* @param string $laidBy who laid the block
	*/
	function setLaidBy($laidBy){
		$this->laidBy = $laidBy;	
	}
	
	/**
	* Get the block horizontal offsetting
	* @return integer horizontal offset
	*/
	function getOffsetHorizontal(){
		return $this->offsetHorizontal;	
	}
	
	/**
	* Get the block vertical offsetting
	* @return integer vertical offset
	*/
	function getOffsetVertical(){
		return $this->offsetVertical;	
	}
	
	/**
	* Get the block shadow casting status
	* @return bool shadow status
	*/
	function getShadow(){
		return $this->shadow;	
	}
	
	/**
	* Set the number of times the block has been laid
	* @param string $count number of times block has been laid
	*/
	function setCount($count){
		$this->count = $count;	
	}
	
	/**
	* Get the number of times the block has been laid
	* @return integer number of times block has been laid
	*/
	function getCount(){
		return $this->count;	
	}
	
	/**
	* Set the date the block has been laid
	* @param string $date date block laid
	*/
	function setLayDate($date){
		// set the date the block was laid
		$this->layDate = $date;
	}
	
	/**
	* Set the time the block has been laid
	* @param string $time time block laid
	*/
	function setLayTime($time){
		// set the time the block was laid
		$this->layTime = $time;
	}
		
	/**
	* Set the position in the stack that the block occupies
	* @param string $position block position in stack
	*/
	function setPosition($position){
		$this->position = $position;	
	}
	
	/**
	* Get the image for the block
	* @return string the block image to be shown for the current block state
	*/
	function getImageState() {
		return $this->imageState;
	}
	
	/**
	* Get the sub message
	* @return string the message to be shown with the block (separate from the block layer essage)
	*/
	function getSubLine(){
		return $this->subLine;
	}
	
	/**
	* Set if the block has an extending class
	* @param bool $code block class existence
	*/
	function setCode($code){
		$this->code = $code;	
	}
	
	/**
	* Get the block extending class status
	* @return bool whether the block has an extending class
	*/
	function getCode(){
		return $this->code;	
	}
	
	/**
	* Set the block type
	* @param string $type the block type
	*/
	function setType($type){
		$this->type=$type;
	}
	
	/**
	* Get the block type
	* @return string the block type
	*/
	function getType(){
		return $this->type;
	}
	
	/**
	* Set the block layer name, the block is told the layer name it will be displaying in case it changes the image state
	* @param string $name the block layer name
	*/
	function setName($name){
		$this->name=$name;
	}
	
	/**
	* Get the block name
	* @return string the block name
	*/
	function getName(){
		return $this->name;	
	}
	
	/**
	* Set the block description
	* @param string $description the block description
	*/
	function setDescription($description){
		$this->description=$description;
	}
	
	/**
	* Get the block description
	* @return string the block description
	*/
	function getDescription(){
		return $this->description;	
	}
	
	/**
	* Set the block display value, whether it will appear in the block selection form
	* @param bool $display the block display status
	*/
	function setDisplay($display){
		$this->display=$display;
	}
	
	/**
	* Set if block type stores custom data in the blocks_data table, used by block types that extend the parent block class
	* @param bool $data custom data flag
	*/
	function setData($data){
		$this->data=$data;
	}
	
	/**
	* Get the block display value
	* @return bool the block display status
	*/
	function getDisplay(){
		return $this->display;	
	}
	
	/** 
	* Set the block status, which is where the block is currently
	* @param string $status the block status
	* @see block::status
	*/
	function setStatus($status) {
		$this->status = $status;
	}
	
	/**
	* Construct the block, does nothing for default block class as stock image vars will be used. Other block types that have extended classes will do something special.
	*/
	function construct() {
	}
}
?>