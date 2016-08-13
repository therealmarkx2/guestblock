<?php
class jackolanternBlock extends block
{
	function getOverride() {
		if(date('m-d',strtotime("now ".$this->timeZone))==date('m-d',strtotime('31 October'))){
			$this->rank = 99;
			$this->override = 1;
			return $this->override;
		}
	}
	
	function construct() {	
		$this->subLine='happy halloween!';		
	}
}
?>