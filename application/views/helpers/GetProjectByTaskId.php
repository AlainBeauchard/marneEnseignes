<?php


class Zend_View_Helper_GetProjectByTaskId extends Zend_View_Helper_Abstract{

	public function GetProjectByTaskId($id_tache){
		$db_taches = new Application_Model_Taches();
		$tache = $db_taches->find($id_tache)->current();
		
		return $tache->id_projet;
	}
}