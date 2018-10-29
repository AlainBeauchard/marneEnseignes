<?php


class Zend_View_Helper_GetSum extends Zend_View_Helper_Abstract{

	public function GetSum($devis)
    {
		$db_items_devis = new Application_Model_ItemDevis();
		$select = $db_items_devis->select()
								->where('id_devis = ?', $devis);

		$rows = $db_items_devis->fetchAll($select);

		$total = 0;
		foreach($rows as $row){
		    if (is_null($row->json)) {
				$total += $row->pht;
            } else {
		        if ($row->typeligne !== 'itemredaction') {
                    $json = json_decode($row->json, true);
                    if ($json['pxvente']) {
                        $total += $json['pxvente'];
                    }
                }
            }
		}

		/*
		// On doit ajouter le montant entete
        $db_devis = new Application_Model_Devis();
        $select = $db_devis->select()
            ->where('id = ?', $devis);

        $rows = $db_devis->fetchAll($select);
        foreach($rows as $row){
            if (! is_null($row->jsonEntete)) {
                $json = json_decode($row->jsonEntete, true);
                foreach($json as $elem) {
                    if ($elem['perimetreTableEntete']) {
                        $total += $elem['perimetreTableEntete'] ;
                    }
                }
            }
        }
		*/

        return sprintf('%0.2f', $total);
	}
}