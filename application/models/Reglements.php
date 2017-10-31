<?php

class Application_Model_Reglements extends Zend_Db_Table_Abstract
{
	protected $_name = "preset_mode_reglement";
	protected $_primary = "id";
	
	public function searchReglt($str){
		$select = $this->select()->where('libelle_reglement like ?', $str . '%');
		$clients = $this->fetchAll($select);
		
		foreach($clients as $row){
			$tmp[] = '{ "id":"' . $row->id . '", "value":"' . $row->libelle_reglement . '"}';
		}
		
		return new Zend_Json_Expr(implode(',', $tmp));
	}

}

