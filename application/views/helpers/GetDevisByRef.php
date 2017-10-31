<?php


class Zend_View_Helper_GetDevisByRef extends Zend_View_Helper_Abstract{

	public function GetDevisByRef($ref){
		$db_devis = new Application_Model_Devis();
		$select = $db_devis->select()->where('ref = ?', $ref);
		
		$devis = $db_devis->fetchRow($select);
		
		if($devis != null)
		{
			return '<a href="/facturation/fiche/id/' . $devis->id . '">Voir le devis</a>';
		}else{
			return "Aucun devis pour ce projet";
		}

	}
}