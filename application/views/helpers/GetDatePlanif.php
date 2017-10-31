<?php


class Zend_View_Helper_GetDatePlanif extends Zend_View_Helper_Abstract{

	public function GetDatePlanif($id_tache){
		$db_calendar = new Application_Model_Calendrier();
		$select = $db_calendar->select()->where('id_tache = ?', $id_tache);
		
		$row = $db_calendar->fetchRow($select);
		
		if( isset($row) )
		{
			if((int)$row->date){
				list($y,$m,$d) = explode('-', $row->date);
			
				return $d."/".$m.'/'.$y;
			}else{
				return "<span style='background-color:orange;'>N.P.</span>";
			}
		}
		else
			return "<span style='background-color:orange;'>N.P.</span>";

	}
}