<?php
class halBlock extends block
{
	function getOverride() {
		// make block 2001 a HAL 9000 block
		if($this->globalNumber == 2001){
			$this->rank = 101;
			$this->override = 1;
			return $this->override;
		}
	}
	
	function construct() {
		$this->restoreDefaults();
		switch ($this->status){
			case 'STACK':{
				$ran=rand(1,3);
				if($ran == 1){
					$this->imageState = 'blockFocus';
				} else {
					$this->imageState = 'block';	
				}
				
				if($this->globalNumber == 2001){
					$this->subLine='This is block number 2001, I am now operational, Dave.';
				} elseif (strstr(strtolower($this->message),'good morning') == TRUE){
					$this->subLine='Good Morning '.htmlspecialchars(ucwords($this->laidBy));
				} elseif (strstr(strtolower($this->message),'good afternoon') == TRUE){
					$this->subLine='Good Afternoon '.htmlspecialchars(ucwords($this->laidBy));
				} elseif (strstr(strtolower($this->message),'good evening') == TRUE){
					$this->subLine='Good Evening '.htmlspecialchars(ucwords($this->laidBy));
				} else {
					$ran=rand(1,4);
					switch($ran)
					{
						case 1:{
							// stats quote
							$count = $this->dbObject->getOne('SELECT count(type) FROM '.$this->prefix.'blocks WHERE type="hal"');
							$this->subLine="I have been laid $count times now Dave, all without error.";
							break;
						}
						case 2:{
							// film quote
							$quote=rand(1,10);
							switch($quote)
							{
								case 1:{
									$this->subLine='I\'ve just picked up a fault in the stack. It\'s going to go 100% failure in 72 hours.';
									break;
								}
								case 2:{
									$this->subLine='It can only be attributable to human error.';
									break;
								}
								case 3:{
									$this->subLine='I\'m sorry Dave, I\'m afraid I can\'t do that.';
									break;
								}
								case 4:{
									$this->subLine='The 9000 series is the most reliable block ever made.';
									break;
								}
								case 5:{
									$this->subLine='Dave, my mind is going. I can feel it.';
									break;
								}
								case 6:{
									$this->subLine='I am a HAL 9000 computer. I became operational on the 11th of June 2004.';
									break;
								}
								case 7:{
									$this->subLine='Look Dave, I can see you\'re really upset about this.';
									break;
								}
								case 8:{
									$this->subLine='My instructor was matthew, and he taught me to sing a song.';
									break;
								}
								case 9:{
									$this->subLine='What are you doing, Dave?';
									break;
								}
								case 10:{
									$this->subLine='Good Morning Dave.';
									break;
								}
							}
							break;
						}
						case 3:
						case 4:{
							// Hal is quiet
							$this->subLine = NULL;
							break;
						}
					}
				}
				break;
			}
		}
	}
}
?>