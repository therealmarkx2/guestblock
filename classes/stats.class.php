<?php
/**
* @author Matthew Hadley
* @package guestblock
* @license http://www.gnu.org/licenses/gpl.txt
* @desc contains code for stats class
*/
/**
* @author Matthew Hadley
* @package guestblock
* @subpackage stats
* @desc stats class handles guestblock stats generation, provides a basic stats set that will be extensible by further stats packages
*/
class stats
{
	/** @var object $guestblock guestblock object to use for stats operations */
	var $guestblock;
	/** @var object $dbObject PEAR DB object to access database */
	var $dbObject;
	/** @var array $config string array of config variables */
	var $config;
	/** @var string $prefix database table string prefix for installation */
	var $prefix;
	/**
	* @var array $blocks cached array of block classes
	* @see guestblock::createBlocks()
	* @see block
	*/
	var $blocks;

	/**
	* constructor function
	* loads up config file
	* @see guestblock
	* @param object $dbObject PEAR DB object to access database
	* @param object $guestblock guestblock object to perform stats operations on
	*/
	function stats($dbObject, $guestblock)
	{
		$this->dbObject=$dbObject;
		$this->guestblock = $guestblock;
		$this->prefix = $this->guestblock->getTablePrefix();
		$this->config = $this->guestblock->getConfig();
		$this->fetchBlocks();
	}

	/**
	* Get blocks types to play with
	*/
	function fetchBlocks(){
		$this->blocks = $this->guestblock->createBlocks(TRUE);
	}
	
	/**
	* Get static type details for a requested block type or an array of all block types
	* @param string $type type of block requested, if NULL return all types
	* @param string $bucket return blocks in requested bucket if not null
	* @param string $bucket return blocks that do not belong to a bucket
	* @return array array of single type requested or multi array of all types
	*/
	function getBlockType($type = NULL, $bucket = NULL, $bucketless = FALSE){
		// get details for a block
		if($type != NULL){
			$where = 'WHERE type="'.$type.'" ';
		}
		if($bucket != NULL){
			$where = 'WHERE '.$this->prefix.'types.bucket="'.$bucket.'" ';
		}
		if($bucketless == TRUE){
			$where = 'WHERE '.$this->prefix.'types.bucket IS NULL or '.$this->prefix.'types.bucket = "" ';
		}
		$result = $this->dbObject->query('SELECT '.$this->prefix.'types.*, '.$this->prefix.'buckets.name AS bucketname FROM '.$this->prefix.'types LEFT JOIN '.$this->prefix.'buckets ON '.$this->prefix.'types.bucket = '.$this->prefix.'buckets.bucket '.$where.'ORDER BY '.$this->prefix.'types.name');
		while($result->fetchInto($row)){
			$blockData[$row['type']] = array(
			'type' => $row['type'],
			'bucket' => $row['bucket'],
			'bucketName' => $row['bucketname'],
			'description' => htmlspecialchars($row['description']),
			'name' => htmlspecialchars($row['name']),
			'display' => $row['display'],
			'code' => $row['code'],
			'active' => $row['active']);
		}
		if($type != NULL){
			$blockData  = $blockData[$type];
		}
		return $blockData;
	}

	/**
	* Get details for block laid
	* @param integer $blockid blockid of block data requested
	* @return array array of data for requested blockid
	*/
	function getBlock($blockid){
		// get details for a block
		if($blockid != NULL){
			$result = $this->dbObject->query('SELECT * FROM '.$this->prefix.'blocks WHERE blockid='.$blockid);
			while($result->fetchInto($row)){
				$blockData = array(
				'blockid' => $row['blockid'],
				'type' => $row['type'],
				'name' => htmlspecialchars($row['name']),
				'message' => htmlspecialchars($row['message']),
				'url' => htmlspecialchars($row['url']),
				'email' => htmlspecialchars($row['email']));
			}
		}
		return $blockData;
	}

