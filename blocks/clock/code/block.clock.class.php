<?php
class clockBlock extends block
{
	function construct() {
		$this->restoreDefaults();
		
		switch ($this->status){
			case 'INERT':
			case 'DISPLAY':{
				$this->imageState = $this->getHands(strtotime(date('Y-m-d H:i',strtotime('now '.$this->config['timeZone']))));
				break;
			}
			case 'ACTIVE':
			case 'STACK':{
				$this->imageState = $this->getHands(strtotime($this->layDate.' '.$this->layTime));
				break;
			}
		}
	}
	
	function getHands($time){
		$bigHand = date('g',$time);
		$quater = date('i',$time);
		if($quater < 7 | $quater > 52){
			if($quater > 52){
				$bigHand++;	
				if($bigHand > 12){
					$bigHand = 1;	
				}
			}
		}
		if($quater > 6 & $quater < 22){
			$littleHand = '-3';
		}	
		if($quater > 21 & $quater < 37){
			$littleHand = '-6';
		}	
		if($quater > 36 & $quater < 52){
			$littleHand = '-9';
		}
		return $bigHand.$littleHand;
	}
}
?>