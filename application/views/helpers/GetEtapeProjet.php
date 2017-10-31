<?php


class Zend_View_Helper_GetEtapeProjet extends Zend_View_Helper_Abstract{

	public function GetEtapeProjet($id_projet){
		$db_taches = new Application_Model_Taches();
		$select = $db_taches->select()->where('id_projet = ' . $id_projet . ' and fait = 0 and visible = 1')
            ->order(['id_projet DESC', 'date_fait']);
		$taches = $db_taches->fetchAll($select);
		
		return $taches;
	}
}