	/**
	* Get buckets that block types belong to
	* @param string $bucket bucket requested, if NULL return all types
	* @return array of buckets
	*/
	function getBucket($bucket = NULL){
		if($bucket != NULL){
			$where = ' WHERE bucket="'.$bucket.'" ';
		}
		$result=$this->dbObject->query('SELECT * FROM '.$this->prefix.'buckets'.$where);
		while($result->fetchInto($row)){
			$resultBlocks=$this->dbObject->query('SELECT * FROM '.$this->prefix.'types WHERE bucket="'.$row['bucket'].'" ORDER BY type');
			while($resultBlocks->fetchInto($rowBlocks)){
				$blocks[] = array(
				'type' => $rowBlocks['type'],
				'name' => $rowBlocks['name']);
			}

			$bucketData[$row['bucket']]=array(
			'bucket' => $row['bucket'],
			'name' => $row['name'],
			'description' => $row['description'],
			'count' => sizeof($blocks),
			'blocks' => $blocks);
			$blocks = NULL;

		}

		return $bucketData;
	}

	/**
	* Get number of blocks laid
	* @param boolean $approved set if should only count blocks that have been approved
	* @return integer block count
	*/
	function getBlocksCount($approved = FALSE){
		if($approved == TRUE){
			$where =  ' WHERE approved="TRUE"';	
		}
		return  $this->dbObject->getOne('SELECT COUNT(blockid) FROM '.$this->prefix.'blocks'.$where);
	}

	/**
	* Get number of types installed
	* @return integer block type count
	*/
	function getTypesCount(){
		return $this->dbObject->getOne('SELECT COUNT(type) FROM '.$this->prefix.'types');
	}

	

	/**
	* Get the latest blocks to be laid as a list
	* @param integer $number te number of blocks to include in the list
	* @return xhtml list of the latest blocks to be laid
	*/
	function getLatestBlocks($number=5, $dateFormat = 'l jS \o\f F Y', $link = NULL){
		// get the blocks to play with
		$blocks = $this->blocks;
		
		$query='SELECT '.$this->prefix.'stacks.date, '.$this->prefix.'blocks.* FROM '.$this->prefix.'blocks LEFT JOIN '.$this->prefix.'stacks ON '.$this->prefix.'blocks.stackid = '.$this->prefix.'stacks.stackid WHERE approved="TRUE" ORDER BY '.$this->prefix.'blocks.stackid DESC, time DESC LIMIT 0,'.$number;
		$result=$this->dbObject->query($query);

		$output = '<ul>';
		while($result->fetchInto($row)){
			$defalutShadow='<img style="z-index:0;position:relative;left:-13px;top:5px" alt="" src="images/stack/shadow.gif"/>';
			$blocks[$row['type']]->setStatus('ACTIVE');
			$blocks[$row['type']]->setLayDate($row['date']);
			$blocks[$row['type']]->setLayTime($row['time']);
			$blocks[$row['type']]->construct();
			$blocks[$row['type']]->getImageState();
			if($row['name']!=NULL){
				$name = htmlspecialchars($row['name']);
			}else{
				$name = 'nobody';
			}
			if($row['url']!=NULL){
				$blocks[$row['type']]->setUrl(htmlspecialchars($row['url']));
				$linkStart='<a href="'.$blocks[$row['type']]->getUrl().'">';
				$linkEnd='</a>';
			} else {
				$linkStart = NULL;
				$linkEnd = NULL;
			}
			if($blocks[$row['type']]->getShadow() == TRUE){
				$shadow = '<img style="z-index:0;position:relative;left:-13px;top:5px" alt="" src="'.$this->config['installPath'].'images/stack/shadow.gif"/> ';
			}
			$output.='<li>';
			if($link != NULL){
				$output.= '<a href="'.$link.'">';		
			}
			$output.='<img alt="'.$blocks[$row['type']]->getName().' block" title="'.$blocks[$row['type']]->getName().' block" style="z-index:1;position:relative;" src="'.$this->config['installPath'].'blocks/'.$blocks[$row['type']]->getType().'/images/'.$blocks[$row['type']]->getImageState().'.gif" />'.$shadow;
			if($link != NULL){
				$output.= '</a>';	
			}
			$output.=' by '.$linkStart.$name.$linkEnd.'<br/>'.substr($row['time'], 0, 5).', '.date($dateFormat,strtotime($row['date'])).'</li>';
		}
		$output .= '</ul>';
		return $output;
	}
	
