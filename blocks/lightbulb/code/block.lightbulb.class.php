<?php
class lightbulbBlock extends block
{
	function construct() {
		$this->restoreDefaults();
		
		switch ($this->status){
			case 'STACK':{
				$ran=rand(1,5);
				switch($ran)
				{
					case $ran==1:{
						$this->imageState='deadbulb';
						$this->subLine='uh-oh, the bulb\'s blown!';
						break;	
					}
				}	
			}	
		}
	}
}
?>