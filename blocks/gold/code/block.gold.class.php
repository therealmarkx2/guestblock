<?php
class goldBlock extends block
{
	var $rank = 100;
	var $override = 150;
	
	function construct() {
		$this->subLine='congratulations, the prized gold block!';
	}
}
?>