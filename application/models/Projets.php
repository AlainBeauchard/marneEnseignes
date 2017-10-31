<?php

class Application_Model_Projets extends Zend_Db_Table_Abstract
{
	protected $_name = "projets";
	protected $_primary = "id_projet";
	
	public function searchProjet($str){
		$select = $this->select()->where('titre like ?', $str . '%');
		$clients = $this->fetchAll($select);
		
		foreach($clients as $row){
			$tmp[] = '{ "id":"' . $row->id_projet . '", "value":"' . $row->titre . '"}';
		}
		
		return new Zend_Json_Expr(implode(',', $tmp));
	}
}

