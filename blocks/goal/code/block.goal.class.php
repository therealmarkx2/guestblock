<?php
class goalBlock extends block
{
	function construct() {
		$this->restoreDefaults();
		switch ($this->status){
			case 'STACK':{
				$ran=rand(1,2);
				switch($ran)
				{
					case $ran==1:{
						$this->imageState='goal';
						$this->subLine='gooooooooooooooooooooooooooooal!';
						break;
					}
				}
			}
		}
	}
}
?>