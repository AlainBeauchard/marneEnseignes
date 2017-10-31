<?php


class Zend_View_Helper_GetDateDone extends Zend_View_Helper_Abstract{

	public function GetDateDone($id_tache){
		$db_calendrier = new Application_Model_Calendrier();
		$select = $db_calendrier->select()->where('id_tache = ?', $id_tache);

		$rows = $db_calendrier->fetchAll($select);
		
		if(count($rows->toArray()) == 0){
			return;
		}else{
			foreach($rows as $cal){
				$date = $cal->date;
			}
			return date('d/m/Y', strtotime($date));	
		}

	}
}