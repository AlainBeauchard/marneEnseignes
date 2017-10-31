<?php


class Zend_View_Helper_GetClientById extends Zend_View_Helper_Abstract{

	public function GetClientById($id_client){
		$db_clients = new Application_Model_Clients();
		$client = $db_clients->find($id_client)->current();
		
		if( isset($client) )
			return $client->societe;
		else
			return '';
	}
}