<?php


class Zend_View_Helper_GetDesignationByIdItem extends Zend_View_Helper_Abstract{

	public function GetDesignationByIdItem($id_item){
		$db_catalogue = new Application_Model_Produits();
		$item = $db_catalogue->find($id_item)->current();
		
		return $item->designation;

	}
}