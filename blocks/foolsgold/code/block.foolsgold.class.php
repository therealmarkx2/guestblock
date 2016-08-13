<?php
class foolsgoldBlock extends block
{
	function getOverride() {
		if(date('m-d',strtotime("now ".$this->timeZone))==date('m-d',strtotime('1 April'))){
			$this->rank = 99;
			$this->override = 1;
			return $this->override;
		}
	}
	
	function construct() {
		$this->subLine='only fool\'s gold';		
	}
}
?>