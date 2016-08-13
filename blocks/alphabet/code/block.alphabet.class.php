<?php
class alphabetBlock extends block
{
	function construct() {
		$this->restoreDefaults();
		
		$alphabet = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z');
		switch ($this->status){
			case 'STACK':{
				$message = trim($this->message);
				$letter = substr($message,0,1);
				$letter = strtolower($letter);
				if(in_array($letter,$alphabet) == FALSE){
					$letter = $alphabet[array_rand($alphabet)];
				}
				$this->imageState=strtolower($letter);	
			}	
		}
	}
}
?>