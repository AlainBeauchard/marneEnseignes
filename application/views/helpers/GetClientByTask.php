<?php


class Zend_View_Helper_GetClientByTask extends Zend_View_Helper_Abstract{

	public function GetClientByTask($id_tache){
		$db_taches = new Application_Model_Taches();
		$tache = $db_taches->find($id_tache)->current();
		
		$db_projet = new Application_Model_Projets();
		$projet = $db_projet->find($tache->id_projet)->current();
		
		$db_client = new Application_Model_Clients();
		$client = $db_client->find($projet->id_client)->current();

		if(isset($client->societe))
		    return $client->societe;
		else
		    return '';
	}
}