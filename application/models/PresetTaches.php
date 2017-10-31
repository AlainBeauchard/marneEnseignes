<?php

class Application_Model_PresetTaches extends Zend_Db_Table_Abstract
{
	protected $_name = "preset_taches";
	protected $_id = "id";
	
	public function searchPreset($str){
		$select = $this->select()->where('libelle_tache like ?', $str . '%');
		$clients = $this->fetchAll($select);
		
		if(count($clients)){
			foreach($clients as $row){
				$tmp[] = '{ "id":"' . $row->id . '", "value":"' . $row->libelle_tache . '"}';
			}
		
			return new Zend_Json_Expr(implode(',', $tmp));
		}else{
			return false;
		}

	}

}

