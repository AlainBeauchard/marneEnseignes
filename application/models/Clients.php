<?php

class Application_Model_Clients extends Zend_Db_Table_Abstract
{
	protected $_name = "clients";
	protected $_primary = "id_client";
	
	public function searchClient($str){
	    $str = ltrim($str,'0');
		$select = $this->select()
			->where('societe like ?', $str . '%')
			->orWhere('ref like ?', $str);
			
		$clients = $this->fetchAll($select);
		
		foreach($clients as $row){
			$tmp[] = '{ "id":"' . $row->id_client . '", "value":"' . $row->societe . '", "ref":"' . $row->ref . '"}';
		}
		
		return new Zend_Json_Expr(implode(',', $tmp));
	}
	
		public function searchCodeClient($str){
	    $str = ltrim($str,'0');
		$select = $this->select()
			->where('ref like ?', $str);
			
		$clients = $this->fetchRow($select);
		
		foreach($clients as $key=>$value){
			$datas[] = '"' . $key . '":"' . $value . '"';
		}
		
		return new Zend_Json_Expr(@implode(',', $datas));
	}

}

