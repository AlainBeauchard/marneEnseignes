<?php


class Zend_View_Helper_GetNumClientById extends Zend_View_Helper_Abstract{

	public function GetNumClientById($id_client){
		$db_clients = new Application_Model_Clients();
		$client = $db_clients->find($id_client)->current();
		
		if( isset( $client ) )
		{
			if((int) $client->ref)
			{
				return sprintf('%04d',$client->ref);
			}else{
				return "";
			}
		}
		else
			return "";
		

	}
}