	/**
	* Get number of blocks laid for the current day
	* @param boolean $approved set if should only count blocks that have been approved
	* @return integer block count
	*/
	function getBlocksTodayCount($approved = FALSE){
		if($approved == TRUE){
			$where =  'approved="TRUE" AND';	
		}
		return $this->dbObject->getOne('SELECT COUNT(blockid) FROM '.$this->prefix.'blocks WHERE '.$where.' stackid = '.$this->guestblock->getCurrentStackId());
	}
	
	/**
	* Get the stack details for the tallest stack made
	* @return array stack details in an array
	*/
	function getBestStack(){
		$bestStack = $this->dbObject->query('SELECT '.$this->prefix.'blocks.stackid, COUNT(blockid) AS blocks, date FROM '.$this->prefix.'blocks
	LEFT JOIN
	'.$this->prefix.'stacks ON
	'.$this->prefix.'blocks.stackid = '.$this->prefix.'stacks.stackid WHERE approved="TRUE"
	GROUP BY '.$this->prefix.'blocks.stackid ORDER BY blocks DESC, '.$this->prefix.'blocks.stackid DESC LIMIT 0,1');
		while($bestStack->fetchInto($row)){
			$stack['stackid'] = $row['stackid'];
			$stack['date'] = date('l jS \o\f F Y',strtotime($row['date']));
			$stack['count'] = $row['blocks'];
			$date = date('Y-m-d',strtotime($row['date']));
			$year = substr($date,0,4);
			$month = substr($date,5,2);
			$day = (substr($date,8,2));
			$stack['link'] = "?year=$year&amp;month=$month&amp;day=$day";
		}
		return $stack;
	}

	/**
	* Get number of stacks built so far
	* @param boolean $approved set if should only count stacks that are not made up entirely of non-approved blocks
	* @return integer stack count
	*/
	function getStacksCount($approved = FALSE){
		if($approved == TRUE){
			// do not count stacks made up of entirely of non-approved blocks
			return $this->dbObject->getOne('SELECT COUNT(DISTINCT('.$this->prefix.'stacks.stackid)) FROM  '.$this->prefix.'stacks LEFT JOIN  '.$this->prefix.'blocks ON  '.$this->prefix.'stacks.stackid =  '.$this->prefix.'blocks.stackid WHERE approved="TRUE"');
		} else {
			return $this->dbObject->getOne('SELECT COUNT(stackid) FROM '.$this->prefix.'stacks');
		}	
	}
	
	/**
	* Get block images and their desciptions in xhtml unordered list
	* @return string xhtml to display block images and their description
	*/
	function getAboutBlockTypes(){
		$output = '<ul id="aboutBlocks">';
		foreach($this->blocks as $block){
			$output .= '<li><a href="#" id="'.$block->getType().'"></a>';
			$block->setStatus('INERT');
			$block->construct();
			$output .= '<h2>'.$block->getName().'</h2>';
			$output.='<img alt="'.$block->getName().' block" title="'.$block->getName().' block" style="z-index:1;position:relative;" src="'.$this->config['installPath'].'blocks/'.$block->getType().'/images/'.$block->getImageState().'.gif" />';
			$output.='<img style="z-index:0;position:relative;left:-13px;top:5px" alt="" src="'.$this->config['installPath'].'images/stack/shadow.gif"/>';
			$output.= $block->getDescription();
			$output .= '</li>';
		}
		$output .= '</ul>';
		return $output;
	}
}
?>