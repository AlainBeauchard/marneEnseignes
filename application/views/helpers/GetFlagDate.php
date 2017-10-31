<?php


class Zend_View_Helper_GetFlagDate extends Zend_View_Helper_Abstract{

	public function GetFlagDate($d){
		
		$today = new Zend_Date();

		$date = new Zend_Date($d);
		$diff = $date->sub($today)->toValue();
		
		if(intval($diff / 86400) <= 10){
			$color = 'red';
		}else{
			$color = 'green';
		}
		
		return $color;
	}
}