<?php


class Zend_View_Helper_GetColorByUser extends Zend_View_Helper_Abstract{

	public function GetColorByUser($id_user){
		$db_users = new Application_Model_Users();
		$user = $db_users->find($id_user)->current();
		
		if( isset( $user ) )
			return $user->color;
		else
			return '';
	}
}