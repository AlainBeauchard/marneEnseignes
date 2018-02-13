<?php

class Application_Model_Devis extends Zend_Db_Table_Abstract
{
	protected $_name = "devis";
	protected $_primary = "id";
	
	public function getMaxYear(){
		$select = $this->select()->order('date ASC')->limit(0,1);
		$rows = $this->fetchRow($select);
		
		list($y,$m,$a) = explode('-', $rows->date);
		
		return $y;
	}
	
	public function getTotalFactures($periode, $etat = 0){
		
		$select = $this->select()
						->where('date like ?', $periode . '-%')
						->where('facture = 1')
						->where('paye = ?', $etat);
						
		$r = $this->fetchAll($select);
		
		$db_items_devis = new Application_Model_ItemDevis();
        $total = 0;
		foreach($r as $f){
			$total += $db_items_devis->getSumFacture($f->id);
		}
		
		return $total;
	}

}

