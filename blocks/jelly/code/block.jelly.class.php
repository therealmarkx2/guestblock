<?php
class jellyBlock extends block
{
	function construct() {
		switch ($this->status){
			case 'ACTIVE':
			case 'STACK':{
				$ran=rand(1,3);
				$this->imageState=$ran;
			}	
		}
	}
}
?>