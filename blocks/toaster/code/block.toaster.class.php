<?php
class toasterBlock extends block
{
	function construct() {
		$this->restoreDefaults();
		switch ($this->status){
			case 'STACK':{
				if($this->position == 'TOP'){
						$this->imageState='burn';
						$this->subLine='burn, baby burn';
						break;	
				}	
			}
		}
	}
}
?>