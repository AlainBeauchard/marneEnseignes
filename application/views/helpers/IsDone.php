<?php


class Zend_View_Helper_IsDone extends Zend_View_Helper_Abstract{

	public function IsDone($id_tache){
		$db_taches = new Application_Model_Taches();
		$tache = $db_taches->find($id_tache)->current();

		if( !isset($tache->fait) || $tache->fait == 0){
			return true;
		}else{
			return false;
		}
	}
}