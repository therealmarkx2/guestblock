<?php
class shrubBlock extends block
{
	function construct() {
		$this->restoreDefaults();
		
		switch ($this->status){
			case 'STACK':{
				$ran=rand(1,10);
				switch($ran)
				{
					case $ran==1:{
						$this->imageState='flower';
						$this->subLine='bloomin\' lovely!';
						break;	
					}
				}	
			}	
		}
	}
}
?>