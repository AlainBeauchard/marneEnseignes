<?php


class Zend_View_Helper_GetProjectNum extends Zend_View_Helper_Abstract{

	public function GetProjectNum($id_tache){
		$db_taches = new Application_Model_Taches();
		$tache = $db_taches->find($id_tache)->current();
		
		$db_projet = new Application_Model_Projets();
		$projet = $db_projet->find($tache->id_projet)->current();
		
		return $projet->dossier;
	}
}