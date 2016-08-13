<?php
class iceBlock extends block
{
	function construct() {
		$this->restoreDefaults();
		
		switch ($this->status){
			case 'ACTIVE':{
					$this->shadow = FALSE;
					$this->imageState='melt';
			}	
		}
	}
}
?>