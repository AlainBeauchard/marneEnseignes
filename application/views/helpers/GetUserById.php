<?php


class Zend_View_Helper_GetUserById extends Zend_View_Helper_Abstract{

	public function GetUserById($id_user){
		$db_users = new Application_Model_Users();
		$user = $db_users->find($id_user)->current();
		
		if( isset( $user ) )
			return $user->nom;
		else
			return '';
	}
}