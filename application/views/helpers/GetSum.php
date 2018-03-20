<?php


class Zend_View_Helper_GetSum extends Zend_View_Helper_Abstract{

	public function GetSum($devis){
		
		$db_items_devis = new Application_Model_ItemDevis();
		$select = $db_items_devis->select()
								->where('id_devis = ?', $devis);
								
		$rows = $db_items_devis->fetchAll($select);
		
		$total = 0;
		foreach($rows as $row){
		    if (is_null($row->json)) {
				$total += $row->pht;
            } else {
		        $json = json_decode($row->json, true);
		        if ($json['pxvente']) {
		            $total += $json['pxvente'] ;
                }
            }
		}
		
		return sprintf('%0.2f', $total);
	}
}