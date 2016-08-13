<?php
class watermelonBlock extends block
{
	function construct() {
		$this->restoreDefaults();
		switch ($this->status){
			case 'STACK':{
				if($this->position == 'TOP'){
						$this->imageState='eat';
						$this->subLine='yummy';
						break;	
				} else {
					$ran=rand(1,10);
					switch($ran)
					{
						case $ran==1:{
							$this->imageState='eaten';
							$this->subLine='mmmm, ready to eat';
							break;
						}
					}
				}
			}
		}
	}
}
?>