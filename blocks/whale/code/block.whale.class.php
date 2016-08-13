<?php
class whaleBlock extends block
{
	function construct() {
		$this->restoreDefaults();
		switch ($this->status){
			case 'STACK':{
				if($this->position == 'TOP'){
						$this->imageState='spout';
						$this->subLine='spouting!';
						break;
				}
			}
		}
	}
}
?>
