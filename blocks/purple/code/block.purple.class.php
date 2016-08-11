<?php
class purpleBlock extends block
{
	function construct() {
		$this->restoreDefaults();
		switch ($this->status){
			case 'STACK':{
				$ran=rand(1,20);
				switch($ran)
				{
					case $ran==1:{
						$this->imageState='dirty';
						$this->subLine='yuk, looks like this block needs some cleaning';
						break;	
					}
				}	
			}	
		}
	}
}
?>