<?php


class Zend_View_Helper_GetTaskById extends Zend_View_Helper_Abstract{

	public function GetTaskById($id_tache){
		$db_taches = new Application_Model_Taches();
		$tache = $db_taches->find($id_tache)->current();
		
		$db_projet = new Application_Model_Projets();
		$projet = $db_projet->find($tache->id_projet)->current();
		
		return $projet->titre . '<br /> - ' . $tache->tache;
	}
}