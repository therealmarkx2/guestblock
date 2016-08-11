<?php
class orangeBlock extends block
{
	function construct() {
		$this->restoreDefaults();
		switch ($this->status){
			case 'STACK':{
				if($this->position == 'TOP'){
					$this->imageState='waving';
					$this->subLine='the block is pleased to see you';
					$this->offsetHorizontal = -13;
				}else{
					$this->imageState = 'block';
					$this->subLine = NULL;	
					$this->offsetHorizontal = NULL;
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
}
?>