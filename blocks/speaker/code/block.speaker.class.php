<?php
class speakerBlock extends block
{
	function construct() {
		switch ($this->status){
			case 'ACTIVE':
			case 'STACK':{
				$ran=rand(1,4);
				$this->imageState=$ran;
			}	
		}
	}
}
?>