<?php

class Application_Model_ItemDevis extends Zend_Db_Table_Abstract
{
	protected $_name = "itemsdevis";
	protected $_primary = "id";
	
	
	public function getSumFacture($id_facture){
		
		$select = $this->select()
						->from($this->_name, 'SUM(pht) as montant')	
						->where('id_devis = ?', $id_facture);
						
		$r = $this->fetchRow($select);
		
		return $r->montant;
		
	}
	

}

